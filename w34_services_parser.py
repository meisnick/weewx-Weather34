import ast
import re


def parse_services_text(raw_text: str):
    cleaned = re.sub(r'.*"##.*\n','', raw_text)
    try:
        return ast.literal_eval(cleaned)
    except Exception:
        return None
