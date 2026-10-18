#!/usr/bin/env python3
"""Rebuild css/modules/modules.bundle.css from css/modules/*.css.

index.php serves the bundle (one request) only while it is at least as new as every module stylesheet
and its "Source count" matches the number of files; otherwise it falls back to linking the 22 files one
by one. Run this after editing any css/modules/*.css and commit the bundle with the change:

    python3 scripts/build_css_bundle.py
"""
from datetime import datetime, timezone
from pathlib import Path

root = Path(__file__).resolve().parent.parent
mod_dir = root / 'css' / 'modules'
out = mod_dir / 'modules.bundle.css'
sources = sorted(p for p in mod_dir.glob('*.css') if p.name != out.name)
n = len(sources)

parts = ['/* ' + '=' * 74,
         '   Weather34 CSS Modules Bundle (Production Artifact)',
         '   Generated: ' + datetime.now(timezone.utc).strftime('%Y-%m-%d %H:%M:%S UTC'),
         f'   Source count: {n}',
         '   Manifest:']
parts += [f'     - [{i}/{n}] {p.name}' for i, p in enumerate(sources, 1)]
parts += ['   ' + '=' * 74 + ' */', '']
text = '\n'.join(parts) + '\n'
for p in sources:
    body = p.read_text(encoding='utf-8').replace('\r\n', '\n')
    text += f'/* ==== {p.name} ==== */\n{body}\n'

with open(out, 'w', encoding='utf-8', newline='\n') as f:
    f.write(text)
print(f'{out.relative_to(root)}: {n} files, {len(text)} bytes')
