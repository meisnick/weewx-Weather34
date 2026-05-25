# Goal: weewx-Weather34 Full-Stack Modular Rewrite

**Repo:** https://github.com/meisnick/weewx-Weather34  
**Branch:** `modularize` (dev Pi: `pi2`, host alias `pi2`)  
**Last updated:** 2026-05-23  
**Status:** CSS variables done. All active modules scoped. Main CSS strip blocked (needs CSS parser). Flex refactor not started.

---

## Objective

Eliminate the ~300 KB monolithic CSS pair, interlinked global styles, and position:absolute pixel-math in module internals where possible for clean, scoped layout. End state: each dashboard module is self-contained — scoped CSS, no global leakage, fixed-size cells (intentionally locked layout size), both themes from one stylesheet.

---

## Root Causes (from 2026-05-20 audit)

| Problem | Status |
|---|---|
| Float/fixed-width layout, 3 hardcoded column layers | ✅ Fixed — CSS Grid, `auto-fill` columns |
| Hardcoded `position1`–`position4` in index.php | ✅ Fixed — loop-driven |
| 13 hardcoded `$.load()` calls in updater.php | ✅ Fixed — single loop |
| Module order not configurable without editing PHP | ✅ Fixed — `modules.php` array config |
| `main.dark.css` + `main.light.css` ~300 KB near-duplicate | ⚠️ Partial — CSS variables added, still two files, strip blocked |
| 1,298 global CSS rules, no module scoping | ✅ Scoped — all active modules have `.mod-*` wrapper + `css/modules/*.css` |
| `position:absolute` + hardcoded px inside every module | ⬜ Not started (Phase 2, one module at a time) |
| Media queries scattered in 11 locations | ⬜ Not started |
| Invalid custom HTML elements (`<trendmovementfallingx>`, etc.) | ⬜ Not started |
| Temperature color: 13-branch if/else duplicated per module | ✅ Partially — `lib/display.php` helpers exist; classes scoped |
| Each module re-includes data layer | ⬜ Not started |

---

## Architecture

### CSS Current State (2026-05-23)

```
css/
  main.dark.css         ← :root {} with 15 color tokens; still has ALL rules (strip blocked)
  main.light.css        ← :root {} with light overrides; still has ALL rules
  modules/
    temperature.css     ← .mod-temperature scoped, dark + [data-theme="light"] overrides
    rainfall.css        ← .mod-rainfall scoped; url() paths use ../
    airquality.css      ← .mod-airquality scoped
    lightning34.css     ← .mod-lightning34 scoped
    barometer.css       ← .mod-barometer scoped
    wind.css            ← .mod-wind scoped
    sun.css             ← .mod-sun scoped
    moonphase.css       ← .mod-moonphase scoped
    conditions.css      ← .mod-conditions scoped
    forecast.css        ← .mod-forecast scoped
    rain-totals.css     ← .mod-rain-totals scoped
    top-lightning.css   ← .mod-top-lightning scoped
    advisory.css        ← .mod-advisory scoped
    clock.css           ← loaded globally, not scope-gated (clock unwrappable)
```

**CSS variable tokens defined in both `:root` blocks:**
`--green #90b12a`, `--green2 #9aba2f`, `--amber #e6a141`, `--amber-rgb 230,161,65`,  
`--orange #ff7c39`, `--orange-d #d05f2d`, `--orange-h #f5650a`,  
`--teal #3b9cac`, `--teal-b #00a4b4`, `--teal-bb #00adbc`,  
`--red #d35d4e`, `--red-l #d86858`, `--bg0`, `--bg1`, `--text-d`, `--text-m`  
(light overrides: `--bg0 #f6f8fc`, `--bg1 #f8f8f8`, `--text #2d2d2d`, `--text-d #aaa`)

**Theme switching:** `<html data-theme="dark|light">` — set in `index.php` from `$theme`. Module CSS uses `[data-theme="light"] .mod-*` native nesting.

**Module CSS rule:** all `url()` paths in `css/modules/*.css` need `../` prefix relative to main CSS location.

**Inline `<style>` in PHP modules with dynamic values** (sun3.php, windspeeddirection.php) must have their selectors scoped to the wrapper class or external module CSS will override the PHP-computed values.

### PHP Current State

```
index.php              ← loop-driven, data-theme on <html>, 13 module CSS links
modules.php            ← user config (gitignored), auto-created from modules.example.php
modules.example.php    ← tracked defaults; clock refresh = 3600s
updater.php            ← loop-driven $.load() calls
lib/display.php        ← tempColorClass(), tempPillClass() etc.
css/modules/*.css      ← per-module scoped CSS
```

**weather34clock.php** — cannot be wrapped. Has an orphan `</div>` that closes the outer `.value` container from `index.php`. Any wrapper intercepts it and breaks layout.

---

## Phases

### ✅ Phase 1 — CSS Grid Layout
### ✅ Phase 3 — Dynamic Module System  
### ✅ Phase 3b — Drag-and-Drop Module Config
### ✅ Phase A — CSS Custom Properties (both theme files)
### ✅ Phase 2+B — Module Scoping + Per-Module CSS (13/14 modules)
### ✅ Light mode consolidation — main.light.css removed; light theme via `:root[data-theme="light"]` + `[data-theme="light"]` overrides in main.dark.css

---

### ⬜ CSS Parser Strip (blocked — deferred until Phase 2 makes module CSS self-contained)

Module CSS files are partial overrides relying on main CSS for baseline layout — stripping main CSS removes the baseline. Safe to attempt again only after Phase 2 flex refactor makes module CSS fully self-contained. Attempted with tinycss2 on pi2; script is working correctly but the precondition isn't met yet.

---

### ⬜ Phase 2 — Module Internal Flex Refactor (deferred / optional)

Clean up `position:absolute; margin-left:Npx` inside each module with modern layout structures (flexbox/grid) for easier code maintenance.
**Note:** Because cells are **intentionally locked at a fixed display size**, dynamic responsiveness/resizing is NOT required. Consequently, this phase is a low-priority cosmetic code cleanup rather than a functional requirement.
**Prerequisite:** module CSS isolated (done).  
**Start with:** temperature (most complex, most impactful).  
**Done when:** module markup is modernized/cleaned where beneficial. No resizing is intended.

---

### 🔒 Branch Isolation (Never Merge)

The `modularize` and `main` branches are **intentionally and permanently separate**. 
*   **No Merging EVER**: These branches must **NEVER** be merged.
*   **Fixes are Independent**: Fixes in `main` have absolutely nothing to do with those in `modularize`. They are entirely separate lifecycles.
*   **Complete Overwrite Model**: If any Pi is updated, it will be a complete overwrite of the existing skin using the `modularize` branch. We do not maintain or carry over legacy fixes from `main` that do not apply to the new modular architecture.

---

## Dev / Deploy Pattern

```bash
# Deploy single file to pi2
scp {file} pi2:/tmp/ && ssh pi2 "sudo cp /tmp/{file} /var/www/html/weewx/weather34/"

# Git commit on pi2 (files owned by www-data)
ssh pi2 "cd /var/www/html/weewx/weather34 && sudo -u www-data git add {files}"
ssh pi2 "cd /var/www/html/weewx/weather34 && sudo -u www-data git commit -m 'message'"

# Push to GitHub from pi2
ssh pi2 "cd /var/www/html/weewx/weather34 && GIT_SSH_COMMAND='ssh -i /var/www/.ssh/github_id_rsa' sudo -E -u www-data git push origin modularize"

# Push to GitHub from pi1
ssh pi "cd /var/www/html/weewx/weather34 && GIT_SSH_COMMAND='ssh -i /home/pi/.ssh/github_id_rsa' sudo -E -u pi git push origin main"

# Pi2 data sync (auto via cron, or manual)
ssh pi2 "sudo /usr/local/bin/w34sync.sh"
```

---

## Progress Log

| Date | Work |
|---|---|
| pre-2026-05-20 | Phase 1 CSS Grid, Phase 3 dynamic modules, Phase 3b drag-drop |
| 2026-05-20 | Phase A CSS vars, Phase 2+B module scoping (partial), Phase 4 lib/display.php |
| 2026-05-21 | PHP→HTML decoupling attempt (abandoned — static HTML + api/live.php approach was premature) |
| 2026-05-22–23 | CSS variables on both theme files; all 13 wrappable modules scoped with per-module CSS; DiviumWX architecture comparison; main CSS strip attempted and blocked; pi2 data sync; weewx port cast fix; clock flash fix; wind arrow fix; alertadvisory CSS port; GitHub SSH setup for www-data |
