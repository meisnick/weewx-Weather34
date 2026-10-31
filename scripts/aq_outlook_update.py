#!/usr/bin/env python3
"""
aq_outlook_update.py
Air-quality cause and outlook for the Air Quality card and its popout. Writes jsondata/aq_outlook.json from
four free, keyless sources:

  smoke    NOAA Hazard Mapping System: analyst-drawn satellite smoke plumes (Light/Medium/Heavy), checked
           against the station's position. Sees smoke anywhere in the air column, not only at the ground.
  forecast AirNow reportingarea.dat: the state/local agency's daily AQI category forecast (today + 3 days)
           for the nearest reporting area, or AQ_REPORTING_AREA.
  monitor  AirNow hourly data for AQ_MONITOR_SITE (optional): black carbon (soot) against its own 30-day
           median tells combustion smoke at ground level from other fine particles.
  model    Open-Meteo air-quality model (CAMS): hourly US AQI, PM2.5 and ozone sub-indices, -12 h to +60 h.

Each source fails on its own; the last good value is kept with its own timestamp. Root cron, hourly
(AirNow posts the hour's data around :20):
  25 * * * * /usr/bin/python3 /usr/local/bin/aq_outlook_update.py >> /var/log/aq_outlook.log 2>&1
"""

import json, math, os, re, sys, statistics, urllib.request
from concurrent.futures import ThreadPoolExecutor
from datetime import datetime, timedelta, timezone
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import w34config as C

LAT, LON = C.LAT, C.LON
OUT_PATH = getattr(C, "AQ_OUTLOOK_PATH", C.WEB_ROOT + "/jsondata/aq_outlook.json")
AREA = getattr(C, "AQ_REPORTING_AREA", "")       # AirNow reporting area name; "" = nearest
SITE = getattr(C, "AQ_MONITOR_SITE", "")         # AirNow site id (AQSID) of a monitor measuring BC; "" = none
BC_BASELINE = getattr(C, "AQ_BC_BASELINE", None)  # usual BC (ug/m3) until 72 h of history have built up

HEADERS = {"User-Agent": "weewx-weather34/aq-outlook"}
HMS_URL = "https://satepsanone.nesdis.noaa.gov/pub/FIRE/web/HMS/Smoke_Polygons/KML/{d:%Y}/{d:%m}/hms_smoke{d:%Y%m%d}.kml"
RA_URL = "https://files.airnowtech.org/airnow/today/reportingarea.dat"
HOURLY_URL = "https://files.airnowtech.org/airnow/today/HourlyData_{d:%Y%m%d%H}.dat"
OM_URL = ("https://air-quality-api.open-meteo.com/v1/air-quality?latitude={lat}&longitude={lon}"
          "&hourly=us_aqi,us_aqi_pm2_5,us_aqi_ozone&timezone={tz}&past_days=1&forecast_days=3")
CATS = ["Good", "Moderate", "Unhealthy for Sensitive Groups", "Unhealthy", "Very Unhealthy", "Hazardous"]
DENSITY = ["Light", "Medium", "Heavy"]


def fetch(url, timeout=60):
    with urllib.request.urlopen(urllib.request.Request(url, headers=HEADERS), timeout=timeout) as r:
        return r.read().decode("utf-8", "replace")


def inside(x, y, pts):
    c, j = False, len(pts) - 1
    for i in range(len(pts)):
        (xi, yi), (xj, yj) = pts[i], pts[j]
        if (yi > y) != (yj > y) and x < (xj - xi) * (y - yi) / (yj - yi) + xi:
            c = not c
        j = i
    return c


def smoke():
    """Densest HMS plume over the station: today's (UTC) analysis, or yesterday's before today's has any plumes."""
    now = datetime.now(timezone.utc)
    for d in (now, now - timedelta(days=1)):
        try:
            kml = fetch(HMS_URL.format(d=d))
        except Exception:
            continue
        marks = re.findall(r"<Placemark>(.*?)</Placemark>", kml, re.S)
        if not marks:
            continue
        best = None
        for pm in marks:
            den = re.search(r"Density:\s*(\w+)", pm)
            den = den.group(1).capitalize() if den else "Light"
            if den not in DENSITY:
                continue
            for co in re.findall(r"<coordinates>(.*?)</coordinates>", pm, re.S):
                pts = [tuple(map(float, t.split(",")[:2])) for t in co.split()]
                if len(pts) > 2 and inside(LON, LAT, pts):
                    if best is None or DENSITY.index(den) > DENSITY.index(best["density"]):
                        t = re.findall(r"(Start|End) Time:\s*\d{4}(\d{3})\s*(\d{2})(\d{2})UTC", pm)
                        sat = re.search(r"Satellite:\s*([^<]+)", pm)
                        best = {"density": den, "start": "%s:%s" % t[0][2:] if t else "",
                                "end": "%s:%s" % t[1][2:] if len(t) > 1 else "", "satellite": sat.group(1).strip() if sat else ""}
        out = {"date": d.strftime("%Y-%m-%d"), "plumes": len(marks), "density": "None"}
        if best:
            out.update(best)
        return out
    raise RuntimeError("no HMS analysis for today or yesterday")


def forecast():
    """Agency AQI forecast per day for the reporting area: the worst category forecast for each day."""
    rows = [r.split("|") for r in fetch(RA_URL).splitlines()]
    rows = [r for r in rows if len(r) >= 17 and r[5] == "F"]
    if AREA:
        area = AREA
    else:
        def dist(r):
            la, lo = math.radians(float(r[9])), math.radians(float(r[10]))
            return math.acos(min(1, math.sin(math.radians(LAT)) * math.sin(la) +
                                 math.cos(math.radians(LAT)) * math.cos(la) * math.cos(math.radians(LON) - lo)))
        area = min(rows, key=dist)[7]
    days = {}
    for r in rows:
        if r[7] != area or r[13] not in CATS:
            continue
        date = datetime.strptime(r[1], "%m/%d/%y").strftime("%Y-%m-%d")
        cur = days.get(date)
        cat = CATS.index(r[13])
        if cur is None or cat > CATS.index(cur["cat"]) or (cat == CATS.index(cur["cat"]) and r[6] == "Y"):
            days[date] = {"date": date, "cat": r[13], "pol": r[11], "action": r[14] == "Yes"}
    if not days:
        raise RuntimeError("no forecast rows for %r" % area)
    agency = next((r[16] for r in rows if r[7] == area), "")
    issued = next((datetime.strptime(r[0], "%m/%d/%y").strftime("%Y-%m-%d") for r in rows if r[7] == area), "")
    return {"area": area, "agency": agency, "issued": issued, "days": [days[k] for k in sorted(days)][:4]}


def monitor(prev):
    """Latest hour of PM2.5, ozone and black carbon at SITE, with BC's ratio to its 30-day median."""
    now = datetime.now(timezone.utc).replace(minute=0, second=0, microsecond=0)
    for back in range(1, 4):
        d = now - timedelta(hours=back)
        try:
            txt = fetch(HOURLY_URL.format(d=d))
        except Exception:
            continue
        vals, name = {}, ""
        for ln in txt.splitlines():
            f = ln.split("|")
            if len(f) >= 8 and f[2] == SITE:
                name = f[3]
                try:
                    vals[f[5]] = float(f[7])
                except ValueError:
                    pass
        if "BC" not in vals and "PM2.5" not in vals:
            continue
        hist = [h for h in (prev or {}).get("bc_hist", []) if h[0] != d.strftime("%Y%m%d%H")]
        if "BC" in vals and vals["BC"] >= 0:
            hist.append([d.strftime("%Y%m%d%H"), vals["BC"]])
        hist = hist[-720:]
        base = statistics.median(h[1] for h in hist) if len(hist) >= 72 else BC_BASELINE
        bc = vals.get("BC")
        return {"site": SITE, "name": name.title(), "time": d.strftime("%Y-%m-%dT%H:00Z"), "bc": bc,
                "pm25": vals.get("PM2.5"), "o3": vals.get("OZONE"), "bc_base": base,
                "bc_ratio": round(bc / base, 1) if bc is not None and base else None, "bc_hist": hist}
    raise RuntimeError("site %s not in the last 3 hourly files" % SITE)


def model():
    h = json.loads(fetch(OM_URL.format(lat=LAT, lon=LON, tz=urllib.request.quote(C.TZ))))["hourly"]
    now = datetime.now().strftime("%Y-%m-%dT%H:00")
    i0 = h["time"].index(now) if now in h["time"] else 0
    lo, hi = max(0, i0 - 12), i0 + 60
    return {"time": h["time"][lo:hi], "aqi": h["us_aqi"][lo:hi], "pm25": h["us_aqi_pm2_5"][lo:hi],
            "o3": h["us_aqi_ozone"][lo:hi]}


def write_atomic(path, data):
    tmp = path + ".tmp"
    with open(tmp, "w") as f:
        json.dump(data, f)
    try:
        os.chmod(tmp, 0o664)
    except Exception:
        pass
    os.replace(tmp, path)


def main():
    try:
        with open(OUT_PATH) as f:
            prev = json.load(f)
    except Exception:
        prev = {}
    out = {"fetched": datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ")}
    jobs = [("smoke", smoke), ("forecast", forecast), ("model", model)]
    if SITE:
        jobs.append(("monitor", lambda: monitor(prev.get("monitor"))))
    with ThreadPoolExecutor(len(jobs)) as ex:   # independent downloads (reportingarea.dat alone is ~2 MB)
        futs = {key: ex.submit(fn) for key, fn in jobs}
    for key, fut in futs.items():
        try:
            out[key] = fut.result()
            out[key]["fetched"] = out["fetched"]
            print("%s %s ok" % (out["fetched"], key))
        except Exception as e:
            print("%s %s failed: %s" % (out["fetched"], key, e), file=sys.stderr)
            if key in prev:
                out[key] = prev[key]   # keep the last good value; its own 'fetched' says how old it is
    write_atomic(OUT_PATH, out)


if __name__ == "__main__":
    main()
