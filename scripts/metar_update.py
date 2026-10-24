#!/usr/bin/env python3
"""
metar_update.py
Fetches METAR from aviationweather.gov (free, no key) and writes me.txt
in CheckWX-compatible JSON format so metar34get.php needs no changes.
"""

import json, math, os, sys, time, urllib.request, urllib.error
from datetime import datetime, timezone
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from w34config import ICAO, ME_PATH, WEB_ROOT

API_URL  = f"https://aviationweather.gov/api/data/metar?ids={ICAO}&hours=0&format=json"
# no-cache: the API's CDN has served reports hours old (2026-10-23 22:00 gave the 11:55Z report)
HEADERS  = {"User-Agent": "weewx-weather34/aviationweather", "Cache-Control": "no-cache"}

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

WX_PH = {
    "DZ": "Drizzle", "RA": "Rain", "SN": "Snow", "SG": "Snow Grains", "IC": "Ice Crystals", "PL": "Ice Pellets",
    "GR": "Hail", "GS": "Small Hail", "UP": "Precipitation", "BR": "Mist", "FG": "Fog", "FU": "Smoke",
    "VA": "Volcanic Ash", "DU": "Widespread Dust", "SA": "Sand", "HZ": "Haze", "PY": "Spray", "PO": "Dust Whirls",
    "SQ": "Squalls", "FC": "Funnel Cloud", "SS": "Sandstorm", "DS": "Dust Storm",
}
WX_DESC = {"MI": "Shallow", "PR": "Partial", "BC": "Patchy", "DR": "Drifting", "BL": "Blowing", "FZ": "Freezing"}


def wx_text(token):
    """Readable text for one present-weather token, e.g. '-SHSN' -> 'Light Snow Showers', 'VCTS' -> 'Thunderstorms
    Nearby'. No intensity prefix is MODERATE."""
    if token in WX_MAP:
        return WX_MAP[token][1]
    t, intensity, near = token, "", False
    if t.startswith("VC"):
        t, near = t[2:], True
    elif t[:1] in "-+":
        intensity, t = ("Light " if t[0] == "-" else "Heavy "), t[1:]
    shower = ts = False
    words = []
    while t[:2] in ("MI", "PR", "BC", "DR", "BL", "SH", "TS", "FZ"):
        d, t = t[:2], t[2:]
        if d == "SH":
            shower = True
        elif d == "TS":
            ts = True
        else:
            words.append(WX_DESC[d])
    words += [WX_PH.get(t[i:i + 2], t[i:i + 2]) for i in range(0, len(t), 2)]
    text = intensity + " ".join(words)
    if shower:
        text = (text + " Showers") if words else "Showers"
    if ts:
        text = "Thunderstorms" + (" with " + text if text.strip() else "")
    if t == "FC" and intensity == "Heavy ":
        text = "Tornado/Waterspout"
    return (text + " Nearby") if near else text


def parse_wx(wxstr):
    """Present-weather tokens in report order, each kept whole (intensity, VC, descriptors), so the dashboard can tell
    -RA from RA from +RA and VCSH from SH. The old substring match collapsed them (e.g. -DZ -> DZ, VCTS -> TS)."""
    if not wxstr:
        return []
    return [{"code": tok, "text": wx_text(tok)} for tok in wxstr.split()][:3]

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
    try:
        os.chmod(tmp, 0o664)
    except Exception:
        pass
    os.replace(tmp, path)
    try:
        os.chmod(path, 0o664)
    except Exception:
        pass

COVER_MAP = {
    "SKC": 0, "CLR": 0, "NSC": 0, "NCD": 0, "CAVOK": 0,
    "FEW": 19, "SCT": 44, "BKN": 75, "OVC": 100, "OVX": 100,
}

HISTORY_PATH    = WEB_ROOT + "/jsondata/cloudcover_history.json"
WEEK_JSON_PATH  = WEB_ROOT + "/w34highcharts/json/cloudcover_week.json"
YEAR_JSON_PATH  = WEB_ROOT + "/w34highcharts/json/cloudcover_year.json"
HISTORY_MAX_DAYS = 366


def cloud_cover_pct(clouds_out, raw_text=""):
    pct = 0
    if clouds_out:
        for layer in clouds_out:
            code = str(layer.get("code", "")).upper()
            if code in COVER_MAP:
                pct = max(pct, COVER_MAP[code])
    elif "VV" in str(raw_text).upper():
        pct = 100
    return pct


def _load_history():
    try:
        with open(HISTORY_PATH) as f:
            data = json.load(f)
        return data if isinstance(data, list) else []
    except (OSError, ValueError):
        return []


def local_utcoffset_minutes():
    return int(time.localtime().tm_gmtoff / 60)


def encode_series(points):
    if not points:
        return []
    points = sorted(points, key=lambda p: p[0])
    base = points[0][0]
    return [[p[0] if i == 0 else p[0] - base, p[1]] for i, p in enumerate(points)]


def local_midnight_epoch(epoch_sec):
    lt = time.localtime(epoch_sec)
    return int(time.mktime((lt.tm_year, lt.tm_mon, lt.tm_mday, 0, 0, 0, lt.tm_wday, lt.tm_yday, -1)))


def update_cloudcover_history(cover):
    now = int(time.time())
    history = _load_history()
    history.append([now, int(cover)])
    cutoff = now - HISTORY_MAX_DAYS * 86400
    write_atomic(HISTORY_PATH, [e for e in history if e[0] >= cutoff])


def regenerate_cloudcover_charts():
    history = _load_history()
    utcoffset = local_utcoffset_minutes()
    now = int(time.time())

    week_points = [e for e in history if e[0] >= now - 7 * 86400]
    week_wrapper = [{
        "utcoffset": utcoffset,
        "cloudcoverplot": {"units": "%", "cloudcoverWeek": encode_series(week_points)},
    }]
    write_atomic(WEEK_JSON_PATH, week_wrapper)

    days = {}
    for epoch_sec, cover in history:
        days.setdefault(local_midnight_epoch(epoch_sec), []).append(cover)
    sorted_days = sorted(days.keys())
    if sorted_days:
        base = sorted_days[0]
        max_series = [[base if i == 0 else d - base, max(days[d])] for i, d in enumerate(sorted_days)]
        avg_series = [[base if i == 0 else d - base, round(sum(days[d]) / len(days[d]))] for i, d in enumerate(sorted_days)]
    else:
        max_series, avg_series = [], []
    year_wrapper = [{
        "utcoffset": utcoffset,
        "cloudcoverplot": {"units": "%", "cloudcoverMax": max_series, "cloudcoverAvg": avg_series},
    }]
    write_atomic(YEAR_JSON_PATH, year_wrapper)


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

    # Never go backwards: a cached/out-of-order response older than the report on disk is dropped (and not added to
    # the cloud-cover history). ISO-8601 'Z' strings compare in time order.
    try:
        with open(ME_PATH) as f:
            current = json.load(f)["data"][0].get("observed", "")
    except Exception:
        current = ""
    if observed and current and observed < current:
        print(f"Older report ({observed}) than the one on disk ({current}); kept it.")
        return

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

    # Cloud cover: persist rolling history + regenerate weekly/yearly chart JSON
    cover = cloud_cover_pct(out["data"][0]["clouds"], out["data"][0]["raw_text"])
    update_cloudcover_history(cover)
    regenerate_cloudcover_charts()

    print(f"Written: {ME_PATH}")
    print("Done.")

if __name__ == "__main__":
    main()
