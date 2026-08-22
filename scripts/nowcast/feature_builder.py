#!/usr/bin/env python3
"""
feature_builder.py - Build hourly feature table from weewx archive.

Never writes to weewx.sdb - opens read-only.
Output goes to /home/pi/forecast/features.db.

Usage:
  python3 feature_builder.py           # initial build or incremental update
  python3 feature_builder.py --status  # show row count and date range
  python3 feature_builder.py --tail 5  # show last 5 rows
"""
import argparse
import sqlite3
from datetime import datetime, timezone
from pathlib import Path

import nowcast_config as cfg

WEEWX_DB   = Path("/var/lib/weewx/weewx.sdb")
FEATURES_DB  = Path(cfg.NOWCAST_LOCAL_FEATURE_DB)

def init_features_db(conn):
    conn.execute("""
        CREATE TABLE IF NOT EXISTS hourly_features (
            ts          INTEGER PRIMARY KEY,
            month       INTEGER,
            hour_utc    INTEGER,
            temp_f      REAL,
            dewpt_f     REAL,
            rh_pct      REAL,
            pressure    REAL,
            wind_dir    REAL,
            wind_mph    REAL,
            solar       REAL,
            rain_in     REAL,
            dp_1h       REAL,
            dp_3h       REAL,
            dt_1h       REAL,
            dew_spread  REAL,
            n_records   INTEGER
        )
    """)
    conn.commit()

def fetch_weewx_hourly(wconn, from_ts, to_ts):
    """Return hourly aggregates from weewx archive as list of dicts."""
    rows = wconn.execute("""
        SELECT
            (dateTime / 3600) * 3600  AS ts,
            AVG(outTemp)              AS temp_f,
            AVG(dewpoint)             AS dewpt_f,
            AVG(outHumidity)          AS rh_pct,
            AVG(barometer)            AS pressure,
            AVG(windDir)              AS wind_dir,
            AVG(windSpeed)            AS wind_mph,
            AVG(radiation)            AS solar,
            SUM(rain)                 AS rain_in,
            COUNT(*)                  AS n
        FROM archive
        WHERE dateTime >= ? AND dateTime < ?
          AND outTemp IS NOT NULL
        GROUP BY ts
        HAVING n >= 3
        ORDER BY ts
    """, (from_ts, to_ts)).fetchall()
    return rows

def build_features(wconn, fconn, from_ts, to_ts, batch=86400*30):
    """Process weewx archive in monthly batches, compute tendencies, insert features."""
    total = 0
    cursor = from_ts

    # Cache already-inserted rows near the boundary for tendency lookups
    # Seed the lookback buffer with rows just before from_ts
    lookback_start = from_ts - (3 * 3600)
    seed_rows = fetch_weewx_hourly(wconn, lookback_start, from_ts)
    buffer = {r["ts"]: r for r in seed_rows}

    while cursor < to_ts:
        end = min(cursor + batch, to_ts)
        rows = fetch_weewx_hourly(wconn, cursor, end)

        insert_rows = []
        for r in rows:
            ts = r["ts"]
            buffer[ts] = r

            dt = datetime.fromtimestamp(ts, tz=timezone.utc)
            prev1 = buffer.get(ts - 3600)
            prev3 = buffer.get(ts - 10800)

            dp_1h = (r["pressure"] - prev1["pressure"]) if prev1 and prev1["pressure"] and r["pressure"] else None
            dp_3h = (r["pressure"] - prev3["pressure"]) if prev3 and prev3["pressure"] and r["pressure"] else None
            dt_1h = (r["temp_f"]   - prev1["temp_f"])   if prev1 and prev1["temp_f"]   and r["temp_f"]   else None
            dew_spread = (r["temp_f"] - r["dewpt_f"])   if r["temp_f"] and r["dewpt_f"] else None

            insert_rows.append((
                ts,
                dt.month,
                dt.hour,
                r["temp_f"],
                r["dewpt_f"],
                r["rh_pct"],
                r["pressure"],
                r["wind_dir"],
                r["wind_mph"],
                r["solar"],
                r["rain_in"],
                dp_1h,
                dp_3h,
                dt_1h,
                dew_spread,
                r["n"],
            ))

        fconn.executemany("""
            INSERT OR IGNORE INTO hourly_features
              (ts, month, hour_utc, temp_f, dewpt_f, rh_pct, pressure,
               wind_dir, wind_mph, solar, rain_in,
               dp_1h, dp_3h, dt_1h, dew_spread, n_records)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        """, insert_rows)
        fconn.commit()
        total += len(insert_rows)

        if insert_rows:
            dt_str = datetime.fromtimestamp(insert_rows[-1][0], tz=timezone.utc).strftime("%Y-%m")
            print(f"  ...through {dt_str} ({total} rows)", flush=True)

        cursor = end

    return total

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--status", action="store_true")
    ap.add_argument("--tail",   type=int, default=0)
    args = ap.parse_args()

    FEATURES_DB.parent.mkdir(parents=True, exist_ok=True)

    fconn = sqlite3.connect(FEATURES_DB)
    fconn.row_factory = sqlite3.Row
    init_features_db(fconn)

    if args.status or args.tail:
        row = fconn.execute("""
            SELECT COUNT(*) as n,
                   datetime(MIN(ts),'unixepoch') as first,
                   datetime(MAX(ts),'unixepoch') as last
            FROM hourly_features
        """).fetchone()
        print(f"Features DB: {FEATURES_DB}")
        print(f"  Rows  : {row['n']}")
        print(f"  First : {row['first']}")
        print(f"  Last  : {row['last']}")
        if args.tail:
            print(f"\nLast {args.tail} rows:")
            for r in fconn.execute(f"""
                SELECT datetime(ts,'unixepoch') as dt, temp_f, pressure,
                       dp_1h, dp_3h, dt_1h, dew_spread,
                       wind_dir, wind_mph
                FROM hourly_features ORDER BY ts DESC LIMIT {args.tail}
            """).fetchall():
                def fmt(v, spec): return format(v, spec) if v is not None else "n/a"
                print(f"  {r['dt']}  {fmt(r['temp_f'],'5.1f')}F  {fmt(r['pressure'],'.2f')}inHg"
                      f"  dp1h={fmt(r['dp_1h'],'+.3f')}  dp3h={fmt(r['dp_3h'],'+.3f')}"
                      f"  dt1h={fmt(r['dt_1h'],'+.1f')}  spread={fmt(r['dew_spread'],'.1f')}"
                      f"  wind {fmt(r['wind_dir'],'.0f')}deg @ {fmt(r['wind_mph'],'.1f')}mph")
        fconn.close()
        return

    if not WEEWX_DB.exists():
        print(f"weewx DB not found: {WEEWX_DB}")
        return

    wconn = sqlite3.connect(f"file:{WEEWX_DB}?mode=ro", uri=True)
    wconn.row_factory = sqlite3.Row

    # Find where to start: resume after last row already in features.db
    last = fconn.execute("SELECT MAX(ts) FROM hourly_features").fetchone()[0]
    weewx_bounds = wconn.execute(
        "SELECT MIN(dateTime) as lo, MAX(dateTime) as hi FROM archive"
    ).fetchone()

    from_ts = (last + 3600) if last else weewx_bounds["lo"]
    to_ts   = weewx_bounds["hi"]

    if from_ts >= to_ts:
        print("Features DB is already current.")
        fconn.close()
        wconn.close()
        return

    start_str = datetime.fromtimestamp(from_ts, tz=timezone.utc).strftime("%Y-%m-%d")
    end_str   = datetime.fromtimestamp(to_ts,   tz=timezone.utc).strftime("%Y-%m-%d")
    print(f"Building features: {start_str} → {end_str}")

    n = build_features(wconn, fconn, from_ts, to_ts)
    print(f"\nDone. {n} rows written to {FEATURES_DB}")

    wconn.close()
    fconn.close()

if __name__ == "__main__":
    main()
