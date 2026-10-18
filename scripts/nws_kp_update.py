#!/usr/bin/env python3
"""
nws_kp_update.py
Fetches the planetary K-index forecast JSON directly from NOAA SWPC and
writes it to jsondata/ki.txt for the space weather/aurora module.
Free, no API key, official US government source.
Allows the dashboard to run independently of pi1.
"""

import json, os, sys, urllib.request

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from w34config import WEB_ROOT

OUT_PATH = os.path.join(WEB_ROOT, "jsondata/ki.txt")
HEADERS = {"User-Agent": "weewx-weather34/nws-kp-update"}

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

def main():
    url = "https://services.swpc.noaa.gov/products/noaa-planetary-k-index-forecast.json"
    print("Fetching NOAA Planetary K-Index Forecast JSON...")
    req = urllib.request.Request(url, headers=HEADERS)
    try:
        with urllib.request.urlopen(req, timeout=15) as r:
            data = json.loads(r.read().decode())
    except Exception as e:
        print(f"Error fetching K-index forecast from NOAA: {e}", file=sys.stderr)
        sys.exit(1)
        
    if not isinstance(data, list) or len(data) == 0:
        print("Error: Invalid data format received from NOAA.", file=sys.stderr)
        sys.exit(1)

    # Keep a maximum of 85 entries for display efficiency
    final_entries = data[-85:]

    # Save to ki.txt
    write_atomic(OUT_PATH, final_entries)
    print(f"Successfully wrote {len(final_entries)} entries to {OUT_PATH}")

if __name__ == "__main__":
    main()
