# CHANGELOG

All notable changes to this maintained fork will be documented in this file.

## [Unreleased]

### Security
- Removed hardcoded WeatherFlow API key from `top_lightning_wf.php`
- Replaced hardcoded UUID token in `settings.php` with placeholder
- Replaced personal email address in `menu.php` with GitHub repo link
- Zeroed out hardcoded coordinates in `template.php` and `initial_settings1.php`
- Removed hardcoded personal URLs from `dark-meteogram.php` and `light-meteogram.php`

### Cleanup
- Removed junk/placeholder files from git tracking:
  - `404.html`, `_config.yml`, `favicon.ico`, `info.php`, `license.txt`
  - `manifest.php`, `notify.php`, `placeholder.txt`, `sw.js`
  - `time_offset.php`, `updater.php`, `updatesection.php`
  - `webserver_ip_address.php`, `wireframe.php`, `wxcharts.php`
  - `pop_metoffice_daynight.php-not working`
- Removed all generated runtime data files from tracking:
  - `jsondata/*` (API response cache files)
  - `serverdata/*` (WeeWX-generated server data)
  - `w34highcharts/json/*` and `w34highcharts/json_day/*` (chart data)
- Added comprehensive `.gitignore` covering:
  - Runtime-generated JSON cache files
  - Server data generated from `.tmpl` templates
  - Highcharts generated JSON data
  - Personal configuration (`settings1.php`)
  - Backup files (`*.bak`)
  - Third-party Highcharts libraries
  - Python cache, OS artifacts

### Documentation
- Rewrote README to document this as a maintained fork
- Added CHANGELOG.md (this file)
- Documented all changes from upstream in PROJECT_COMPARISON.md

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
