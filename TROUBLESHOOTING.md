# Troubleshooting

Quick reference for the most common configuration issues.

---

## Dashboard shows a blank page

**Cause:** The `mbstring` PHP extension is missing.



---

## Temperature values are obviously wrong (e.g. 161°F daily average)

**Cause:** Unit system mismatch between WeeWX archive data and the Weather34 realtime service. Both must use the same unit system.

In `/etc/weewx/weewx.conf`, verify both of these match:

```ini
[StdConvert]
    target_unit = US

[Weather34RealTime]
    unit_system = US
```

If `unit_system = METRICWX` while `target_unit = US`, the realtime file reports temperatures in Celsius but archive values are in Fahrenheit — the conversion runs twice.

---

## `appTemp` (Apparent Temperature) shows as blank or NULL

**Cause:** The GW1000 hardware does not provide apparent temperature. WeeWX must calculate it in software.

Add or verify this section in `/etc/weewx/weewx.conf`:

```ini
[StdWXCalculate]
    [[Calculations]]
        appTemp = software
```

---

## WeeWX crashes every archive cycle: `KeyError: 'w34Highcharts'`

**Cause:** The `weather34.py` service unconditionally accesses `config_dict['StdReport']['w34Highcharts']` at every archive record, even when the report is disabled.

Add this section under `[StdReport]` in `/etc/weewx/weewx.conf`:

```ini
[StdReport]
    ...
    [[w34Highcharts]]
        HTML_ROOT = /var/www/html/weewx/weather34/w34highcharts
        enable = false
```

The same applies to `[[RSYNC]]` — it must exist even if RSYNC is not used.

---

## WeeWX log shows `FileNotFoundError: /etc/weewx/skins/Weather34`

**Cause:** The skin Cheetah templates were not copied to the WeeWX skins directory.



---

## WeeWX log shows `ValueError: Unacceptable pattern: PosixPath('.')`

**Cause:** Python 3.13 tightened `Path.glob()` validation. An empty `copy_once` value in `skin.conf` triggers this. The fix is already applied in the current `skins/Weather34/skin.conf` (CopyGenerator removed from the generator list).

If you are using a custom skin.conf, remove `weewx.reportengine.CopyGenerator` from `generator_list`:

```ini
[Generators]
    generator_list = weewx.cheetahgenerator.CheetahGenerator
```

---

## Forecast data not updating

Check the cron log:



Common causes:
- `scripts/w34config.py` not created (copy from `w34config.example.py` and fill in your coordinates)
- Log file not writable by www-data — run `sudo chown www-data /var/log/nws_forecast.log`
- Network timeout to Open-Meteo — transient; the next cron run will retry

---

## METAR / current conditions not updating



Verify your ICAO airport code is set correctly in `scripts/w34config.py`. The code must be a valid ICAO identifier (4 letters, e.g. `KORD`), not an FAA code (3 letters).

---

## Weather alerts not showing (US stations)

Verify `position4` in `settings1.php` is set to `top_advisory_nws.php`, and that your NWS zone codes are correct in `scripts/w34config.py`:

```python
ALERT_ZONES = "FLZ052,FLC011"   # example — find yours at weather.gov
```

Zone codes can be forecast zones (FLZ...) or county zones (FLC...). Use the [NWS zone finder](https://www.weather.gov/pfl/) to identify yours.

---

## Cloud cover chart shows values over 100%

**Cause:** The Highcharts data grouping approximation was set to `sum` instead of `average`. This is fixed in the current `plots.js`. If you have a cached version, clear your browser cache or bump the `?v=` version string on the script tag in `dark-charts.html`.

---

## GW1000 not detected

Verify the GW1000/GW2000 IP address is correct:



This scans the local network for Ecowitt gateways and prints their IP addresses.

---

## Permission denied writing to serverdata or jsondata

WeeWX runs as the `weewx` user and needs write access to the skin's data directories:

```bash
sudo usermod -a -G www-data weewx
sudo chown -R weewx:www-data /var/www/html/weewx/weather34/serverdata
sudo chown -R weewx:www-data /var/www/html/weewx/weather34/jsondata
sudo chmod -R 775 /var/www/html/weewx/weather34/serverdata
sudo chmod -R 775 /var/www/html/weewx/weather34/jsondata
sudo systemctl restart weewx
```



---

## Day/night icon shows sun at night

**Cause:** PHP defaults to UTC on Debian Trixie. The day/night comparison in archivedata.php uses local time strings but PHP interprets them in UTC, so at midnight local time the UTC hour can appear to be past sunrise.

Set the PHP timezone in both ini files and reload Apache:

```bash
sudo sed -i 's/;date.timezone =/date.timezone = America/Chicago/' /etc/php/8.4/apache2/php.ini /etc/php/8.4/cli/php.ini
sudo systemctl reload apache2
```



Replace `America/Chicago` with your local timezone (`timedatectl list-timezones` to find yours).


