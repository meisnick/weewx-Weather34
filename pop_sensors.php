<?php include('w34CombinedData.php'); error_reporting(0); ?>
<?php
$sensorDesc = [
    'WH65'  => 'Outdoor Weather Array (temp, humidity, wind, UV, solar, rain)',
    'WH68'  => 'Solar-Powered Anemometer (wind speed &amp; direction)',
    'WS80'  => 'Sonic Anemometer (wind speed &amp; direction)',
    'WH40'  => 'Tipping Bucket Rain Gauge',
    'WH32'  => 'Outdoor Temp &amp; Humidity Sensor',
    'WH26'  => 'Outdoor Temp &amp; Humidity Sensor',
    'WH31'  => 'Indoor Temp &amp; Humidity Sensor',
    'WH51'  => 'Soil Moisture Sensor',
    'WH41'  => 'PM2.5 Air Quality Sensor',
    'WH43'  => 'PM2.5 Air Quality Sensor',
    'WH57'  => 'Lightning Detector',
    'WH55'  => 'Water Leak Sensor',
    'WN34'  => 'Waterproof Temperature Probe',
    'WH45'  => 'CO2 &amp; Air Quality Sensor (PM2.5/PM10)',
    'WN35'  => 'Leaf Wetness Sensor',
    'WS90'  => 'Sonic Weather Station Array',
    'WS85'  => 'Sonic Weather Station (wind/rain/solar)',
    'GW1000'=> 'Ecowitt Gateway Console',
    'GW1100'=> 'Ecowitt Gateway Console',
    'GW2000'=> 'Ecowitt Gateway Console',
];

$output = shell_exec('sudo /usr/bin/weectl device --sensors 2>&1');
$lines  = explode("\n", trim($output));

$active      = [];
$disabled    = [];
$registering = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (!$line || strpos($line, 'Sensor') === 0 || strpos($line, 'Using') === 0 ||
        strpos($line, 'Interrogating') === 0) continue;

    if (!preg_match('/^(\w+(?:\s+ch\d+)?)\s+(.+)$/', $line, $m)) continue;

    $name   = trim($m[1]);
    $status = trim($m[2]);
    $model  = preg_replace('/\s+ch\d+$/', '', $name);

    if (strpos($status, 'is disabled') !== false) {
        $disabled[] = ['name' => $name, 'model' => $model];
    } elseif (strpos($status, 'registering') !== false) {
        $registering[] = ['name' => $name, 'model' => $model];
    } else {
        preg_match('/sensor ID:\s*(\S+)/i',  $status, $id);
        preg_match('/signal:\s*(\d+)/i',     $status, $sig);
        preg_match('/battery:\s*([^\s(]+)\s*\(([^)]+)\)/i', $status, $batt);
        $active[] = [
            'name'    => $name,
            'model'   => $model,
            'id'      => isset($id[1])   ? $id[1]   : '—',
            'signal'  => isset($sig[1])  ? intval($sig[1]) : null,
            'battVal' => isset($batt[1]) ? $batt[1] : '—',
            'battOK'  => isset($batt[2]) ? strtolower($batt[2]) : 'unknown',
        ];
    }
}

$bgBody  = ($theme === 'dark') ? 'rgba(28,29,34,1)'   : '#f4f6fb';
$cardBg  = ($theme === 'dark') ? 'rgba(36,38,44,1)'   : '#fff';
$border  = ($theme === 'dark') ? 'rgba(84,85,86,0.35)': '#e2e6ef';
$txtMain = ($theme === 'dark') ? '#ccc' : '#444';
$txtSub  = ($theme === 'dark') ? '#888' : '#888';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { background: <?php echo $bgBody; ?>; font-family: Arial, Helvetica, sans-serif;
       font-size: 12px; color: <?php echo $txtMain; ?>; padding: 12px; }
h2 { font-size: 14px; font-weight: bold; color: <?php echo $txtMain; ?>;
     padding: 6px 0 10px 0; border-bottom: 1px solid <?php echo $border; ?>; margin-bottom: 10px; }
.section-title { font-size: 11px; font-weight: bold; text-transform: uppercase;
                 letter-spacing: .5px; color: <?php echo $txtSub; ?>; margin: 12px 0 6px 0; }
.card { background: <?php echo $cardBg; ?>; border: 1px solid <?php echo $border; ?>;
        border-radius: 6px; padding: 8px 10px; margin-bottom: 6px;
        display: flex; align-items: center; gap: 10px; }
.sensor-name { font-weight: bold; font-size: 13px; min-width: 80px; }
.sensor-desc { color: <?php echo $txtSub; ?>; font-size: 11px; flex: 1; }
.meta { display: flex; gap: 12px; align-items: center; flex-shrink: 0; }
.pill { border-radius: 20px; padding: 2px 9px; font-size: 11px; font-weight: bold; }
.ok   { background: rgba(80,180,80,0.2);  color: #4a4; border: 1px solid #4a4; }
.low  { background: rgba(220,120,30,0.2); color: #c70; border: 1px solid #c70; }
.unk  { background: rgba(120,120,120,0.15); color: <?php echo $txtSub; ?>;
        border: 1px solid <?php echo $border; ?>; }
.signal { display: flex; align-items: flex-end; gap: 2px; height: 14px; }
.signal span { display: inline-block; width: 4px; background: <?php echo $border; ?>; border-radius: 1px; }
.signal span.on { background: #4a4; }
.signal span.s1 { height: 4px; }
.signal span.s2 { height: 7px; }
.signal span.s3 { height: 10px; }
.signal span.s4 { height: 13px; }
.id-tag { font-size: 10px; color: <?php echo $txtSub; ?>; font-family: monospace; }
.dim { opacity: .45; }
.reg-list { color: <?php echo $txtSub; ?>; font-size: 11px; padding: 4px 0; line-height: 1.8; }
</style>
</head>
<body>
<h2>GW1000 Sensor Status
  <span style="font-weight:normal;font-size:11px;color:<?php echo $txtSub; ?>">
    &mdash; live from gateway &mdash; <?php echo date('H:i:s'); ?>
  </span>
</h2>

<?php
function signalBars($level) {
    $bars = '';
    for ($i = 1; $i <= 4; $i++) {
        $on = ($level !== null && $i <= $level) ? ' on' : '';
        $bars .= "<span class='s{$i}{$on}'></span>";
    }
    return "<div class='signal'>{$bars}</div>";
}

function battPill($val, $ok) {
    $label = htmlspecialchars($val);
    if ($ok === 'ok')  return "<span class='pill ok'>&#9679; {$label} OK</span>";
    if ($ok === 'low') return "<span class='pill low'>&#9650; {$label} LOW</span>";
    return "<span class='pill unk'>{$label}</span>";
}

if (!empty($active)):
    echo "<div class='section-title'>Active Sensors (" . count($active) . ")</div>";
    foreach ($active as $s):
        $desc = isset($sensorDesc[$s['model']]) ? $sensorDesc[$s['model']] : 'Sensor';
?>
<div class="card">
    <div class="sensor-name"><?php echo htmlspecialchars($s['name']); ?></div>
    <div class="sensor-desc"><?php echo $desc; ?></div>
    <div class="meta">
        <span class="id-tag">ID:&nbsp;<?php echo htmlspecialchars($s['id']); ?></span>
        <?php echo signalBars($s['signal']); ?>
        <?php echo battPill($s['battVal'], $s['battOK']); ?>
    </div>
</div>
<?php endforeach; endif; ?>

<?php if (!empty($disabled)): ?>
<div class="section-title">Disabled (<?php echo count($disabled); ?>)</div>
<?php foreach ($disabled as $s):
    $desc = isset($sensorDesc[$s['model']]) ? $sensorDesc[$s['model']] : '';
?>
<div class="card dim">
    <div class="sensor-name"><?php echo htmlspecialchars($s['name']); ?></div>
    <div class="sensor-desc"><?php echo $desc; ?></div>
    <div class="meta"><span class="pill unk">Disabled</span></div>
</div>
<?php endforeach; endif; ?>

<?php
$regModels = array_values(array_unique(array_column($registering, 'model')));
if (!empty($regModels)):
    $parts = [];
    foreach ($regModels as $rm) {
        $d = isset($sensorDesc[$rm]) ? ' (' . $sensorDesc[$rm] . ')' : '';
        $parts[] = htmlspecialchars($rm) . $d;
    }
?>
<div class="section-title">Not Connected (<?php echo count($registering); ?> slots / <?php echo count($regModels); ?> types)</div>
<div class="reg-list"><?php echo implode(' &nbsp;|&nbsp; ', $parts); ?></div>
<?php endif; ?>

</body>
</html>
