"""
solar_util.py - sunrise/sunset and time-of-day context for the nowcast scripts.

Shared by analogue_forecast.py (and through it backtest_harness.py and forecast_narrator.py).
Location and timezone come from w34config.py (LAT, LON, TZ).
"""
import math
from datetime import datetime, timezone, timedelta
from zoneinfo import ZoneInfo
import os
import sys
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
import w34config as cfg
LAT = cfg.LAT
LON = cfg.LON
LOCAL_TZ = ZoneInfo(getattr(cfg, "TZ", "America/Chicago"))

def calculate_sun_times(d):
    """
    NOAA Solar Calculation Algorithm for the configured LAT/LON.
    Returns (sunrise_ts, sunset_ts) in UTC unix timestamps.
    """
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
    """
    Determines diurnal timing and sunrise/sunset transition events across the forecast window.
    """
    dt_now = datetime.fromtimestamp(ts_now, tz=timezone.utc)
    dt_local = dt_now.astimezone(LOCAL_TZ)

    # Calculate sun times for yesterday, today, and tomorrow to catch crossing windows cleanly
    sr_today, ss_today = calculate_sun_times(dt_now.date())
    sr_tomorrow, ss_tomorrow = calculate_sun_times(dt_now.date() + timedelta(days=1))
    sr_yesterday, ss_yesterday = calculate_sun_times(dt_now.date() - timedelta(days=1))

    ts_end = ts_now + horizon_hours * 3600

    # Check if sunset or sunrise falls within (ts_now, ts_end]
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

    # Determine local time of day name (forecast_narrator.py requires these exact phrases)
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

if __name__ == "__main__":
    # 4 PM summer -> crosses sunset; 4 AM summer -> crosses sunrise; 1 PM summer -> afternoon
    for label, dt in (("4 PM July", datetime(2026, 7, 15, 21, 0, tzinfo=timezone.utc)),
                      ("4 AM July", datetime(2026, 7, 15, 9, 0, tzinfo=timezone.utc)),
                      ("1 PM July", datetime(2026, 7, 15, 18, 0, tzinfo=timezone.utc)),
                      ("1 PM January", datetime(2026, 1, 15, 19, 0, tzinfo=timezone.utc))):
        print(label + ":", get_solar_context(dt.timestamp()))
