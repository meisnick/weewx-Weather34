# Weather34 skin for WeeWX

> **A community-maintained fork of the original weewx-Weather34 skin.**
> The upstream project reached end-of-life in August 2023. This fork keeps the skin working and compatible with modern systems.

## Branches

| Branch | WeeWX | PHP | Python | OS | Status | Description |
|--------|-------|-----|--------|----|--------|-------------|
| `main` | **5.3.1** | **8.4** | **3.13** | Debian 13 Trixie 64-bit | Active | Upstream-compatible standard layout |
| `modularize` | **5.3.1** | **8.4** | **3.13** | Debian 13 Trixie 64-bit | Active | **Enhanced with dynamic layout engine & modular CSS** |
| `legacy-4.x` | 4.10.2 | 8.1 | 3.9 | Debian 11 Bullseye | Frozen | Traditional steepleian-style releases |

## System Requirements (main and modularize branches)

- **WeeWX:** 5.x (tested 5.3.1)
- **PHP:** 8.2+ (tested 8.4 with `php-cli`)
- **Python:** 3.9+ (tested 3.13)
- **OS:** Debian 12 Bookworm or 13 Trixie (64-bit recommended)
- **Hardware driver:** Ecowitt GW1000 / GW2000 via [weewx-contrib/weewx-gw1000](https://github.com/weewx-contrib/weewx-gw1000)
- **Ollama (optional — for modularize branch forecast discussion):** Local installation with `gemma3:1b` model pulled

## Quick Start

### 1 — Install WeeWX 5

```bash
sudo apt install apt-transport-https
wget -qO - https://weewx.com/keys.html | sudo gpg --dearmor -o /etc/apt/trusted.gpg.d/weewx.gpg
echo "deb [arch=all] https://weewx.com/apt/python3 buster main" | sudo tee /etc/apt/sources.list.d/weewx.list
sudo apt update && sudo apt install weewx python3-packaging python3-six
```

### 2 — Install GW1000 driver

```bash
sudo weectl extension install --yes \
  https://github.com/weewx-contrib/weewx-gw1000/archive/refs/heads/master.zip
```

### 3 — Deploy the skin

```bash
sudo git clone https://github.com/meisnick/weewx-Weather34.git /var/www/html/weewx/weather34
sudo chown -R www-data:www-data /var/www/html/weewx/weather34

# Create local station config (gitignored — never committed)
cp scripts/w34config.example.py scripts/w34config.py
# Edit scripts/w34config.py with your lat/lon, ICAO code, NWS alert zones
```

### 4 — Configure WeeWX

Copy `weewx5.conf.example` as a starting point for `/etc/weewx/weewx.conf`. Replace all `YOUR_*` placeholders with your station details.

Key sections that **must** be present (weather34.py will crash without them):

- `[[w34Highcharts]]` under `[StdReport]` — required even if Highcharts generation is disabled
- `[[RSYNC]]` under `[StdReport]` — referenced unconditionally at archive time
- `[StdWXCalculate]` with `appTemp = software` — GW1000 does not provide appTemp directly
- `[DatabaseTypes]` — required by WeeWX 5 for SQLite path resolution

### 5 — Copy user extensions and set up cron

```bash
sudo cp user/*.py /etc/weewx/bin/user/
sudo cp scripts/nws_forecast_update.py scripts/metar_update.py \
        scripts/nws_alerts_update.py scripts/cloud_cover_update.py \
        scripts/update_aqi.sh scripts/w34config.py /usr/local/bin/
sudo chmod +x /usr/local/bin/*.py /usr/local/bin/update_aqi.sh

sudo touch /var/log/{nws_forecast,metar_update,nws_alerts,cloud_cover}.log
sudo chown www-data:www-data /var/log/{nws_forecast,metar_update,nws_alerts,cloud_cover}.log
```

Root cron (`sudo crontab -e`):
```
0 * * * *       /usr/local/bin/update_aqi.sh
1-56/5 * * * *  /usr/bin/python3 /usr/local/bin/cloud_cover_update.py >> /var/log/cloud_cover.log 2>&1
15 * * * *      /usr/bin/python3 /usr/local/bin/nws_forecast_update.py >> /var/log/nws_forecast.log 2>&1
*/15 * * * *    /usr/bin/python3 /usr/local/bin/metar_update.py >> /var/log/metar_update.log 2>&1
*/5 * * * *     /usr/bin/python3 /usr/local/bin/nws_alerts_update.py >> /var/log/nws_alerts.log 2>&1
```

### 6 — Migrating from WeeWX 4

```bash
sudo systemctl stop weewx
sudo cp /var/lib/weewx/weewx.sdb /var/lib/weewx/weewx.sdb.bak
sudo weectl database update --yes
sudo systemctl start weewx
```

### 7 — Additional Setup for modularize Branch (Space Weather & LLM Forecast Discussion)

The `modularize` branch introduces two highly dynamic modules that require secondary setup: the **Space Weather** auroral probability generator and the **Forecast Discussion** LLM summarizer.

#### A. Install Ollama & Pull the Model (for Forecast Discussion)
If you wish to use the Area Forecast Discussion (AFD) summarizer, install Ollama and retrieve the required lightweight LLM model:

```bash
# Install Ollama locally
curl -fsSL https://ollama.com/install.sh | sh

# Pull the lightweight, high-performance Gemma 3 1B parameter model
ollama pull gemma3:1b
```

#### B. Setup Background Scripts & Cron Jobs
Unlike other scripts, these run inside the deployed skin root folder (e.g., `/var/www/html/weewx/weather34/`) to resolve relative layout configuration files (`settings1.php`) and write directly to `jsondata/` cached endpoints.

```bash
# Touch and configure permissions for the new script logs
sudo touch /var/log/{aurora_prob,afd_summarizer}.log
sudo chown www-data:www-data /var/log/{aurora_prob,afd_summarizer}.log
```

Open the root cron tab (`sudo crontab -e`) and the web-server user's cron tab (`sudo crontab -u www-data -e`) respectively, and append the following schedules:

**Root Cron (`sudo crontab -e`):**
```text
# Space Weather (Aurora) Probability Generator - NOAA OVATION Parser (runs every 5 minutes)
*/5 * * * *     php /var/www/html/weewx/weather34/update_aurora_prob.php >> /var/log/aurora_prob.log 2>&1
```

**Web Server User Cron (`sudo crontab -u www-data -e`):**
```text
# LLM Forecast Discussion Summarizer (runs hourly at 10 past the hour)
10 * * * *     /usr/bin/python3 /var/www/html/weewx/weather34/ollama_afd_summarizer.py >> /var/log/ollama_afd_summarizer.log 2>&1
```

---

## Repository Layout

All PHP modules are served directly from the web root — subdirectories would break the relative `include()` paths and cross-file links that the skin relies on. The flat layout is intentional. File naming conventions provide the grouping:

### PHP naming conventions

| Prefix / pattern | Role |
|---|---|
| `index.php` | Main dashboard — assembles all position modules |
| `w34*.php` | Core skin files (data aggregation, realtime, highcharts search) |
| `common.php`, `shared.php` | Included by almost every page — settings, shared functions |
| `settings1.php`, `initial_settings1.php` | Live station config written by templateSetup.php |
| `top_*.php` | Top-bar advisory/alert strip modules (position 4) |
| `pop_*.php` | Lightbox popup panels — opened via `data-lity` links |
| `forecast*.php` | Forecast display modules (3-period, large, hourly, etc.) |
| `outlook*.php` | Extended outlook / UV index panels |
| `*module.php` | Slot modules for positions 6, 12, and last (indoor temp, AQI, etc.) |
| `templateSetup.php` | Browser-based settings UI — writes to `settings1.php` |
| `metar34*.php` | METAR / current conditions display |
| `*_lookup.php` | AJAX endpoints called by templateSetup.php (zone/ICAO detection) |

### Directories

| Directory | Contents |
|---|---|
| `scripts/` | Python cron scripts for forecast, METAR, NWS alerts, cloud cover |
| `css/` | Stylesheets — `*.dark.css` / `*.light.css` pairs per component |
| `js/` | Vendor JS libraries (jQuery, gauge, lightbox) |
| `w34highcharts/` | Highcharts subsystem — PHP data endpoints, JS scripts, theme CSS |
| `metar/` | METAR fetch helper (`metar34get.php`) |
| `daylightmap/` | Daylight world map popup |
| `languages/` | Translation strings |
| `skins/Weather34/` | WeeWX Cheetah templates (`skin.conf`, `archivedata.php.tmpl`, etc.) |
| `user/` | WeeWX Python extensions (`weather34.py`, `gw1000.py`) |
| `jsondata/` | Runtime data files written by cron scripts — gitignored |
| `serverdata/` | Runtime PHP files generated by WeeWX at archive time — gitignored |

---

## What Changed from Upstream

### API Migration — Free Government Sources

All third-party paid or deprecated APIs replaced with free, keyless, officially maintained sources.

| Data | Old Source | New Source | Script |
|------|-----------|------------|--------|
| Forecast | AerisWeather (deprecated) | [Open-Meteo](https://open-meteo.com/) CC BY 4.0 | `nws_forecast_update.py` |
| METAR / Conditions | CheckWX API | [aviationweather.gov](https://aviationweather.gov/) NOAA/AWC | `metar_update.py` |
| Weather Alerts | EU MeteoAlarm / WU | [api.weather.gov](https://www.weather.gov/documentation/services-web-api) NWS | `nws_alerts_update.py` |
| Cloud Cover | sat24.com (dead Jan 2024) | [Open-Meteo archive API](https://archive-api.open-meteo.com/) | `cloud_cover_update.py` |

### Renamed Files (deprecated service names removed)

| Old | New |
|-----|-----|
| `forecast3aw.php` | `forecast3om.php` (Open-Meteo) |
| `pop_aeris_hourly.php` | `pop_forecast_hourly.php` |
| `pop_aeris_daynight.php` | `pop_forecast_daynight.php` |
| `jsondata/awd.txt` | `jsondata/forecast_daily.txt` |
| `jsondata/awh.txt` | `jsondata/forecast_hourly.txt` |
| `solaruvds.php` | `solaruv.php` |
| `outlookds.php` | `outlook.php` |
| CSS class `darksky*` | `forecast*` (both themes) |

### WeeWX 5 + Python 3.13 Compatibility
- `user/weather34.py`: `distutils.version.StrictVersion` → `packaging.version.Version` (distutils removed Python 3.12+)
- `user/gw1000.py`: updated to WeeWX 5 compatible driver from weewx-contrib

### PHP 8.4 Compatibility
- `common.php`: `ob_start('mb_output_handler')` → `ob_start()` — deprecated PHP 8.2, fatal PHP 8.4
- `weather34skydata.php`, `outlook.php`: `${var}` → `{$var}` string interpolation (deprecated PHP 8.2+)
- `php8.4-mbstring` required — install separately on Debian Trixie

### Settings Page (templateSetup.php)

The settings page has been substantially overhauled:

- **Dark mode** — page now follows the site theme (`$theme` from `settings1.php`) instead of always rendering white
- **Weather Data Scripts section** — documents all three cron scripts in-UI; links to `w34config.py` for configuration
- **NWS alert zone auto-detection** — one-click lookup via `api.weather.gov/points/LAT,LON`; extracts forecast zone and county codes and writes them directly to `w34config.py`. Manual override field also provided
- **ICAO auto-detection** — same one-click flow; queries the NWS observation stations list for the nearest airport and writes the 4-letter code to `w34config.py`. Manual override field also provided
- **Module picker cleanup** — removed dead options: EU/UK/AU/RW advisory modules (non-functional for US), earthquake notifications (module removed)
- **Webcam section** — stale link to archived third-party GitHub removed; image vs. live stream URL requirements documented accurately
- **PHP warning fixes** — undefined array key warnings on `$_POST` reads silenced with `??` null coalescing

### Webcam / Live Stream

- `pop_cam.php` now renders a full-width `<iframe>` when `$videoWeatherCamURL` is set, enabling live stream playback from [go2rtc](https://github.com/AlexxIT/go2rtc) or any iframe-embeddable source. Supports WebRTC (`/webrtc.html?src=...`) and HLS endpoints
- Falls back to static snapshot `<img>` when no stream URL is configured
- Removed broken `filemtime("http://www.winterman.org.uk/image1.jpg")` cache-buster; replaced with `time()`
- `webcamsmall.php` dashboard tile correctly switches to moonphase at night by design — documented in settings UI

### Other Fixes
- Highcharts accessibility warning suppressed via global `setOptions`
- Cloud cover chart `dataGrouping.approximation`: `sum` → `average` (prevented 500%+ readings)
- `css/homeindoor.*.css`: `url(css/fonts/...)` → `url(fonts/...)` (double-path bug in browser)
- Missing CSS placeholder files added: `auxillary`, `baromalmanac`, `popup.light`
- Weather Underground forecast attribution corrected to IBM The Weather Company (`api.weather.com/v3`)
- Earthquake module removed (API dead since 2023); `notifyEarthquake`/`notifyMagnitude` variables cleaned from `settings1.php`
- All hardcoded credentials, API keys, and coordinates removed from committed files
- `top_advisory_eu.php`, `pop_europealerts.php`: null guard added for empty `awa.txt` (EU alerts not applicable for US stations)

---

### Modularization & Dynamic Layout Engine (modularize branch)

The `modularize` branch introduces an extensive, modern overhaul of the layout, styling, and customization engine of the Weather34 skin, moving away from rigid templates to a highly dynamic, scalable system.

#### 1. Dynamic Layout System & Live Customization
- **CSS Grid Conversion**: The entire page and dashboard layout has been converted from a static rigid container to a modern, flexible **CSS Grid**, facilitating seamless alignment across different viewport resolutions.
- **Drag-and-Drop Dashboard Editor**: Integrated a live interactive dashboard layout customization page (`templateSetup_pi2.php` and `templateSetup.php`) powered by **SortableJS**. Users can toggle into edit mode, drag-and-drop modules directly on the dashboard grid to rearrange them, and lock/unlock the layout. Arrangements are saved instantly via the new `module_save.php` endpoint.
- **Dynamic Layout Column Controls**: Added support for locking/configuring grid layout columns (3 / 4 / 6 / auto) on the fly via layout settings.
- **Grid Module Dropdown Filters**: Scoped and separated layout dropdown options in settings to ensure top-bar slots and primary grid slots only display their respective permitted modules.

#### 2. Modular & Scoped Stylesheets
- **Theme Consolidation**: Consolidated and cleaned up styling by removing the bulky, redundant `main.light.css` and unifying light and dark theme rules into `css/main.dark.css` using modern CSS variables.
- **Scoped Sub-stylesheets**: Extracted bulky card-specific CSS from the monolithic main stylesheet into highly organized, scoped stylesheets under `css/modules/` (e.g., `rainfall.css`, `temperature.css`, `wind.css`, `airquality.css`, `aurora.css`, `lightning34.css`, `barometer.css`, `conditions.css`, `forecast.css`, `moonphase.css`, `sun.css`, `top-lightning.css`).
- **Dynamic CSS Glob Loader**: Built a dynamic PHP glob importer that automatically loads only the CSS stylesheets belonging to modules that are currently active on the dashboard, significantly optimizing page weight and browser rendering times.
- **Module Wrapping & Isolation**: Scoped individual PHP fragments inside dedicated container classes (e.g. `.mod-lightning34`, `.mod-airquality`, `.mod-rainfall`, `.mod-temperature`) to completely prevent style collisions or unexpected element alignment bleed.

#### 3. Redesigned & Enhanced Modules
- **Air Quality (AQ) Module**:
  - Re-architected into a clean **two-column layout** with an inset shadow depth and a comprehensive grid of pollutant pills (PM2.5, PM10, NO2, SO2, O3, CO) styled with specific range badges.
  - Overhauled the details popup (`aqipopup.php`) into a smooth **Tabbed Layout** to prevent overflow scrollbars.
  - Added a historical AQI trend Highcharts graph in the popup, using Flexbox scaling to eliminate clipping.
- **Lightning Module**:
  - Overhauled to v2, showcasing a brand-new high-contrast badge + historical strikes layout.
  - Displays actual live/latest lightning strike statistics from the archive database, with distances automatically formatted in miles.
  - Migrated styling from absolute `rem` units to self-contained `em` units to ensure excellent responsive scalability.
  - Solved corner-bleeding, alignment, and WebKit/Safari border-radius rendering quirks.
- **Space Weather Module (formerly Aurora)**:
  - Rebranded the legacy "aurora" module into the full **Space Weather Module** (`aurora_module.php` and `pop_aurora.php`).
  - Integrated real-time NOAA OVATION 30-minute auroral visibility forecast probability using custom background scripts.
  - Fixed Kp forecast timezone-shifting bugs and repositioned status pills for visual alignment.
- **NWS Radar Module**:
  - Built a brand new, extremely polished NWS Radar module (`radar_module.php`) with real-time station picking and dark mode inversion.
  - Designed double-buffered, zero-flash transition logic in JavaScript to completely eliminate the harsh white background flashes during radar loop updates.
  - Paired with an interactive, scroll-and-pan radar viewer popup (`pop_radar.php`).
- **Forecast Discussion (AI Integration)**:
  - Added a **Forecast Discussion** module (`forecastdiscussion.php`, `css/modules/forecastdiscussion.css`) powered by a custom **Ollama-based NWS Area Forecast Discussion (AFD) summarizer** script (`ollama_afd_summarizer.py`), allowing local LLM summaries of weather agency discussions to be read right on the dashboard.

#### 4. Developer Reference
- Added [WEATHER34_MODULE_GUIDE.md](WEATHER34_MODULE_GUIDE.md) to serve as a comprehensive, repeatable design system documentation and development reference for creators building new grid cards, popups, and badges in the Weather34 template.

---

## Data Sources & Attribution

| Source | Purpose | License |
|--------|---------|---------|
| [Open-Meteo](https://open-meteo.com/) | Forecast + cloud cover | [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/) — attribution required |
| [NOAA Aviation Weather Center](https://aviationweather.gov/) | METAR current conditions | US Government / Public Domain |
| [NOAA National Weather Service](https://www.weather.gov/documentation/services-web-api) | Weather alerts | US Government / Public Domain |
| [IBM The Weather Company](https://www.weather.com/) | Extended forecast (`wu.txt`) | Requires API key |
| [WAQI](https://waqi.info/) | Air quality index | Requires free API token |
| [NOAA Space Weather](https://www.swpc.noaa.gov/) | Kp-index / aurora | US Government / Public Domain |
| [WeeWX](https://weewx.com/) | Weather station daemon | [GPL v3](https://github.com/weewx/weewx/blob/master/LICENSE.txt) |
| [basmilius/weather-icons](https://github.com/basmilius/weather-icons) | Animated forecast SVG icons | MIT |

Open-Meteo attribution per their [terms](https://open-meteo.com/en/terms): data used under CC BY 4.0.
NOAA data is US government work and not subject to copyright within the United States.

---

## License

Copyright (c) 2016–2019 Brian Underdown (https://weather34.com)

Permission is granted to use and modify for personal use. Redistribution or resale requires prior permission from the original author. See original license at https://weather34.com/homeweatherstation.

This fork adds modifications under the same non-commercial terms.

---

## Credits & Attribution

- **Original template:** Brian Underdown — [weather34.com](https://weather34.com/homeweatherstation)
- **WeeWX skin port and primary maintainer:** Ian Millard (Steepleian) — [steepleian/weewx-Weather34](https://github.com/steepleian/weewx-Weather34)
  Ian ported Weather34 to WeeWX and maintained it through v4.3.0 (August 2023). This fork is built directly on his work. The upstream repo is the authoritative reference for WeeWX 4 installations.

- **This fork:** [meisnick/weewx-Weather34](https://github.com/meisnick/weewx-Weather34)
  Continued maintenance after upstream EOL: WeeWX 5.x support, PHP 8.4 / Python 3.13 compatibility, API migration to free government sources, and cleanup of deprecated services.

## Documentation

| Document | Purpose |
|----------|---------|
| [INSTALLATION.md](INSTALLATION.md) | Fresh installation from a bare Pi |
| [MIGRATION.md](MIGRATION.md) | Upgrading from WeeWX 4 or steepleian fork |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Common configuration issues and fixes |
| [CHANGELOG.md](CHANGELOG.md) | Full change history |

See [CHANGELOG.md](CHANGELOG.md) for complete change history.
