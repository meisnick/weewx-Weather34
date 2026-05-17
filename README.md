# Weather34 skin for WeeWX

> **A community-maintained fork of the original weewx-Weather34 skin.**
> The upstream project reached end-of-life in August 2023. This fork keeps the skin working and compatible with modern systems.

## Branches

| Branch | WeeWX | PHP | Python | OS | Status |
|--------|-------|-----|--------|----|--------|
| `main` | **5.3.1** | **8.4** | **3.13** | Debian 13 Trixie 64-bit | Active |
| `legacy-4.x` | 4.10.2 | 8.1 | 3.9 | Debian 11 Bullseye | Frozen |

## System Requirements (main branch)

- **WeeWX:** 5.x (tested 5.3.1)
- **PHP:** 8.2+ (tested 8.4)
- **Python:** 3.9+ (tested 3.13)
- **OS:** Debian 12 Bookworm or 13 Trixie (64-bit recommended)
- **Hardware driver:** Ecowitt GW1000 / GW2000 via [weewx-contrib/weewx-gw1000](https://github.com/weewx-contrib/weewx-gw1000)

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
# test
