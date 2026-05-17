# CHANGELOG

All notable changes to this maintained fork will be documented in this file.

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
