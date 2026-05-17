<?php include('w34CombinedData.php'); error_reporting(0);

$sensorDesc = [
    'WH65'  => 'Outdoor weather array — temp, humidity, wind, UV, solar, rain',
    'WH68'  => 'Solar-powered anemometer — wind speed &amp; direction',
    'WS80'  => 'Sonic anemometer — wind speed &amp; direction',
    'WH40'  => 'Tipping bucket rain gauge',
    'WH32'  => 'Outdoor temp &amp; humidity sensor',
    'WH26'  => 'Outdoor temp &amp; humidity sensor',
    'WH31'  => 'Indoor temp &amp; humidity sensor',
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

if ($theme === 'dark') {
    echo '<style>body{margin:8px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;color:silver;background-color:rgba(33,34,39,.95)}
.row{display:flex;align-items:center;gap:8px;padding:5px 4px;border-bottom:1px solid rgba(84,85,86,0.3);font-size:11px}
.row:last-child{border-bottom:0}
.hdr{padding:4px;margin:8px 0 2px 0;font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:rgba(84,85,86,1);font-weight:bold}
.sname{font-weight:bold;color:#ccc;min-width:72px;font-size:12px}
.sdesc{color:rgba(150,155,165,1);flex:1;font-size:10px}
.sid{font-family:monospace;font-size:10px;color:rgba(100,105,115,1);min-width:52px}
.bar{display:inline-block;width:4px;margin-right:2px;background:rgba(84,85,86,0.4);vertical-align:bottom}
.bar.on{background:#5a9}
.ok{color:#5a9;font-weight:bold}
.low{color:#e08020;font-weight:bold}
.unk{color:rgba(100,105,115,1)}
.dim{opacity:.4}
.reg{color:rgba(100,105,115,1);font-size:10px;line-height:1.9}
</style>';
} else {
    echo '<style>body{margin:8px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;color:#333;background-color:#fff}
.row{display:flex;align-items:center;gap:8px;padding:5px 4px;border-bottom:1px solid #e2e6ef;font-size:11px}
.row:last-child{border-bottom:0}
.hdr{padding:4px;margin:8px 0 2px 0;font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:#999;font-weight:bold}
.sname{font-weight:bold;color:#333;min-width:72px;font-size:12px}
.sdesc{color:#888;flex:1;font-size:10px}
.sid{font-family:monospace;font-size:10px;color:#bbb;min-width:52px}
.bar{display:inline-block;width:4px;margin-right:2px;background:#ddd;vertical-align:bottom}
.bar.on{background:#5a9}
.ok{color:#3a8;font-weight:bold}
.low{color:#c70;font-weight:bold}
.unk{color:#bbb}
.dim{opacity:.4}
.reg{color:#bbb;font-size:10px;line-height:1.9}
</style>';
}
?>
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
        if ($s['battOK'] === 'ok')        { $bClass = 'ok';  $bLabel = htmlspecialchars($s['battVal']) . ' OK'; }
        elseif ($s['battOK'] === 'low')   { $bClass = 'low'; $bLabel = htmlspecialchars($s['battVal']) . ' LOW'; }
        else                              { $bClass = 'unk'; $bLabel = 'not reported by gateway'; }
        echo "<div class='row'>"
           . "<span class='sname'>" . htmlspecialchars($s['name']) . "</span>"
           . "<span class='sdesc'>" . $desc . "</span>"
           . "<span class='sid'>ID: " . htmlspecialchars($s['id']) . "</span>"
           . "<span style='display:inline-flex;align-items:flex-end;gap:2px;height:13px'>" . bars($s['signal']) . "</span>"
           . "<span class='{$bClass}'>{$bLabel}</span>"
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
?>
</body>
