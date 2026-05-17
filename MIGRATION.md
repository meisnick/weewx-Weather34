# Migration Guide

## Migrating from WeeWX 4 (steepleian/weewx-Weather34)

This guide covers upgrading an existing WeeWX 4 installation running the steepleian Weather34 skin to WeeWX 5 with this fork.

**Before you start:** Take a full backup of your weewx database and your existing web root. The database migration is non-destructive, but having a backup is good practice.

---

### 1. Back Up Your Data

```bash
# Back up the database
sudo cp /var/lib/weewx/weewx.sdb /var/lib/weewx/weewx.sdb.bak-$(date +%Y%m%d)

# Back up your existing skin (optional — this fork replaces it)
sudo cp -r /var/www/html/weewx/weather34 ~/weather34-backup-$(date +%Y%m%d)

# Back up your WeeWX 4 config for reference
sudo cp /etc/weewx/weewx.conf ~/weewx4.conf.bak
```

---

### 2. Install WeeWX 5

Stop WeeWX 4 first:

```bash
sudo systemctl stop weewx
```

Follow **Steps 1–3** of the [Installation Guide](INSTALLATION.md) to install WeeWX 5, the GW1000 driver, and PHP dependencies.

> WeeWX 5 installs alongside WeeWX 4. The apt package will update in place.

---

### 3. Migrate the Database

WeeWX 5 can read a WeeWX 4 SQLite database directly after a schema update:

```bash
sudo weectl database update --yes
```

This applies any schema changes WeeWX 5 requires to your existing database. Your 4+ years of historical data is preserved.

---

### 4. Deploy the New Skin

Follow **Steps 4–8** of the [Installation Guide](INSTALLATION.md).

The key differences from the steepleian WeeWX 4 skin:

| What changed | Detail |
|-------------|--------|
| Forecast source | AerisWeather removed; Open-Meteo via `nws_forecast_update.py` |
| METAR source | CheckWX removed; aviationweather.gov via `metar_update.py` |
| Alerts source | EU MeteoAlarm removed; NWS API via `nws_alerts_update.py` (US only) |
| Cloud cover | sat24.com removed; Open-Meteo via `cloud_cover_update.py` |
| File names | `awd.txt`→`forecast_daily.txt`, `awh.txt`→`forecast_hourly.txt`, `pop_aeris_*`→`pop_forecast_*` |
| CSS classes | `darksky*` renamed to `forecast*` in both themes |

---

### 5. Configure WeeWX 5

**Do not reuse your WeeWX 4 `weewx.conf`** — the format changed substantially. Start from `weewx5.conf.example` and transfer your station settings:

```bash
sudo cp /var/www/html/weewx/weather34/weewx5.conf.example /etc/weewx/weewx.conf
```

Transfer these values from your WeeWX 4 config:

- `[Station]` — latitude, longitude, altitude, location name
- `[GW1000]` — ip_address, port
- `[StdRESTful]` — your PWSWeather / Wunderground station IDs and passwords
- `[Weather34WebServices]` — your WAQI token and TWC API key

The following sections in `weewx5.conf.example` are **required** and have no WeeWX 4 equivalent — do not remove them:

```ini
[StdWXCalculate]
    [[Calculations]]
        appTemp = software      # GW1000 does not provide appTemp directly

[StdReport]
    [[w34Highcharts]]
        HTML_ROOT = /var/www/html/weewx/weather34/w34highcharts
        enable = false
    [[RSYNC]]
        enable = false
        ...

[DatabaseTypes]
    [[SQLite]]
        driver = weedb.sqlite
        SQLITE_ROOT = /var/lib/weewx
```

---

### 6. Set the Correct Unit System

In `[Weather34RealTime]`, ensure:

```ini
[Weather34RealTime]
    unit_system = US
```

This must match WeeWX's `target_unit = US` in `[StdConvert]`. A mismatch causes archive temperatures (daily average, min, max) to be double-converted and display incorrect values.

---

### 7. Backfill Cloud Cover

If you have historical data pre-dating this fork, cloud cover (`signal8` field) will be zero for all past records. Run the one-time backfill script to populate it from the Open-Meteo archive API:

```bash
sudo python3 /var/www/html/weewx/weather34/scripts/cloud_cover_backfill.py
```

This fetches hourly cloud cover back to your earliest database record. Depending on database size, it takes a few minutes. Rebuild daily summaries afterward:

```bash
sudo systemctl stop weewx
sudo wee_database /etc/weewx/weewx.conf --drop-daily
sudo wee_database /etc/weewx/weewx.conf --rebuild-daily
sudo systemctl start weewx
```

---

### 8. Start WeeWX 5

```bash
sudo systemctl enable weewx
sudo systemctl start weewx
sudo journalctl -u weewx -f
```

Confirm the log shows no errors and data begins flowing within 30 seconds.

---

## Migrating from an Earlier Version of This Fork

If you are already running this fork on WeeWX 4 and want to upgrade to WeeWX 5 in place, follow the same steps above. The database migration and skin deployment are the same. Your existing `scripts/w34config.py` carries over unchanged.
