# Weather34 skin for WeeWX

> **A community-maintained fork of the original weewx-Weather34 skin.** The upstream project (steepleian/weewx-Weather34) reached end-of-life in August 2023. This fork keeps the skin alive, working, and compatible with modern systems.

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

## Installation

Follow the instructions in [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md).

**If upgrading from the original upstream version**, review the migration notes in [CHANGELOG.md](CHANGELOG.md) for breaking changes.

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

## Credits

This fork is based on the original weewx-Weather34 by Ian Millard (Steepleian), Jerry Dietrich, and many contributors. The original template was created by Brian Underdown (weather34.com).

See [CHANGELOG.md](CHANGELOG.md) for a complete history of changes in this fork.

## Original Project

- **Upstream (EOL):** https://github.com/steepleian/weewx-Weather34
- **Successor (in development):** https://github.com/Millardiang/weewx-divumwx
