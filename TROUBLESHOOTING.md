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

WeeWX runs as the `weewx` user, background data-fetch tasks run under `root` via cron, and developers or administrators may run scripts manually under their local user account (e.g. `pi`). All three contexts need write access to `/var/www/html/weewx/weather34/jsondata/`.

### 1. Align Group Memberships
Make sure all users who need to execute scripts are in the `www-data` group:

```bash
sudo usermod -a -G www-data weewx
sudo usermod -a -G www-data pi  # Replace 'pi' with your local login username
```

> [!IMPORTANT]
> Group membership changes only take effect on your next login. If you added your current user, log out and log back in before continuing.

### 2. Set Directory Permissions (with setgid)
Enforce the directory permissions and set the **setgid bit** (`chmod g+s` or `2775`) on `jsondata`. This ensures that all new files created inside the directory—even those written by a root cron job or your local user—automatically belong to the `www-data` group instead of the creating user's primary group:

```bash
sudo chown -R weewx:www-data /var/www/html/weewx/weather34/serverdata
sudo chown -R weewx:www-data /var/www/html/weewx/weather34/jsondata
sudo chmod -R 775 /var/www/html/weewx/weather34/serverdata
sudo chmod -R 2775 /var/www/html/weewx/weather34/jsondata
sudo systemctl restart weewx
```

### 3. File Permissions (Atomic Writes and Cron)
Cron jobs running under `root` use the system default umask (`022`), creating files with `644` (`-rw-r--r--`) permissions. Any subsequent script executing atomic writes (e.g., writing a `.tmp` file and using `os.replace` or `mv` to replace a data file) will destroy the original `664` permissions and lock out the web server (`www-data`) and other users from writing to it.

All built-in Weather34 scripts (`nws_forecast_update.py`, `metar_update.py`, `nws_alerts_update.py`, `update_aurora_prob.php`, and `update_noaa_scales.sh` / `update_aurora_power.sh`) have been updated to explicitly enforce group-writable **`664` (`-rw-rw-r--`)** permissions on output files. If you run your own data-gathering scripts, ensure they set `umask 002` or run `chmod 664` on their output endpoints.

If permissions on existing files have been reset to `644`, you can restore group write access with:

```bash
sudo chmod 664 /var/www/html/weewx/weather34/jsondata/*
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


