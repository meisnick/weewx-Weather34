#!/usr/bin/env python3
"""
forecast_logger.py — NWS hourly forecast logger for MKX/85,85

Fetches the NWS hourly forecast and stores each forecast period so we can
later compare what was predicted vs. what the weewx station actually recorded.

Deploy to pi:
  scp forecast_logger.py pi:/home/pi/
  ssh pi "crontab -e"   # add:  0 */4 * * * /usr/bin/python3 /home/pi/forecast_logger.py

Forecast DB: /var/lib/weewx/forecast_log.db
weewx DB:    /var/lib/weewx/weewx.sdb

Usage:
  python3 forecast_logger.py             # log latest forecast (cron mode)
  python3 forecast_logger.py --report    # print verification table
  python3 forecast_logger.py --report --days 14
"""
import argparse
import json
import re
import sqlite3
import urllib.request
from datetime import datetime, timezone, timedelta
from pathlib import Path

import os
import sys
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
import w34config as cfg

FORECAST_URL  = "https://api.weather.gov/gridpoints/MKX/85,85/forecast/hourly"
FORECAST_DB   = Path(cfg.NOWCAST_LOCAL_LOG_DB)
WEEWX_DB      = Path("/var/lib/weewx/weewx.sdb")
HEADERS       = {"User-Agent": "weewx-forecast-compare/1.0 (meisnickbuck@gmail.com)"}

WIND_DIR_DEGREES = {
    "N": 0, "NNE": 22, "NE": 45, "ENE": 67,
    "E": 90, "ESE": 112, "SE": 135, "SSE": 157,
    "S": 180, "SSW": 202, "SW": 225, "WSW": 247,
    "W": 270, "WNW": 292, "NW": 315, "NNW": 337,
}

def wind_dir_to_deg(s):
    return WIND_DIR_DEGREES.get(s.strip().upper())

def wind_speed_mph(s):
    """Parse '10 mph' or '10 to 15 mph' → average mph as float."""
    nums = [int(x) for x in re.findall(r'\d+', s)]
    return sum(nums) / len(nums) if nums else None

def iso_to_unix(s):
    """Parse NWS ISO-8601 timestamp → Unix integer."""
    dt = datetime.fromisoformat(s)
    return int(dt.astimezone(timezone.utc).timestamp())

def init_db(conn):
    conn.execute("""
        CREATE TABLE IF NOT EXISTS forecasts (
            id             INTEGER PRIMARY KEY,
            run_time       INTEGER NOT NULL,
            valid_time     INTEGER NOT NULL,
            lead_h         INTEGER NOT NULL,
            temp_f         REAL,
            wind_dir       TEXT,
            wind_dir_deg   INTEGER,
            wind_spd_mph   REAL,
            precip_pct     INTEGER,
            short_forecast TEXT,
            UNIQUE(run_time, valid_time)
        )
    """)
    conn.commit()

def fetch_forecast():
    req = urllib.request.Request(FORECAST_URL, headers=HEADERS)
    with urllib.request.urlopen(req, timeout=20) as r:
        return json.loads(r.read())

def log_forecast(conn, data):
    run_time = int(datetime.now(timezone.utc).timestamp())
    periods = data["properties"]["periods"]
    inserted = 0
    for p in periods:
        valid_time = iso_to_unix(p["startTime"])
        lead_h = round((valid_time - run_time) / 3600)
        if lead_h < 0 or lead_h > 168:
            continue
        precip = None
        if p.get("probabilityOfPrecipitation") and p["probabilityOfPrecipitation"].get("value") is not None:
            precip = int(p["probabilityOfPrecipitation"]["value"])
        try:
            conn.execute("""
                INSERT OR IGNORE INTO forecasts
                  (run_time, valid_time, lead_h, temp_f, wind_dir, wind_dir_deg,
                   wind_spd_mph, precip_pct, short_forecast)
                VALUES (?,?,?,?,?,?,?,?,?)
            """, (
                run_time,
                valid_time,
                lead_h,
                float(p["temperature"]) if p["temperatureUnit"] == "F" else float(p["temperature"]) * 9/5 + 32,
                p.get("windDirection"),
                wind_dir_to_deg(p.get("windDirection", "")),
                wind_speed_mph(p.get("windSpeed", "")),
                precip,
                p.get("shortForecast", "")[:80],
            ))
            inserted += conn.execute("SELECT changes()").fetchone()[0]
        except Exception as e:
            print(f"  Insert error for valid_time {valid_time}: {e}")
    conn.commit()
    return inserted, len(periods)

def get_weewx_hourly(weewx_conn, valid_time):
    """Average weewx 5-min records within ±30 min of valid_time."""
    lo, hi = valid_time - 1800, valid_time + 1800
    row = weewx_conn.execute("""
        SELECT
            AVG(outTemp)    as temp_f,
            AVG(dewpoint)   as dewpt_f,
            AVG(outHumidity) as rh,
            AVG(windDir)    as wind_deg,
            AVG(windSpeed)  as wind_mph,
            AVG(barometer)  as pressure,
            COUNT(*)        as n
        FROM archive
        WHERE dateTime >= ? AND dateTime < ?
          AND outTemp IS NOT NULL
    """, (lo, hi)).fetchone()
    return row if row and row["n"] and row["n"] >= 2 else None

def report(conn, weewx_conn, days=30, lead_filter=None):
    cutoff = int((datetime.now(timezone.utc) - timedelta(days=days)).timestamp())
    now    = int(datetime.now(timezone.utc).timestamp())

    # For each past valid_time, pick the forecast made ~24h before (lead 22-26h)
    # or closest available lead if --lead is specified
    if lead_filter:
        lead_lo, lead_hi = lead_filter - 2, lead_filter + 2
    else:
        lead_lo, lead_hi = 22, 26

    rows = conn.execute("""
        SELECT valid_time, AVG(temp_f) as fc_temp,
               wind_dir, AVG(wind_spd_mph) as fc_wind,
               MIN(ABS(lead_h - ?)) as best_lead
        FROM forecasts
        WHERE valid_time >= ? AND valid_time < ?
          AND lead_h BETWEEN ? AND ?
        GROUP BY valid_time
        ORDER BY valid_time
    """, (
        (lead_lo + lead_hi) // 2,
        cutoff, now,
        lead_lo, lead_hi,
    )).fetchall()

    if not rows:
        print("No verified forecast periods yet (need forecast data logged before the valid time).")
        return

    print(f"\n{'Valid Time (CDT)':<20} {'FC':>5} {'Actual':>7} {'Bias':>6}  {'Wind Dir':>8}  Short Forecast")
    print("-" * 85)

    total_bias = []
    by_dir = {}

    for row in rows:
        actual = get_weewx_hourly(weewx_conn, row["valid_time"])
        if not actual:
            continue

        fc_temp  = row["fc_temp"]
        act_temp = actual["temp_f"]
        bias     = act_temp - fc_temp
        total_bias.append(bias)

        wdir = row["wind_dir"] or "?"
        by_dir.setdefault(wdir, []).append(bias)

        dt = datetime.fromtimestamp(row["valid_time"], tz=timezone.utc).astimezone(
            timezone(timedelta(hours=-5))
        )
        print(f"{dt.strftime('%m/%d %H:%M'):>16}      {fc_temp:5.1f}F  {act_temp:6.1f}F  {bias:+5.1f}F  {wdir:>8}")

    if total_bias:
        mean = sum(total_bias) / len(total_bias)
        print(f"\n  Overall mean bias: {mean:+.1f}F  ({len(total_bias)} hours verified)")
        print(f"\n  Bias by NWS forecast wind direction:")
        for d, biases in sorted(by_dir.items(), key=lambda x: WIND_DIR_DEGREES.get(x[0], 999)):
            m = sum(biases) / len(biases)
            print(f"    {d:>4}: {m:+.1f}F  (n={len(biases)})")

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--report", action="store_true")
    ap.add_argument("--days",   type=int, default=30)
    ap.add_argument("--lead",   type=int, default=None,
                    help="Lead time in hours to verify (default: 24h)")
    args = ap.parse_args()

    FORECAST_DB.parent.mkdir(parents=True, exist_ok=True)
    conn = sqlite3.connect(FORECAST_DB)
    conn.row_factory = sqlite3.Row
    init_db(conn)

    if args.report:
        if not WEEWX_DB.exists():
            print(f"weewx DB not found at {WEEWX_DB}")
            return
        wconn = sqlite3.connect(f"file:{WEEWX_DB}?mode=ro", uri=True)
        wconn.row_factory = sqlite3.Row
        report(conn, wconn, days=args.days, lead_filter=args.lead)
        wconn.close()
    else:
        try:
            data = fetch_forecast()
            inserted, total = log_forecast(conn, data)
            now_str = datetime.now().strftime("%Y-%m-%d %H:%M")
            print(f"[{now_str}] Logged {inserted} new forecast periods ({total} fetched)")
        except Exception as e:
            print(f"ERROR: {e}")

    conn.close()

if __name__ == "__main__":
    main()
