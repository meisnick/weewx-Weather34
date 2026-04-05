# WeeWX 5.x Migration Guide

This document covers migrating weewx-Weather34 from WeeWX 4.x to WeeWX 5.x.

**Current Status:** Compatible — minimal changes required.

WeeWX 5.3.1 is the current stable release. The Weather34 skin is largely compatible with WeeWX 5.x out of the box, but there are a few areas that need attention.

---

## Compatibility Assessment

| Component | Status | Notes |
|-----------|--------|-------|
| `weather34.py` | ✅ Compatible | Uses `packaging.version`, not `distutils` |
| `w34highchartsSearchX.py` | ✅ Compatible | Pure search list extension |
| `lastnonzero.py` | ✅ Compatible | Standard WeeWX extension |
| `stats.py` | ✅ Compatible | Standard WeeWX extension |
| `ml.py` | ✅ Compatible | Standard WeeWX extension |
| `lastrain.py` | ✅ Compatible | Standard WeeWX extension |
| `archivedata.php.tmpl` | ✅ Compatible | Standard Cheetah templates |
| Almanac properties (`$almanac.sun.alt`, etc.) | ✅ Compatible | Old names still return raw floats |
| GW1000 driver (v0.6.3) | ✅ Compatible | Supports WeeWX 3.7+ |

---

## Required Changes

### 1. Installation Scripts

**Files:** `w34_installer.py`, `w34_uninstaller.py`

The installers used Python 2-style `raw_input`. These have been updated to Python 3-only with a compatible `w34_input()` wrapper function.

The installer does not call `wee_*` commands directly. References to `wee_extension`, `wee_reports`, and `wee_debug` are in the documentation files, not the installer code itself.

### 2. weewx.conf Configuration

**File:** (user's weewx.conf, not in repo)

When migrating `weewx.conf` to WeeWX 5.x:

**Required additions:**

1. Add `xtype_services` to `[Engine] [[Services]]`:
   ```
   [Engine]
       [[Services]]
           xtype_services = weewx.wxxtypes.StdWXXTypes, weewx.wxxtypes.StdPressureCooker, weewx.wxxtypes.StdRainRater, weewx.wxxtypes.StdDelta
   ```

2. Verify `[StdWXCalculate] [[Calculations]]` includes all derived types needed by Weather34. Weather34 uses many derived calculations — ensure these are listed:
   ```
   [StdWXCalculate]
       [[Calculations]]
           pressure = prefer_hardware
           altimeter = prefer_hardware
           appTemp = prefer_hardware
           barometer = prefer_hardware
           cloudbase = prefer_hardware
           dewpoint = prefer_hardware
           ET = prefer_hardware
           heatindex = prefer_hardware
           humidex = prefer_hardware
           inDewpoint = prefer_hardware
           maxSolarRad = prefer_hardware
           rainRate = prefer_hardware
           windchill = prefer_hardware
           windrun = prefer_hardware
   ```

3. Update config version string:
   ```
   [Station]
       version = 5.x.x
   ```

### 3. GW1000 Driver Installation (WeeWX 5.x)

The GW1000 driver v0.6.3 is fully compatible with WeeWX 5.x.

**Installation (WeeWX 5.x):**
```bash
# Download the extension package
wget https://github.com/weewx-contrib/weewx-gw1000/releases/latest/download/weewx-gw1000.tar.gz

# Install using weectl (not wee_extension)
sudo weectl extension install weewx-gw1000.tar.gz
```

**Config section:**
```
[GW1000]
    driver = user.gw1000
    ip_address = YOUR_GW1000_IP
    poll_interval = 60
```

---

## Recommended Updates

### 4. Update Installation Documentation

**Files:** `INSTALLATION_GUIDE.md`, `TROUBLESHOOTING.md`, `HIGHCHARTS_GUIDE.md`

References to old WeeWX 4.x commands have been updated in the documentation:

| Old Command | New Command |
|-------------|-------------|
| `wee_extension --uninstall crt` | `weectl extension uninstall crt` |
| `python3 ./[PATH]/wee_reports` | `weectl report run` |
| `./wee_debug --output` | `weectl debug --output` |

### 5. Remove Deprecated Services

**File:** `INSTALLATION_GUIDE.md`

References to old WeeWX 4.x commands need updating:

| Old Command | New Command |
|-------------|-------------|
| `wee_extension --install=...` | `weectl extension install ...` |
| `wee_extension --uninstall=...` | `weectl extension uninstall ...` |
| `wee_reports` | `weectl report run` |
| `wee_config` | `weectl station reconfigure` |
| `wee_database` | `weectl database` |
| `wee_debug` | `weectl debug` |
| `wee_device` | `weectl device` |

### 5. Remove Deprecated Services

Weather34's `services.txt` references several deprecated services:

| Service | Status | Action |
|---------|--------|--------|
| `earthquake` | Removed | Remove from `services.txt` |
| `aq.aeris` | Deprecated | Remove from `services.txt` |
| `aq.aqi.purpleair` | Check | May need AQI translator script |
| `ki` (K-index) | Deprecated | Remove from `services.txt` |

### 6. Ephem Version

The installation guide requires `pyephem`. Ensure the latest version is installed:
```bash
pip3 install --upgrade pyephem
```

---

## Known WeeWX 5.x Changes (No Action Needed)

These changes are already compatible with Weather34's current code:

- **Almanac properties**: Old names (`$almanac.sun.alt`, `$almanac.sun.az`, etc.) still return raw floating-point numbers in degrees. New names (`azimuth`, `altitude`) return formatted ValueHelpers. Both work.
- **`distutils` removal**: Weather34 already uses `packaging.version` (not `distutils`).
- **`wview_extended` schema**: This is now the default. Weather34's `archivedata.php.tmpl` references fields available in this schema.
- **`user.extensions` in reports**: Custom functions are now available during report generation.
- **Python 2 dropped**: Weather34 is Python 3 only — no changes needed.
- **`schema` module**: Now at `weewx.schemas` — Weather34 doesn't import schema directly.
- **Pillow API**: Weather34's `weather34.py` only uses `PIL.Image` for optional QR code generation — not the deprecated APIs.

---

## Testing Checklist

After migration:

- [ ] WeeWX starts without errors
- [ ] Weather34 service starts (`Weather34RealTime` in logs)
- [ ] Main page loads at `/weewx/weather34/`
- [ ] Lightning panel shows data
- [ ] Highcharts charts render (dark and light themes)
- [ ] Realtime data updates (`w34realtime.txt` is generated)
- [ ] Archived data is generated (`archivedata.php`)
- [ ] All popup/modal charts work
- [ ] No PHP errors in web server logs
- [ ] No Python errors in syslog

---

## Rollback

If migration fails, WeeWX 4.x configurations are generally backward-compatible. Keep a backup of:
- `/etc/weewx/weewx.conf`
- `/usr/share/weewx/user/weather34.py`
- `/var/www/html/weewx/weather34/settings1.php`
