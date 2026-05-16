#!/usr/bin/env python3
"""
nws_forecast_update.py
Fetches forecast from Open-Meteo (free, no API key) and writes awd.txt / awh.txt
in Aeris-compatible format for Weather34's forecast3aw.php renderer.
Configure location in scripts/w34config.py (see w34config.example.py).
"""

import json
import urllib.request
import urllib.error
import urllib.parse
import sys
import os
from datetime import datetime, timezone, timedelta
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from w34config import LAT, LON, TZ, AWD_PATH, AWH_PATH, PLACE_NAME, PLACE_STATE, PLACE_COUNTRY

HEADERS = {"User-Agent": "weewx-weather34/open-meteo"}

# Open-Meteo single request — both daily and hourly in one call
OM_URL = "https://api.open-meteo.com/v1/forecast?" + urllib.parse.urlencode({
    "latitude":   LAT,
    "longitude":  LON,
    "timezone":   TZ,
    "forecast_days": 8,
    "temperature_unit": "fahrenheit",
    "wind_speed_unit":  "mph",
    "precipitation_unit": "inch",
    "daily": ",".join([
        "weathercode",
        "temperature_2m_max",
        "temperature_2m_min",
        "precipitation_sum",
        "precipitation_probability_max",
        "windspeed_10m_max",
        "windgusts_10m_max",
        "winddirection_10m_dominant",
        "uv_index_max",
        "sunrise",
        "sunset",
    ]),
    "hourly": ",".join([
        "weathercode",
        "temperature_2m",
        "dewpoint_2m",
        "relativehumidity_2m",
        "precipitation",
        "precipitation_probability",
        "windspeed_10m",
        "windgusts_10m",
        "winddirection_10m",
        "uv_index",
        "is_day",
    ]),
})

STATION_LAT = LAT
STATION_LON = LON

# WMO weather code -> (day_icon_code, night_icon_code, weather_primary)
# lookupTable.json codes: 0=clear-night 1=clear-day 2=pcloudy-night 3=pcloudy-day
# 5=mist 6=fog 7=cloudy 8=overcast 9=lrain-shower-night 10=lrain-shower-day
# 11=drizzle 12=light-rain 13=hrain-shower-night 14=hrain-shower-day 15=heavy-rain
# 16=sleet-night 17=sleet-day 18=sleet 19=hail-night 20=hail-day 21=hail
# 22=lsnow-night 23=lsnow-day 24=light-snow 25=hsnow-night 26=hsnow-day 27=heavy-snow
# 28=thunder-night 29=thunder-day 30=thunder
WMO_MAP = {
    0:  ("1",  "0",  "Sunny"),           # Clear sky
    1:  ("1",  "0",  "Mostly Sunny"),    # Mainly clear
    2:  ("3",  "2",  "Partly Cloudy"),   # Partly cloudy
    3:  ("8",  "8",  "Overcast"),        # Overcast
    45: ("6",  "6",  "Fog"),             # Fog
    48: ("6",  "6",  "Fog"),             # Icy fog
    51: ("11", "11", "Drizzle"),         # Light drizzle
    53: ("11", "11", "Drizzle"),         # Moderate drizzle
    55: ("11", "11", "Heavy Drizzle"),   # Dense drizzle
    56: ("17", "16", "Freezing Drizzle"),
    57: ("17", "16", "Freezing Drizzle"),
    61: ("12", "12", "Light Rain"),
    63: ("15", "15", "Rain"),
    65: ("15", "15", "Heavy Rain"),
    66: ("17", "16", "Freezing Rain"),
    67: ("17", "16", "Freezing Rain"),
    71: ("24", "24", "Light Snow"),
    73: ("24", "24", "Snow"),
    75: ("27", "27", "Heavy Snow"),
    77: ("24", "24", "Snow Grains"),
    80: ("10", "9",  "Rain Showers"),
    81: ("14", "13", "Rain Showers"),
    82: ("14", "13", "Heavy Showers"),
    85: ("23", "22", "Snow Showers"),
    86: ("26", "25", "Heavy Snow Showers"),
    95: ("29", "28", "Thunderstorms"),
    96: ("29", "28", "Thunderstorms with Hail"),
    99: ("29", "28", "Thunderstorms with Hail"),
}

WIND_DIR_CARDINAL = [
    "N","NNE","NE","ENE","E","ESE","SE","SSE",
    "S","SSW","SW","WSW","W","WNW","NW","NNW","N"
]

def deg_to_cardinal(deg):
    if deg is None:
        return ""
    return WIND_DIR_CARDINAL[round(deg / 22.5) % 16]

def mph_to_kph(mph):
    if mph is None:
        return 0
    return round(mph * 1.60934, 1)

def f_to_c(f):
    if f is None:
        return None
    return round((f - 32) * 5 / 9, 1)

def in_to_mm(inches):
    if inches is None:
        return 0
    return round(inches * 25.4, 2)

def fetch_json(url):
    req = urllib.request.Request(url, headers=HEADERS)
    try:
        with urllib.request.urlopen(req, timeout=15) as r:
            return json.loads(r.read().decode())
    except urllib.error.HTTPError as e:
        print(f"HTTP error {e.code} fetching {url}", file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(f"Error fetching {url}: {e}", file=sys.stderr)
        sys.exit(1)

def wmo_icon(code, is_day):
    entry = WMO_MAP.get(code, ("3", "2", "Partly Cloudy"))
    return entry[0] if is_day else entry[1]

def wmo_primary(code):
    return WMO_MAP.get(code, ("3", "2", "Partly Cloudy"))[2]

def to_ts(dt_str):
    """Parse Open-Meteo datetime string (no timezone, local) to UTC Unix timestamp."""
    try:
        # Open-Meteo returns local time strings like "2026-05-16T07:00"
        # We parse as naive then attach local offset from utc_offset_seconds
        return int(datetime.fromisoformat(dt_str).timestamp())
    except Exception:
        return 0

def build_awd(data):
    """
    Build awd.txt from Open-Meteo daily arrays.
    Daily data has one entry per day. We synthesize day+night periods
    to match the alternating structure forecast3aw.php expects.
    """
    daily    = data["daily"]
    times    = daily["time"]           # ["2026-05-16", ...]
    sunrises = daily.get("sunrise", [])
    sunsets  = daily.get("sunset", [])

    out_periods = []

    hourly_times  = data["hourly"]["time"]
    hourly_wcodes = data["hourly"]["weathercode"] or []
    hourly_is_day = data["hourly"]["is_day"] or []
    hourly_humid  = data["hourly"].get("relativehumidity_2m") or []

    def avg_humidity(date_str, hour_start, hour_end, next_date_str=None):
        """Average hourly humidity for a date between two hours (inclusive).
        If next_date_str given, also includes hours 0..hour_end of next date."""
        vals = [
            hourly_humid[j]
            for j, t in enumerate(hourly_times)
            if j < len(hourly_humid) and hourly_humid[j] is not None
            and (
                (t.startswith(date_str) and hour_start <= int(t[11:13]) <= hour_end)
                or (next_date_str and t.startswith(next_date_str) and int(t[11:13]) <= hour_end)
            )
        ]
        return round(sum(vals) / len(vals)) if vals else 0

    for i, date_str in enumerate(times[:7]):
        daily_wcode = (daily["weathercode"] or [None]*20)[i]

        # For today (i==0), use the dominant daytime hourly code from hours 10-18
        # to avoid daily code being dominated by overnight/early-morning rain.
        if i == 0:
            day_codes = [
                hourly_wcodes[j]
                for j, t in enumerate(hourly_times)
                if t.startswith(date_str) and 10 <= int(t[11:13]) <= 18
                and j < len(hourly_wcodes) and hourly_wcodes[j] is not None
            ]
            wcode = max(set(day_codes), key=day_codes.count) if day_codes else daily_wcode
        else:
            wcode = daily_wcode
        hi_f     = (daily["temperature_2m_max"] or [None]*20)[i]
        lo_f     = (daily["temperature_2m_min"] or [None]*20)[i]
        hi_c     = f_to_c(hi_f)
        lo_c     = f_to_c(lo_f)
        precip_in = (daily["precipitation_sum"] or [0]*20)[i] or 0
        precip_mm = in_to_mm(precip_in)
        pop      = (daily["precipitation_probability_max"] or [0]*20)[i] or 0
        wind_mph = (daily["windspeed_10m_max"] or [0]*20)[i] or 0
        gust_mph = (daily["windgusts_10m_max"] or [0]*20)[i] or 0
        wind_deg = (daily["winddirection_10m_dominant"] or [0]*20)[i] or 0
        wind_dir = deg_to_cardinal(wind_deg)
        wind_kph = mph_to_kph(wind_mph)
        gust_kph = mph_to_kph(gust_mph)
        uvi      = (daily["uv_index_max"] or [0]*20)[i] or 0
        next_date = times[i+1] if i+1 < len(times) else None
        day_humidity  = avg_humidity(date_str, 7, 18)
        night_humidity = avg_humidity(date_str, 19, 23, next_date)

        # Day period
        day_iso  = f"{date_str}T07:00:00"
        day_ts   = to_ts(day_iso)
        day_code = wmo_icon(wcode, True)
        day_summary = wmo_primary(wcode)

        out_periods.append({
            "timestamp":           day_ts,
            "validTime":           day_iso,
            "dateTimeISO":         day_iso,
            "maxTempC":            hi_c,
            "maxTempF":            hi_f,
            "minTempC":            None,
            "minTempF":            None,
            "avgTempC":            hi_c,
            "avgTempF":            hi_f,
            "tempC":               hi_c,
            "tempF":               hi_f,
            "maxFeelslikeC":       hi_c,
            "minFeelslikeC":       hi_c,
            "avgFeelslikeC":       hi_c,
            "feelslikeC":          hi_c,
            "feelslikeF":          hi_f,
            "maxDewpointC":        None,
            "minDewpointC":        None,
            "avgDewpointC":        None,
            "dewpointC":           None,
            "dewpointF":           None,
            "maxHumidity":         day_humidity,
            "minHumidity":         day_humidity,
            "humidity":            day_humidity,
            "pop":                 int(pop),
            "precipMM":            precip_mm,
            "precipIN":            round(precip_in, 2),
            "iceaccum":            0,
            "iceaccumMM":          0,
            "iceaccumIN":          0,
            "snowCM":              0,
            "snowIN":              0,
            "pressureMB":          None,
            "pressureIN":          None,
            "windDir":             wind_dir,
            "windDirDEG":          wind_deg,
            "windSpeedKTS":        round(wind_mph * 0.868976, 0),
            "windSpeedKPH":        wind_kph,
            "windSpeedMPH":        wind_mph,
            "windSpeedMPS":        round(wind_mph * 0.44704, 1),
            "windGustKTS":         round(gust_mph * 0.868976, 0),
            "windGustKPH":         gust_kph,
            "windGustMPH":         gust_mph,
            "windGustMPS":         round(gust_mph * 0.44704, 1),
            "windDirMax":          wind_dir,
            "windDirMaxDEG":       wind_deg,
            "windSpeedMaxKPH":     wind_kph,
            "windSpeedMaxMPH":     wind_mph,
            "windDirMin":          wind_dir,
            "windDirMinDEG":       wind_deg,
            "windSpeedMinKPH":     wind_kph,
            "windSpeedMinMPH":     wind_mph,
            "uvi":                 uvi,
            "weatherPrimary":      day_summary,
            "weatherPrimaryCoded": day_summary,
            "cloudsCoded":         "",
            "icon":                day_code,
            "isDay":               True,
        })

        # Night period
        night_iso = f"{date_str}T19:00:00"
        night_ts  = to_ts(night_iso)
        night_code = wmo_icon(wcode, False)
        night_summary = day_summary.replace('Sunny', 'Clear')

        out_periods.append({
            "timestamp":           night_ts,
            "validTime":           night_iso,
            "dateTimeISO":         night_iso,
            "maxTempC":            None,
            "maxTempF":            None,
            "minTempC":            lo_c,
            "minTempF":            lo_f,
            "avgTempC":            lo_c,
            "avgTempF":            lo_f,
            "tempC":               lo_c,
            "tempF":               lo_f,
            "maxFeelslikeC":       lo_c,
            "minFeelslikeC":       lo_c,
            "avgFeelslikeC":       lo_c,
            "feelslikeC":          lo_c,
            "feelslikeF":          lo_f,
            "maxDewpointC":        None,
            "minDewpointC":        None,
            "avgDewpointC":        None,
            "dewpointC":           None,
            "dewpointF":           None,
            "maxHumidity":         night_humidity,
            "minHumidity":         night_humidity,
            "humidity":            night_humidity,
            "pop":                 int(pop),
            "precipMM":            precip_mm,
            "precipIN":            round(precip_in, 2),
            "iceaccum":            0,
            "iceaccumMM":          0,
            "iceaccumIN":          0,
            "snowCM":              0,
            "snowIN":              0,
            "pressureMB":          None,
            "pressureIN":          None,
            "windDir":             wind_dir,
            "windDirDEG":          wind_deg,
            "windSpeedKTS":        round(wind_mph * 0.868976, 0),
            "windSpeedKPH":        wind_kph,
            "windSpeedMPH":        wind_mph,
            "windSpeedMPS":        round(wind_mph * 0.44704, 1),
            "windGustKTS":         round(gust_mph * 0.868976, 0),
            "windGustKPH":         gust_kph,
            "windGustMPH":         gust_mph,
            "windGustMPS":         round(gust_mph * 0.44704, 1),
            "windDirMax":          wind_dir,
            "windDirMaxDEG":       wind_deg,
            "windSpeedMaxKPH":     wind_kph,
            "windSpeedMaxMPH":     wind_mph,
            "windDirMin":          wind_dir,
            "windDirMinDEG":       wind_deg,
            "windSpeedMinKPH":     wind_kph,
            "windSpeedMinMPH":     wind_mph,
            "uvi":                 0,
            "weatherPrimary":      night_summary,
            "weatherPrimaryCoded": night_summary,
            "cloudsCoded":         "",
            "icon":                night_code,
            "isDay":               False,
        })

    return {
        "success": True,
        "error": None,
        "response": [{
            "loc":      {"long": STATION_LON, "lat": STATION_LAT},
            "interval": "daynight",
            "place":    {"name": PLACE_NAME, "state": PLACE_STATE, "country": PLACE_COUNTRY},
            "periods":  out_periods,
        }]
    }


def build_awh(data):
    """
    Build awh.txt from Open-Meteo hourly arrays.
    Open-Meteo returns 192 hourly values (8 days). We emit 24.
    """
    hourly   = data["hourly"]
    times    = hourly["time"]           # ["2026-05-16T00:00", ...]
    out_periods = []

    for i in range(min(24, len(times))):
        dt_iso   = times[i]
        ts       = to_ts(dt_iso)
        wcode    = (hourly["weathercode"] or [0]*200)[i] or 0
        temp_f   = (hourly["temperature_2m"] or [None]*200)[i]
        temp_c   = f_to_c(temp_f)
        dew_c    = (hourly["dewpoint_2m"] or [None]*200)[i]
        dew_f    = round(dew_c * 9/5 + 32, 1) if dew_c is not None else None
        humidity = (hourly["relativehumidity_2m"] or [0]*200)[i] or 0
        precip_in = (hourly["precipitation"] or [0]*200)[i] or 0
        precip_mm = in_to_mm(precip_in)
        pop      = (hourly["precipitation_probability"] or [0]*200)[i] or 0
        wind_mph = (hourly["windspeed_10m"] or [0]*200)[i] or 0
        gust_mph = (hourly["windgusts_10m"] or [0]*200)[i] or 0
        wind_deg = (hourly["winddirection_10m"] or [0]*200)[i] or 0
        wind_dir = deg_to_cardinal(wind_deg)
        wind_kph = mph_to_kph(wind_mph)
        gust_kph = mph_to_kph(gust_mph)
        uvi      = (hourly["uv_index"] or [0]*200)[i] or 0
        is_day   = bool((hourly["is_day"] or [1]*200)[i])
        icon_code = wmo_icon(wcode, is_day)
        summary  = wmo_primary(wcode)

        out_periods.append({
            "timestamp":        ts,
            "validTime":        dt_iso,
            "dateTimeISO":      dt_iso,
            "tempC":            temp_c,
            "tempF":            temp_f,
            "avgTempC":         temp_c,
            "avgTempF":         temp_f,
            "maxTempC":         temp_c,
            "maxTempF":         temp_f,
            "minTempC":         temp_c,
            "minTempF":         temp_f,
            "feelslikeC":       temp_c,
            "feelslikeF":       temp_f,
            "dewpointC":        round(dew_c, 1) if dew_c is not None else None,
            "dewpointF":        dew_f,
            "humidity":         humidity,
            "pop":              int(pop),
            "precipMM":         precip_mm,
            "precipIN":         round(precip_in, 2),
            "snowCM":           0,
            "snowIN":           0,
            "pressureMB":       None,
            "pressureIN":       None,
            "windDir":          wind_dir,
            "windDirDEG":       wind_deg,
            "windSpeedKPH":     wind_kph,
            "windSpeedMPH":     wind_mph,
            "windSpeedMaxKPH":  wind_kph,
            "windSpeedMaxMPH":  wind_mph,
            "windSpeedMinKPH":  wind_kph,
            "windSpeedMinMPH":  wind_mph,
            "windGustKPH":      gust_kph,
            "windGustMPH":      gust_mph,
            "uvi":              uvi,
            "weatherPrimary":   summary,
            "icon":             icon_code,
            "isDay":            is_day,
        })

    return {
        "success": True,
        "error": None,
        "response": [{
            "loc":      {"long": STATION_LON, "lat": STATION_LAT},
            "interval": "1hour",
            "place":    {"name": PLACE_NAME, "state": PLACE_STATE, "country": PLACE_COUNTRY},
            "periods":  out_periods,
        }]
    }


def write_atomic(path, data):
    tmp = path + ".tmp"
    with open(tmp, "w") as f:
        json.dump(data, f)
    os.replace(tmp, path)
    print(f"Written: {path}")


def main():
    print("Fetching Open-Meteo forecast...")
    data = fetch_json(OM_URL)

    print("Building awd.txt (daily)...")
    awd = build_awd(data)
    write_atomic(AWD_PATH, awd)

    print("Building awh.txt (hourly)...")
    awh = build_awh(data)
    write_atomic(AWH_PATH, awh)

    print("Done.")


if __name__ == "__main__":
    main()
