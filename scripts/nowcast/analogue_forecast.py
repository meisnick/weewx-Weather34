#!/usr/bin/env python3
"""
analogue_forecast.py - Short-term forecast via historical analogue matching.

Finds hours in the 4-year weewx archive that closely match current conditions,
then reports what actually happened over the next 1-6 hours in those analogues.

Usage:
  python3 analogue_forecast.py              # forecast from latest conditions
  python3 analogue_forecast.py --ts 1720000000 # forecast for specific historical timestamp
  python3 analogue_forecast.py --n 20       # use 20 analogues (default 15)
  python3 analogue_forecast.py --ahead 6    # look 6 hours ahead (default 6)
  python3 analogue_forecast.py --verbose    # show each analogue
  python3 analogue_forecast.py --json       # machine-readable output for LLM
"""
import argparse
import json
import math
import sqlite3
import sys
from datetime import datetime, timezone, timedelta
from pathlib import Path
import os
import sys
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
import w34config as cfg

FEATURES_DB = Path(cfg.NOWCAST_LOCAL_FEATURE_DB)

# Solar / Diurnal utility
LAT = cfg.LAT
LON = cfg.LON

def get_central_offset(dt_utc):
    """Returns timezone for US Central Time (CDT UTC-5 or CST UTC-6)."""
    year = dt_utc.year
    mar1_w = datetime(year, 3, 1, tzinfo=timezone.utc).weekday()
    first_sun_mar = 1 + (6 - mar1_w) % 7
    second_sun_mar = first_sun_mar + 7
    dst_start = datetime(year, 3, second_sun_mar, 8, 0, tzinfo=timezone.utc)
    
    nov1_w = datetime(year, 11, 1, tzinfo=timezone.utc).weekday()
    first_sun_nov = 1 + (6 - nov1_w) % 7
    dst_end = datetime(year, 11, first_sun_nov, 7, 0, tzinfo=timezone.utc)
    
    if dst_start <= dt_utc < dst_end:
        return timezone(timedelta(hours=-5), name="CDT")
    else:
        return timezone(timedelta(hours=-6), name="CST")

def calculate_sun_times(d):
    """NOAA Solar Calculation Algorithm for configured LAT/LON."""
    day_of_year = d.timetuple().tm_yday
    gamma = 2 * math.pi / 365 * (day_of_year - 1 + 0.5)
    eqtime = 229.18 * (0.000075 + 0.001868 * math.cos(gamma) - 0.032077 * math.sin(gamma) \
             - 0.014615 * math.cos(2 * gamma) - 0.040849 * math.sin(2 * gamma))
    decl = 0.006918 - 0.399912 * math.cos(gamma) + 0.070257 * math.sin(gamma) \
           - 0.006758 * math.cos(2 * gamma) + 0.000907 * math.sin(2 * gamma) \
           - 0.002697 * math.cos(3 * gamma) + 0.00148 * math.sin(3 * gamma)
    
    zenith_rad = math.radians(90.833)
    lat_rad = math.radians(LAT)
    
    cos_ha = (math.cos(zenith_rad) / (math.cos(lat_rad) * math.cos(decl))) - (math.tan(lat_rad) * math.tan(decl))
    cos_ha = max(-1.0, min(1.0, cos_ha))
    ha_deg = math.degrees(math.acos(cos_ha))
    
    solar_noon = 720 - 4 * LON - eqtime
    sunrise_min = solar_noon - ha_deg * 4
    sunset_min = solar_noon + ha_deg * 4
    
    sunrise_ts = datetime(d.year, d.month, d.day, tzinfo=timezone.utc).timestamp() + sunrise_min * 60
    sunset_ts = datetime(d.year, d.month, d.day, tzinfo=timezone.utc).timestamp() + sunset_min * 60
    return sunrise_ts, sunset_ts

def get_solar_context(ts_now, horizon_hours=6):
    """Determines diurnal timing and sunrise/sunset transition events."""
    dt_now = datetime.fromtimestamp(ts_now, tz=timezone.utc)
    tz_local = get_central_offset(dt_now)
    dt_local = dt_now.astimezone(tz_local)
    
    sr_today, ss_today = calculate_sun_times(dt_now.date())
    sr_tomorrow, ss_tomorrow = calculate_sun_times(dt_now.date() + timedelta(days=1))
    sr_yesterday, ss_yesterday = calculate_sun_times(dt_now.date() - timedelta(days=1))
    
    ts_end = ts_now + horizon_hours * 3600
    
    sunset_crossed = None
    sunrise_crossed = None
    
    for ss in (ss_yesterday, ss_today, ss_tomorrow):
        if ts_now < ss <= ts_end:
            sunset_crossed = ss
            break
            
    for sr in (sr_yesterday, sr_today, sr_tomorrow):
        if ts_now < sr <= ts_end:
            sunrise_crossed = sr
            break
            
    hour = dt_local.hour
    if sunset_crossed is not None:
        timing_phrase = "this evening after sunset"
        period = "sunset_crossing"
    elif sunrise_crossed is not None:
        timing_phrase = "through sunrise into morning"
        period = "sunrise_crossing"
    elif 5 <= hour < 12:
        timing_phrase = "through the morning"
        period = "morning"
    elif 12 <= hour < 17:
        timing_phrase = "this afternoon"
        period = "afternoon"
    elif 17 <= hour < 21:
        timing_phrase = "this evening"
        period = "evening"
    else:
        timing_phrase = "overnight"
        period = "night"
        
    return {
        "local_dt_str": dt_local.strftime("%Y-%m-%d %I:%M %p %Z"),
        "local_hour": hour,
        "is_day": bool(sr_today <= ts_now < ss_today),
        "sunset_crossed": sunset_crossed is not None,
        "sunrise_crossed": sunrise_crossed is not None,
        "timing_phrase": timing_phrase,
        "period": period,
    }

# Distance feature weights — higher = more influence on match quality.
# Pressure tendency is the strongest single-station predictor.
# Diurnal alignment ensures analogues match solar cycle and time-of-day cooling/warming.
WEIGHTS = {
    "dp_3h":      3.0,
    "dp_1h":      2.0,
    "temp_f":     2.0,   # soft gate — prevents matching days 25°F apart
    "dew_spread": 1.5,
    "dt_1h":      1.5,
    "wind_cos":   1.2,   # cosine of angular wind direction difference
    "rh_pct":     1.0,
}

DIURNAL_HOUR_WEIGHT = 2.0

AHEAD_HOURS = [1, 2, 4, 6]

def angular_diff_cos(a, b):
    """Cosine of angle between two wind directions (1=same, -1=opposite)."""
    if a is None or b is None:
        return 0.0
    return math.cos(math.radians(a - b))

def doy(ts):
    """Day of year for a Unix timestamp."""
    return datetime.fromtimestamp(ts, tz=timezone.utc).timetuple().tm_yday

def seasonal_filter(query_ts, candidate_ts, window_days=42):
    """True if candidate is within ±window_days of query day-of-year."""
    qdoy = doy(query_ts)
    cdoy = doy(candidate_ts)
    diff = abs(qdoy - cdoy)
    return min(diff, 365 - diff) <= window_days

def compute_stats(conn, feature_col):
    """Mean and std of a feature column for normalization."""
    row = conn.execute(
        f"SELECT AVG({feature_col}), AVG({feature_col}*{feature_col}) "
        f"FROM hourly_features WHERE {feature_col} IS NOT NULL"
    ).fetchone()
    if not row or row[0] is None:
        return 0.0, 1.0
    mean = row[0]
    std  = math.sqrt(max(row[1] - mean * mean, 1e-9))
    return mean, std

def get_conditions_at_ts(conn, ts):
    """Return conditions for a specific timestamp."""
    row = conn.execute("""
        SELECT * FROM hourly_features
        WHERE ts = ? AND temp_f IS NOT NULL
    """, (ts,)).fetchone()
    if row:
        return dict(row)
    row = conn.execute("""
        SELECT * FROM hourly_features
        WHERE ts <= ? AND temp_f IS NOT NULL AND dp_3h IS NOT NULL
        ORDER BY ts DESC LIMIT 1
    """, (ts,)).fetchone()
    return dict(row) if row else None

def get_current_conditions(conn):
    """Return the most recent complete hour from features.db."""
    row = conn.execute("""
        SELECT * FROM hourly_features
        WHERE temp_f IS NOT NULL AND dp_3h IS NOT NULL
        ORDER BY ts DESC LIMIT 1
    """).fetchone()
    return dict(row) if row else None

def find_analogues(conn, current, n_analogues, norms):
    """
    Find the N closest historical hours to current conditions.
    Excludes ±48 hours around query_ts to avoid matching near-present.
    Includes diurnal hour-of-day penalty to maintain time-of-day awareness.
    Returns list of (distance, ts, row_dict).
    """
    qts = current["ts"]
    cutoff_lo = qts - 48 * 3600
    cutoff_hi = qts + 48 * 3600
    qhour = current["hour_utc"]

    rows = conn.execute("""
        SELECT * FROM hourly_features
        WHERE (ts < ? OR ts > ?) AND dp_3h IS NOT NULL AND temp_f IS NOT NULL
    """, (cutoff_lo, cutoff_hi)).fetchall()

    scored = []
    cwd = current.get("wind_dir")

    for r in rows:
        if not seasonal_filter(current["ts"], r["ts"]):
            continue

        dist = 0.0
        for feat, w in WEIGHTS.items():
            if feat == "wind_cos":
                diff = 1.0 - angular_diff_cos(cwd, r["wind_dir"])
            else:
                mn, sd = norms[feat]
                cv = current.get(feat)
                rv = r[feat]
                if cv is None or rv is None:
                    diff = 0.0
                else:
                    diff = ((cv - mn) / sd) - ((rv - mn) / sd)
            dist += w * diff * diff

        # Diurnal circular hour alignment (0-12 hr difference mapped smoothly)
        hdiff = min(abs(qhour - r["hour_utc"]), 24 - abs(qhour - r["hour_utc"]))
        h_norm = hdiff / 6.0
        dist += DIURNAL_HOUR_WEIGHT * h_norm * h_norm

        scored.append((math.sqrt(dist), r["ts"], dict(r)))

    scored.sort(key=lambda x: x[0])
    return scored[:n_analogues]

def look_forward(conn, analogue_ts, ahead_hours):
    """
    For each ahead hour, fetch the actual temp and rain from features.db.
    Returns dict: {hours: (temp_f, rain_in)} or None if missing.
    """
    outcomes = {}
    for h in ahead_hours:
        target = analogue_ts + h * 3600
        row = conn.execute(
            "SELECT temp_f, rain_in FROM hourly_features WHERE ts = ?", (target,)
        ).fetchone()
        outcomes[h] = (row["temp_f"], row["rain_in"]) if row and row["temp_f"] is not None else None
    return outcomes

def summarise_analogues(analogues, ahead_hours, conn):
    """
    Collect look-forward outcomes for all analogues.
    Returns: {h: {"temps": [...], "rain_any": count, "n": count}}
    """
    buckets = {h: {"temps": [], "rain_n": 0, "n": 0} for h in ahead_hours}

    for dist, ats, arow in analogues:
        outcomes = look_forward(conn, ats, ahead_hours)
        for h in ahead_hours:
            o = outcomes.get(h)
            if o is None:
                continue
            temp, rain = o
            buckets[h]["temps"].append(temp)
            buckets[h]["n"] += 1
            if rain is not None and rain > 0.005:
                buckets[h]["rain_n"] += 1

    result = {}
    for h, b in buckets.items():
        if not b["temps"]:
            continue
        temps = b["temps"]
        n     = b["n"]
        mean  = sum(temps) / n
        std   = math.sqrt(sum((t - mean)**2 for t in temps) / n)
        result[h] = {
            "mean_f":    round(mean, 1),
            "std_f":     round(std, 1),
            "min_f":     round(min(temps), 1),
            "max_f":     round(max(temps), 1),
            "rain_pct":  round(100 * b["rain_n"] / n),
            "n":         n,
        }
    return result

def pressure_label(dp3h):
    if dp3h is None:      return "steady"
    if dp3h >=  0.06:     return "rising rapidly"
    if dp3h >=  0.02:     return "rising"
    if dp3h >= -0.02:     return "steady"
    if dp3h >= -0.06:     return "falling"
    return "falling rapidly"

def wind_label(deg):
    if deg is None: return "calm/variable"
    dirs = ["N","NNE","NE","ENE","E","ESE","SE","SSE",
            "S","SSW","SW","WSW","W","WNW","NW","NNW"]
    return dirs[round(deg / 22.5) % 16]

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--db",      type=str,  default=str(FEATURES_DB), help="Path to features.db")
    ap.add_argument("--ts",      type=int,  default=None, help="Historical timestamp to forecast from")
    ap.add_argument("--n",       type=int,  default=15)
    ap.add_argument("--ahead",   type=int,  default=6)
    ap.add_argument("--verbose", action="store_true")
    ap.add_argument("--json",    action="store_true")
    args = ap.parse_args()

    ahead_hours = [h for h in AHEAD_HOURS if h <= args.ahead]

    conn = sqlite3.connect(args.db)
    conn.row_factory = sqlite3.Row

    if args.ts:
        current = get_conditions_at_ts(conn, args.ts)
    else:
        current = get_current_conditions(conn)

    if not current:
        print("No conditions found in features.db", file=sys.stderr)
        conn.close()
        return

    # Pre-compute normalisation stats for distance features
    norms = {}
    for feat in WEIGHTS:
        if feat == "wind_cos":
            continue
        norms[feat] = compute_stats(conn, feat)

    analogues = find_analogues(conn, current, args.n, norms)
    forecast  = summarise_analogues(analogues, ahead_hours, conn)
    solar_ctx = get_solar_context(current["ts"], horizon_hours=args.ahead)

    dt_str = datetime.fromtimestamp(current["ts"], tz=timezone.utc).strftime("%Y-%m-%d %H:%M UTC")

    if args.json:
        out = {
            "as_of": dt_str,
            "current": {
                "ts":         current["ts"],
                "temp_f":     current["temp_f"],
                "pressure":   current["pressure"],
                "dp_3h":      current["dp_3h"],
                "dp_1h":      current["dp_1h"],
                "rh_pct":     current["rh_pct"],
                "dew_spread": current["dew_spread"],
                "wind_dir":   current["wind_dir"],
                "wind_mph":   current["wind_mph"],
                "pressure_trend": pressure_label(current["dp_3h"]),
                "wind_label": wind_label(current["wind_dir"]),
            },
            "solar_context": solar_ctx,
            "n_analogues": len(analogues),
            "forecast":    forecast,
            "analogues": [
                {
                    "ts": ats,
                    "date": datetime.fromtimestamp(ats, tz=timezone.utc).strftime("%Y-%m-%d %H:%M"),
                    "distance": round(dist, 3),
                    "temp_f": round(arow["temp_f"], 1),
                    "wind_dir": arow["wind_dir"],
                    "wind_label": wind_label(arow["wind_dir"]),
                    "wind_mph": round(arow["wind_mph"], 1),
                    "pressure": round(arow["pressure"], 2),
                    "dp_3h": round(arow["dp_3h"], 3) if arow["dp_3h"] is not None else None,
                }
                for dist, ats, arow in analogues
            ]
        }
        print(json.dumps(out, indent=2))
        conn.close()
        return

    # Human-readable output
    print(f"\nCurrent conditions ({dt_str} / {solar_ctx['local_dt_str']})")
    print(f"  Timing     : {solar_ctx['timing_phrase']} (day={solar_ctx['is_day']})")
    print(f"  Temp       : {current['temp_f']:.1f}°F  (trend: {current['dt_1h']:+.1f}°F/hr)" if current['dt_1h'] else f"  Temp       : {current['temp_f']:.1f}°F")
    print(f"  Pressure   : {current['pressure']:.2f} inHg  ({pressure_label(current['dp_3h'])}, 3h change: {current['dp_3h']:+.3f})")
    print(f"  Humidity   : {current['rh_pct']:.0f}%  (dew spread: {current['dew_spread']:.1f}°F)")
    print(f"  Wind       : {wind_label(current['wind_dir'])} ({current['wind_dir']:.0f}°) @ {current['wind_mph']:.1f} mph" if current['wind_dir'] else f"  Wind       : calm/variable")

    print(f"\nFound {len(analogues)} analogues (same ±6 weeks, {len(analogues)} best matches)\n")

    if args.verbose:
        print("  Analogues used:")
        for dist, ats, arow in analogues[:10]:
            adt = datetime.fromtimestamp(ats, tz=timezone.utc).strftime("%Y-%m-%d %H:%M")
            print(f"    {adt}  dist={dist:.2f}  {arow['temp_f']:.1f}°F  "
                  f"dp3h={arow['dp_3h']:+.3f}  wind={wind_label(arow['wind_dir'])}")
        print()

    print(f"  {'Hr':>3}  {'Temp range':>18}  {'Mean':>6}  {'Rain%':>6}  {'n':>4}")
    print("  " + "─" * 48)
    for h in ahead_hours:
        if h not in forecast:
            print(f"  +{h}h  (insufficient data)")
            continue
        f = forecast[h]
        rng = f"{f['min_f']:.0f}–{f['max_f']:.0f}°F"
        print(f"  +{h}h  {rng:>18}  {f['mean_f']:>5.1f}°F  {f['rain_pct']:>5}%  {f['n']:>4}")

    conn.close()
    print()

if __name__ == "__main__":
    main()
