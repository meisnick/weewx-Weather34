#!/usr/bin/env python3
"""
metar_update.py
Fetches METAR from aviationweather.gov (free, no key) and writes me.txt
in CheckWX-compatible JSON format so metar34get.php needs no changes.
"""

import json, math, os, sys, urllib.request, urllib.error
from datetime import datetime, timezone
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from w34config import ICAO, ME_PATH

API_URL  = f"https://aviationweather.gov/api/data/metar?ids={ICAO}&hours=0&format=json"
HEADERS  = {"User-Agent": "weewx-weather34/aviationweather"}

CLOUD_TEXT = {
    "SKC": "Sky Clear", "CLR": "Clear", "CAVOK": "Clear",
    "FEW": "Few", "SCT": "Scattered", "BKN": "Broken",
    "OVC": "Overcast", "OVX": "Sky Obscured",
}

# wxString token -> (CheckWX code, display text)
WX_MAP = {
    "+TSRA": ("TSRA",  "Heavy Thunderstorms with Rain"),
    "TSRA":  ("TSRA",  "Thunderstorms with Rain"),
    "+TS":   ("+TS",   "Heavy Thunderstorms"),
    "-TS":   ("-TS",   "Thunderstorms"),
    "TS":    ("TS",    "Thunderstorms"),
    "+SHRA": ("+SHRA", "Heavy Rain Showers"),
    "-SHRA": ("-SHRA", "Light Rain Showers"),
    "SHRA":  ("SHRA",  "Rain Showers"),
    "+RASN": ("+RA",   "Heavy Rain and Snow"),
    "-RASN": ("-RA",   "Light Rain and Snow"),
    "RASN":  ("RA",    "Rain and Snow"),
    "+RA":   ("+RA",   "Heavy Rain"),
    "-RA":   ("-RA",   "Light Rain"),
    "RA":    ("RA",    "Rain"),
    "+SN":   ("+SN",   "Heavy Snow"),
    "-SN":   ("-SN",   "Light Snow"),
    "SN":    ("SN",    "Snow"),
    "SG":    ("SG",    "Snow Grains"),
    "SNINCR":("SNINCR","Increasing Snow"),
    "+FZRA": ("FZRA",  "Heavy Freezing Rain"),
    "-FZRA": ("FZRA",  "Light Freezing Rain"),
    "FZRA":  ("FZRA",  "Freezing Rain"),
    "FZFG":  ("FZFG",  "Freezing Fog"),
    "-FZDZ": ("DZ",    "Light Freezing Drizzle"),
    "FZDZ":  ("DZ",    "Freezing Drizzle"),
    "+DZ":   ("DZ",    "Heavy Drizzle"),
    "-DZ":   ("DZ",    "Light Drizzle"),
    "DZ":    ("DZ",    "Drizzle"),
    "+PL":   ("PL",    "Heavy Ice Pellets"),
    "-PL":   ("PL",    "Light Ice Pellets"),
    "PL":    ("PL",    "Ice Pellets"),
    "GR":    ("GR",    "Hail"),
    "GS":    ("GS",    "Small Hail"),
    "IC":    ("IC",    "Ice Crystals"),
    "BCFG":  ("BCFG",  "Patchy Fog"),
    "MIFG":  ("FG",    "Shallow Fog"),
    "FG":    ("FG",    "Fog"),
    "BR":    ("BR",    "Mist"),
    "HZ":    ("HZ",    "Haze"),
    "FU":    ("FU",    "Smoke"),
    "VA":    ("VA",    "Volcanic Ash"),
    "DU":    ("DU",    "Widespread Dust"),
    "SA":    ("SA",    "Sand"),
    "SS":    ("SS",    "Sandstorm"),
    "DS":    ("DS",    "Dust Storm"),
    "PO":    ("PO",    "Dust Whirls"),
    "SQ":    ("SQ",    "Squalls"),
    "+FC":   ("+FC",   "Tornado/Waterspout"),
}

def parse_wx(wxstr):
    if not wxstr:
        return []
    results = []
    remaining = wxstr
    for token in sorted(WX_MAP, key=len, reverse=True):
        if token in remaining:
            code, text = WX_MAP[token]
            results.append({"code": code, "text": text})
            remaining = remaining.replace(token, "")
    return results[:2]

def humidity(t, d):
    if t is None or d is None:
        return None
    a, b = 17.625, 243.04
    return round(100 * math.exp(a*d/(b+d)) / math.exp(a*t/(b+t)))

def vis_meters(v):
    if v is None:
        return None
    s = str(v).replace("+", "")
    try:
        return round(float(s) * 1609.34)
    except ValueError:
        return None

def fetch():
    req = urllib.request.Request(API_URL, headers=HEADERS)
    try:
        with urllib.request.urlopen(req, timeout=15) as r:
            return json.loads(r.read().decode())
    except Exception as e:
        print(f"Fetch error: {e}", file=sys.stderr)
        sys.exit(1)

def write_atomic(path, data):
    tmp = path + ".tmp"
    with open(tmp, "w") as f:
        json.dump(data, f)
    os.replace(tmp, path)

def main():
    print(f"Fetching METAR {ICAO} from aviationweather.gov...")
    data = fetch()
    if not data:
        print("No data returned", file=sys.stderr)
        sys.exit(1)

    m = data[0]
    tc   = m.get("temp")
    dc   = m.get("dewp")
    tf   = round(tc * 9/5 + 32, 1) if tc is not None else None
    df   = round(dc * 9/5 + 32, 1) if dc is not None else None
    altm = m.get("altim")
    alth = round(altm / 33.8639, 2) if altm else None
    wspd = m.get("wspd") or 0
    wgst = m.get("wgst")

    raw_clouds = m.get("clouds", [])
    clouds_out = []
    for c in raw_clouds:
        code = c.get("cover", "")
        base = c.get("base")
        clouds_out.append({
            "code":   code,
            "text":   CLOUD_TEXT.get(code, code),
            "feet":   base,
            "meters": round(base * 0.3048) if base else None,
        })
    if not clouds_out:
        cover = m.get("cover", "CLR")
        clouds_out = [{"code": cover, "text": CLOUD_TEXT.get(cover, cover), "feet": None, "meters": None}]

    rt = m.get("reportTime", "")
    observed = rt[:19] + "Z" if len(rt) >= 19 else rt

    out = {
        "results": 1,
        "data": [{
            "icao":       m.get("icaoId", ICAO),
            "raw_text":   m.get("rawOb", ""),
            "observed":   observed,
            "conditions": parse_wx(m.get("wxString", "")),
            "clouds":     clouds_out,
            "barometer":  {
                "hg":  alth,
                "mb":  altm,
                "hpa": altm,
                "kpa": round(altm / 10, 2) if altm else None,
            },
            "dewpoint":    {"celsius": dc,   "fahrenheit": df},
            "temperature": {"celsius": tc,   "fahrenheit": tf},
            "humidity":    {"percent": humidity(tc, dc)},
            "visibility":  {"meters":  vis_meters(m.get("visib"))},
            "wind": {
                "degrees":   m.get("wdir", 0),
                "speed_kts": wspd,
                "speed_mph": round(wspd * 1.15078, 1),
                "speed_kph": round(wspd * 1.852,   1),
                "speed_mps": round(wspd * 0.514444,1),
                "gust_kts":  wgst,
                "gust_mph":  round(wgst * 1.15078, 1) if wgst else None,
            },
            "station": {
                "name": m.get("name", ""),
                "geometry": {
                    "type":        "Point",
                    "coordinates": [m.get("lon"), m.get("lat")],
                },
            },
            "flight_category": m.get("fltCat", ""),
        }]
    }

    write_atomic(ME_PATH, out)
    print(f"Written: {ME_PATH}")
    print("Done.")

if __name__ == "__main__":
    main()
