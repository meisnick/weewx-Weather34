<?php
/**
 * sensor_health.php — GW1000 sensor health check ("check engine light").
 *
 * Reads the most recent archive record and evaluates per-sensor signal and
 * battery state. Sensor health is persisted via [GW1000] field_map_extensions
 * in weewx.conf, so this is a cheap DB read -- no shell_exec, no sudo.
 *
 * Usage:
 *   include: w34_sensor_health()  => ['state'=>'ok|warn|fault', 'issues'=>[], ...]
 *   direct:  sensor_health.php    => JSON
 */

if (!defined('W34_HEALTH_DB')) define('W34_HEALTH_DB', '/var/lib/weewx/weewx.sdb');

// Archive record older than this means data has stopped flowing entirely.
define('W34_HEALTH_STALE_SECS', 900);

/**
 * Sensors this station actually owns, mapped to the archive columns that
 * field_map_extensions writes them into.
 *
 * batt_type governs how the raw battery value is read:
 *   volt   - voltage, low at/below 1.2
 *   int    - 0-5 level: <=1 low, 6 = DC mains, anything above 5 is the
 *            hardware's out-of-range sentinel and means the sensor is dead
 *   binary - 0 = OK, 1 = low
 *   none   - hardware cannot report battery (WH40); signal is the only signal
 */
function w34_health_sensors() {
    return [
        'WH68' => ['label' => 'Wind array',   'sig' => 'signal2', 'batt' => 'windBatteryStatus',    'batt_type' => 'volt'],
        'WH40' => ['label' => 'Rain gauge',   'sig' => 'signal1', 'batt' => 'rainBatteryStatus',    'batt_type' => 'none'],
        'WH32' => ['label' => 'Outdoor T/H',  'sig' => 'signal3', 'batt' => 'outTempBatteryStatus', 'batt_type' => 'binary'],
        'WH57' => ['label' => 'Lightning',    'sig' => 'signal5', 'batt' => 'batteryStatus5',       'batt_type' => 'int'],
    ];
}

function w34_health_batt_state($type, $v) {
    if ($v === null || $type === 'none') return ['unknown', null];
    $v = (float)$v;
    switch ($type) {
        case 'volt':
            return [$v <= 1.2 ? 'low' : 'ok', sprintf('%.2fV', $v)];
        case 'int':
            if ($v <= 1) return ['low', (string)(int)$v];
            if ($v == 6) return ['ok', 'DC'];
            if ($v <= 5) return ['ok', (string)(int)$v];
            // Out of range. The GW1000 reports 15 (0x0F) for a sensor it can
            // no longer hear -- this is a dead sensor, not "not reported".
            return ['dead', (string)(int)$v];
        case 'binary':
            return [$v == 1 ? 'low' : 'ok', $v == 1 ? 'LOW' : 'OK'];
    }
    return ['unknown', null];
}

function w34_sensor_health() {
    $sensors = w34_health_sensors();
    $out = ['state' => 'ok', 'issues' => [], 'sensors' => [], 'age' => null, 'error' => null];

    try {
        $db = new PDO('sqlite:' . W34_HEALTH_DB, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    } catch (Exception $e) {
        return ['state' => 'fault', 'issues' => ['Cannot open weather database'],
                'sensors' => [], 'age' => null, 'error' => $e->getMessage()];
    }

    $cols = ['dateTime'];
    foreach ($sensors as $s) { $cols[] = $s['sig']; $cols[] = $s['batt']; }
    $sql = 'SELECT ' . implode(',', $cols) . ' FROM archive ORDER BY dateTime DESC LIMIT 1';

    $row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return ['state' => 'fault', 'issues' => ['No archive records'],
                'sensors' => [], 'age' => null, 'error' => null];
    }

    $age = time() - (int)$row['dateTime'];
    $out['age'] = $age;
    $out['updated'] = (int)$row['dateTime'];

    if ($age > W34_HEALTH_STALE_SECS) {
        $out['state'] = 'fault';
        $out['issues'][] = sprintf('No data for %d min', round($age / 60));
    }

    $rank = ['ok' => 0, 'warn' => 1, 'fault' => 2];

    foreach ($sensors as $model => $s) {
        $sig  = $row[$s['sig']];
        $sig  = ($sig === null) ? null : (int)$sig;
        list($bState, $bLabel) = w34_health_batt_state($s['batt_type'], $row[$s['batt']]);

        $state = 'ok'; $why = '';

        if ($sig === null) {
            // Column not yet populated (record predates the field map).
            $state = 'ok'; $why = 'no data yet';
        } elseif ($sig === 0) {
            $state = 'fault'; $why = 'not receiving';
        } elseif ($sig <= 1) {
            $state = 'warn';  $why = 'weak signal';
        }

        if ($bState === 'dead' && $state !== 'fault') {
            $state = 'fault'; $why = 'battery failed';
        } elseif ($bState === 'low' && $state === 'ok') {
            $state = 'warn';  $why = 'battery low';
        }

        if ($state !== 'ok') {
            $out['issues'][] = $s['label'] . ' — ' . $why;
        }
        if ($rank[$state] > $rank[$out['state']]) $out['state'] = $state;

        $out['sensors'][$model] = [
            'label'  => $s['label'],
            'signal' => $sig,
            'batt'   => $bLabel,
            'battState' => $bState,
            'state'  => $state,
            'why'    => $why,
        ];
    }

    return $out;
}

// Direct request -> JSON
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'sensor_health.php') {
    header('Content-Type: application/json');
    header('Cache-Control: no-store');
    echo json_encode(w34_sensor_health());
}
