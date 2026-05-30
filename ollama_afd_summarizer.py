#!/usr/bin/env python3
import urllib.request
import json
import re
import os
import time
from datetime import datetime

# Configuration
NWS_TXT_URL = "https://tgftp.nws.noaa.gov/data/raw/fx/fxus63.kmkx.afd.mkx.txt"
OLLAMA_URL  = "http://localhost:11434/api/chat"
MODEL_NAME  = "gemma3:1b"
CACHE_PATH  = os.path.join(os.path.dirname(os.path.abspath(__file__)), "jsondata", "afd_summary.json")

# ── Prompt constants (V8) ─────────────────────────────────────────────────────

SYSTEM_PROMPT = (
    "You are a precise weather assistant. "
    "Your ONLY output is valid JSON: {\"summary\": \"...\"}. "
    "Write one summary under 8 words. Use plain language a 6th grader understands. "
    "FORBIDDEN words — never use these: shortwave, trough, ridge, advection, vorticity, "
    "jet (meteorological context), gradient, isobar, geopotential, anomalous, confluence, "
    "divergence, return flow, omega, synoptic, hodograph, lapse rate, precipitable water, "
    "CAPE, CIN, MOS, NBM, GFS, NAM, ECMWF. "
    "Never start with a location name. Never repeat the prompt. Never use introductory phrases. "
    "Start directly with the weather fact."
)

GLOBAL_FEW_SHOT = [
    (
        "Summarize weather in under 8 words. No jargon.\n\n"
        "Weather text:\nSHORT TERM...Patchy dense fog may occur close to the Lake Michigan "
        "shore overnight into the middle morning hours. Otherwise, warm air advection and "
        "low-level jet axis keeping most showers west and northwest.",
        '{"summary": "Patchy fog overnight, warm and mostly dry."}'
    ),
    (
        "Summarize weather in under 8 words. No jargon.\n\n"
        "Weather text:\nA shortwave trough digging into the region will enhance warm air "
        "advection ahead of it. An upper-level ridge will build behind the system and "
        "promote above-normal temperatures through the weekend.",
        '{"summary": "Storm clears Friday, warming above normal weekend."}'
    ),
]

SHORT_TERM_FEW_SHOT = [
    (
        "Write: 'Highs [TEMP], [WEATHER].' Max 8 words.\n\n"
        "Weather text:\n"
        "SHORT TERM...Tonight through Wednesday: A cold front will push through Tuesday "
        "afternoon. Highs today in the upper 80s, then dropping to the low 70s Wednesday "
        "behind the front. Isolated storms possible Tuesday evening.",
        '{"summary": "Highs upper 80s, storms Tuesday, 70s Wednesday."}'
    ),
]

LONG_TERM_FEW_SHOT = [
    (
        "One sentence, 8 words max. No 'with', 'and'.\n\n"
        "Weather text:\n"
        "MOSTLY UNEVENTFUL EXTENDED FORECAST. HIGH PRESSURE AREA LINGERS "
        "THROUGH MID-WEEK. SIGNALS FOR A WEAK COLD FRONT "
        "SATURDAY INTO SUNDAY. SMALL CHANCE FOR SHOWERS WITH THE FRONT. "
        "HIGHS IN THE UPPER 60S TO LOW 80S.",
        '{"summary": "High pressure mid-week, weak front Saturday."}'
    ),
]

OUTLOOK_FEW_SHOT = [
    (
        "8 words max. Simple weather summary.\n\n"
        "Weather text:\n"
        "THE RIDGE OF HIGH PRESSURE WILL BUILD ACROSS THE REGION "
        "KEEPING THINGS WARM AND DRY THROUGH THE WEEKEND. AN "
        "UPPER-LEVEL TROUGH APPROACHING FROM THE WEST MAY BRING "
        "SHOWERS AND THUNDERSTORMS BY LATE NEXT WEEK.",
        '{"summary": "Warm and dry, storms possible late week."}'
    ),
]

SECTION_PROMPTS = {
    "key_messages": (
        "8 words max. State ONLY the single most significant active advisory "
        "by its exact issued name and when it ends. One advisory only. "
        "If no active advisory, name the biggest hazard and when. No filler. "
        "Example: 'Beach Hazards Statement ends Thursday afternoon.'"
    ),
    "short_term": (
        "Write: 'Highs [TEMP], [WEATHER].' Max 8 words total. "
        "Find the high temperature in the text and substitute it for [TEMP] — "
        "use the actual number or range (e.g. 'mid-70s', 'upper 60s', '82°'). "
        "For [WEATHER] use one brief weather fact. "
        "No 'and', 'with', 'also'. "
        "Example: 'Highs upper 70s, beach hazards Saturday.'"
    ),
    "long_term": (
        "One sentence, 8 words max. "
        "State the most significant weather change and when. "
        "FORBIDDEN words: ridge, shortwave, trough, advection, WAA. "
        "No 'with', 'and', 'but', 'also'. "
        "Example: 'Storm chance Thursday, upper 80s mid-week.'"
    ),
    "outlook": (
        "8 words max. Simple weather summary for end of week or next week. "
        "Use only plain words: warm/cool/hot/cold, sunny/cloudy, dry/rainy/stormy. "
        "No 'with', 'and', 'but', 'then'. "
        "FORBIDDEN: ridge, trough, omega block, pressure, front, gradient, shortwave, "
        "jet, advection, flow, anomalous, blocking, vorticity, pattern, system, "
        "upper level, low level, mid level. "
        "Example: 'Warm and dry, storms possible late week.'"
    ),
}

# Words that should never appear in output — retry once if found
OUTPUT_JARGON = {
    "shortwave", "trough", "ridge", "advection", "vorticity", "gradient",
    "isobar", "return flow", "omega block", "synoptic", "confluence",
    "divergence", "upper level", "low level", "mid level",
}

# Input scrubbing: replace jargon in the source text before sending to the model.
# The model can't echo words it never sees. Applied only to long_term and outlook.
_INPUT_SUBS = [
    (r'\bsurface\s+ridg\w*\b', 'high pressure'),
    (r'\bridg\w*\b',           'high pressure area'),
    (r'\btrough\b',            'storm system'),
    (r'\bshortwave\b',         'disturbance'),
    (r'\badvection\b',         ''),
    (r'\bvorticity\b',         ''),
    (r'\bsynoptic\b',          ''),
    (r'\bomega block\b',       'stalled weather pattern'),
    (r'\bupper.?level\b',      ''),
    (r'\bmid.?level\b',        ''),
    (r'\blow.?level\b',        ''),
    (r'\b(?:WAA|CAA)\b',       ''),
    (r'\b850\s*mb\b',          ''),
    (r'\b500\s*mb\b',          ''),
    (r'\b700\s*mb\b',          ''),
    (r'\bbackdoor\b',              ''),
    (r'\bconvective\s+pulses?\b', 'storms'),
    (r'\bpulses?\b',              'storms'),
]

def scrub_jargon(text):
    """Remove or replace meteorological jargon from input text before LLM query."""
    for pattern, replacement in _INPUT_SUBS:
        text = re.sub(pattern, replacement, text, flags=re.IGNORECASE)
    # Collapse runs of whitespace left by empty replacements
    text = re.sub(r'[ \t]{2,}', ' ', text)
    return text.strip()


def get_latest_afd(retries=3, delay=5):
    req = urllib.request.Request(NWS_TXT_URL, headers={'User-Agent': 'weewx-Weather34-LLM-summarizer'})
    for attempt in range(retries):
        try:
            with urllib.request.urlopen(req, timeout=30) as r:
                return r.read().decode('utf-8', errors='ignore')
        except Exception as e:
            print(f"Fetch attempt {attempt+1} failed: {e}")
            if attempt < retries - 1:
                time.sleep(delay * (2 ** attempt))
            else:
                raise

def extract_sections(text):
    def grab(pattern, src):
        m = re.search(pattern, src, re.IGNORECASE | re.DOTALL)
        if not m:
            return ""
        chunk = m.group(1)
        stop = re.search(r'\n\.\w|\n&&', chunk)
        return chunk[:stop.start()].strip() if stop else chunk.strip()

    return {
        "key_messages": grab(r'\.(?:KEY MESSAGES|SYNOPSIS|NEAR TERM|DISCUSSION)\.\.\.(.*)', text),
        "short_term":   grab(r'\.(?:SHORT TERM|NEAR TERM|DISCUSSION)\.\.\.(.*)', text),
        "long_term":    grab(r'\.LONG TERM\.\.\.(.*)', text),
    }

def build_outlook_fallback(long_term_text):
    if not long_term_text:
        return "Pattern details unavailable"
    if re.search(r'\bdry\b', long_term_text, re.IGNORECASE):
        return "Dry pattern continues into next week"
    if re.search(r'\babove normal\b', long_term_text, re.IGNORECASE):
        return "Above normal temperatures persist"
    return "Pattern details unavailable"

def clean_and_split_paragraphs(text):
    raw_paras = [p.strip() for p in re.split(r'\n\n+', text) if p.strip()]
    cleaned = []
    for p in raw_paras:
        wc = len(p.split())
        if wc < 3:
            continue
        if wc < 8 and p.endswith(':'):
            continue
        if "issued" in p.lower() and ("am" in p.lower() or "pm" in p.lower()):
            continue
        cleaned.append(p)
    return cleaned

def clean_short_term(text):
    return "\n\n".join(clean_and_split_paragraphs(text))

def get_long_term_text(long_term_text):
    paras = clean_and_split_paragraphs(long_term_text)
    if len(paras) <= 2:
        return "\n\n".join(paras)
    return "\n\n".join(paras[:-1])

def get_outlook_text(long_term_text):
    paras = clean_and_split_paragraphs(long_term_text)
    return paras[-1] if paras else ""


def _has_jargon(text):
    lower = text.lower()
    return any(re.search(r'\b' + re.escape(w) + r'\b', lower) for w in OUTPUT_JARGON)


def query_section(section_text, section_key, retries=2):
    if not section_text.strip():
        if section_key == "outlook":
            return build_outlook_fallback(section_text)
        return ""

    # Scrub jargon from input for sections where the model tends to echo it back
    if section_key in ("long_term", "outlook"):
        section_text = scrub_jargon(section_text)

    messages = [{"role": "system", "content": SYSTEM_PROMPT}]

    for user_ex, asst_ex in GLOBAL_FEW_SHOT:
        messages.append({"role": "user",      "content": user_ex})
        messages.append({"role": "assistant", "content": asst_ex})

    if section_key == "short_term":
        for user_ex, asst_ex in SHORT_TERM_FEW_SHOT:
            messages.append({"role": "user",      "content": user_ex})
            messages.append({"role": "assistant", "content": asst_ex})
    elif section_key == "long_term":
        for user_ex, asst_ex in LONG_TERM_FEW_SHOT:
            messages.append({"role": "user",      "content": user_ex})
            messages.append({"role": "assistant", "content": asst_ex})
    elif section_key == "outlook":
        for user_ex, asst_ex in OUTLOOK_FEW_SHOT:
            messages.append({"role": "user",      "content": user_ex})
            messages.append({"role": "assistant", "content": asst_ex})

    messages.append({
        "role": "user",
        "content": f"{SECTION_PROMPTS[section_key]}\n\nWeather text:\n{section_text}"
    })

    data = {
        "model": MODEL_NAME,
        "messages": messages,
        "stream": False,
        "format": {
            "type": "object",
            "properties": {"summary": {"type": "string"}},
            "required": ["summary"]
        },
        "options": {"temperature": 0.05, "num_predict": 60}
    }

    last_summary = ""
    for attempt in range(retries + 1):  # +1 for the jargon retry
        try:
            req = urllib.request.Request(
                OLLAMA_URL,
                data=json.dumps(data).encode('utf-8'),
                headers={'Content-Type': 'application/json'}
            )
            with urllib.request.urlopen(req, timeout=120) as r:
                response = json.loads(r.read().decode('utf-8'))
                content = response['message']['content'].strip()
                content = re.sub(r'<think>.*?</think>', '', content, flags=re.DOTALL).strip()
                print(f"[{section_key}] Raw Ollama content: {repr(content)}")

                summary = ""
                try:
                    parsed = json.loads(content)
                    summary = parsed.get("summary", "").strip()
                except Exception as je:
                    print(f"[{section_key}] JSON parse failed: {je}. Trying regex fallback.")
                    m = re.search(r'"summary"\s*:\s*"(.*)"', content, re.DOTALL)
                    if m:
                        val = m.group(1)
                        val = re.sub(r'"\s*\}\s*$', '', val)
                        summary = val.strip()
                    else:
                        m = re.search(r'"summary"\s*:\s*"(.*)', content, re.DOTALL)
                        if m:
                            val = m.group(1)
                            val = re.sub(r'"\s*\}\s*$', '', val)
                            val = val.rstrip('"').rstrip('}').strip()
                            summary = val

                if summary:
                    last_summary = summary
                    if not _has_jargon(summary):
                        return summary
                    # Jargon found — retry once with slightly higher temperature
                    print(f"[{section_key}] Jargon detected in '{summary}', retrying...")
                    data["options"]["temperature"] = 0.2

        except Exception as e:
            print(f"Query for {section_key} attempt {attempt+1} failed: {e}")
            if attempt >= retries:
                if section_key == "outlook":
                    return build_outlook_fallback(section_text)
                return ""

    # Return best effort even if jargon remained after retry
    if last_summary:
        return last_summary
    if section_key == "outlook":
        return build_outlook_fallback(section_text)
    return ""

def apply_highlights(text):
    rules = [
        # Red — hazard / alert words
        (r'\b(swim risk|advisory|warning|watch|hazard|high wind|danger|'
         r'fire weather|fire risk|beach hazards?|frost|freeze)\b',                          'red'),
        # Orange — thunderstorms, severe storms, extreme heat
        (r'\b(thunderstorms?|severe storms?|upper 80s|near 90|'
         r'excessive heat|heat index|hot|heat wave|muggy)\b',                               'orange'),
        # Blue — precipitation (non-severe rain / snow / fog)
        (r'\b(rain|showers?|storms?|precip|precipitation|snow|flurries|'
         r'drizzle|wintry mix|sleet|fog|patchy fog)\b',                                     'blue'),
        # Amber — warm / fronts / breezy / above-normal temps
        (r'\b(warm|warming|above normal|low 80s|mid 80s|'
         r'fronts?|cold front|warm front|breezy|gusty)\b',                                  'amber'),
        # Green — dry / cool / pleasant / clearing
        (r'\b(mostly dry|dry|quiet|cools?|cooling|cool|below normal|'
         r'pleasant|mild|clearing|sunny|clear)\b',                                          'green'),
    ]
    for pattern, color in rules:
        text = re.sub(pattern, rf'<span class="hl-{color}">\g<0></span>', text, flags=re.IGNORECASE)
    return text

def main():
    try:
        print("Fetching raw weather discussion...")
        raw_text = get_latest_afd()

        print("Extracting relevant discussion sections...")
        sections = extract_sections(raw_text)

        print("Querying sections individually via Ollama...")
        key_msg_src = sections.get("key_messages", "")
        if not key_msg_src.strip():
            key_msg_src = raw_text[:1500]

        key_messages_summary = query_section(key_msg_src, "key_messages")
        short_term_summary   = query_section(clean_short_term(sections.get("short_term", "")), "short_term")
        long_term_src        = sections.get("long_term", "")
        long_term_summary    = query_section(get_long_term_text(long_term_src), "long_term")
        outlook_summary      = query_section(get_outlook_text(long_term_src), "outlook")

        label_map = [
            ("Key Messages", key_messages_summary),
            ("Short Term",   short_term_summary),
            ("Long Term",    long_term_summary),
            ("Outlook",      outlook_summary),
        ]

        bullets = []
        for label, summary in label_map:
            if summary:
                highlighted = apply_highlights(summary)
                bullets.append(f"<strong>{label}</strong>: {highlighted}")

        if not bullets:
            raise Exception("No bullet points were successfully generated.")

        output = {
            "success": True,
            "issued": datetime.now().isoformat(),
            "bullets": bullets,
            "raw_sections": {
                "key_messages": key_msg_src.strip(),
                "short_term":   sections.get("short_term", "").strip(),
                "long_term":    get_long_term_text(long_term_src).strip(),
                "outlook":      get_outlook_text(long_term_src).strip()
            }
        }

        os.makedirs(os.path.dirname(CACHE_PATH), exist_ok=True)
        with open(CACHE_PATH, 'w') as f:
            json.dump(output, f, indent=2)

        print("Successfully generated and cached highlighted summary!")

    except Exception as e:
        print(f"Error occurred: {e}")

        cache_preserved = False
        if os.path.exists(CACHE_PATH):
            try:
                with open(CACHE_PATH, 'r') as f:
                    old_data = json.load(f)
                if old_data.get("success") and "issued" in old_data:
                    issued_dt = datetime.fromisoformat(old_data["issued"])
                    age_hours = (datetime.now() - issued_dt).total_seconds() / 3600.0
                    if age_hours < 12.0:
                        print(f"Preserving cached summary from {issued_dt.isoformat()} (age: {age_hours:.1f} hours).")
                        cache_preserved = True
            except Exception as cache_err:
                print(f"Failed to read cache for preservation: {cache_err}")

        if not cache_preserved:
            fallback = {
                "success": False,
                "issued": datetime.now().isoformat(),
                "bullets": [
                    '<span class="hl-red">Inference Error</span> during summarization.',
                    'Check Ollama background logs.',
                    'Verify network connectivity on Pi.'
                ]
            }
            os.makedirs(os.path.dirname(CACHE_PATH), exist_ok=True)
            with open(CACHE_PATH, 'w') as f:
                json.dump(fallback, f, indent=2)

if __name__ == "__main__":
    main()
