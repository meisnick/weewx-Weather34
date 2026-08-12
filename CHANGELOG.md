# CHANGELOG

All notable changes to this maintained fork will be documented in this file.

## [2026-08-12] — Full Modularization, Light/Dark Theme Engine, Unit & i18n Fixes (modularize branch)

Complete overhaul of the Weather34 layout, stylesheet system, internationalization framework, unit conversion logic, and notification popup pipeline.

### 1. Modular CSS Architecture & Light Mode System
- **Theme-Aware Main Stylesheets**: Created dedicated `css/main.light.css` alongside `css/main.dark.css`. Updated `index.php` `<link>` tag to load `css/main.<?php echo $theme1; ?>.css` dynamically.
- **Scoped Module CSS Namespaces**: Re-architected all component stylesheets under `css/modules/` (`temperature.css`, `sun.css`, `wind.css`, `aurora.css`, `forecastdiscussion.css`, `localforecast.css`, `lightning34.css`, `airqualitymodule.css`, `barometer.css`, `conditions.css`, `moonphase.css`, `rainfall.css`, `top-lightning.css`) with normalized `.mod-<module>` container namespaces to isolate layout rules.
- **Light Theme Contrast & Alignment**: Corrected invalid `color-adjust` CSS syntax, fixed wind direction text contrast in light theme, restored dark bottom bar styling on temperature tiles, corrected sun dial ring duplication, and aligned lightning/UV badge borders.

### 2. Complete i18n / Multi-Language Coverage
- **Phase B Internationalization**: Wired all remaining hardcoded module titles and internal labels to `$lang[...]` lookups across language files (`lang.en.php`, `lang.fr.php`, `lang.blank.php`).
- **Localized Title Management**: Shifted default module title ownership to `moduleTitle()` in `index.php` using localized `$lang[...]` string lookups.
- **Beaufort Scale Translation**: Replaced hardcoded English Beaufort scale strings in `windspeeddirection.php` with localized `$lang['Calm']`, `$lang['Lightbreeze']`, etc.

### 3. Unit Conversion & Math Normalization
- **`windspeeddirection.php`**: Removed duplicate `windrun` multiplier (previously re-multiplying values already converted to miles/km by `w34CombinedData.php`).
- **`barometer.php`**: Fixed malformed closing HTML tag `</weather34-barometerlimitminf>` and removed redundant in-place `kPa` array conversions.
- **`rainfall.php`**: Fixed rainwater beaker height formula so zero-rain states (`rain_today = 0`) output valid `0.0px` heights instead of invalid `px;` CSS.
- **`indoortemperature.php`**: Corrected threshold logic so sub-freezing indoor readings (≤ 0°C) render in blue badges instead of being skipped.
- **`max-minwind.php`**: Decoupled max gust circle rendering from temperature unit checks so gusts render under both C and F preferences.
- **`forecast3omlarge.php`**: Made high temperature tags output dynamic `$tempunit` instead of hardcoded F/C labels.
- **`currentconditionsw34.php`**: Standardized on `$weather['temp_units']`, `$weather['wind_units']`, `$weather['rain_units']`, and localized cardinal directions (`$lang['Northdir']`, `$lang['NEdir']`, etc.).

### 4. Dashboard Overlay Notifications (`notify.php`)
- **Restored Overlay Alerts**: Wired previously orphaned `notify.php` directly into `index.php` footer.
- **Alert Trigger Checks**: Re-enabled dashboard toast alerts for low console/station battery, high UV index caution, heat exhaustion risk, wind advisory/warning, wind chill, and freezing dewpoints.
- **PHP 8.4 Null-Coalescing Guards**: Added `??` guards for `$uvisvg` and `$notifications` in `notify.php`.

### 5. Template Setup UI Audit (`templateSetup.php`)
- **Security & Setup Verification**: Audited local IP security restrictions, settings persistence to `settings1.php`, ICAO airport auto-detection (`icao_lookup.php`), NWS alert zone auto-lookup (`nws_zone_lookup.php`), and drag-and-drop module layout builder (`module_save.php`).
- **Obsolete Settings Documented**: Documented dead legacy keys (WeatherFlow Tempest API, Weather Underground API notice, USA Weather Finder).

Popup charts (temperature/humidity/etc.) rendered months-old data — the weekly chart
JSON under `w34highcharts/json/` had been frozen since mid-May. Three stacked causes,
all fixed:

### WeeWX report generation
- `weewx5.conf.example`: `[[w34Highcharts]]` set `enable = true` and added the
  `skin = w34Highcharts` key. On WeeWX 5.x the report thread dies with `KeyError: 'skin'`
  if the skin key is absent, and with `enable = false` the weekly JSON is never generated —
  either way the popup charts freeze at the last-written date.

### Filesystem permissions (INSTALLATION.md §4)
- Documented the missing write-permission step for `w34highcharts/json` and
  `w34highcharts/json_day`. The report runs as the `weewx` user; these dirs are commonly
  left `www-data`-owned with no group write (e.g. after a `chown -R www-data:www-data`
  for git), so weewx hits `PermissionError` on the `.tmp` files and the CheetahGenerator
  crashes every cycle. Fixed with `chown weewx:www-data` + `chmod 2775` (setgid), matching
  the existing `jsondata`/`serverdata` treatment.

### Browser caching (INSTALLATION.md §8)
- Extended the Apache `no-cache` headers beyond `w34highcharts/` to also cover
  `serverdata/` and `jsondata/`. The dashboard fetches all data feeds with jQuery and no
  cache-buster, so any feed that stops updating earns a multi-day heuristic browser-cache
  lifetime and keeps showing stale data even after the server recovers. Requires
  `a2enmod headers`.

> Deploy note: live pi2 serves from the stock `000-default.conf` (DocumentRoot =
> `.../weewx/weather34`) rather than the `weather34.conf` vhost shown in §8; the same
> `<Directory>` header blocks were applied there. Config lives outside the repo (contains
> API keys) and is not committed.

## [2026-05-17] — WeeWX 5.x Migration (main branch)

### Branch Structure
- `main` — WeeWX 5.x + PHP 8.4 + Python 3.13 (current, Debian 13 Trixie 64-bit)
- `legacy-4.x` — WeeWX 4.10.2 + PHP 8.1 + Python 3.9 (Debian 11 Bullseye, preserved)

### WeeWX 5.x Compatibility
- `user/weather34.py`: replaced `from distutils.version import StrictVersion` with
  `from packaging.version import Version` — `distutils` removed in Python 3.12+
- `user/gw1000.py`: updated to weewx-contrib WeeWX 5 compatible driver
  (`weectl extension install https://github.com/weewx-contrib/weewx-gw1000`)
- `weewx5.conf.example`: complete WeeWX 5.3.1 configuration template including:
  - `[StdWXCalculate]` with `appTemp = software` (GW1000 does not provide appTemp)
  - `[[w34Highcharts]]` under `[StdReport]` (required — weather34.py crashes without it)
  - `[DatabaseTypes]` section (WeeWX 5 format, replaces WeeWX 4 `database_path`)
  - `[[RSYNC]]` under `[StdReport]` (required — weather34.py accesses it unconditionally)
- Database migrated from WeeWX 4 via binary SQLite copy + `weectl database update`

### PHP 8.4 Compatibility
- `common.php`: `ob_start('mb_output_handler')` → `ob_start()` — `mb_output_handler`
  deprecated PHP 8.2, causes blank pages (fatal) in PHP 8.4
- `weather34skydata.php`: `${moon}` → `{$moon}` — deprecated `${var}` interpolation
- `outlook.php`: `${stationName}` → `{$stationName}` — same deprecation (2 occurrences)
- `php8.4-mbstring` required — not installed by default on Debian Trixie

### CSS Fixes
- `css/homeindoor.dark.css`, `css/homeindoor.light.css`: `url(css/fonts/...)` inside a
  CSS file resolves to `css/css/fonts/` (double path). Fixed to `url(fonts/...)`
- Added missing CSS placeholder files (referenced as style hooks, never had content):
  `css/auxillary.dark.css`, `css/auxillary.light.css`, `css/baromalmanac.dark.css`,
  `css/baromalmanac.light.css`, `css/popup.light.css`

### Highcharts
- `w34highcharts/scripts/plots.js`: `Highcharts.setOptions({accessibility:{enabled:false}})`
  suppresses accessibility module warning on Highcharts 11+

### Installation Notes (WeeWX 5)
See `weewx5.conf.example` for the complete configuration template. Key steps:
1. Install WeeWX 5 via apt: `https://weewx.com/apt/python3`
2. Install GW1000 driver: `sudo weectl extension install --yes https://github.com/weewx-contrib/weewx-gw1000/archive/refs/heads/master.zip`
3. Install `python3-six` and `python3-packaging` before the above
4. Copy `user/` files to `/etc/weewx/bin/user/`
5. Deploy skin to web root; set `www-data` ownership
6. Copy `scripts/w34config.example.py` → `scripts/w34config.py` with your station details
7. Run `weectl database update` after migrating an existing WeeWX 4 database

---

## [2026-05-16] — Naming Cleanup, Earthquake Removal & Git Structure

### Tier 2: Deprecated service naming cleanup
- Renamed forecast data files: `awd.txt` → `forecast_daily.txt`, `awh.txt` → `forecast_hourly.txt`
- Renamed forecast PHP files: `forecast3aw.php` → `forecast3om.php`, `forecast3awlarge.php` → `forecast3omlarge.php`
- Renamed popup files: `pop_aeris_{hourly,hourly_table,daynight,daynight_table}.php` → `pop_forecast_*`
- Config variables renamed: `AWD_PATH`/`AWH_PATH` → `FORECAST_DAILY_PATH`/`FORECAST_HOURLY_PATH`
- CSS classes: all `darksky*` renamed to `forecast*` across both themes and all PHP files
- All references updated: `settings1.php`, `initial_settings1.php`, `index.php`, `pop_menu_forecast.php`, `templateSetup.php`, `scripts/`, `.gitignore`

### Tier 3: Weather Underground attribution corrected
- `wu.txt` forecast data is fetched from IBM The Weather Company API (`api.weather.com/v3`), not Weather Underground's defunct public API
- Updated attribution in `outlookwu.php`, `pop_outlookwu.php`, `uvindexwu.php`, `templateSetup.php`, `settings.php`
- `menu.php` WU personal weather station link left unchanged (uploading to WU PWS network is a separate active service)

### Tier 4: DarkSky (ds) suffix removal
- `dsuvindex.php` → `uvindex.php`
- `uvindexds.php` → `uvindex_detail.php`
- `outlookds.php` → `outlook.php`
- `solaruvds.php` → `solaruv.php`
- These files use local weewx sensor data only; `ds` suffix was dead legacy naming from DarkSky era
- `templateSetup.php` reference updated

### Git structure: www/ path migration
- Original upstream tracked all files under `www/`; deployed Pi has files at web root
- Resolved 1,329 ghost `www/` entries via `git rm --cached -r www/`
- Re-tracked all PHP template files and CSS at correct root-level paths
- Added `css/icons/`, `css/fonts/`, `img/` to `.gitignore` — 925 static SVG icons not suited for a PHP fork repo

### Earthquake module removed
- Service non-functional since 2023 (earthquakereport.com API dead, `eq.txt` stale since Mar 2023)
- Deleted: `earthquake.php`, `eq.php`, `eq_uk.php`, `pop_eqlist.php`, `pop_eqlist_uk.php`
- Cleaned all references: `index.php`, `notify.php`, `shared.php`, `settings.php`, `updater.php`, `initial_settings1.php`, `templateSetup.php`
- Renamed `earthquake()` JS loader → `position3()` (generic slot loader); `$eqRefresh` → `$position3Refresh`
- Fixed copy-paste bug: `purpleairqualitymodule` position block was incorrectly linking to earthquake list (now links to `aqipopup.php`)
- `weather34card--earthquake1/2/3` CSS classes in UV/AQI files left intact — they are color-gradient definitions, not earthquake-specific

### EU alert module guard
- `top_advisory_eu.php`, `pop_europealerts.php`: added null guard for empty `awa.txt` response
- AerisWeather EU alerts not applicable for US stations; `awa.txt` returns empty response since AerisWeather trial expired (Apr 2026)

### AQI pipeline confirmed active
- `aq.txt` fetched hourly by weewx `aq` service from WAQI API (`api.waqi.info`)
- `update_aqi.sh` transforms `aq.txt` → `aqiJson.txt` (PM2.5/PM10 extraction) — no changes needed

---

## [2026-05] — API Migration: Open-Meteo / NOAA

### Forecast (awd.txt / awh.txt)
- Replaced AerisWeather forecast API (deprecated) with [Open-Meteo](https://open-meteo.com/) — free, no key, CC BY 4.0
- `scripts/nws_forecast_update.py`: fetches Open-Meteo 8-day forecast, writes Aeris-compatible JSON
- Fixed `icon1` → `icon` field name in `forecast3aw.php`, `pop_aeris_hourly.php`, `pop_aeris_hourly_table.php`, `pop_aeris_daynight.php`, `pop_aeris_daynight_table.php`
- Today's daytime icon now derives from afternoon hourly WMO codes (not the daily worst-case which includes overnight rain)
- Night forecast periods now display "Clear" / "Mostly Clear" instead of "Sunny"
- Known limitation: daily humidity field unavailable from Open-Meteo daily endpoint (shows 0); hourly humidity is correct

### METAR Current Conditions (me.txt)
- Replaced CheckWX API with [aviationweather.gov](https://aviationweather.gov/) (NOAA/AWC) — free, no key, public domain
- `scripts/metar_update.py`: fetches METAR, writes CheckWX-compatible JSON to `me.txt`; `metar34get.php` unchanged
- Added `metar34sky.php`: lightweight sky icon/description parser with no conflicting includes (safe to use in any PHP context)
- Fixed `currentconditionsw34.php`: now uses METAR sky icon, description, and visibility rather than stale `awc.txt` and broken `cloud_cover` field (was always 0 due to missing realtime data field 204)
- Updated `pop_metarnearby.php` API Info section: NOAA logo and link replacing CheckWX

### Weather Alerts
- Replaced EU MeteoAlarm / Weather Underground advisory module (both non-functional for US users) with [NWS Alerts API](https://www.weather.gov/documentation/services-web-api) — free, no key, public domain
- `scripts/nws_alerts_update.py`: fetches active alerts for configured NWS zones, writes `nws_alerts.txt`
- `top_advisory_nws.php`: colour-coded severity display (Extreme=red, Severe=orange, Moderate=yellow, Minor=blue, clear=green)
- `settings1.php`: `position4` switched from `top_advisory_rw.php` to `top_advisory_nws.php`

### Configuration & Privacy
- `scripts/w34config.example.py`: template for all site-specific settings (lat/lon, ICAO, NWS zones)
- `scripts/w34config.py`: gitignored — actual station coordinates and identifiers never committed
- All three scripts import from `w34config` at runtime; no location data in any committed file

### Cron (www-data)
```
15 * * * *   /usr/bin/python3 /usr/local/bin/nws_forecast_update.py >> /var/log/nws_forecast.log 2>&1
*/15 * * * * /usr/bin/python3 /usr/local/bin/metar_update.py >> /var/log/metar_update.log 2>&1
*/5 * * * *  /usr/bin/python3 /usr/local/bin/nws_alerts_update.py >> /var/log/nws_alerts.log 2>&1
```

---

## [2026-05-16] — Cloud Cover, Forecast Fixes & UI Corrections

### Cloud Cover Chart (cloudcoverplot)
- `scripts/cloud_cover_update.py`: fetches Open-Meteo hourly `cloudcover`, patches weewx `signal8` field every 5 min via root cron
- `scripts/cloud_cover_backfill.py`: one-time script using Open-Meteo archive API to backfill all 248,383 zero records back to March 2022
- `scripts/fix_sat24.py`: overwrote 193,852 pre-Jan-2024 records stored in sat24.com okta scale (0–2) with correct Open-Meteo percent values (0–100)
- Rebuilt weewx daily summaries (`wee_database --drop-daily --rebuild-daily`) to reflect corrected archive data
- Dashboard link switched from `span='weekly'` (7 days) to `span='yearly'` (4-year history with 1d/1w/1m/6m/1yr/All)
- Chart type changed from `column` → `area` for yearly view; restored both Max and Avg series
- `plotOptions.area.dataGrouping.approximation` set to `'average'`: auto-grouped bars now show mean cloud cover, not sum (prevented 564% readings when multiple days were condensed into one bar)
- Apache no-cache headers added for `w34highcharts/` directory; `?v=` cache-buster added to `plots.js` script tag

### Forecast Popup Fixes (pop_aeris_* files)
- Fixed `['weather']` → `['weatherPrimary']` field name in all four forecast popouts (condition text was blank)
- Fixed rain display: amount and probability now shown separately (`0.13 in · 45% chance`)
- Fixed daily humidity showing `0%`: `nws_forecast_update.py` now aggregates hourly humidity from Open-Meteo into day/night periods
- Fixed tonight showing "Sunny" instead of "Clear" in night periods
- Fixed today's forecast icon using daily worst-case code (caused rain icon all day after morning showers cleared)
- Fixed `=` vs `==` bug in rain block causing it to always display even on dry hours

### Attribution & Repo Cleanup
- Updated all 47 references from `steepleian/weewx-Weather34` → `meisnick/weewx-Weather34`
- Forecast popout attribution updated: AerisWeather → Open-Meteo (CC BY 4.0), Yr.no → basmilius/weather-icons (MIT)
- Table-style forecast popouts now include attribution footer (previously had none)
- SSH access setup: key-based auth (`~/.ssh/pi_id`), `~/.ssh/config` Host alias `pi`, no-cache Apache config committed
- `CLAUDE.md` updated with SSH alias instructions

### Known Issues (updated)
- Open-Meteo daily humidity fix landed — hourly data correct, daily aggregated from hourly ✓
- sat24.com cloud cover data corrected via historical backfill ✓

---

## [Unreleased]

### Local Highcharts
- Replaced broken external CDN links with local Highcharts libraries
- Updated deprecated `Highcharts.Color()` constructor calls for Highcharts 11+ compatibility
- Fixed chart margins and container heights in `dark-meteogram.php` and `light-meteogram.php`
- Charts now load reliably without depending on third-party CDNs

### Lightning Panel
- Rewrote `top_lightning_wf.php` — removed hardcoded WeatherFlow API key and station ID
- Lightning data now sourced from WeeWX-generated `jsondata/wf.txt` file
- Added Ecowitt GW1000 lightning detector support
- Added GW1000-specific accumulator fields in `skin.conf`
- Extended `archivedata.php.tmpl` with Ecowitt lightning fields

### PHP 8.1+ Compatibility
- Fixed `json_decode()` calls to use associative array mode throughout
- Fixed NOAA KP index API response format changes
- Fixed `int('%')` crash in `w34highchartsSearchX.py`
- Wrapped `archivedata.php` loading in try/catch in `w34CombinedData.php`

### Security & Privacy
- Removed all hardcoded API keys, tokens, and credentials
- Zeroed out station-specific coordinates in templates
- Replaced personal email addresses and URLs with generic placeholders
- Added comprehensive `.gitignore` preventing runtime data and personal config from being committed

### Services Cleanup
- Removed AerisWeather API dependency (service deprecated)
- Removed earthquake service (API no longer functional)
- Added AQI translator script for local data

### Cleanup
- Removed junk/placeholder files from git tracking
- Removed all generated runtime data files from tracking (`jsondata/*`, `serverdata/*`, `w34highcharts/json/*`)
- Consolidated duplicate changelog files

---

## Changes inherited from upstream (meisnick/weewx-Weather34)

### Upstream v4.3.0 (Mar 2023) - Last upstream release
- DarkSky API removal (discontinued by Apple)
- AerisWeather as default forecast/alerts provider
- NWS (National Weather Service) support additions
- Internal temperature and humidity checks in `w34CombinedData.php`
- Forecast menu link fix in `index.php`
- Earthquake service fix

### Upstream v4.2.8.4
- Ecowitt lightning detector support
- LastNonZero service additions
- European weather alerts improvements

### Upstream v4.2.0 - v4.2.7
- PHP 8.x compatibility fixes
- WeeWX 4.7 remote sync compatibility
- Cloud cover data from sat24.com
- AQI module updates
- BOM (Australia) alerts support
- Various bug fixes

---

## Known Issues

| Issue | Status |
|-------|--------|
| sat24.com cloud cover data source unreachable (since Jan 2024) | Backfilled with Open-Meteo archive API (2026-05) |
| Earthquake service removed | Accepted |
| AerisWeather API deprecated | Replaced with Open-Meteo (2026-05) |
| CheckWX METAR API | Replaced with aviationweather.gov (2026-05) |
| Weather Underground advisory module non-functional for US | Replaced with NWS Alerts API (2026-05) |
| Open-Meteo daily humidity always 0 | Fixed — aggregated from hourly data (2026-05) |
