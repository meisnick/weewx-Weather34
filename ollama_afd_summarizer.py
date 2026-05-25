#!/usr/bin/env python3
import urllib.request
import json
import re
import os
from datetime import datetime

# Configuration
NWS_TXT_URL = "https://tgftp.nws.noaa.gov/data/raw/fx/fxus63.kmkx.afd.mkx.txt"
OLLAMA_URL = "http://localhost:11434/api/chat"
MODEL_NAME = "gemma3:1b"
CACHE_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), "jsondata", "afd_summary.json")

SECTION_PROMPTS = {
    "key_messages": (
        "8 words max. Name the active weather advisory or hazard and when it ends. "
        "Example: 'Dense Fog Advisory ends mid-morning Monday.' "
        "No general statements."
    ),
    "short_term": (
        "Write one complete sentence, 8 words max. "
        "Must include a temperature number. "
        "Example: 'Highs mid-80s, fog clears by afternoon.'"
    ),
    "long_term": (
        "Write one complete sentence, 8 words max. "
        "Identify the main transition, front, or weather change. "
        "Example: 'Cold front Wednesday brings storms and cooling.'"
    ),
    "outlook": (
        "8 words max. Describe the end-of-week weather in plain terms a non-meteorologist understands. "
        "No words like: ridge, trough, omega block, pressure, front, gradient. "
        "Example: 'Dry and warm through the weekend.'"
    ),
}

def get_latest_afd():
    req = urllib.request.Request(NWS_TXT_URL, headers={'User-Agent': 'weewx-Weather34-LLM-summarizer'})
    with urllib.request.urlopen(req, timeout=30) as r:
        return r.read().decode('utf-8', errors='ignore')

def extract_sections(text):
    """Pull each AFD section into its own string."""
    def grab(pattern, src):
        m = re.search(pattern, src, re.IGNORECASE | re.DOTALL)
        if not m:
            return ""
        # Clip at next section header or &&
        chunk = m.group(1)
        stop = re.search(r'\n\.\w|\n&&', chunk)
        return chunk[:stop.start()].strip() if stop else chunk.strip()

    # Robust multi-fallback headers
    key_messages_pattern = r'\.(?:KEY MESSAGES|SYNOPSIS|NEAR TERM|DISCUSSION)\.\.\.(.*)'
    short_term_pattern = r'\.(?:SHORT TERM|NEAR TERM|DISCUSSION)\.\.\.(.*)'
    long_term_pattern = r'\.LONG TERM\.\.\.(.*)'

    return {
        "key_messages": grab(key_messages_pattern, text),
        "short_term":   grab(short_term_pattern, text),
        "long_term":    grab(long_term_pattern, text),
    }

def build_outlook_fallback(long_term_text):
    """Last-resort: keyword scrape for dry/above-normal language."""
    if not long_term_text:
        return "Pattern details unavailable"
    if re.search(r'\bdry\b', long_term_text, re.IGNORECASE):
        return "Dry pattern continues into next week"
    if re.search(r'\babove normal\b', long_term_text, re.IGNORECASE):
        return "Above normal temperatures persist"
    return "Pattern details unavailable"

def clean_and_split_paragraphs(text):
    """Split section text into clean content paragraphs, removing signatures, issuance headers, and short header lines."""
    raw_paras = [p.strip() for p in re.split(r'\n\n+', text) if p.strip()]
    cleaned_paras = []
    for p in raw_paras:
        word_count = len(p.split())
        # Ignore forecaster signatures (usually 1-2 words at the end, no punctuation)
        if word_count < 3:
            continue
        # Ignore timeframe header lines like "Tuesday night through Sunday:" (typically ends with colon)
        if word_count < 8 and p.endswith(':'):
            continue
        # Ignore issuance lines like "Issued 114 AM CDT Mon May 25 2026"
        if "issued" in p.lower() and ("am" in p.lower() or "pm" in p.lower()):
            continue
        cleaned_paras.append(p)
    return cleaned_paras

def get_long_term_text(long_term_text):
    """Days 3 to 5: earlier content paragraphs (everything except the last one)."""
    paras = clean_and_split_paragraphs(long_term_text)
    if len(paras) <= 2:
        return "\n\n".join(paras)
    return "\n\n".join(paras[:-1])

def get_outlook_text(long_term_text):
    """Days 5 to 7: the last content paragraph representing the furthest out outlook."""
    paras = clean_and_split_paragraphs(long_term_text)
    if not paras:
        return ""
    return paras[-1]



def query_section(section_text, section_key, retries=2):
    if not section_text.strip():
        if section_key == "outlook":
            return build_outlook_fallback(section_text)
        return ""

    prompt = SECTION_PROMPTS[section_key]
    data = {
        "model": MODEL_NAME,
        "messages": [
            {
                "role": "system",
                "content": "You are a precise weather assistant. Your only job is to write a single, ultra-concise weather summary of under 8 words using simple, plain language. Never use meteorology jargon. Never repeat the prompt. Never use introductory phrases. Start directly with the summary."
            },
            {
                "role": "user",
                "content": "Summarize weather in under 8 words. Use plain, simple language with no jargon.\n\nWeather text:\nSHORT TERM...Patchy dense fog may occur close to the Lake Michigan shore overnight into the middle morning hours. Otherwise, warm air advection and low-level jet axis keeping most showers west and northwest."
            },
            {
                "role": "assistant",
                "content": "{\"summary\": \"Patchy fog overnight, warm and mostly dry.\"}"
            },
            {
                "role": "user",
                "content": f"{prompt}\n\nWeather text:\n{section_text}"
            }
        ],
        "stream": False,
        "format": {
            "type": "object",
            "properties": {
                "summary": {"type": "string"}
            },
            "required": ["summary"]
        },
        "options": {
            "temperature": 0.05,
            "num_predict": 60
        }
    }

    for attempt in range(retries):
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
                    print(f"[{section_key}] Standard JSON parse failed: {je}. Trying robust regex fallback.")
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
                    return summary
        except Exception as e:
            print(f"Query for {section_key} attempt {attempt+1} failed: {e}")
            if attempt == retries - 1:
                if section_key == "outlook":
                    return build_outlook_fallback(section_text)
                return ""
    return ""

def apply_highlights(text):
    rules = [
        (r'\b(swim risk|advisory|warning|watch|hazard|high wind|danger)\b',                'red'),
        (r'\b(rain|showers?|thunderstorms?|storms?|precip|precipitation|snow|flurries)\b',  'blue'),
        (r'\b(upper 80s|near 90|90 degrees|excessive heat|heat index|hotter|hot)\b',       'orange'),
        (r'\b(warm|warming|fronts?|backdoor cold front|cold front|warm front|above normal|low 80s|mid 80s)\b', 'amber'),
        (r'\b(mostly dry|dry|quiet|cools?|cooling|cool|below normal|pleasant)\b',          'green'),
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
            key_msg_src = raw_text[:1500] # Use synopsis/lead discussion as fallback
            
        key_messages_summary = query_section(key_msg_src, "key_messages")
        short_term_summary = query_section(sections.get("short_term", ""), "short_term")
        long_term_src = sections.get("long_term", "")
        long_term_summary = query_section(get_long_term_text(long_term_src), "long_term")
        outlook_summary = query_section(get_outlook_text(long_term_src), "outlook")
        
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
                "short_term": sections.get("short_term", "").strip(),
                "long_term": get_long_term_text(long_term_src).strip(),
                "outlook": get_outlook_text(long_term_src).strip()
            }
        }
        
        os.makedirs(os.path.dirname(CACHE_PATH), exist_ok=True)
        with open(CACHE_PATH, 'w') as f:
            json.dump(output, f, indent=2)
            
        print("Successfully generated and cached highlighted summary!")
        
    except Exception as e:
        print(f"Error occurred: {e}")
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
