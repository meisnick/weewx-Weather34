# Weather34 Module Development Guide

Reference for building new grid modules and popups in the weewx-Weather34 template.
Derived from building the Space Weather (`aurora_module.php`) module from scratch.

---

## 1. Architecture Overview

Modules are PHP **fragments** — no `<!DOCTYPE>`, `<html>`, `<head>`, or `<body>` tags.
They are loaded into `<div id="grid_N">` via jQuery `.load()` at page load and on a
refresh interval. The browser renders them inside an already-complete HTML document.

```
.weather-item (195px tall, text-align:center)
  └── <div class="chartforecast">  ← popup links, position:absolute
  └── <span class="moduletitle">   ← card header label
  └── <br>
  └── <div id="grid_N">            ← module content injected here (~157px available)
        ├── updatedtime div        ← always first, position:absolute
        └── module content wrapper
```

**Key constraint:** `.weather-item` has `text-align: center`. All module content
must actively override this or it will be center-aligned.

---

## 2. Files to Create / Modify

| File | Action | Purpose |
|------|--------|---------|
| `module_name.php` | Create | The grid card PHP fragment |
| `css/modules/module_name.css` | Create | Scoped CSS (auto-loaded by glob) |
| `index.php` | Modify | Register title and popup links |
| `templateSetup.php` | Modify | Add to `$grid_available` dropdown |
| `modules.php` | Modify | Add to live `$grid_modules` array (gitignored) |
| `pop_module_name.php` | Create (optional) | Lightbox popup page |
| `/usr/local/bin/update_X.sh` | Create (if new data) | Cron fetch script |

---

## 3. Grid Card Dimensions

```
┌─────────────────────────────────────────────┐  ← 320px wide (318px content)
│ ░░░░░ inset box-shadow (moduletitle area) ░░░│  top ~20px
│  moduletitle text                    ●time  │  ← updatedtime: position:absolute
├─────────────────────────────────────────────┤
│                                             │
│         module content (~135px)             │  ← ~157px total in #grid_N
│                                             │
├─────────────────────────────────────────────┤
│▓▓▓▓▓▓▓▓▓▓▓ border-bottom 18px ▓▓▓▓▓▓▓▓▓▓▓▓│
└─────────────────────────────────────────────┘  total: 195px
```

- **Total card height:** 195px (includes 18px `border-bottom` stripe)
- **`#grid_N` available height:** ~157px
- **Card width:** 320px — `padding: 0 5px` gives ~310px usable

---

## 4. PHP Module Template

```php
<?php
include('shared.php');        // $online, $offline SVGs; unit conversion fns
include_once('settings1.php'); // $timeFormat, $TZ, $lat, $lon etc.
// include_once('w34CombinedData.php');  // add if you need $weather[] sensor data

// --- Load your data safely ---
$value     = '--';   // safe display default — never outputs 0 or blank on failure
$data_ok   = false;
$file_path = 'jsondata/yourdata.json';

if (file_exists($file_path) && (time() - filemtime($file_path) < 3600)) {
    $raw  = file_get_contents($file_path);
    $data = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE) {   // guards against partial curl downloads
        $value   = $data['key'] ?? '--';
        $data_ok = true;
    }
}

// $data_ok drives both the updatedtime indicator and any conditional UI below
?>
<div class="updatedtime"><span>
    <?php if ($data_ok):
        echo $online . ' ' . date($timeFormat, filemtime('jsondata/yourdata.json'));
    else:
        echo $offline . ' <offline>Offline</offline>';
    endif; ?>
</span></div>

<div class="mod-yourmodule">
    <!-- your content here -->
</div>
```

**Rules:**
- `updatedtime` must be the **first element** output.
- No `margin-top` on the main wrapper — use `padding-top` instead (see §6).
- Keep all CSS class names prefixed with `mod-yourmodule-` to avoid collisions.

---

## 5. CSS Module Template

```css
/* yourmodule — scoped to .mod-yourmodule */

/* ── Wrapper ──────────────────────────────────────────────────────────────── */
/*
 * display:flex overrides weather-item's global text-align:center.
 * padding-top (NOT margin-top) prevents margin collapse through #grid_N,
 * which would shift the absolutely-positioned updatedtime downward.
 */
.mod-yourmodule {
  display: flex;
  flex-direction: column;
  text-align: left;
  padding-top: 8px;         /* spacing from top — use padding, never margin */
  padding-left: 4px;
  padding-right: 5px;
  width: 100%;
  box-sizing: border-box;
}

/* ── Value badge (pill style) ─────────────────────────────────────────────── */
.mod-yourmodule .ym-badge {
  font-family: weathertext2, Arial, sans-serif;
  font-size: 1.5rem;
  width: 8rem;
  height: 3rem;
  padding-top: 7px;
  border-radius: 3px;
  border-bottom: 15px solid rgba(0, 0, 0, 0.3); /* characteristic pill shadow */
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  float: left;              /* float:left counters text-align:center */
  position: relative;
  margin: 35px 10px 10px 40px;
  top: 10px;
}

/* Badge color states */
.ym-quiet  { background: var(--green); }
.ym-minor  { background: var(--amber); }
.ym-active { background: var(--orange); }
.ym-storm  { background: var(--red); }

/* ── Section labels ───────────────────────────────────────────────────────── */
/* .65em + color:silver matches solarluxtodayword / moonset1 convention */
.mod-yourmodule .ym-label {
  font-size: .65em;
  color: silver;
  font-family: Arial, sans-serif;
}

/* ── Section header ───────────────────────────────────────────────────────── */
.mod-yourmodule .ym-heading {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .8em;
  color: silver;
}

/* ── Light theme overrides ────────────────────────────────────────────────── */
[data-theme="light"] .mod-yourmodule .ym-badge {
  border-bottom-color: #e6e8ef;
}
```

---

## 6. Critical Layout Gotchas

### 6.1 `padding-top` vs `margin-top` on the wrapper
**Always use `padding-top`, never `margin-top`**, on the first in-flow element inside
`#grid_N`. A `margin-top` with no border or padding above it will **margin-collapse
through `#grid_N`**, shifting the entire `#grid_N` content area down and making the
`position:absolute` updatedtime appear lower than in other modules.

```css
/* ✅ Correct */
.mod-yourmodule { padding-top: 8px; }

/* ❌ Wrong — causes updatedtime to appear ~8px too low */
.mod-yourmodule { margin-top: 8px; }
```

### 6.2 `text-align: center` override
`.weather-item` has `text-align: center`. Two valid patterns to override:

**Pattern A — flex wrapper** (complex multi-column layouts like the Space Weather card):
```css
.mod-yourmodule { display: flex; text-align: left; }
```

**Pattern B — `float: left` on the badge** (simple badge + label modules like UV/Solar):
```css
.mod-yourmodule .badge-container { float: left; position: relative; ... }
```

### 6.3 The `updatedtime` element
The global CSS positions it automatically — do not override it:
```css
/* From main.dark.css — applies to ALL modules */
.updatedtime {
  position: absolute;
  margin-left: 235px;
  margin-top: -15px;       /* pulls it up into the moduletitle zone */
  font: .65em weathertext2;
  color: silver;
}
```
Just put `<div class="updatedtime"><span>...</span></div>` as the first element
and the global CSS handles placement. Only override if you have a specific reason.

### 6.4 `display: contents` vs `display: flex` wrappers
Most original Weather34 modules use `display: contents` on their wrapper (transparent
to layout, children flow directly into `#grid_N`). The flex wrapper approach (used in
the Space Weather module) is equally valid for complex layouts — pick based on need.

### 6.5 JavaScript in grid modules

Because grid modules are loaded via jQuery `.load()` (AJAX fragment injection), the
fragment arrives **after** `DOMContentLoaded` and `$(document).ready()` have already
fired. `$(document).ready()` inside a module will never execute.

**Rule:** Place any module-specific `<script>` at the **absolute bottom** of
`module_name.php`. It executes immediately on injection — no wrapper needed.

```php
<!-- bottom of module_name.php, after all HTML -->
<script>
(function () {
    // IIFE keeps all vars local — avoids polluting window scope
    // (multiple module loads on refresh would re-declare otherwise)
    var val = <?php echo json_encode($value); ?>;
    document.getElementById('ym-readout').textContent = val;
}());
</script>
```

**Rules:**
- Wrap in an IIFE `(function(){...}())` — the module refreshes on interval, so global
  var declarations would collide with themselves on the second load.
- Prefix any IDs used by JavaScript with your module prefix (`ym-`, `aurora-`, etc.)
  for the same collision reason.
- Heavy libraries (Highcharts, Chart.js) belong in the **popup**, not the grid card.
  Keep grid module scripts to a few lines max.

---

## 7. Color Palette & Typography

### CSS Variables (dark theme defaults)
```
--green:     #90b12a   ← Kp quiet / UV low / AQI good
--green2:    #9aba2f   ← alternate green (badge backgrounds in legacy code)
--amber:     #e6a141   ← Kp minor / UV moderate / AQI moderate
--amber-rgb: 230,161,65  ← use with rgba() for tinted backgrounds
--orange:    #ff7c39   ← Kp active / UV high
--orange-d:  #d05f2d   ← darker orange variant
--red:       #d35d4e   ← Kp storm / UV extreme / AQI unhealthy
--bg0:       #393d40   ← panel/card backgrounds, "none state" for badges
--bg1:       #38383c   ← slightly darker background
--text-d:    #bbb      ← primary muted text
--text-m:    #777      ← secondary muted text / timestamps
```
`color: silver` (`#c0c0c0`) is the standard label color used throughout the
original Weather34 modules — prefer it over `var(--text-d)` for sub-labels
to match existing visual weight.

### Typography conventions
```
Font sizes:
  1.5rem  — large badge value (UV index, Kp number in big pill)
  .9em    — valuetext: main section value labels (font-family: weathertext2)
  .8em    — section heading / internal header
  .75em   — Kp max pill, medium badge text
  .65em   — sub-labels, unit labels, timestamps  ← color: silver
  .55rem  — very small labels (e.g. moonphase sub-text)

Font families:
  weathertext2   — all badge values, headings, module titles
  Arial          — data text, table cells, descriptive labels
```

### Badge / pill style (consistent across all modules)

There are two primary badge styles used in Weather34 modules depending on the design layout:

#### Style A: Pressed Badge (Standard legacy style)
Used for standard dashboard cards and small-to-medium badges.
```css
{
  border-radius: 3px;
  border-bottom: Npx solid rgba(0, 0, 0, 0.3);  /* the signature bottom shadow */
  color: #fff;
  font-family: weathertext2, Arial, sans-serif;
}
```
The `border-bottom: rgba(0,0,0,0.3)` gives every badge its characteristic "pressed" look. Use `15px` for large badges, `4–6px` for small pills.

#### Style B: High-Contrast Solid-Bottom Badge (UV & Strikes style)
Redesigned for cleaner visual weight, perfectly mirroring the modern **UV Current** and **Strikes** badge layout:
* **Height:** `4.5rem` (~58px–72px depending on context).
* **Corner Radius:** `2px` (must include `-webkit-`, `-moz-`, and `-o-` prefixes).
* **Top Label (`.lbl`):** `font-size: 0.55rem; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; font-family: Arial, Helvetica, sans-serif;`
* **Badge Value (`.val`):** `font-size: 1.45rem; line-height: 1.1; font-family: weathertext2, Arial, sans-serif; color: #fff;`
* **Bottom Bar (`.mod-lt-badge-bot` / Solid Border):** 
  - Height of `15px` (`line-height: 15px;` without borders or extra padding).
  - Background is set to solid `var(--bg1)` (dark charcoal/gray) to act as a solid bottom bar.
  - Text is `font-size: 0.55rem; text-transform: uppercase; letter-spacing: 0.5px; font-family: Arial, Helvetica, sans-serif;`.
  - Color is set to `silver` to match other `Last 3 Hrs` text nodes across the dashboard.
* **Anti-Aliasing & Color Bleed Prevention (Critical for WebKit/Blink):**
  - **Dynamic Background Rule:** To prevent background color leakage behind the grey bottom bar, never apply the dynamic background color (e.g., orange/amber) to the outer `.mod-lt-badge` wrapper.
  - **The Fix:** Leave the outer wrapper's background transparent. Apply the inline `style="background-color: ..."` rule exclusively to the **top** container (`.mod-lt-badge-top`).
  - **Sub-Element Radiuses:** Define top corner radiuses (`2px` top-left/top-right) on `.mod-lt-badge-top` and bottom corner radiuses (`2px` bottom-left/bottom-right) on `.mod-lt-badge-bot` to align perfectly with the parent container clipping mask.

#### Style C: Minimalist Transparent Data Pill (Rain & Lightning style)
Used for inline grid sub-data fields (e.g., Year/Month/Last Strike counts, sensor distances, and energy top-pills):
* **Outer Container (`.rainmodulehome` / `.lt-pill` / `.mod-lt-distance-pill` / `.mod-lt-top-pill`):**
  - **Height:** `1.1rem`
  - **Width:** `4rem` (or `width: auto; padding: 0 6px;` to safely handle longer values like 5-digit measurements).
  - **Border:** `1px solid var(--bg0)` (card-theme border; resolves to `#393d40` in dark theme).
  - **Corner Radius:** `2px` (include `-webkit-border-radius: 2px;` for layout engine support).
  - **Background:** `transparent` (for clean card integration).
  - **Display:** `display: flex; align-items: center; justify-content: center; overflow: hidden; box-sizing: border-box; line-height: 16px;`.
  - *Light theme override:* `border-color: #e6e8ef; background-color: transparent;`
* **Value Element (`.val` / `raiblue`):**
  - **Font Size:** `0.65rem`
  - **Font Family:** `weathertext2, Arial, sans-serif`
  - **Color:** Thematic color (e.g., `var(--teal)` for rain, `var(--orange)` for lightning).
  - *Note:* Date strings (`.date-text`) use `Arial, sans-serif` and a reduced size of `0.55rem` to avoid compression.
* **Unit Element (`.unit` / `smallrainunit2`):**
  - **Font Size:** `0.5rem`
  - **Font Family:** `Arial, Helvetica, system`
  - **Color:** `silver` (`#c0c0c0`)
  - **Margin:** `margin-left: 2px;`

#### Grid Card Layout & Spacing Rules
For dual-column grid modules combining a left-side badge with a right-side 2x2 pill grid (e.g. Strikes/Lightning modules), follow these spacing rules to ensure pixel-perfect alignment and balance:

1. **The Main Row Wrapper (`.mod-lt-main`):**
   - **Display:** `display: flex; flex-direction: row; align-items: center; justify-content: space-between; gap: 15px;`
   - **Vertical Centering Offset:** Set `margin-top: 44px;` to shift the core contents downwards and center them vertically inside the card container.

2. **The Left Column / Badge Container (`.mod-lt-left-col`):**
   - **Display:** `display: flex; flex-direction: column; gap: 6px; align-items: center; width: 85px; flex-shrink: 0;`
   - **Offset Nudge:** Use `position: relative; left: 10px;` to shift the badge container rightward, reducing excess horizontal whitespace.

3. **The 2x2 Data Grid (`.mod-lt-grid`):**
   - **Display & Template:** `display: grid; grid-template-columns: 4rem 4rem; gap: 5px 10px;`
     - *Rule:* Always size grid columns explicitly using the exact pill width (e.g. `4rem 4rem`) instead of fractional sizing (`1fr 1fr`). This keeps the pills tight and prevents them from stretching.
     - *Column Gap:* Must be set to exactly **`10px`** to achieve standard close-column styling.
   - **Offset Nudge:** Use `position: relative; left: -10px;` to shift the grid leftward, bringing it into perfect visual symmetry with the left badge.

4. **Top-Right Card Pill (`.mod-lt-top-pill`):**
   - **Positioning:** `position: absolute; top: 4px; right: 10px;` (shifted down exactly `2px` from legacy layout to center with the header).
   - **Style:** Always styled using **Style C** pill conventions (transparent background, card-themed border, and auto-centering).

---

## 8. Registering a New Module

### 8.1 `index.php` — add to both switch statements

```php
// In moduleTitle():
case 'your_module.php':
    return 'Your Module Title';

// In modulePopups():
case 'your_module.php':
    $out = '<span class="yearpopup"><a href="pop_your_module.php" data-lity>'
         . $info . ' Details</a></span>';
    break;
```

### 8.2 `templateSetup.php` — add to `$grid_available`

```php
$grid_available = [
    // ... existing entries ...
    'your_module.php' => 'Your Module Display Name',
];
```

### 8.3 `modules.php` — add to `$grid_modules` (gitignored live config)

```php
$grid_modules[] = [
    'module'  => 'your_module.php',
    'title'   => '',        // '' = use moduleTitle() auto-lookup
    'refresh' => 300,       // seconds between jQuery .load() refreshes
];
```

**Refresh interval guidance:**
- Real-time sensor data: 4–60s
- Cached API data (hourly fetch): 300–900s
- Astronomical / rarely changing: 3600s
- Match to your cron fetch cadence — no point refreshing faster than the data updates

---

## 9. Popup Architecture

Popups are full HTML pages opened by lity lightbox (`data-lity` attribute).

### Lity iframe dimensions
- **Width:** `max-width: 820px`
- **Height:** `padding-top: 69%` of container width ≈ **566px** at full width
- Close button: top-right corner, ~50px from the right edge

### No-scrollbar pattern (from `pop_menu_forecast.php`)
```css
html, body {
  height: 100%;
  overflow: hidden;   /* ← this is what eliminates the scrollbar */
  margin: 0;
}
body {
  display: flex;
  flex-direction: column;
}
```

### Standard popup structure
```
body (flex column, height:100%, overflow:hidden)
├── .pop-header    (flex:0 0 auto  — always visible, ~32px)
├── .pop-tabs      (flex:0 0 auto  — optional tab row, ~30px)
└── .pop-content   (flex:1; min-height:0; position:relative; overflow:hidden)
     └── .tabcontent  (position:absolute; top:0;left:0;right:0;bottom:0)
```

### Fitting images inside a flex-grown container
The **only reliable** method — `height:auto` images will overflow:
```css
/* Parent: must have a defined size from flex */
.img-frame {
  flex: 1;
  min-height: 0;
  position: relative;
  overflow: hidden;
}
/* Child: absolutely fills and scales to fit */
.img-frame img {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  object-fit: contain;
}
```

### Tab buttons (matching `pop_menu_forecast.php` style exactly)
```css
.tablink {
  background-color: #555;
  color: white;
  border: 2px solid <bg_chrome>;
  border-radius: 5px;
  margin-left: 5px;
  outline: none;
  cursor: pointer;
  padding: 5px 8px;
  font-size: 10px;
  font-family: Arial, sans-serif;
}
.tablink:hover { background-color: #777; }
/* Active tab set via JS: el.style.backgroundColor = 'rgba(194, 102, 58)' */
```

### Close button clearance
The lity close button overlaps the popup's top-right corner. Add `margin-right: 50px`
to the **last element** of any fixed header to prevent content being hidden behind it:
```css
.pop-header .last-item { margin-right: 50px; }
```

### Theme-aware color variables (PHP)
```php
$is_dark   = ($theme !== 'light');
$bg        = $is_dark ? '#151819' : '#fff';
$bg_chrome = $is_dark ? '#1e2124' : '#f0f2f5';
$bg_card   = $is_dark ? '#252729' : '#e8eaef';
$text      = $is_dark ? '#ddd'    : '#222';
$text_dim  = $is_dark ? '#777'    : '#666';
$border    = $is_dark ? '#2e3033' : '#ccc';
$box_none  = $is_dark ? '#393d40' : '#d0d4db';
$grid_ln   = $is_dark ? '#2a2c2f' : '#e0e2e6'; // chart gridlines
```

### 9.5 Popup PHP skeleton (`pop_yourmodule.php`)

Popups are full HTML pages (unlike grid modules). Start from this skeleton — it wires
up the no-scroll body, the lity close-button clearance, and the theme variables in one
block.

```php
<?php
include_once('settings1.php');
include_once('shared.php');

$is_dark   = ($theme !== 'light');
$bg        = $is_dark ? '#151819' : '#fff';
$bg_chrome = $is_dark ? '#1e2124' : '#f0f2f5';
$bg_card   = $is_dark ? '#252729' : '#e8eaef';
$text      = $is_dark ? '#ddd'    : '#222';
$text_dim  = $is_dark ? '#777'    : '#666';
$border    = $is_dark ? '#2e3033' : '#ccc';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Module Details</title>
<style>
html, body {
  height: 100%; overflow: hidden; margin: 0;
  background: <?php echo $bg; ?>; color: <?php echo $text; ?>;
  font-family: Arial, sans-serif; font-size: 13px;
}
body { display: flex; flex-direction: column; }

/* Header — always visible at top */
.pop-header {
  flex: 0 0 auto;
  display: flex; align-items: center; justify-content: space-between;
  padding: 6px 10px;
  background: <?php echo $bg_chrome; ?>;
  border-bottom: 1px solid <?php echo $border; ?>;
}
/* Last item must clear the lity close button in the top-right corner */
.pop-header .pop-last { margin-right: 50px; }

/* Optional tab row */
.pop-tabs { flex: 0 0 auto; padding: 4px 8px; background: <?php echo $bg_chrome; ?>; }
.tablink {
  background-color: #555; color: white;
  border: 2px solid <?php echo $bg_chrome; ?>; border-radius: 5px;
  margin-left: 5px; padding: 5px 8px;
  font-size: 10px; font-family: Arial, sans-serif; cursor: pointer; outline: none;
}
.tablink:hover { background-color: #777; }

/* Content area — fills remaining height, no overflow */
.pop-content {
  flex: 1; min-height: 0;
  position: relative; overflow: hidden;
}
.tabcontent {
  display: none;
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  overflow: hidden;
}

/* Image fitting inside a flex-grown frame */
.img-frame { flex: 1; min-height: 0; position: relative; overflow: hidden; }
.img-frame img {
  position: absolute; top: 0; left: 0;
  width: 100%; height: 100%; object-fit: contain;
}
</style>
</head>
<body>

<div class="pop-header">
  <div>Your Module Title</div>
  <div class="pop-last">Updated: <?php echo date($timeFormat); ?></div>
</div>

<!-- Uncomment if using tabs:
<div class="pop-tabs">
  <button class="tablink" onclick="openTab(event,'tab1')">Tab 1</button>
  <button class="tablink" onclick="openTab(event,'tab2')">Tab 2</button>
</div>
-->

<div class="pop-content">
  <div id="tab1" class="tabcontent" style="display:block; ...">
    <!-- your content -->
  </div>
</div>

<script>
function openTab(evt, name) {
  document.querySelectorAll('.tabcontent').forEach(function(t){ t.style.display='none'; });
  document.querySelectorAll('.tablink').forEach(function(b){ b.style.backgroundColor=''; });
  document.getElementById(name).style.display = 'block';
  evt.currentTarget.style.backgroundColor = 'rgba(194,102,58)';
}
</script>
</body>
</html>
```

**Key points:**
- `include_once('settings1.php')` first — it sets `$theme`.
- `html, body { height:100%; overflow:hidden }` is mandatory — skipping it causes a scrollbar.
- The `.pop-last` rule (`margin-right: 50px`) must be on the rightmost header element or the lity close button covers it.
- For single-image popups, omit the tab row entirely and put `.img-frame` directly in `.pop-content`.

---

## 10. Adding a New Data Source

```bash
#!/bin/bash
# /usr/local/bin/update_yourdata.sh
DEST=/var/www/html/weewx/weather34/jsondata/yourdata.json
TMP=${DEST}.tmp
curl -s --connect-timeout 15 \
  "https://api.example.com/data.json" \
  -o "$TMP" \
  && mv "$TMP" "$DEST" \
  || rm -f "$TMP"
```

**Deploy:**
```bash
sudo cp update_yourdata.sh /usr/local/bin/
sudo chmod +x /usr/local/bin/update_yourdata.sh
sudo /usr/local/bin/update_yourdata.sh  # run immediately to populate cache
```

**Add to crontab** (`sudo crontab -e` or via the append pattern):
```
*/5 * * * *  /usr/local/bin/update_yourdata.sh   # every 5 min
0   * * * *  /usr/local/bin/update_yourdata.sh   # hourly
```

**PHP freshness check pattern:**
```php
$max_age = 3600; // seconds
$ok = file_exists('jsondata/yourdata.json')
   && (time() - filemtime('jsondata/yourdata.json') < $max_age);
```

---

## 11. New Module Checklist

- [ ] Create `module_name.php` (fragment, starts with `include('shared.php')`)
- [ ] `updatedtime` div is the **first element** output
- [ ] Wrapper uses `padding-top`, not `margin-top`
- [ ] Wrapper uses `display: flex; text-align: left` (or `float: left` on badge) to override global centering
- [ ] Data loading uses `json_last_error() === JSON_ERROR_NONE` guard; default value is `'--'` not `0`
- [ ] Create `css/modules/module_name.css` (auto-loaded — no extra step needed)
- [ ] All CSS class names prefixed to avoid collisions
- [ ] Font sizes use `em`/`rem` not `px` for labels (`.65em` for sub-labels, `.8em` for headings)
- [ ] Colors use CSS variables (`var(--green)` etc.) not hardcoded hex
- [ ] `[data-theme="light"]` overrides added for any hardcoded dark colors
- [ ] Register in `index.php`: `moduleTitle()` + `modulePopups()`
- [ ] Register in `templateSetup.php`: `$grid_available`
- [ ] Add to `modules.php`: `$grid_modules` with appropriate refresh interval
- [ ] If new data source: cron script created, deployed, run immediately, added to crontab
- [ ] If inline JS: wrapped in IIFE `(function(){...}())` and placed at bottom of file
- [ ] PHP syntax check: `php -l module_name.php`
- [ ] HTTP smoke test: `curl -s http://localhost/weewx/weather34/module_name.php`
- [ ] Dashboard loads clean: `curl -s -o /dev/null -w '%{http_code}' http://localhost/weewx/weather34/index.php`
- [ ] If popup: `html, body { height:100%; overflow:hidden; }` — no scrollbar
- [ ] If popup: close button clearance — `margin-right: 50px` on last header element
- [ ] Commit to `modularize` branch: `sudo -u www-data git add ... && sudo -u www-data git commit`
