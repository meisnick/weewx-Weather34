#!/usr/bin/env python3
"""
cloud_cover_update.py
Fetches Open-Meteo hourly cloud cover for the past 7 days and backfills
the weewx archive table's signal8 field (cloud cover %) so the
cloudcoverplot Highcharts graph has real data instead of all-zero values.

Runs hourly via cron. Only writes to records where signal8 is NULL or 0.
"""

import json, os, sys, sqlite3, urllib.request
from datetime import datetime, timezone

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from w34config import LAT, LON, TZ

DB_PATH = "/var/lib/weewx/weewx.sdb"
HEADERS = {"User-Agent": "weewx-weather34/cloud-cover"}
API_URL = (
    f"https://api.open-meteo.com/v1/forecast"
    f"?latitude={LAT}&longitude={LON}"
    f"&hourly=cloudcover"
    f"&past_days=7&forecast_days=1"
    f"&timezone={TZ}"
)

def fetch_cloud_cover():
    req = urllib.request.Request(API_URL, headers=HEADERS)
    try:
        with urllib.request.urlopen(req, timeout=15) as r:
            data = json.loads(r.read().decode())
        times = data["hourly"]["time"]       # "2026-05-09T00:00"
        cover = data["hourly"]["cloudcover"]  # 0-100 %
        # Build unix_ts -> cloudcover dict
        result = {}
        for t, c in zip(times, cover):
            dt = datetime.fromisoformat(t)
            # fromisoformat gives naive local time; convert to UTC unix ts
            if dt.tzinfo is None:
                import zoneinfo
                try:
                    tz = zoneinfo.ZoneInfo(TZ)
                    dt = dt.replace(tzinfo=tz)
                except Exception:
                    pass
            result[int(dt.timestamp())] = c
        return result
    except Exception as e:
        print(f"Fetch error: {e}", file=sys.stderr)
        sys.exit(1)

def nearest_cover(ts, cover_map):
    """Return cloud cover for the hourly bucket containing ts."""
    # Round down to the nearest hour
    hour_ts = (ts // 3600) * 3600
    return cover_map.get(hour_ts, cover_map.get(hour_ts - 3600))

def update_database(cover_map):
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()

    # Fetch archive records in the past 7 days where signal8 is 0 or NULL
    seven_days_ago = int(datetime.now(timezone.utc).timestamp()) - 7 * 86400
    c.execute(
        "SELECT dateTime FROM archive WHERE dateTime >= ? AND (signal8 IS NULL OR signal8 = 0)",
        (seven_days_ago,)
    )
    rows = c.fetchall()
    updated = 0
    for (ts,) in rows:
        pct = nearest_cover(ts, cover_map)
        if pct is not None:
            c.execute("UPDATE archive SET signal8 = ? WHERE dateTime = ?", (float(pct), ts))
            updated += 1

    conn.commit()
    conn.close()
    print(f"Updated {updated} archive records with cloud cover data")

def main():
    print("Fetching Open-Meteo cloud cover (7-day history)...")
    cover_map = fetch_cloud_cover()
    print(f"Got {len(cover_map)} hourly values")
    update_database(cover_map)
    print("Done.")

if __name__ == "__main__":
    main()
