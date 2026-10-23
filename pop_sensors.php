<?php include_once('w34CombinedData.php'); error_reporting(0);
if (isset($_GET['theme'])) {
    $theme = ($_GET['theme'] === 'light') ? 'light' : 'dark';
}

$sensorDesc = [
    'WH65'  => 'Outdoor weather array — temp, humidity, wind, UV, solar, rain',
    'WH68'  => 'Solar-powered anemometer — wind speed &amp; direction',
    'WS80'  => 'Sonic anemometer — wind speed &amp; direction',
    'WH40'  => 'Tipping bucket rain gauge',
    'WH32'  => 'Outdoor temp &amp; humidity sensor',
    'WH26'  => 'Outdoor temp &amp; humidity sensor',
    'WH31'  => 'Indoor temp &amp; humidity sensor (WH31/WN31)',
    'WH51'  => 'Soil moisture sensor',
    'WH41'  => 'PM2.5 air quality sensor',
    'WH43'  => 'PM2.5 air quality sensor',
    'WH57'  => 'Lightning detector',
    'WH55'  => 'Water leak sensor',
    'WN34'  => 'Waterproof temperature probe',
    'WH45'  => 'CO2 &amp; air quality sensor (PM2.5/PM10)',
    'WN35'  => 'Leaf wetness sensor',
    'WS90'  => 'Sonic weather station array',
    'WS85'  => 'Sonic weather station — wind, rain, solar',
    'GW1000'=> 'Ecowitt gateway console',
    'GW1100'=> 'Ecowitt gateway console',
    'GW2000'=> 'Ecowitt gateway console',
];

$sensorBattType = [
    'WH68'  => 'volt',
    'WS80'  => 'volt',
    'WS85'  => 'volt',
    'WS90'  => 'volt',
    'WN34'  => 'volt',
    'WN35'  => 'volt',
    'WH51'  => 'volt',
    'WH40'  => 'volt',
    'WH41'  => 'level',
    'WH43'  => 'level',
    'WH57'  => 'level',
    'WH55'  => 'level',
    'WH45'  => 'level',
    'WH65'  => 'binary',
    'WH32'  => 'binary',
    'WH26'  => 'binary',
    'WH31'  => 'binary',
];

$output = shell_exec('sudo /usr/bin/weectl device --sensors 2>&1');
$lines  = explode("\n", trim($output));
$active = []; $registering = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (!$line || strpos($line, 'Sensor') === 0 || strpos($line, 'Using') === 0 ||
        strpos($line, 'Interrogating') === 0) continue;
    if (!preg_match('/^(\w+(?:\s+ch\d+)?)\s+(.+)$/', $line, $m)) continue;
    $name  = trim($m[1]);
    $status= trim($m[2]);
    $model = preg_replace('/\s+ch\d+$/', '', $name);
    if (strpos($status, 'is disabled') !== false) {
        // GW1000 marks sensor types as disabled when they conflict with an active sensor
        // (e.g. WH65/WS80 disabled because WH68 handles wind) — skip, user doesn't own them
        continue;
    } elseif (strpos($status, 'registering') !== false) {
        $registering[] = ['name' => $name, 'model' => $model];
    } else {
        preg_match('/sensor ID:\s*(\S+)/i',              $status, $id);
        preg_match('/signal:\s*(\d+)/i',                 $status, $sig);
        preg_match('/battery:\s*([^\s(]+)\s*\(([^)]+)\)/i', $status, $batt);
        $bVal = isset($batt[1]) ? $batt[1] : null;
        $bOK  = isset($batt[2]) ? strtolower($batt[2]) : 'unknown';
        // None/Unknown means sensor doesn't report battery (e.g. wired rain gauge)
        if ($bVal === null || strtolower($bVal) === 'none' || $bOK === 'unknown') {
            $bVal = null; $bOK = 'none';
        }
        $active[] = [
            'name'    => $name,
            'model'   => $model,
            'id'      => isset($id[1])  ? $id[1]  : '—',
            'signal'  => isset($sig[1]) ? intval($sig[1]) : null,
            'battVal' => $bVal,
            'battOK'  => $bOK,
        ];
    }
}

?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GW1000 Sensor Status</title>
<link rel="stylesheet" href="css/tokens.css">
<style>
body{margin:8px 44px 8px 8px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;color:silver;background-color:var(--pop-bg)}
[data-theme="light"] body{color:#333}
.weather34darkbrowser{position:relative;background:0;width:100%;height:30px;margin:auto;margin-top:-5px;margin-left:0;border-top-left-radius:5px;border-top-right-radius:5px;padding-top:10px;margin-right:44px}
.weather34darkbrowser[url]:after{content:attr(url);color:var(--pop-chrome-title);font-size:14px;text-align:center;position:absolute;left:0;right:0;top:0;padding:4px 15px;margin:11px 44px 0 auto;font-family:arial;height:20px}
.row{display:flex;align-items:center;gap:0;padding:5px 4px;border-bottom:1px solid var(--pop-sensor-row-border)}
.row:last-child{border-bottom:0}
.hdr{padding:4px;margin:8px 0 2px 0;font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:rgba(150,155,165,1);font-weight:bold}
[data-theme="light"] .hdr{color:var(--modal-meta-ink)}
.left{flex:1;min-width:0}
.right{display:flex;align-items:center;gap:10px;flex-shrink:0}
.sname{font-weight:bold;color:var(--pop-chrome-title);font-size:12px;margin-right:8px}
.sdesc{color:rgba(150,155,165,1);font-size:10px}
[data-theme="light"] .sdesc{color:var(--modal-meta-ink)}
.snote{font-size:9px;color:var(--modal-footer-ink);font-style:italic;margin-top:2px}
[data-theme="light"] .snote{color:var(--modal-footer-ink)}
.sid{font-family:monospace;font-size:10px;color:var(--modal-footer-ink);width:62px}
[data-theme="light"] .sid{color:var(--modal-footer-ink)}
.bars{display:inline-flex;align-items:flex-end;gap:2px;height:13px;width:22px}
.bar{display:inline-block;width:4px;background:var(--pop-sensor-bar)}
.bar.on{background:#5a9}
.batt{width:62px;text-align:right;font-size:11px}
.ok{color:var(--pop-status-ok);font-weight:bold}
.low{color:var(--pop-status-warn);font-weight:bold}
.unk{color:var(--modal-footer-ink)}
[data-theme="light"] .unk{color:var(--modal-footer-ink)}
.dead{color:var(--pop-status-dead);font-weight:bold}
.reg{color:var(--modal-footer-ink);font-size:10px;line-height:1.9}
[data-theme="light"] .reg{color:var(--modal-footer-ink)}
</style>
</head>
<body>
<div class="weather34darkbrowser" url="GW1000 Sensor Status &nbsp;— &nbsp;<?php echo date('H:i:s'); ?>"></div>

<?php
function bars($level) {
    $h = [4, 7, 10, 13];
    $out = '';
    for ($i = 0; $i < 4; $i++) {
        $on = ($level !== null && $i < $level) ? ' on' : '';
        $out .= "<span class='bar{$on}' style='height:{$h[$i]}px'></span>";
    }
    return $out;
}

if (!empty($active)) {
    echo "<div class='hdr'>Active sensors (" . count($active) . ")</div>";
    foreach ($active as $s) {
        $desc = isset($sensorDesc[$s['model']]) ? $sensorDesc[$s['model']] : '';
        $bType = isset($sensorBattType[$s['model']]) ? $sensorBattType[$s['model']] : 'binary';
        if ($s['battOK'] === 'ok' || $s['battOK'] === 'low') {
            $bClass = $s['battOK'];
            $note = '';
            if ($bType === 'volt') {
                $bLabel = htmlspecialchars($s['battVal']) . ' V';
            } elseif ($bType === 'level') {
                $bLabel = htmlspecialchars($s['battVal']) . '/5';
            } else {
                $bLabel = ($s['battOK'] === 'low') ? 'LOW' : 'OK';
            }
        } elseif ($s['battVal'] === null || strcasecmp($s['battVal'], 'None') === 0) {
            // Hardware genuinely cannot report battery (e.g. WH40 rain gauge).
            $bClass = 'unk'; $bLabel = '—'; $note = 'battery not reported by hardware';
        } else {
            // Out-of-range value: the gateway reports a sentinel (15/0x0F) for a
            // sensor it can no longer hear. That is a dead sensor, not "unknown".
            $bClass = 'dead'; $bLabel = htmlspecialchars($s['battVal']) . ' FAULT';
            $note = 'battery failed or sensor not responding';
        }
        // A registered sensor at signal 0 is offline regardless of battery.
        if ($s['signal'] === 0) {
            $bClass = 'dead';
            if ($bLabel === '—') $bLabel = 'OFFLINE';
            $note = 'not being received (signal 0)';
        }
        echo "<div class='row'>"
           . "<div class='left'>"
           .   "<span class='sname'>" . htmlspecialchars($s['name']) . "</span>"
           .   "<span class='sdesc'>" . $desc . "</span>"
           .   ($note ? "<div class='snote'>{$note}</div>" : "")
           . "</div>"
           . "<div class='right'>"
           .   "<span class='sid'>ID:&nbsp;" . htmlspecialchars($s['id']) . "</span>"
           .   "<span class='bars'>" . bars($s['signal']) . "</span>"
           .   "<span class='batt {$bClass}'>{$bLabel}</span>"
           . "</div>"
           . "</div>";
    }
}


$regModels = array_values(array_unique(array_column($registering, 'model')));
if (!empty($regModels)) {
    echo "<div class='hdr'>Not connected — " . count($registering) . " slots, " . count($regModels) . " types</div>";
    $parts = [];
    foreach ($regModels as $rm) {
        $d = isset($sensorDesc[$rm]) ? ' — ' . $sensorDesc[$rm] : '';
        $parts[] = '<b>' . htmlspecialchars($rm) . '</b>' . $d;
    }
    echo "<div class='reg'>" . implode('<br>', $parts) . "</div>";
}
if (empty($active) && empty($regModels)) {
    echo "<div class='reg' style='padding:20px 4px;'>No active sensor data available.</div>";
}
?>
</body>
</html>
