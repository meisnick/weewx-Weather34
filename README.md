# Weather34 skin for WeeWX

> **A community-maintained fork of the original weewx-Weather34 skin.** The upstream project (meisnick/weewx-Weather34) reached end-of-life in August 2023. This fork keeps the skin alive, working, and compatible with modern systems.

## System Requirements

- **WeeWX:** 4.x (tested with 4.10.2)
- **PHP:** 8.1+ (compatibility fixes applied)
- **OS:** Debian 11+ (Bullseye) / Raspbian Bullseye+
- **Python:** 3.x

## Features & Changes from Upstream

### Local Highcharts Libraries
- Replaced broken external CDN links with local Highcharts libraries
- Updated deprecated `Highcharts.Color()` constructor calls for Highcharts 11+ compatibility
- Fixed chart margins and container heights
- Charts now load reliably without depending on third-party CDNs

### Lightning Panel Updates
- `top_lightning_wf.php` rewritten — removed hardcoded WeatherFlow API key and station ID
- Lightning data now sourced from the WeeWX-generated `jsondata/wf.txt` file
- Ecowitt GW1000 lightning detector support added
- GW1000-specific accumulator fields in `skin.conf`
- Extended `archivedata.php.tmpl` with Ecowitt lightning fields

### PHP 8.1+ Compatibility
- Fixed `json_decode()` calls to use associative array mode throughout
- Fixed NOAA KP index API response format changes
- Fixed `int('%')` crash in `w34highchartsSearchX.py`
- `w34CombinedData.php` wrapped in try/catch for `archivedata.php` loading failures

### Security & Privacy
- All hardcoded API keys, tokens, and credentials removed
- Station-specific coordinates zeroed out in templates
- Personal email addresses and URLs replaced with placeholders
- Comprehensive `.gitignore` prevents runtime data and personal config from being committed

### Services Cleanup
- Removed AerisWeather API dependency (service deprecated)
- Removed earthquake service (API no longer functional)
- Added AQI translator script for local data

### API Migration — Free, Keyless Government Sources (2026)

Replaced all remaining third-party paid/deprecated forecast and conditions APIs with
free, keyless, officially maintained sources. No account or API key required.

**Forecast (`awd.txt` / `awh.txt`)**
- `scripts/nws_forecast_update.py` replaces the AerisWeather forecast dependency
- Source: [Open-Meteo](https://open-meteo.com/) — open-source, free, CC BY 4.0
- Outputs Aeris-compatible JSON so `forecast3aw.php` and related templates need no changes
- Fixed `icon1` → `icon` field name in `forecast3aw.php` and `pop_aeris_*` files
- Today's daytime icon now uses afternoon hourly conditions rather than the daily worst-case code
- Night periods now show "Clear" / "Mostly Clear" instead of "Sunny"

**METAR Current Conditions (`me.txt`)**
- `scripts/metar_update.py` replaces the CheckWX API dependency
- Source: [aviationweather.gov](https://aviationweather.gov/) (NOAA/AWC) — no key, public domain
- Outputs CheckWX-compatible JSON; `metar34get.php` and `pop_metarnearby.php` unchanged
- `metar34sky.php` — lightweight sky icon/description parser safe to include anywhere
- `currentconditionsw34.php` — now uses METAR sky icon and visibility instead of stale `awc.txt`

**Weather Alerts (`nws_alerts.txt`)**
- `scripts/nws_alerts_update.py` replaces the EU MeteoAlarm / Weather Underground advisory module
- Source: [api.weather.gov](https://www.weather.gov/documentation/services-web-api) (NOAA NWS) — no key, public domain
- `top_advisory_nws.php` — colour-coded severity display; green "No Active Advisories" when clear
- Configure your forecast zone and county zone in `scripts/w34config.py`

**Configuration**
- `scripts/w34config.example.py` — copy to `scripts/w34config.py` and set your lat/lon/ICAO/zones
- `scripts/w34config.py` is gitignored and never committed — location data stays on your device
- All three scripts run via cron as `www-data` (forecast hourly, METAR every 15 min, alerts every 5 min)

## Installation

Follow the instructions in [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md).

**If upgrading from the original upstream version (meisnick/weewx-Weather34)**, read [UPGRADE.md](UPGRADE.md) first.

**If upgrading WeeWX from 4.x to 5.x**, see [WEEWX5_MIGRATION.md](WEEWX5_MIGRATION.md).

## Screenshots

**Dark Theme**

![Dark Theme](https://user-images.githubusercontent.com/18438654/86633765-fb60a200-bfc8-11ea-99dc-f8dc8de56e8c.png)

**Light Theme**

![Light Theme](https://user-images.githubusercontent.com/18438654/86635273-c8b7a900-bfca-11ea-9efd-76962364c2fd.png)

## License

Copyright (c) 2016-2019 by Brian Underdown (https://weather34.com)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Template"), to deal in the Template without restriction, including without limitation the rights to, can use, can not copy without prior permission, can modify for personal use, can use and publish for personal use, can not distribute without prior permission, can not sublicense without prior permission, and can not sell copies of the Template, and subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Template.

THE TEMPLATE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE TEMPLATE OR THE USE OR OTHER DEALINGS IN THE TEMPLATE.

Attribution-NonCommercial 4.0 International based on a work at https://weather34.com/homeweatherstation

## Documentation

| Document | Purpose |
|----------|---------|
| [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md) | Fresh installation |
| [UPGRADE.md](UPGRADE.md) | Upgrading from meisnick/weewx-Weather34 |
| [WEEWX5_MIGRATION.md](WEEWX5_MIGRATION.md) | Upgrading from WeeWX 4.x to 5.x |
| [TROUBLESHOOTING.md](TROUBLESHOOTING.md) | Common issues and fixes |

## Data Sources & Attribution

| Source | Purpose | License |
|--------|---------|---------|
| [Open-Meteo](https://open-meteo.com/) | Forecast data (awd.txt / awh.txt) | [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/) — attribution required |
| [NOAA Aviation Weather Center](https://aviationweather.gov/) | METAR current conditions (me.txt) | US Government / Public Domain |
| [NOAA National Weather Service API](https://www.weather.gov/documentation/services-web-api) | Weather alerts (nws_alerts.txt) | US Government / Public Domain |
| [WeeWX](https://weewx.com/) | Weather station daemon | [GPL v3](https://github.com/weewx/weewx/blob/master/LICENSE.txt) |

Open-Meteo attribution per their [terms](https://open-meteo.com/en/terms): data used under CC BY 4.0.
NOAA data is produced by the US government and is not subject to copyright protection within the United States.

## Credits

This fork is based on the original weewx-Weather34 by Ian Millard (Steepleian), Jerry Dietrich, and many contributors. The original template was created by Brian Underdown (weather34.com).

See [CHANGELOG.md](CHANGELOG.md) for a complete history of changes in this fork.

## Original Project

- **Original template:** https://weather34.com/homeweatherstation — Brian Underdown
- **WeeWX skin fork (EOL Aug 2023):** https://github.com/meisnick/weewx-Weather34 — Ian Millard
- **Successor (in development):** https://github.com/Millardiang/weewx-divumwx
