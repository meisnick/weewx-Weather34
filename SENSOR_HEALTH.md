# Sensor health / check-engine light

Surfaces GW1000 sensor failures in the top bar instead of leaving them silent.

## Why this exists

A WH57 lightning detector died on a flat battery and nothing showed it. Three
things had to be wrong at once:

1. Signal and battery were never persisted, so an outage left no trace in the
   archive and could not be diagnosed after the fact.
2. The only health page (`pop_sensors.php`) was a manual live poll buried in the
   menu, with no memory of a previous state to compare against.
3. **When the gateway can no longer hear a sensor it reports battery `15`
   (0x0F).** `gw1000.py` `batt_state_desc()` has no case for out-of-range, so 15
   fell through to `'Unknown'`, and `pop_sensors.php` rendered `'Unknown'` as an
   em-dash plus *"battery not reported by hardware"* — which is the legitimate,
   permanent state of the WH40 rain gauge. A dead sensor looked healthy.

## Required weewx.conf change

**The light shows "no data yet" until this is applied.** The archive columns
below already exist in `wview_extended` and were unused, so no schema migration
is needed.

```ini
[GW1000]
    [[field_map_extensions]]
        signal1 = wh40_sig
        signal2 = wh68_sig
        signal3 = wh32_sig
        signal5 = wh57_sig
        rainBatteryStatus = wh40_batt
        windBatteryStatus = wh68_batt
        outTempBatteryStatus = wh32_batt
        batteryStatus5 = wh57_batt

[Accumulator]
    # health is state, not a rate -- take the last value in the interval
    [[signal1]]
        extractor = last
    # ... repeat for each column mapped above
```

The driver's `construct_field_map()` de-duplicates, so the default identity
entries (`wh57_batt: wh57_batt`) drop out on their own. Restart weewx and
confirm with:

```sh
sqlite3 /var/lib/weewx/weewx.sdb \
  "SELECT signal1,signal2,signal3,signal5,batteryStatus5 \
   FROM archive ORDER BY dateTime DESC LIMIT 1;"
```

Adjust the sensor list to match the station — this maps WH40 (rain), WH68
(wind), WH32 (outdoor T/H) and WH57 (lightning).

## Battery scales differ per sensor

There is no single threshold. `sensor_health.php` encodes these:

| Sensor | Type     | Reading                                        |
|--------|----------|------------------------------------------------|
| WH57   | `int`    | 0-5 level; `<=1` low, `6` = DC mains, `>5` dead |
| WH68   | `volt`   | voltage; `<=1.2` low                            |
| WH32   | `binary` | `0` OK, `1` low                                 |
| WH40   | `none`   | **always** None — hardware cannot report it, so signal is its only health indicator |

## Parts

- `sensor_health.php` — reads the latest archive row, returns state as JSON.
  Include it for `w34_sensor_health()`, or request it directly. Plain DB read,
  no `shell_exec`/sudo. Override `W34_HEALTH_DB` before including to test
  against a copy.
- `menu.php` — `<weather34mbhealth>` in `weather34toolbar__left`, re-polls every
  60s, click opens `pop_sensors.php`.
- `css/main.dark.css` — appended block. Matches `weather34mbuptime`
  declaration-for-declaration when healthy; colour changes only on a fault.
  The global `a` rules (`font-family: arial`, `color: silver`, `font-size: 1em`)
  beat inheritance, so the anchor states are restated at higher specificity.
- `pop_sensors.php` — out-of-range battery now renders FAULT, and a registered
  sensor at `signal 0` renders OFFLINE.

## States

`ok` → green · `warn` → amber (low battery, weak signal) · `fault` → red, pulsing
(sensor not receiving, battery sentinel, or archive stale >15 min).

The light stays visible when healthy on purpose: an indicator that only appears
on failure gives no evidence the monitor itself still works, which is the exact
failure mode this fixes.
