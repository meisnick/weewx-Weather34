import ast
import re

def parse_services_text(raw_text: str):
    cleaned = re.sub(r'.*"##.*\n','', raw_text)
    try:
        return ast.literal_eval(cleaned)
    except Exception:
        return None


def test_parse_basic_services():
    raw = '{"weather Underground": True, "METAR": False}'
    parsed = parse_services_text(raw)
    assert isinstance(parsed, dict)
    assert parsed["weather Underground"] is True
    assert parsed["METAR"] is False
