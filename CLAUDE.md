# Claude Code — Project Instructions

## Remote Access

SSH access is configured via `~/.ssh/config`. Always use the alias — never construct inline `ssh -i keyfile user@ip` strings.

### Pi2 — WeeWX 5.x (Production / main branch)
```bash
ssh pi2 "command here"
```
- **Host alias:** `pi2`
- **IP:** 192.168.1.129
- **User:** pi
- **OS:** Debian 13 Trixie 64-bit
- **Web root:** `/var/www/html/weewx/weather34/`
- **WeeWX config:** `/etc/weewx/weewx.conf`
- **WeeWX user extensions:** `/etc/weewx/bin/user/`
- **WeeWX version:** 5.3.1

### Pi1 — WeeWX 4.x (Legacy / legacy-4.x branch)
```bash
ssh pi "command here"
```
- **Host alias:** `pi`
- **IP:** 192.168.1.5
- **User:** pi
- **OS:** Debian 11 Bullseye 32-bit
- **Web root:** `/var/www/html/weewx/weather34/`
- **WeeWX config:** `/etc/weewx/weewx.conf`
- **WeeWX version:** 4.10.2

## Stack

- **WeeWX 5.x** — weather station daemon, collects from Ecowitt GW1000 via `user.gw1000` driver
- **Weather34 template** — PHP 8.4 frontend served by Apache at the web root
- **Data scripts** (run via root cron on Pi2):
  - `nws_forecast_update.py` — Open-Meteo 8-day forecast → `jsondata/forecast_daily.txt` / `forecast_hourly.txt`
  - `metar_update.py` — aviationweather.gov METAR → `jsondata/me.txt`
  - `nws_alerts_update.py` — NWS Alerts API → `jsondata/nws_alerts.txt`
  - `cloud_cover_update.py` — Open-Meteo cloud cover → patches `signal8` in weewx.sdb
  - `update_aqi.sh` — transforms `jsondata/aq.txt` (WAQI, written by WeeWX service) → `jsondata/aqiJson.txt`
- **git push** from Pi1 (has SSH key configured): `sudo GIT_SSH_COMMAND='ssh -i /home/pi/.ssh/id_rsa' git push origin main`

## Git Branches

| Branch | WeeWX | PHP | Python | OS |
|--------|-------|-----|--------|----|
| `main` | 5.3.1 | 8.4 | 3.13 | Debian 13 Trixie 64-bit |
| `legacy-4.x` | 4.10.2 | 8.1 | 3.9 | Debian 11 Bullseye 32-bit |

## Key Config Files

- `weewx5.conf.example` — generalized WeeWX 5 configuration template
- `scripts/w34config.example.py` — station location/credentials template (gitignored real file: `scripts/w34config.py`)
- `/etc/weewx/weewx.conf` — live WeeWX config (not in git — contains API keys and coordinates)
