import math
from datetime import datetime, timezone, timedelta
import os
import sys
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
import w34config as cfg
LAT = cfg.LAT
LON = cfg.LON

def get_central_offset(dt_utc):
    """
    Returns timezone object for US Central Time (CDT UTC-5 or CST UTC-6).
    CDT begins 2nd Sunday in March at 02:00 local (08:00 UTC).
    CDT ends 1st Sunday in November at 02:00 local (07:00 UTC).
    """
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
    """
    NOAA Solar Calculation Algorithm for Fredonia, WI.
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
    tz_local = get_central_offset(dt_now)
    dt_local = dt_now.astimezone(tz_local)
    
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
            
    # Determine local time of day name
    hour = dt_local.hour
    if sunset_crossed is not None:
        timing_phrase = "this evening after sunset"
        period = "sunset_crossing"
    elif sunrise_crossed is not None:
        timing_phrase = "through sunrise into the morning"
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
        "is_day": (sr_today <= ts_now < ss_today),
        "sunset_crossed": sunset_crossed is not None,
        "sunrise_crossed": sunrise_crossed is not None,
        "timing_phrase": timing_phrase,
        "period": period,
    }

if __name__ == "__main__":
    # Test cases
    # 1. 4 PM summer -> crosses sunset around 8 PM
    dt1 = datetime(2026, 7, 15, 21, 0, tzinfo=timezone.utc) # 4 PM CDT
    ctx1 = get_solar_context(dt1.timestamp())
    print("4 PM July:", ctx1)
    
    # 2. 4 AM summer -> crosses sunrise around 5:30 AM
    dt2 = datetime(2026, 7, 15, 9, 0, tzinfo=timezone.utc) # 4 AM CDT
    ctx2 = get_solar_context(dt2.timestamp())
    print("4 AM July:", ctx2)
    
    # 3. 1 PM summer -> afternoon
    dt3 = datetime(2026, 7, 15, 18, 0, tzinfo=timezone.utc) # 1 PM CDT
    ctx3 = get_solar_context(dt3.timestamp())
    print("1 PM July:", ctx3)
