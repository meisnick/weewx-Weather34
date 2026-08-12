<?php
include('shared.php');
include('settings.php');
include('common.php');

// ── Current Kp ───────────────────────────────────────────────────────────────
$kp = 0.0; $kp_type = 'estimated';
if (file_exists('jsondata/ki.txt')) {
    $entries = json_decode(file_get_contents('jsondata/ki.txt'), true);
    if (is_array($entries)) {
        foreach (array_reverse($entries) as $e) {
            if (in_array($e['observed'], ['observed', 'estimated'])) {
                $kp = (float)$e['kp']; $kp_type = $e['observed']; break;
            }
        }
    }
}

// ── NOAA R/S/G scales ────────────────────────────────────────────────────────
$rsg = ['R' => 0, 'S' => 0, 'G' => 0];
if (file_exists('jsondata/noaa_scales.json')) {
    $ns = json_decode(file_get_contents('jsondata/noaa_scales.json'), true);
    if ($ns && isset($ns['0'])) {
        foreach (['R', 'S', 'G'] as $k) {
            $rsg[$k] = max(0, (int)($ns['0'][$k]['Scale'] ?? 0));
        }
    }
}

// ── Real-time Aurora Probability from Ovation grid ──────────────────────────
$aurora_prob = null; $prob_ok = false;
if (file_exists('jsondata/aurora_prob.json')) {
    $ap_json = json_decode(file_get_contents('jsondata/aurora_prob.json'), true);
    if ($ap_json && isset($ap_json['probability'])) {
        $aurora_prob = (int)$ap_json['probability'];
        $prob_ok = (time() - filemtime('jsondata/aurora_prob.json') < 1800);
    }
}

// ── Hemisphere power → Highcharts series arrays ───────────────────────────────
$north_pts = []; $south_pts = [];
$power_ok = file_exists('jsondata/aurora_power.txt')
         && (time() - filemtime('jsondata/aurora_power.txt') < 600);
if ($power_ok) {
    foreach (file('jsondata/aurora_power.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line[0] === '#') continue;
        $p = preg_split('/\s+/', trim($line));
        if (count($p) < 4) continue;
        $ts = strtotime(str_replace('_', ' ', $p[0]) . ' UTC');
        if (!$ts) continue;
        $north_pts[] = [$ts * 1000, (int)$p[2]];
        $south_pts[] = [$ts * 1000, (int)$p[3]];
    }
}

// ── 3-day forecast text → structured sections ─────────────────────────────────
$forecast_issued = '';
$ap_data = []; $prob_data = []; $kp_rows = []; $kp_days = [];
$forecast_ok = file_exists('jsondata/aurora_forecast.txt')
            && (time() - filemtime('jsondata/aurora_forecast.txt') < 7200);
if ($forecast_ok) {
    $flines = file('jsondata/aurora_forecast.txt', FILE_IGNORE_NEW_LINES);
    $sec = null; $kp_hdr_seen = false;
    foreach ($flines as $line) {
        $t = trim($line);
        if (preg_match('/^:Issued:\s*(.+)/i', $t, $m)) { $forecast_issued = $m[1]; }
        if (!$t || $t[0] === ':' || $t[0] === '#') continue;

        if (stripos($t, 'Ap Index') !== false)            { $sec = 'ap'; continue; }
        if (stripos($t, 'Probabilities') !== false)        { $sec = 'prob'; continue; }
        if (stripos($t, 'Kp index forecast') !== false)   { $sec = 'kp'; $kp_hdr_seen = false; continue; }

        if ($sec === 'ap') {
            // "Observed Ap 22 May 006" — split at last whitespace before trailing digits/dashes
            if (preg_match('/^(Observed|Estimated|Predicted)\s+Ap\s+(.+?)\s+([\d][\d\-\/]+)\s*$/i', $t, $m)) {
                $ap_data[] = ['type' => $m[1], 'date' => $m[2], 'val' => str_replace('-', ' / ', $m[3])];
            }
        } elseif ($sec === 'prob') {
            // "Active                15/10/15"
            if (preg_match('/^(.+?)\s{2,}([\d\/]+)\s*$/', $t, $m)) {
                $prob_data[] = ['label' => trim($m[1]), 'pcts' => explode('/', trim($m[2]))];
            }
        } elseif ($sec === 'kp') {
            if (!$kp_hdr_seen) {
                // "             May 24    May 25    May 26"
                preg_match_all('/\w+ \d+/', $t, $m);
                $kp_days = $m[0];
                $kp_hdr_seen = true;
            } else {
                // "00-03UT        1.67      1.67      1.33"
                $p = preg_split('/\s+/', $t);
                if (count($p) >= 4) {
                    $kp_rows[] = ['period' => $p[0], 'vals' => array_slice($p, 1, 3)];
                }
            }
        }
    }
}

// ── Derived display values ────────────────────────────────────────────────────
if ($kp >= 7)      { $storm = $lang['SevereStorm']; $kp_color = '#d35d4e'; }
elseif ($kp >= 5)  { $storm = $lang['G1PlusStorm'];  $kp_color = '#ff7c39'; }
elseif ($kp >= 4)  { $storm = $lang['Active'];        $kp_color = '#e6a141'; }
else               { $storm = $lang['Quiet'];          $kp_color = '#90b12a'; }

$is_dark   = ($theme !== 'light');
$bg        = $is_dark ? '#151819' : '#fff';
$bg_chrome = $is_dark ? '#1e2124' : '#f0f2f5';
$bg_card   = $is_dark ? '#252729' : '#e8eaef';
$text      = $is_dark ? '#ddd'    : '#222';
$text_dim  = $is_dark ? '#777'    : '#666';
$border    = $is_dark ? '#2e3033' : '#ccc';
$box_none  = $is_dark ? '#393d40' : '#d0d4db';
$grid_ln   = $is_dark ? '#2a2c2f' : '#e0e2e6';

function rsg_cls(int $s): string {
    if ($s >= 5) return 'mod-aurora-rsg-scale-5';
    if ($s == 4) return 'mod-aurora-rsg-scale-4';
    if ($s == 3) return 'mod-aurora-rsg-scale-3';
    if ($s == 2) return 'mod-aurora-rsg-scale-2';
    if ($s == 1) return 'mod-aurora-rsg-scale-1';
    return 'mod-aurora-rsg-scale-0';
}
function kp_color_val(string $v): string {
    $f = (float)$v;
    if ($f >= 5) return '#d35d4e';
    if ($f >= 4) return '#ff7c39';
    if ($f >= 3) return '#e6a141';
    return '#90b12a';
}

$cb = date('YmdH');
$north_json = json_encode($north_pts);
$south_json = json_encode($south_pts);
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo $lang['SpaceWeather']; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@font-face {
  font-family: weathertext2;
  src: url(css/fonts/verbatim-regular.woff) format("woff"),
       url(css/fonts/verbatim-regular.woff2) format("woff2"),
       url(css/fonts/verbatim-regular.ttf) format("truetype");
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
  height: 100%; overflow: hidden;
  font-family: weathertext2, Arial, sans-serif;
  font-size: 13px;
  background: <?php echo $bg; ?>;
  color: <?php echo $text; ?>;
  -webkit-font-smoothing: antialiased;
}
body { display: flex; flex-direction: column; }

/* ── Header strip ─────────────────────────────────────────────────────────── */
.pop-header {
  flex: 0 0 auto;
  background: <?php echo $bg_chrome; ?>;
  border-bottom: 1px solid <?php echo $border; ?>;
  padding: 5px 10px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pop-title {
  flex: 1;
  font-family: weathertext2, Arial, sans-serif;
  font-size: .8em;
  color: <?php echo $text; ?>;
  letter-spacing: 0.3px;
}
.pop-kp-badge {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .75em;
  padding: 2px 8px;
  border-radius: 3px;
  border-bottom: 3px solid rgba(0,0,0,0.3);
  background: <?php echo $kp_color; ?>;
  color: #fff;
  white-space: nowrap;
}
.pop-prob-badge {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .75em;
  padding: 2px 8px;
  border-radius: 3px;
  border-bottom: 3px solid rgba(0,0,0,0.3);
  background: <?php echo ($aurora_prob >= 50) ? '#d35d4e' : (($aurora_prob >= 25) ? '#ff7c39' : (($aurora_prob >= 10) ? '#e6a141' : (($aurora_prob > 0) ? '#90b12a' : $box_none))); ?>;
  color: #fff;
  white-space: nowrap;
}
.pop-storm {
  font-size: .65em;
  color: <?php echo $text_dim; ?>;
  white-space: nowrap;
  margin-right: 50px;
}
.pop-rsg-mini { display: flex; gap: 3px; margin-right: 50px; }
.mod-aurora-rsg-mini {
  width: 22px; height: 28px;
  border-radius: 2px;
  border-bottom: 3px solid rgba(0,0,0,0.3);
  display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 1px;
}
.mod-aurora-rsg-mini-letter { font-size: 12px; font-weight: bold; color: #fff; line-height: 1; font-family: weathertext2, Arial; }
.mod-aurora-rsg-mini-val    { font-size: 7px; color: rgba(255,255,255,0.8); }
.mod-aurora-rsg-scale-0 { background: <?php echo $box_none; ?>; }
.mod-aurora-rsg-scale-1 { background: #ebd825; }
.mod-aurora-rsg-scale-2 { background: #e6a141; }
.mod-aurora-rsg-scale-3 { background: #ff7c39; }
.mod-aurora-rsg-scale-4 { background: #d35d4e; }
.mod-aurora-rsg-scale-5 { background: #8d2315; }

.mod-aurora-rsg-scale-1 .mod-aurora-rsg-mini-letter,
.mod-aurora-rsg-scale-2 .mod-aurora-rsg-mini-letter,
.mod-aurora-rsg-scale-3 .mod-aurora-rsg-mini-letter {
  color: #111111;
}

.mod-aurora-rsg-scale-1 .mod-aurora-rsg-mini-val,
.mod-aurora-rsg-scale-2 .mod-aurora-rsg-mini-val,
.mod-aurora-rsg-scale-3 .mod-aurora-rsg-mini-val {
  color: rgba(0, 0, 0, 0.75);
}

/* ── Tab row — matches pop_menu_forecast.php style exactly ───────────────── */
.pop-tabs {
  flex: 0 0 auto;
  background: <?php echo $bg_chrome; ?>;
  border-bottom: 1px solid <?php echo $border; ?>;
  padding: 4px 5px;
  display: flex;
  flex-wrap: wrap;
  gap: 0;
}
.tablink {
  background-color: #555;
  color: white;
  border: 2px solid <?php echo $bg_chrome; ?>;
  border-radius: 5px;
  margin-top: 0;
  margin-left: 5px;
  outline: none;
  cursor: pointer;
  padding: 5px 8px;
  font-size: 10px;
  font-family: Arial, sans-serif;
}
.tablink:hover { background-color: #777; }

/* ── Content wrapper ─────────────────────────────────────────────────────── */
.pop-content {
  flex: 1;
  min-height: 0;
  position: relative;
  overflow: hidden;
}
.tabcontent {
  display: none;
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  overflow: hidden;
}

/* ── Shared image layout (Tab 1 + Tab 4) ─────────────────────────────────── */
.img-row {
  display: flex;
  gap: 8px;
  padding: 8px;
  height: 100%;
}
.img-panel {
  flex: 1; min-width: 0;
  display: flex; flex-direction: column; gap: 4px;
}
.img-frame {
  flex: 1; min-height: 0;
  position: relative;
  border-radius: 3px;
  border: 1px solid <?php echo $border; ?>;
  background: <?php echo $bg_chrome; ?>;
  overflow: hidden;
}
.img-frame img {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  object-fit: contain; display: block;
}
.img-label {
  flex-shrink: 0;
  font-size: .65em; color: silver;
  text-align: center; text-transform: uppercase; letter-spacing: 0.5px;
}

/* ── Tab 2: 3-day forecast ───────────────────────────────────────────────── */
.forecast-body {
  height: 100%; overflow-y: auto;
  padding: 8px 10px;
  display: flex; flex-direction: column; gap: 8px;
}
.fcst-card {
  background: <?php echo $bg_card; ?>;
  border-radius: 3px; padding: 8px 10px; flex-shrink: 0;
}
.fcst-card-title {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .65em; color: silver;
  text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
}
.fcst-table {
  width: 100%; border-collapse: collapse;
  font-size: .7em; font-family: Arial, sans-serif;
}
.fcst-table th {
  color: <?php echo $text_dim; ?>; font-weight: normal;
  padding: 3px 8px; text-align: right; border-bottom: 1px solid <?php echo $border; ?>;
}
.fcst-table td { padding: 3px 8px; text-align: right; color: <?php echo $text; ?>; }
.fcst-table th:first-child, .fcst-table td:first-child { text-align: left; }
.fcst-table tbody tr:nth-child(even) td { background: rgba(128,128,128,0.06); }
.fcst-issued {
  font-size: .6em; color: <?php echo $text_dim; ?>;
  text-align: right; padding: 2px 0;
}

/* ── Tab 3: Hemisphere Power chart ──────────────────────────────────────── */
#hemi-chart-wrap { width: 100%; height: 100%; padding: 6px 8px; }
#hemi-chart { width: 100%; height: 100%; }
.unavail {
  display: flex; align-items: center; justify-content: center;
  height: 100%; font-size: .8em; color: <?php echo $text_dim; ?>;
  font-family: Arial, sans-serif;
}
</style>
</head>
<body>

<!-- Header: title + Kp badge + storm + RSG mini-badges -->
<div class="pop-header">
  <span class="pop-title"><?php echo $lang['SpaceWeather']; ?></span>
  <span class="pop-prob-badge"><?php echo $lang['Ovation']; ?>&nbsp;<?php echo $prob_ok ? $aurora_prob . '%' : '--'; ?></span>
  <span class="pop-kp-badge"><?php echo $lang['Kp']; ?>&nbsp;<?php echo number_format($kp, 1); ?></span>
  <span class="pop-storm"><?php echo ucfirst($kp_type); ?> &mdash; <?php echo $storm; ?></span>
  <div class="pop-rsg-mini">
    <?php foreach (['R', 'S', 'G'] as $k):
      $sc = $rsg[$k]; $cls = rsg_cls($sc); $lbl = $sc > 0 ? ($k.$sc) : '&mdash;';
    ?>
    <div class="mod-aurora-rsg-mini <?php echo $cls; ?>">
      <span class="mod-aurora-rsg-mini-letter"><?php echo $k; ?></span>
      <span class="mod-aurora-rsg-mini-val"><?php echo $lbl; ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Tab buttons -->
<div class="pop-tabs">
  <button class="tablink" onclick="openTab('t1',this)" id="defaultOpen"><?php echo $lang['ViewLine']; ?></button>
  <button class="tablink" onclick="openTab('t2',this)"><?php echo $lang['Forecast3Day']; ?></button>
  <button class="tablink" onclick="openTab('t3',this)"><?php echo $lang['HemPower']; ?></button>
  <button class="tablink" onclick="openTab('t4',this)"><?php echo $lang['Ovation']; ?></button>
</div>

<!-- Content panels -->
<div class="pop-content">

  <!-- Tab 1: Tonight + Tomorrow viewline -->
  <div id="t1" class="tabcontent">
    <div class="img-row">
      <div class="img-panel">
        <div class="img-frame">
          <img src="https://services.swpc.noaa.gov/experimental/images/aurora_dashboard/tonights_static_viewline_forecast.png?t=<?php echo $cb; ?>" alt="<?php echo $lang['Tonight']; ?>">
        </div>
        <div class="img-label"><?php echo $lang['Tonight']; ?></div>
      </div>
      <div class="img-panel">
        <div class="img-frame">
          <img src="https://services.swpc.noaa.gov/experimental/images/aurora_dashboard/tomorrow_nights_static_viewline_forecast.png?t=<?php echo $cb; ?>" alt="<?php echo $lang['Tomorrownight']; ?>">
        </div>
        <div class="img-label"><?php echo $lang['Tomorrownight']; ?></div>
      </div>
    </div>
  </div>

  <!-- Tab 2: 3-day forecast text parsed into tables -->
  <div id="t2" class="tabcontent">
    <div class="forecast-body">
      <?php if (!$forecast_ok): ?>
        <div class="unavail"><?php echo $lang['ForecastUnavailable']; ?></div>
      <?php else: ?>

      <?php if ($ap_data): ?>
      <div class="fcst-card">
        <div class="fcst-card-title"><?php echo $lang['ApIndex']; ?></div>
        <table class="fcst-table">
          <thead><tr><th><?php echo $lang['Type']; ?></th><th><?php echo $lang['Date']; ?></th><th><?php echo $lang['Ap']; ?></th></tr></thead>
          <tbody>
            <?php foreach ($ap_data as $r): ?>
            <tr>
              <td><?php echo htmlspecialchars($r['type']); ?></td>
              <td><?php echo htmlspecialchars($r['date']); ?></td>
              <td><?php echo htmlspecialchars($r['val']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

      <?php if ($prob_data && $kp_days): ?>
      <div class="fcst-card">
        <div class="fcst-card-title"><?php echo $lang['GeomagneticActivityProbabilities']; ?></div>
        <table class="fcst-table">
          <thead>
            <tr>
              <th><?php echo $lang['Level']; ?></th>
              <?php foreach ($kp_days as $d): ?><th><?php echo htmlspecialchars($d); ?></th><?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($prob_data as $r): ?>
            <tr>
              <td><?php echo htmlspecialchars($r['label']); ?></td>
              <?php foreach ($r['pcts'] as $pct): ?>
              <td><?php echo htmlspecialchars(trim($pct)); ?>%</td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

      <?php if ($kp_rows && $kp_days): ?>
      <div class="fcst-card">
        <div class="fcst-card-title"><?php echo $lang['KpIndexForecast']; ?></div>
        <table class="fcst-table">
          <thead>
            <tr>
              <th><?php echo $lang['Period']; ?></th>
              <?php foreach ($kp_days as $d): ?><th><?php echo htmlspecialchars($d); ?></th><?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($kp_rows as $r): ?>
            <tr>
              <td><?php echo htmlspecialchars($r['period']); ?></td>
              <?php foreach ($r['vals'] as $v): $col = kp_color_val(trim($v)); ?>
              <td style="color:<?php echo $col; ?>;font-weight:<?php echo (float)$v >= 4 ? '600' : 'normal'; ?>">
                <?php echo htmlspecialchars(trim($v)); ?>
              </td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>

      <?php if ($forecast_issued): ?>
      <div class="fcst-issued"><?php echo $lang['Issued']; ?>: <?php echo htmlspecialchars($forecast_issued); ?> &middot; NOAA SWPC</div>
      <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>

  <!-- Tab 3: Hemisphere power Highcharts -->
  <div id="t3" class="tabcontent">
    <?php if (!$power_ok || empty($north_pts)): ?>
      <div class="unavail"><?php echo $lang['HemispherePowerUnavailable']; ?></div>
    <?php else: ?>
      <div id="hemi-chart-wrap">
        <div id="hemi-chart"></div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Tab 4: North + South ovation images -->
  <div id="t4" class="tabcontent">
    <div class="img-row">
      <div class="img-panel">
        <div class="img-frame">
          <img src="https://services.swpc.noaa.gov/images/animations/ovation/north/latest.jpg?t=<?php echo $cb; ?>" alt="<?php echo $lang['NorthernHemisphereOvation']; ?>">
        </div>
        <div class="img-label"><?php echo $lang['NorthernHemisphereOvation']; ?></div>
      </div>
      <div class="img-panel">
        <div class="img-frame">
          <img src="https://services.swpc.noaa.gov/images/animations/ovation/south/latest.jpg?t=<?php echo $cb; ?>" alt="<?php echo $lang['SouthernHemisphereOvation']; ?>">
        </div>
        <div class="img-label"><?php echo $lang['SouthernHemisphereOvation']; ?></div>
      </div>
    </div>
  </div>

</div><!-- .pop-content -->

<script>
// ── Tab switching ─────────────────────────────────────────────────────────────
function openTab(name, el) {
  document.querySelectorAll('.tabcontent').forEach(function(t){ t.style.display = 'none'; });
  document.querySelectorAll('.tablink').forEach(function(t){ t.style.backgroundColor = ''; });
  document.getElementById(name).style.display = 'block';
  el.style.backgroundColor = 'rgba(194, 102, 58)';
  if (name === 't3') initHemiChart();
}
document.getElementById('defaultOpen').click();

// ── Hemisphere power chart — lazy-loads Highcharts on first tab click ─────────
var hemiChart = null;
function initHemiChart() {
  if (hemiChart) { hemiChart.reflow(); return; }
  if (typeof Highcharts !== 'undefined') { buildChart(); return; }
  var s = document.createElement('script');
  s.src = 'https://code.highcharts.com/highcharts.js';
  s.onload = buildChart;
  document.head.appendChild(s);
}

function buildChart() {
  hemiChart = Highcharts.chart('hemi-chart', {
    chart: {
      type: 'area',
      backgroundColor: '<?php echo $bg; ?>',
      style: { fontFamily: 'Arial, sans-serif' },
      margin: [24, 16, 46, 52],
      animation: false
    },
    title: { text: null },
    credits: { enabled: false },
    legend: {
      enabled: true, align: 'right', verticalAlign: 'top',
      itemStyle: { color: '<?php echo $text_dim; ?>', fontWeight: 'normal', fontSize: '10px' }
    },
    xAxis: {
      type: 'datetime',
      labels: { style: { color: '<?php echo $text_dim; ?>' }, format: '{value:%H:%M}', step: 3 },
      lineColor: '<?php echo $border; ?>', tickColor: '<?php echo $border; ?>',
      title: { text: '<?php echo $lang['ObservationTimeUTC']; ?>', style: { color: '<?php echo $text_dim; ?>', fontSize: '9px' } }
    },
    yAxis: {
      title: { text: 'GW', style: { color: '<?php echo $text_dim; ?>', fontSize: '10px' } },
      labels: { style: { color: '<?php echo $text_dim; ?>' } },
      gridLineColor: '<?php echo $grid_ln; ?>',
      min: 0
    },
    tooltip: {
      shared: true, xDateFormat: '%H:%M UTC', valueSuffix: ' GW',
      backgroundColor: '<?php echo $bg_chrome; ?>',
      borderColor: '<?php echo $border; ?>',
      style: { color: '<?php echo $text; ?>', fontSize: '10px' }
    },
    plotOptions: {
      area: { marker: { enabled: false }, fillOpacity: 0.12, lineWidth: 1.5 }
    },
    series: [
      { name: '<?php echo $lang['NorthGW']; ?>', color: '#90b12a', data: <?php echo $north_json; ?> },
      { name: '<?php echo $lang['SouthGW']; ?>', color: '#e6a141', data: <?php echo $south_json; ?> }
    ]
  });
}
</script>
</body>
</html>
