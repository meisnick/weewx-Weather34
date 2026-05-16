#!/usr/bin/env python3
"""
cloud_cover_backfill.py  —  ONE-TIME historical backfill
Fetches cloud cover from Open-Meteo archive API for the full database
date range and updates all zero/null signal8 records in the weewx archive.

Run once as root:
  sudo python3 /usr/local/bin/cloud_cover_backfill.py
"""

import json, os, sys, sqlite3, urllib.request, urllib.parse
from datetime import datetime, timezone, timedelta

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from w34config import LAT, LON, TZ

DB_PATH  = "/var/lib/weewx/weewx.sdb"
HEADERS  = {"User-Agent": "weewx-weather34/cloud-cover-backfill"}

ARCHIVE_URL = "https://archive-api.open-meteo.com/v1/archive"
FORECAST_URL = "https://api.open-meteo.com/v1/forecast"


def fetch(url, params):
    full = url + "?" + urllib.parse.urlencode(params)
    print(f"  GET {full[:100]}...")
    req = urllib.request.Request(full, headers=HEADERS)
    try:
        with urllib.request.urlopen(req, timeout=60) as r:
            return json.loads(r.read().decode())
    except Exception as e:
        print(f"  Fetch error: {e}", file=sys.stderr)
        return None


def build_cover_map(times, values):
    """Map each hourly local-time string to its unix timestamp."""
    cover = {}
    for t, v in zip(times, values):
        if v is None:
            continue
        try:
            import zoneinfo
            dt = datetime.fromisoformat(t).replace(tzinfo=zoneinfo.ZoneInfo(TZ))
        except Exception:
            dt = datetime.fromisoformat(t)
        cover[(int(dt.timestamp()) // 3600) * 3600] = v
    return cover


def get_db_range():
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute("SELECT MIN(dateTime), MAX(dateTime) FROM archive WHERE signal8 IS NULL OR signal8 = 0")
    mn, mx = c.fetchone()
    conn.close()
    return mn, mx


def update_records(cover_map):
    conn = sqlite3.connect(DB_PATH, timeout=30)
    c = conn.cursor()

    c.execute("SELECT dateTime FROM archive WHERE signal8 IS NULL OR signal8 = 0")
    rows = c.fetchall()
    print(f"  {len(rows)} records to update...")

    updated = 0
    skipped = 0
    batch = []

    for (ts,) in rows:
        bucket = (ts // 3600) * 3600
        pct = cover_map.get(bucket) or cover_map.get(bucket - 3600)
        if pct is not None:
            batch.append((float(pct), ts))
            updated += 1
        else:
            skipped += 1

        if len(batch) >= 500:
            c.executemany("UPDATE archive SET signal8 = ? WHERE dateTime = ?", batch)
            conn.commit()
            batch = []
            print(f"  ...{updated} updated so far", end="\r")

    if batch:
        c.executemany("UPDATE archive SET signal8 = ? WHERE dateTime = ?", batch)
        conn.commit()

    conn.close()
    print(f"\n  Updated: {updated}  |  No data found for: {skipped}")
    return updated


def main():
    print("Cloud cover historical backfill")
    print("================================")

    mn_ts, mx_ts = get_db_range()
    start_date = datetime.fromtimestamp(mn_ts, tz=timezone.utc).strftime("%Y-%m-%d")
    # Archive API lags ~5 days; use yesterday as end for archive, forecast API for recent
    yesterday = (datetime.now(timezone.utc) - timedelta(days=1)).strftime("%Y-%m-%d")
    today = datetime.now(timezone.utc).strftime("%Y-%m-%d")

    print(f"\nDB zero records: {start_date} → {today}")
    cover_map = {}

    # 1. Open-Meteo Archive API (historical, up to ~yesterday)
    print(f"\n[1/2] Fetching archive data {start_date} → {yesterday}...")
    data = fetch(ARCHIVE_URL, {
        "latitude":  LAT,
        "longitude": LON,
        "start_date": start_date,
        "end_date":   yesterday,
        "hourly":     "cloudcover",
        "timezone":   TZ,
    })
    if data and "hourly" in data:
        chunk = build_cover_map(data["hourly"]["time"], data["hourly"]["cloudcover"])
        cover_map.update(chunk)
        print(f"  Got {len(chunk)} hourly values")
    else:
        print("  WARNING: archive fetch failed")

    # 2. Open-Meteo Forecast API (last 7 days + today)
    print(f"\n[2/2] Fetching recent data (last 7 days + today)...")
    data2 = fetch(FORECAST_URL, {
        "latitude":   LAT,
        "longitude":  LON,
        "hourly":     "cloudcover",
        "past_days":  7,
        "forecast_days": 1,
        "timezone":   TZ,
    })
    if data2 and "hourly" in data2:
        chunk2 = build_cover_map(data2["hourly"]["time"], data2["hourly"]["cloudcover"])
        cover_map.update(chunk2)
        print(f"  Got {len(chunk2)} hourly values")
    else:
        print("  WARNING: forecast fetch failed")

    print(f"\nTotal hourly cover values: {len(cover_map)}")

    # 3. Update database
    print("\n[3/3] Updating database...")
    updated = update_records(cover_map)

    print(f"\nDone. {updated} records backfilled.")
    print("Weewx will pick up the changes on the next report generation cycle.")


if __name__ == "__main__":
    main()
