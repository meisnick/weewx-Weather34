# Box-level files

Files that live outside the web root on the weather boxes. Deploying the repo does not install these;
copy them by hand when they change.

## Permissions after weewx upgrades

The weewx package's postinst runs `chown -R weewx:weewx` and `chmod 664` on every file under
`/var/www/html/weewx`, which contains this repo. That strips the execute bit from the git hooks and
`w34highcharts/w34_reports`, and gives `.git` to weewx, which breaks `sudo -u www-data git merge`.

```sh
sudo install -m 755 scripts/system/w34-restore-perms /usr/local/sbin/w34-restore-perms
sudo install -m 644 scripts/system/99-weather34-perms /etc/apt/apt.conf.d/99-weather34-perms
```

apt then runs `w34-restore-perms` as root after every dpkg run.

Ownership model on both boxes: the tree is `weewx:weewx`, files `664`, directories `2775`; `www-data`
is in the `weewx` group and `weewx` is in `www-data`, so both the web server and weewx can write.
`.git` belongs to `www-data`, which runs git.

## Cron fetchers in /usr/local/bin

Root's crontab runs these from `/usr/local/bin`:

| File | Writes | Install as |
| :--- | :--- | :--- |
| `scripts/metar_update.py` | `jsondata/me.txt` | symlink |
| `scripts/nws_alerts_update.py` | `jsondata/nws_alerts.txt` | symlink |
| `scripts/nws_forecast_update.py` | `jsondata/forecast_daily.txt`, `forecast_hourly.txt` | symlink |
| `scripts/nws_kp_update.py` | `jsondata/ki.txt` | symlink |
| `scripts/update_aqi.sh` | `jsondata/aqiJson.txt` (from `aq.txt`, written by the weather34 extension's `aq` service) | copy, mode 755 |
| `scripts/update_aurora_power.sh` | `jsondata/aurora_power.txt` | copy, mode 755 |
| `scripts/update_noaa_scales.sh` | `jsondata/noaa_scales.json`, `aurora_forecast.txt` | copy, mode 755 |

Cron runs the Python fetchers through `python3`, so symlinks into the repo work and deploys keep them
current. It executes the shell scripts directly, and the postinst above would strip their execute bit
inside the repo, so those are copies.

`/usr/local/bin/w34config.py` is a symlink to `scripts/w34config.py`, which is the single station
config. The nowcast scripts, the root fetchers and `icao_lookup.php`/`nws_zone_lookup.php` all use it.
