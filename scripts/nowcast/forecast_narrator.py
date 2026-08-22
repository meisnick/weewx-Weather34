#!/usr/bin/env python3
"""
forecast_narrator.py - Turn analogue forecast JSON into a plain-English sentence.

Reads JSON from stdin (output of analogue_forecast.py --json), queries
gemma3:1b via Ollama, returns a single forecast sentence.

Usage:
  python3 analogue_forecast.py --json | python3 forecast_narrator.py
"""
import json
import os
import re
import sys
import time
import urllib.request
from pathlib import Path
import analogue_forecast as af
import os
import sys
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
import w34config as cfg

# Try relative/local paths first, fall back to Pi standard path
SCRIPT_DIR = Path(__file__).resolve().parent
if Path("/var/www/html/weewx/weather34/jsondata").exists():
    OUTPUT_JSON = Path("/var/www/html/weewx/weather34/jsondata/local_forecast.json")
else:
    OUTPUT_JSON = SCRIPT_DIR / "local_forecast.json"

OLLAMA_URL = cfg.OLLAMA_URL
MODEL      = cfg.OLLAMA_MODEL

SYSTEM = (
    "You are a precise weather assistant. "
    "Your ONLY output is valid JSON: {\"forecast\": \"...\"}. "
    "Write one forecast sentence, 12-22 words. Plain language only. "
    "No jargon. No location names. No introductory phrases. "
    f"Preserve mandatory elements: the exact time-of-day phrase, {cfg.NOWCAST_ONSHORE_NAME.lower()} if mentioned, rain chance if mentioned, and temperature trend."
)

FEW_SHOT = [
    (
        "Refine into a polished forecast sentence (12-22 words):\n"
        "Draft: Pressure falling rapidly with rain likely this evening after sunset as temperatures cool from 72°F to near 61°F.",
        '{"forecast": "Pressure falling fast with rain likely this evening after sunset as temperatures cool into the low 60s."}'
    ),
    (
        "Refine into a polished forecast sentence (12-22 words):\n"
        "Draft: Dry conditions, warming from 58°F to near 73°F through sunrise into morning.",
        '{"forecast": "Dry conditions with temperatures warming from 58°F to near 73°F through sunrise into the morning."}'
    ),
    (
        "Refine into a polished forecast sentence (12-22 words):\n"
        "Draft: Lake breeze with temperatures staying near 62°F this afternoon, dry conditions, pressure rising.",
        '{"forecast": "Lake breeze keeping temperatures near 62°F this afternoon, with dry conditions and rising pressure."}'
    ),
    (
        "Refine into a polished forecast sentence (12-22 words):\n"
        "Draft: Lake breeze with temperatures cooling from 75°F to near 60°F this evening after sunset, rain likely, pressure falling.",
        '{"forecast": "Lake breeze keeping temperatures cooling to near 60°F this evening after sunset, with rain likely and falling pressure."}'
    ),
    (
        "Refine into a polished forecast sentence (12-22 words):\n"
        "Draft: Temperatures steady near 46°F before warming to near 56°F through sunrise into morning, dry conditions.",
        '{"forecast": "Temperatures holding near 46°F before warming to near 56°F through sunrise into the morning under dry skies."}'
    ),
]

LAKE_ONSHORE  = cfg.NOWCAST_ONSHORE_WINDS
NOWCAST_ONSHORE_NAME  = cfg.NOWCAST_ONSHORE_NAME
LAKE_NEUTRAL  = {"N", "NNE", "NNW", "S", "SSE", "SSW"}
LAKE_OFFSHORE = {"W", "WNW", "WSW", "SW", "NW"}

def interpret(data):
    """Pre-reason the key facts into an explicit draft sentence with mandatory terms."""
    cur = data["current"]
    fc  = data["forecast"]
    solar_ctx = data.get("solar_context", {})

    temp_now  = cur["temp_f"]
    temp_6h   = fc.get("6", fc.get("4", {})).get("mean_f")
    temp_1h   = fc.get("1", {}).get("mean_f")
    
    # Calculate max rain probability across future window from analogues
    rain_pct  = max((fc[h]["rain_pct"] for h in fc), default=0)
    
    # Meteorological Physical Rain Heuristics:
    # 1. Any analogue detected rain (e.g. >= 5%)
    # 2. Strong single-station pressure drop (dp_3h <= -0.030)
    # 3. Moisture saturation (dew_spread <= 7.5 or RH >= 75%) with falling pressure (dp_3h < 0)
    # 4. Deep moisture (dew_spread <= 4.0 or RH >= 88%)
    dew_spread = cur.get("dew_spread")
    rh_pct = cur.get("rh_pct")
    dp_3h = cur.get("dp_3h")
    
    if rain_pct >= 5:
        # Analogues captured precipitation in some past occurrences
        rain_pct = max(rain_pct, 20)
    elif dp_3h is not None and dp_3h <= -0.030:
        # Rapid barometric pressure drop indicates approaching frontal system/trough
        rain_pct = max(rain_pct, 25)
    elif (dew_spread is not None and dew_spread <= 7.5) and (dp_3h is not None and dp_3h <= -0.015):
        # High moisture combined with falling pressure
        rain_pct = max(rain_pct, 25)
    elif (dew_spread is not None and dew_spread <= 4.0) or (rh_pct is not None and rh_pct >= 88.0):
        # Surface saturation
        rain_pct = max(rain_pct, 20)

    pressure  = cur.get("pressure_trend", af.pressure_label(cur.get("dp_3h")))
    wind      = cur.get("wind_label", af.wind_label(cur.get("wind_dir")))
    lake      = wind in LAKE_ONSHORE

    # Timing context
    sunset_crossed = solar_ctx.get("sunset_crossed", False)
    sunrise_crossed = solar_ctx.get("sunrise_crossed", False)
    timing_phrase = solar_ctx.get("timing_phrase", "")
    if sunset_crossed:
        timing_phrase = "this evening after sunset"
    elif sunrise_crossed:
        timing_phrase = "through sunrise into morning"

    # Temperature trajectory
    if temp_6h is not None and temp_now is not None:
        delta = temp_6h - temp_now
        if delta >= 4:
            temp_story = f"warming from {temp_now:.0f}°F to near {temp_6h:.0f}°F {timing_phrase}"
        elif delta <= -4:
            temp_story = f"cooling from {temp_now:.0f}°F to near {temp_6h:.0f}°F {timing_phrase}"
        else:
            temp_story = f"staying near {temp_now:.0f}°F {timing_phrase}"
    else:
        temp_story = f"near {temp_now:.0f}°F {timing_phrase}" if temp_now else f"steady {timing_phrase}"

    # Lake modifier (crucial meteorological feature)
    lake_prefix = f"{NOWCAST_ONSHORE_NAME} with temperatures " if lake else "Temperatures "

    # Rain note
    if rain_pct >= 40:
        rain_note = f", rain likely"
    elif rain_pct >= 15:
        rain_note = f", chance of showers"
    else:
        rain_note = ", dry conditions"

    # Pressure note
    if "rapidly" in pressure:
        pressure_note = f", pressure {pressure}"
    elif pressure in ("rising", "falling"):
        pressure_note = f", pressure {pressure}"
    else:
        pressure_note = ""

    draft = f"{lake_prefix}{temp_story}{rain_note}{pressure_note}."

    return (
        f"Refine into a polished forecast sentence (12-22 words):\n"
        f"Draft: {draft}\n\n"
        f"MANDATORY: You must include the exact phrase '{timing_phrase}', "
        f"{'mention the ' + NOWCAST_ONSHORE_NAME.lower() + ', ' if lake else ''}"
        f"and state the temperature and rain details accurately."
    )

def build_prompt(data):
    return interpret(data)

def query_ollama(task_prompt):
    messages = [{"role": "system", "content": SYSTEM}]
    for user_ex, asst_ex in FEW_SHOT:
        messages.append({"role": "user",      "content": user_ex})
        messages.append({"role": "assistant", "content": asst_ex})
    messages.append({"role": "user", "content": task_prompt})

    payload = {
        "model":   MODEL,
        "messages": messages,
        "stream":  False,
        "format": {
            "type": "object",
            "properties": {"forecast": {"type": "string"}},
            "required": ["forecast"],
        },
        "options": {"temperature": 0.05, "num_predict": 80},
    }

    req = urllib.request.Request(
        OLLAMA_URL,
        data=json.dumps(payload).encode(),
        headers={"Content-Type": "application/json"},
    )
    with urllib.request.urlopen(req, timeout=120) as r:
        resp = json.loads(r.read())

    raw = resp["message"]["content"].strip()
    raw = re.sub(r"<think>.*?</think>", "", raw, flags=re.DOTALL).strip()
    try:
        return json.loads(raw).get("forecast", "").strip()
    except Exception:
        m = re.search(r'"forecast"\s*:\s*"(.*?)"', raw)
        return m.group(1).strip() if m else raw

def write_output(data, sentence):
    """Write combined JSON atomically to the web directory."""
    cur = data["current"]
    fc  = data["forecast"]
    rain_pct = max((fc[h]["rain_pct"] for h in fc), default=0)

    out = {
        "generated_utc": data["as_of"],
        "generated_ts":  int(time.time()),
        "forecast":      sentence,
        "current_temp_f": round(cur["temp_f"], 1) if cur["temp_f"] is not None else None,
        "pressure":       round(cur["pressure"], 2) if cur["pressure"] is not None else None,
        "pressure_trend": cur["pressure_trend"],
        "wind_label":     cur["wind_label"],
        "wind_dir_deg":   round(cur["wind_dir"], 1) if cur["wind_dir"] is not None else None,
        "rh_pct":         round(cur["rh_pct"], 1) if cur["rh_pct"] is not None else None,
        "rain_pct_6h":    rain_pct,
        "solar_context":  data.get("solar_context", {}),
        "n_analogues":    data["n_analogues"],
        "forecast_intervals": fc,
        "analogues":      data.get("analogues", []),
    }

    OUTPUT_JSON.parent.mkdir(parents=True, exist_ok=True)
    tmp = OUTPUT_JSON.with_suffix(".tmp")
    tmp.write_text(json.dumps(out, indent=2))
    os.replace(tmp, OUTPUT_JSON)
    print(f"Written to {OUTPUT_JSON}")

def main():
    import argparse
    ap = argparse.ArgumentParser()
    ap.add_argument("--write", action="store_true",
                    help=f"Write combined JSON to {OUTPUT_JSON}")
    args = ap.parse_args()

    raw = sys.stdin.read().strip()
    if not raw:
        print("No input. Pipe from: python3 analogue_forecast.py --json", file=sys.stderr)
        sys.exit(1)

    try:
        data = json.loads(raw)
    except json.JSONDecodeError as e:
        print(f"Invalid JSON input: {e}", file=sys.stderr)
        sys.exit(1)

    prompt   = build_prompt(data)
    sentence = query_ollama(prompt)

    if args.write:
        write_output(data, sentence)
    else:
        print(sentence)

if __name__ == "__main__":
    main()
