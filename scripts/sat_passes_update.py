#!/usr/bin/env python3
"""
sat_passes_update.py
Clear satellite passes over the station for the Satellite popout's Close-up tab. Writes jsondata/sat_passes.json from
NASA's Harmonized Landsat and Sentinel-2 imagery (HLS, 30 m) as NASA GIBS serves it (free, keyless map tiles):
Landsat 8/9 (HLS_L30) and Sentinel-2 (HLS_S30) between them pass every two to three days.

For each day of the last 90 and each source it fetches the zoom-10 tile holding the station (about 40 km across) and
scores how much of it the pass covers and how much is cloud (bright, colourless pixels; fresh snow counts too). A pass
covering 90% of the tile with at most 5% cloud is clear. Scores for days more than a week old are kept and not fetched
again (GIBS adds passes a few days late). The popout lists the six latest clear passes; when there are fewer than three
(a cloudy month, or snow) it adds the least cloudy ones with their cloud share. Root cron, every six hours:
  45 */6 * * * /usr/bin/python3 /usr/local/bin/sat_passes_update.py >> /var/log/sat_passes.log 2>&1
"""

import io, json, math, os, sys, urllib.error, urllib.request
from concurrent.futures import ThreadPoolExecutor
from datetime import date, datetime, timedelta, timezone
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import w34config as C
from PIL import Image

LAT, LON = C.LAT, C.LON
OUT_PATH = getattr(C, "SAT_PASSES_PATH", C.WEB_ROOT + "/jsondata/sat_passes.json")
HEADERS = {"User-Agent": "weewx-weather34/sat-passes"}
TILE = ("https://gibs.earthdata.nasa.gov/wmts/epsg3857/best/HLS_%s_Nadir_BRDF_Adjusted_Reflectance/default/%s/"
        "GoogleMapsCompatible_Level12/%d/%d/%d.png")
SOURCES = {"L30": "Landsat", "S30": "Sentinel-2"}
DAYS, RECHECK, Z = 90, 7, 10
CLEAR_COVER, CLEAR_CLOUD, KEEP, MIN_CLEAR = .9, .05, 6, 3


def tile_xy(z):
    n = 2 ** z
    return int((LON + 180) / 360 * n), int((1 - math.asinh(math.tan(math.radians(LAT))) / math.pi) / 2 * n)


def score(src, day):
    """[cover, cloud] of the station's tile on that day, None when GIBS has no pass (404) or the fetch fails."""
    x, y = tile_xy(Z)
    try:
        req = urllib.request.Request(TILE % (src, day, Z, y, x), headers=HEADERS)
        with urllib.request.urlopen(req, timeout=40) as r:
            im = Image.open(io.BytesIO(r.read())).convert("RGBA").resize((64, 64))
    except urllib.error.HTTPError as e:
        return [0.0, None] if e.code in (400, 404) else None
    except Exception:
        return None
    px = list(im.getdata())
    valid = [p for p in px if p[3] > 0 and p[0] + p[1] + p[2] > 0]
    if not valid:
        return [0.0, None]
    cloud = sum(1 for r, g, b, a in valid if min(r, g, b) > 170 and max(r, g, b) - min(r, g, b) < 35)
    return [round(len(valid) / len(px), 3), round(cloud / len(valid), 3)]


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
            scores = json.load(f).get("scores", {})
    except Exception:
        scores = {}
    today = datetime.now(timezone.utc).date()
    days = [(today - timedelta(days=i)).isoformat() for i in range(DAYS)]
    recent = set(days[:RECHECK])
    jobs = [(s, d) for d in days for s in SOURCES if "%s:%s" % (s, d) not in scores or d in recent]
    with ThreadPoolExecutor(16) as ex:   # small tiles, one per source and day
        results = list(ex.map(lambda j: (j, score(*j)), jobs))
    for (s, d), r in results:
        if r is not None:
            scores["%s:%s" % (s, d)] = r
    scores = {k: v for k, v in scores.items() if k.split(":")[1] in set(days)}
    seen = [(k.split(":")[1], k.split(":")[0], v[0], v[1]) for k, v in scores.items() if v[0] >= CLEAR_COVER and v[1] is not None]
    seen.sort(reverse=True)
    clear = [p for p in seen if p[3] <= CLEAR_CLOUD]
    picks = clear[:KEEP]
    if len(picks) < MIN_CLEAR:   # a cloudy (or snowy) stretch: add the least cloudy of the rest
        rest = sorted((p for p in seen if p not in picks), key=lambda p: p[3])
        picks = sorted(picks + rest[:MIN_CLEAR - len(picks)], reverse=True)
    passes = [{"date": d, "src": s, "sat": SOURCES[s], "cloud": c} for d, s, cov, c in picks]
    write_atomic(OUT_PATH, {"fetched": datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"), "passes": passes, "scores": scores})
    print("%s %d fetched, %d passes: %s" % (datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"), len(jobs),
                                          len(passes), ", ".join("%s %s" % (p["date"], p["src"]) for p in passes)))


if __name__ == "__main__":
    main()
