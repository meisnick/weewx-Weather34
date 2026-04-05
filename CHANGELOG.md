# CHANGELOG

All notable changes to this maintained fork will be documented in this file.

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

## Changes inherited from upstream (steepleian/weewx-Weather34)

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

## Known Issues (inherited from upstream)

| Issue | Status |
|-------|--------|
| sat24.com cloud cover data source unreachable (since Jan 2024) | No fix available |
| Earthquake service removed | Accepted |
| AerisWeather API may require updated credentials | Monitor |
| Weather Underground API key validity unknown | Monitor |
