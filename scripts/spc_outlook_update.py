#!/usr/bin/env python3
"""
spc_outlook_update.py
Severe-storm outlook for the Storm Outlook popout. Writes jsondata/spc_outlook.json from the NWS Storm
Prediction Center's convective outlooks (free, keyless GeoJSON at www.spc.noaa.gov/products/):

  Day 1, Day 2  categorical risk (general thunder, marginal ... high) and the tornado, wind and hail probabilities,
                with the hatched "significant" areas
  Day 3         categorical risk and the probability of any severe weather
  Day 4 to 8    the probability of any severe weather (15% and 30% areas)

For each day it records the risk and the hazard probabilities at the station, the nearest severe-risk area when
the station is outside one, the outlook polygons (for the popout's map) and, for Days 1 to 3, the summary from
the forecaster's discussion. Each day fails on its own; the last good copy is kept. Root cron,
twice an hour (SPC issues Day 1 five times a day):
  7,37 * * * * /usr/bin/python3 /usr/local/bin/spc_outlook_update.py >> /var/log/spc_outlook.log 2>&1
"""

import json, math, os, re, sys, urllib.request
from concurrent.futures import ThreadPoolExecutor
from datetime import datetime, timezone
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import w34config as C

LAT, LON = C.LAT, C.LON
OUT_PATH = getattr(C, "SPC_OUTLOOK_PATH", C.WEB_ROOT + "/jsondata/spc_outlook.json")

HEADERS = {"User-Agent": "weewx-weather34/spc-outlook"}
BASE = "https://www.spc.noaa.gov/products/"
CAT = ["TSTM", "MRGL", "SLGT", "ENH", "MDT", "HIGH"]
# day -> {layer: GeoJSON path}; the text discussion for Days 1-3
DAYS = {
    1: {"cat": "outlook/day1otlk_cat", "torn": "outlook/day1otlk_torn", "wind": "outlook/day1otlk_wind",
        "hail": "outlook/day1otlk_hail", "sigtorn": "outlook/day1otlk_sigtorn", "sigwind": "outlook/day1otlk_sigwind",
        "sighail": "outlook/day1otlk_sighail"},
    2: {"cat": "outlook/day2otlk_cat", "torn": "outlook/day2otlk_torn", "wind": "outlook/day2otlk_wind",
        "hail": "outlook/day2otlk_hail", "sigtorn": "outlook/day2otlk_sigtorn", "sigwind": "outlook/day2otlk_sigwind",
        "sighail": "outlook/day2otlk_sighail"},
    3: {"cat": "outlook/day3otlk_cat", "prob": "outlook/day3otlk_prob", "sigprob": "outlook/day3otlk_sigprob"},
}
for d in range(4, 9):
    DAYS[d] = {"prob": "exper/day4-8/day%dprob" % d}
NEAR_KM = 800   # report the nearest severe-risk area within this distance


def fetch(url, timeout=40):
    with urllib.request.urlopen(urllib.request.Request(url, headers=HEADERS), timeout=timeout) as r:
        return r.read().decode("utf-8", "replace")


def polygons(geom):
    """GeoJSON geometry -> list of polygons, each a list of rings of (lon, lat)."""
    t = geom.get("type")
    if t == "Polygon":
        return [geom["coordinates"]]
    if t == "MultiPolygon":
        return geom["coordinates"]
    if t == "GeometryCollection":
        return [p for g in geom.get("geometries", []) for p in polygons(g)]
    return []


def inside(x, y, polys):
    """Even-odd test over every ring, so holes count."""
    c = False
    for poly in polys:
        for ring in poly:
            j = len(ring) - 1
            for i in range(len(ring)):
                (xi, yi), (xj, yj) = ring[i][:2], ring[j][:2]
                if (yi > y) != (yj > y) and x < (xj - xi) * (y - yi) / (yj - yi) + xi:
                    c = not c
                j = i
    return c


def nearest(polys):
    """Distance (km) and compass bearing from the station to the closest point of the polygons' edges."""
    kx, ky = 111.32 * math.cos(math.radians(LAT)), 110.57   # km per degree, flat around the station
    best = None
    for poly in polys:
        for ring in poly:
            for (x1, y1), (x2, y2) in zip(ring, ring[1:]):
                ax, ay, bx, by = (x1 - LON) * kx, (y1 - LAT) * ky, (x2 - LON) * kx, (y2 - LAT) * ky
                dx, dy = bx - ax, by - ay
                L = dx * dx + dy * dy
                t = max(0.0, min(1.0, -(ax * dx + ay * dy) / L)) if L else 0.0
                px, py = ax + t * dx, ay + t * dy
                d = math.hypot(px, py)
                if best is None or d < best[0]:
                    best = (d, px, py)
    if best is None:
        return None
    return round(best[0]), round(math.degrees(math.atan2(best[1], best[2])) % 360)


def iso(p, key):
    """VALID_ISO and friends when present (newer files), else from the bare UTC stamp, e.g. 202407151630."""
    if p.get(key + "_ISO"):
        return p[key + "_ISO"]
    try:
        return datetime.strptime(str(p.get(key, "")), "%Y%m%d%H%M").strftime("%Y-%m-%dT%H:%M:00+00:00")
    except ValueError:
        return None


def layer(path):
    gj = json.loads(fetch(BASE + path + ".lyr.geojson"))
    feats, meta = [], {}
    for f in gj.get("features", []):
        p = f.get("properties", {})
        if not meta and p.get("VALID"):
            meta = {"valid": iso(p, "VALID"), "expire": iso(p, "EXPIRE"), "issue": iso(p, "ISSUE"),
                    "forecaster": p.get("FORECASTER", "")}
        polys = polygons(f.get("geometry") or {})
        if not polys:
            continue   # "Less Than 2% All Areas" and the like
        feats.append({"label": p.get("LABEL", ""), "label2": p.get("LABEL2", ""), "dn": p.get("DN", 0),
                      "fill": p.get("fill", ""), "stroke": p.get("stroke", ""), "polys": polys})
    return feats, meta


def pct(label):
    try:
        return round(float(label) * 100)
    except ValueError:
        return None


def discussion(day):
    """The SUMMARY paragraph of the Day 1-3 outlook text (the all-caps headline above it says the same)."""
    txt = fetch(BASE + "outlook/day%dotlk.txt" % day).replace("\r", "")
    summ = re.search(r"\.\.\.SUMMARY\.\.\.\s*\n(.*?)\n\s*\n", txt, re.S)
    return {"summary": re.sub(r"\s+", " ", summ.group(1)).strip() if summ else ""}


def slim(f):
    """A feature for the map: rings as flat [lon, lat, ...] rounded to 0.01 degree."""
    rings = [[[c for x, y in ring for c in (round(x, 2), round(y, 2))] for ring in poly] for poly in f["polys"]]
    return {k: v for k, v in f.items() if k != "polys"} | {"polys": rings}


def build_day(day):
    out = {"day": day, "layers": {}, "point": {}}
    for key, path in DAYS[day].items():
        feats, meta = layer(path)
        if meta and "valid" not in out:
            out.update(meta)
        here = [f for f in feats if inside(LON, LAT, f["polys"])]
        if key == "cat":
            hit = max(here, key=lambda f: CAT.index(f["label"]) if f["label"] in CAT else -1, default=None)
            out["point"]["cat"] = hit["label"] if hit else ""
            severe = [f for f in feats if f["label"] in CAT[1:]]
            if severe and (not hit or hit["label"] == "TSTM"):
                near = []
                for f in severe:
                    n = nearest(f["polys"])
                    if n and n[0] <= NEAR_KM:
                        near.append((n[0], n[1], f["label"]))
                if near:
                    d, b, lab = min(near)
                    out["point"]["near"] = {"cat": lab, "km": d, "bearing": b}
        elif key.startswith("sig"):
            out["point"][key] = out["point"].get(key, False) or bool(here)
        else:
            # older and archived files carry the hatched "SIGN" area inside the probability layer: move it to the sig layer
            sign = [f for f in feats if pct(f["label"]) is None]
            if sign:
                feats = [f for f in feats if f not in sign]
                out["point"]["sig" + key] = out["point"].get("sig" + key, False) or any(f in here for f in sign)
                out["layers"].setdefault("sig" + key, []).extend(slim(f) for f in sign)
            probs = [pct(f["label"]) for f in here if pct(f["label"]) is not None]
            out["point"][key] = max(probs) if probs else 0
        out["layers"].setdefault(key, []).extend(slim(f) for f in feats)
    if day <= 3:
        try:
            out.update(discussion(day))
        except Exception as e:
            print("day %d discussion failed: %s" % (day, e), file=sys.stderr)
    return out


def write_atomic(path, data):
    tmp = path + ".tmp"
    with open(tmp, "w") as f:
        json.dump(data, f, separators=(",", ":"))
    try:
        os.chmod(tmp, 0o664)
    except Exception:
        pass
    os.replace(tmp, path)


def main():
    try:
        with open(OUT_PATH) as f:
            prev = {d["day"]: d for d in json.load(f).get("days", [])}
    except Exception:
        prev = {}
    now = datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ")
    with ThreadPoolExecutor(len(DAYS)) as ex:   # eight independent days, a few small files each
        futs = {d: ex.submit(build_day, d) for d in DAYS}
    days = []
    for d, fut in futs.items():
        try:
            r = fut.result()
            r["fetched"] = now
            days.append(r)
            print("%s day %d ok: %s" % (now, d, r["point"]))
        except Exception as e:
            print("%s day %d failed: %s" % (now, d, e), file=sys.stderr)
            if d in prev:
                days.append(prev[d])   # the last good copy; its own 'fetched' says how old it is
    write_atomic(OUT_PATH, {"fetched": now, "days": days})


if __name__ == "__main__":
    main()
