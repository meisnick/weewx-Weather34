<?php
// pop_localforecast.php — Hyperlocal Nowcast Verification Details (Lity Lightbox Popup)
include('w34CombinedData.php');
include_once('settings1.php');
error_reporting(0);

$file_path = 'jsondata/local_forecast.json';
$data_ok = false;
$forecast_sentence = 'No nowcast available.';
$forecast_intervals = [];
$analogues = [];
$generated_utc = '--';

if (file_exists($file_path)) {
    $raw = @file_get_contents($file_path);
    $data = @json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $forecast_sentence = $data['forecast'] ?? '';
        $forecast_intervals = $data['forecast_intervals'] ?? [];
        $analogues = $data['analogues'] ?? [];
        $generated_utc = $data['generated_utc'] ?? '--';
        $data_ok = true;
    }
}

// Dynamic Winter Precipitation Check
$is_freezing = false;
if ($data_ok) {
    $temp_f = $data['current_temp_f'] ?? null;
    if ($temp_f !== null && $temp_f <= 32.0) {
        $is_freezing = true;
    }
}
$precip_name = $is_freezing ? 'Snow' : 'Rain';

$is_dark   = ($theme !== 'light');
$bg        = $is_dark ? '#151819' : '#fff';
$bg_chrome = $is_dark ? '#1e2124' : '#f0f2f5';
$bg_card   = $is_dark ? '#252729' : '#e8eaef';
$text      = $is_dark ? '#ddd'    : '#222';
$text_dim  = $is_dark ? '#777'    : '#666';
$border    = $is_dark ? '#2e3033' : '#ccc';

// Unit conversion helper functions
function formatTemp($temp_f, $unit) {
    if ($unit === 'C') {
        return round(($temp_f - 32) * 5 / 9, 1);
    }
    return round($temp_f, 1);
}

function formatStdDev($std_f, $unit) {
    if ($unit === 'C') {
        return round($std_f * 5 / 9, 1);
    }
    return round($std_f, 1);
}

function formatWindSpeed($mph, $unit) {
    if ($unit === 'km/h') {
        return round(1.609344 * $mph, 1);
    } elseif ($unit === 'kts') {
        return round(0.868976 * $mph, 1);
    } elseif ($unit === 'm/s') {
        return round(0.44704 * $mph, 1);
    }
    return round($mph, 1);
}

function formatPressure($inHg, $unit) {
    if ($unit === 'hPa' || $unit === 'mb') {
        return round($inHg * 33.863886666667, 1);
    } elseif ($unit === 'kPa') {
        return round($inHg * 3.3863886666667, 2);
    }
    return round($inHg, 2);
}

function formatPressureDiff($diff_inHg, $unit) {
    if ($diff_inHg === null) return 'steady';
    if ($unit === 'hPa' || $unit === 'mb') {
        $val = $diff_inHg * 33.863886666667;
        return ($val >= 0 ? '+' : '') . number_format($val, 2);
    } elseif ($unit === 'kPa') {
        $val = $diff_inHg * 3.3863886666667;
        return ($val >= 0 ? '+' : '') . number_format($val, 3);
    }
    return ($diff_inHg >= 0 ? '+' : '') . number_format($diff_inHg, 3);
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hyperlocal Nowcast Details</title>
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
  font-family: Arial, sans-serif;
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
  justify-content: space-between;
}
.pop-title {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .8em;
  color: <?php echo $text; ?>;
  letter-spacing: 0.3px;
}
.pop-issued {
  font-size: .65em;
  color: <?php echo $text_dim; ?>;
  white-space: nowrap;
  margin-right: 50px; /* clear lity close button */
}

/* ── Tab row ──────────────────────────────────────────────────────────────── */
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

/* ── Content layout ──────────────────────────────────────────────────────── */
.discussion-body {
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 8px 10px;
}
.fcst-card {
  background: <?php echo $bg_card; ?>;
  border-radius: 3px;
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
}
.fcst-card-title {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .75em;
  color: silver;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
  flex: 0 0 auto;
}
.fcst-card-text {
  flex: 1;
  overflow-y: auto;
  font-family: Arial, sans-serif;
  font-size: .85em;
  color: <?php echo $text; ?>;
  line-height: 1.5;
  padding-right: 2px;
  
  /* Hide scrollbar completely */
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.fcst-card-text::-webkit-scrollbar {
  display: none;
}

/* ── Desaturated Table Style ────────────────────────────────────────────── */
.pop-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 5px;
  font-size: 0.85em;
  text-align: left;
}
.pop-table th, .pop-table td {
  padding: 6px 8px;
  border-bottom: 1px solid <?php echo $border; ?>;
  vertical-align: middle;
}
.pop-table th {
  font-family: weathertext2, Arial, sans-serif;
  color: silver;
  text-transform: uppercase;
  font-size: 0.72em;
  letter-spacing: 0.4px;
  background: rgba(0, 0, 0, 0.12);
  border-top: 1px solid <?php echo $border; ?>;
}
.pop-table tr:hover td {
  background: rgba(255, 255, 255, 0.02);
}
[data-theme="light"] .pop-table tr:hover td {
  background: rgba(0, 0, 0, 0.015);
}

.nowcast-bubble {
  background: rgba(33, 34, 39, 0.25);
  border: 1px solid <?php echo $border; ?>;
  border-radius: 4px;
  padding: 10px 12px;
  font-style: italic;
  font-size: 0.9em;
  line-height: 1.45;
  margin-bottom: 10px;
  color: #fff;
}
[data-theme="light"] .nowcast-bubble {
  background: rgba(240, 242, 245, 0.6);
  color: #222;
}

.rain-green { color: #90b12a; font-weight: bold; }
.rain-blue  { color: #4a8b9f; font-weight: bold; }
.rain-amber { color: #e6a141; font-weight: bold; }
.rain-red   { color: #d35d4e; font-weight: bold; }
</style>
</head>
<body>

<!-- Header -->
<div class="pop-header">
  <span class="pop-title">Hyperlocal Short-Term Nowcast</span>
  <span class="pop-issued">As of: <?php echo htmlspecialchars($generated_utc); ?></span>
</div>

<!-- Tab buttons -->
<div class="pop-tabs">
  <button class="tablink" onclick="openTab('t1', this)" id="defaultOpen">Statistical Forecast</button>
  <button class="tablink" onclick="openTab('t2', this)">Top 15 Analogues</button>
</div>

<!-- Content panels -->
<div class="pop-content">

  <!-- Tab 1: Statistical Forecast -->
  <div id="t1" class="tabcontent">
    <div class="discussion-body">
      <div class="fcst-card">
        <div class="fcst-card-title">6-Hour Forecast Trajectory</div>
        <div class="fcst-card-text">
          <div class="nowcast-bubble">
            “ <?php echo htmlspecialchars($forecast_sentence); ?> ”
          </div>
          
          <table class="pop-table">
            <thead>
              <tr>
                <th>Interval</th>
                <th>Temp Range (Min–Max)</th>
                <th>Mean Temp</th>
                <th><?php echo $precip_name; ?> Probability</th>
                <th>Matches</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($forecast_intervals)): ?>
                <tr><td colspan="5" style="text-align:center;padding:20px;color:gray;">No forecast intervals available.</td></tr>
              <?php else: 
                foreach ($forecast_intervals as $h => $f): 
                  $rain = intval($f['rain_pct']);
                  $r_style = 'rain-green';
                  if ($rain > 0 && $rain < 30) $r_style = 'rain-blue';
                  elseif ($rain >= 30 && $rain < 60) $r_style = 'rain-amber';
                  elseif ($rain >= 60) $r_style = 'rain-red';
                  
                  $min_val = formatTemp($f['min_f'], $tempunit);
                  $max_val = formatTemp($f['max_f'], $tempunit);
                  $mean_val = formatTemp($f['mean_f'], $tempunit);
                  $std_val = formatStdDev($f['std_f'], $tempunit);
              ?>
                <tr>
                  <td><strong>+<?php echo $h; ?> Hours</strong></td>
                  <td><?php echo $min_val; ?>&deg;<?php echo $tempunit; ?> &ndash; <?php echo $max_val; ?>&deg;<?php echo $tempunit; ?></td>
                  <td><strong><?php echo $mean_val; ?>&deg;<?php echo $tempunit; ?></strong> (std-dev: &plusmn;<?php echo $std_val; ?>&deg;<?php echo $tempunit; ?>)</td>
                  <td><span class="<?php echo $r_style; ?>"><?php echo $rain; ?>%</span></td>
                  <td><?php echo $f['n']; ?> matches</td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Tab 2: Top 15 Analogues -->
  <div id="t2" class="tabcontent">
    <div class="discussion-body">
      <div class="fcst-card">
        <div class="fcst-card-title">Top 15 Historical Matching Hours (from features.db)</div>
        <div class="fcst-card-text">
          <table class="pop-table">
            <thead>
              <tr>
                <th>Historical Date &amp; Time</th>
                <th>Distance</th>
                <th>Observed Temp</th>
                <th>Observed Pressure</th>
                <th>3h Trend</th>
                <th>Observed Wind</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($analogues)): ?>
                <tr><td colspan="6" style="text-align:center;padding:20px;color:gray;">No historical analogues matched yet.</td></tr>
              <?php else: 
                foreach ($analogues as $a): 
                  $dist = floatval($a['distance']);
                  $d_col = $is_dark ? '#eee' : '#111';
                  if ($dist < 0.8) $d_col = $is_dark ? '#90b12a' : '#5e8210';
                  elseif ($dist < 1.0) $d_col = $is_dark ? '#e6a141' : '#b27214';
                  
                  $temp_val = formatTemp($a['temp_f'], $tempunit);
                  $press_val = formatPressure($a['pressure'], $pressureunit);
                  $dp_val = formatPressureDiff($a['dp_3h'], $pressureunit);
                  $wind_val = formatWindSpeed($a['wind_mph'], $windunit);
              ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($a['date']); ?></strong></td>
                  <td><span style="color:<?php echo $d_col; ?>;font-weight:bold;"><?php echo number_format($dist, 3); ?></span></td>
                  <td><?php echo $temp_val; ?>&deg;<?php echo $tempunit; ?></td>
                  <td><?php echo $press_val; ?> <?php echo $pressureunit; ?></td>
                  <td><?php echo $dp_val; ?></td>
                  <td><?php echo htmlspecialchars($a['wind_label']); ?> @ <?php echo $wind_val; ?> <?php echo $windunit; ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
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
}
document.getElementById('defaultOpen').click();
</script>

</body>
</html>
