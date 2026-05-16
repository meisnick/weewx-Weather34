#!/usr/bin/env python3
"""
nws_alerts_update.py
Fetches active NWS weather alerts for the configured zone and county and
writes a simplified JSON to nws_alerts.txt for top_advisory_nws.php.
Free, no API key, official US government source.
Configure zones in scripts/w34config.py (see w34config.example.py).
"""

import json, os, sys, urllib.request, urllib.error
from datetime import datetime, timezone
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from w34config import ALERT_ZONES, ALERTS_PATH

ALERTS_URL = f"https://api.weather.gov/alerts/active?zone={ALERT_ZONES}"
OUT_PATH   = ALERTS_PATH
HEADERS    = {"User-Agent": "weewx-weather34/nws-alerts",
              "Accept": "application/geo+json"}

SEVERITY_ORDER = {"Extreme": 0, "Severe": 1, "Moderate": 2, "Minor": 3, "Unknown": 4}

def fetch(url):
    req = urllib.request.Request(url, headers=HEADERS)
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
    print(f"Fetching NWS alerts for zones {ALERT_ZONES}...")
    data = fetch(ALERTS_URL)
    features = data.get("features", [])

    alerts = []
    for f in features:
        p = f.get("properties", {})
        # Skip cancelled/expired
        if p.get("status") in ("Cancel", "Expire"):
            continue
        alerts.append({
            "event":       p.get("event", ""),
            "severity":    p.get("severity", "Unknown"),
            "urgency":     p.get("urgency", "Unknown"),
            "certainty":   p.get("certainty", "Unknown"),
            "headline":    p.get("headline", ""),
            "description": (p.get("description", "") or "")[:400],
            "effective":   p.get("effective", ""),
            "expires":     p.get("expires", ""),
            "sender":      p.get("senderName", "NWS"),
        })

    # Sort by severity
    alerts.sort(key=lambda a: SEVERITY_ORDER.get(a["severity"], 4))

    out = {
        "fetched": datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"),
        "count":   len(alerts),
        "alerts":  alerts,
    }

    write_atomic(OUT_PATH, out)
    print(f"Written {len(alerts)} alert(s) to {OUT_PATH}")
    print("Done.")

if __name__ == "__main__":
    main()
