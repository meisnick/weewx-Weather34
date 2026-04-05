# Upgrading from steepleian/weewx-Weather34

This guide covers upgrading from the original `steepleian/weewx-Weather34` repository (which is EOL) to this maintained fork `meisnick/weewx-Weather34`.

**Read this entire guide before starting. There are breaking changes.**

---

## What Changed Between Versions

### Breaking Changes (Requires Action)

| Change | Impact | Action Required |
|--------|--------|-----------------|
| AerisWeather API removed | Forecast and alerts panels may fail | Use Weather Underground or NWS forecasts |
| Earthquake service removed | Earthquake panel shows no data | Accept — USGS API no longer compatible |
| Lightning data source changed | If using WeatherFlow hardware, lightning panel needs update | See Lightning Hardware section below |
| sat24.com cloud cover broken | Cloud cover chart shows no data | Currently no fix — accepting as known issue |
| Some popup modules removed | Some modal panels may be missing | Run `templateSetup.php` to reconfigure |

### Files That Will Be Replaced

The installer will overwrite these files. **Your customizations in `settings1.php` are safe** (it's in `.gitignore` and won't be overwritten), but any changes made directly to installed files will be lost:

- `www/w34CombinedData.php` — PHP 8.1 fixes, KP index format
- `www/lightning34.php` — Ecowitt GW1000 rewrite
- `www/pop_aurora.php` — PHP 8.1 fixes
- `www/w34highcharts/scripts/plots.js` — Highcharts 11+ fixes
- `www/w34highcharts/dark-charts.html` — Local Highcharts libs
- `www/w34highcharts/light-charts.html` — Local Highcharts libs
- `www/top_lightning.php` — Includes updated lightning data
- `user/w34highchartsSearchX.py` — Strike count crash fix
- `skins/Weather34/serverdata/archivedata.php.tmpl` — Station metadata, Ecowitt fields

### Files That Are Safe (Not Replaced)

These are in `.gitignore` and will not be touched by the installer:

- `settings1.php` — Your station settings, API keys, passwords
- `jsondata/*.txt` — Cached API responses
- `serverdata/*.php` — Generated runtime data
- `w34highcharts/json/*.json` — Chart data
- Any `.bak` backup files

---

## Pre-Upgrade Checklist

### 1. Backup Everything

```bash
# Create a backup directory
mkdir ~/weather34_backup_$(date +%Y%m%d)
cd ~/weather34_backup_$(date +%Y%m%d)

# Backup your settings
cp /var/www/html/weewx/weather34/settings1.php ./

# Backup your weewx.conf
cp /etc/weewx/weewx.conf ./

# Backup your entire www directory
cp -r /var/www/html/weewx/weather34 ./www_backup

# Backup user extensions
cp -r /usr/share/weewx/user/*.py ./user_backup/

# Verify backups exist
ls -la
```

### 2. Document Your Current Configuration

```bash
# Note your current WeeWX version
weewxd --version

# Note your PHP version
php --version

# Note which services are enabled (check weewx.conf)
grep -A5 "process_services\|data_services" /etc/weewx/weewx.conf

# Note your station type
grep "station_type\|hardware" /etc/weewx/weewx.conf
```

---

## Upgrade Methods

### Option A: Fresh Install (Recommended if Upgrading WeeWX Too)

Best if you're upgrading both WeeWX and the skin together, or if you want a clean slate.

1. **Backup** (see above)
2. **Run the uninstaller** from the old repository:

```bash
cd ~/weewx-Weather34
sudo python3 w34_uninstaller.py
```

3. **Pull the new repository**:

```bash
cd ~/weewx-Weather34
git remote set-url origin https://github.com/meisnick/weewx-Weather34.git
git pull origin main
```

4. **Re-run the installer**:

```bash
sudo python3 w34_installer.py
```

5. **Restore your settings**:

```bash
# Copy your backed-up settings1.php back
sudo cp ~/weather34_backup_YYYYMMDD/settings1.php /var/www/html/weewx/weather34/

# Set correct ownership
sudo chown www-data:www-data /var/www/html/weewx/weather34/settings1.php
```

6. **Verify and restart**:

```bash
# Check settings page is accessible
# Browse to http://your-pi/weather34/templateSetup.php

# Restart WeeWX
sudo systemctl restart weewx

# Check for errors
sudo journalctl -u weewx -n 50 --no-pager
```

### Option B: In-Place Update (Existing Install Intact)

Best if you want to keep your current installation structure and only update the skin files.

1. **Backup** (see above)
2. **Update the repository**:

```bash
cd ~/weewx-Weather34
git remote set-url origin https://github.com/meisnick/weewx-Weather34.git
git fetch origin
git reset --hard origin/main
```

3. **Copy updated files to installation**:

```bash
# Stop WeeWX first
sudo systemctl stop weewx

# Update PHP files
sudo cp -r www/* /var/www/html/weewx/weather34/

# Update skin files
sudo cp -r skins/Weather34/* /etc/weewx/skins/Weather34/

# Update Python extensions
sudo cp -r user/*.py /usr/share/weewx/user/

# Set correct ownership
sudo chown -R www-data:www-data /var/www/html/weewx/weather34/
sudo chown -R weewx:weewx /etc/weewx/skins/
sudo chown weewx:weewx /usr/share/weewx/user/*.py
```

4. **Restore your settings** (if reset overwrote them):

```bash
sudo cp ~/weather34_backup_YYYYMMDD/settings1.php /var/www/html/weewx/weather34/
sudo chown www-data:www-data /var/www/html/weewx/weather34/settings1.php
```

5. **Restart and verify**:

```bash
sudo systemctl start weewx
sudo journalctl -u weewx -n 50 --no-pager
```

---

## Post-Upgrade Tasks

### 1. Run templateSetup.php

```bash
# Open in browser
http://your-pi-address/weather34/templateSetup.php
```

Navigate through the settings and save. Pay attention to:
- **Chart source**: Ensure 'w34highcharts' is selected if you use charts
- **Web services**: Re-enter any API keys that were in services
- **Lightning hardware**: Verify correct selection (Ecowitt/GW1000 or your hardware)

### 2. Verify WeeWX Services

Check that your services are still configured in `/etc/weewx/weewx.conf`:

```bash
grep "process_services" /etc/weewx/weewx.conf
```

You should see entries like:
```
process_services = ..., user.weather34.Weather34RealTime, user.w34_db_backup.W34_DB_Backup
```

### 3. If You Use the GW1000 Driver

The GW1000 driver is now installed separately:

```bash
# Check if it's installed
weectl extension list
# or (WeeWX 4.x):
wee_extension --list

# If not installed, install it:
wget https://github.com/weewx-contrib/weewx-gw1000/releases/latest/download/weewx-gw1000.tar.gz
sudo weectl extension install weewx-gw1000.tar.gz
```

### 4. Check for PHP Errors

```bash
# Apache error log
sudo tail -50 /var/log/apache2/error.log | grep -i weather34

# Or nginx error log (if using nginx)
sudo tail -50 /var/log/nginx/error.log | grep -i weather34
```

### 5. Verify Charts

If you use Highcharts:
1. Open the main weather page
2. Click on any chart
3. Verify charts render without errors in browser console (F12)

### 6. Run WeeWX Reports Manually

```bash
# WeeWX 5.x:
sudo weectl report run

# WeeWX 4.x:
sudo python3 /path/to/wee_reports
```

---

## Common Issues After Upgrade

### Issue: Blank page or PHP errors

```bash
# Check PHP error log
sudo tail -20 /var/log/apache2/error.log

# Verify PHP version
php --version

# Check required PHP modules
php -m | grep -E "gd|mbstring|curl|xml|zip|bcmath"
```

### Issue: Charts not loading

```bash
# Check Highcharts files exist
ls -la /var/www/html/weewx/weather34/w34highcharts/scripts/

# Check JSON data is being generated
ls -la /var/www/html/weewx/weather34/w34highcharts/json/

# Run reports manually
sudo weectl report run
```

### Issue: Weather Underground forecast not working

1. Go to `templateSetup.php`
2. Verify Weather Underground API key is entered
3. Check that the station ID is correct
4. Verify the WU service is enabled in `weewx.conf`

### Issue: Lightning panel shows no data

If using Ecowitt GW1000, verify:
1. `lightning34.php` exists and is readable
2. The `jsondata/wf.txt` file exists (created by WeeWX)
3. Lightning hardware is set correctly in `templateSetup.php`

### Issue: Services not starting

```bash
# Check WeeWX logs
sudo journalctl -u weewx -f

# Look for specific errors
sudo grep -i error /var/log/syslog | grep weewx
```

---

## Rollback

If the upgrade fails completely:

```bash
# Stop WeeWX
sudo systemctl stop weewx

# Restore from backup
sudo cp -r ~/weather34_backup_YYYYMMDD/www_backup/* /var/www/html/weewx/weather34/
sudo cp ~/weather34_backup_YYYYMMDD/settings1.php /var/www/html/weewx/weather34/
sudo cp ~/weather34_backup_YYYYMMDD/weewx.conf /etc/weewx/weewx.conf

# Restore user extensions
sudo cp ~/weather34_backup_YYYYMMDD/user_backup/* /usr/share/weewx/user/

# Set ownership
sudo chown -R www-data:www-data /var/www/html/weewx/weather34/
sudo chown -R weewx:weewx /etc/weewx/skins/
sudo chown weewx:weewx /usr/share/weewx/user/*.py

# Restart
sudo systemctl start weewx
```

---

## Getting Help

If you encounter issues:
1. Check this guide's troubleshooting section
2. Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
3. Run the debug utility:
   - WeeWX 5.x: `weectl debug --output`
   - WeeWX 4.x: `./wee_debug --output`
4. Open an issue at https://github.com/meisnick/weewx-Weather34/issues
