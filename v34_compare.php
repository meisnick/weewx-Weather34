<?php
/* v34_compare.php — per-module visual parity harness (DEV ONLY)
 * Usage: /v34_compare.php?module=wind&mode=ref|mod&freeze=1
 *   ref : reference — full main.dark.css + all css/modules/*.css (like index.php)
 *   mod : modular    — framework.dark.css + all css/modules/*.css (no main.dark/light)
 *   freeze=1 normalizes the live clock so ref/mod are comparable.
 */
$mode   = $_GET['mode'] ?? 'ref';
$freeze = isset($_GET['freeze']) && $_GET['freeze'] === '1';
$module = preg_replace('/[^a-z0-9\-]/i', '', $_GET['module'] ?? 'wind');
$theme  = $_GET['theme'] ?? 'dark';
$theme1 = ($theme === 'light') ? 'light' : 'dark';

$css_main = ($mode === 'ref')
    ? 'css/main.' . $theme1 . '.css'
    : 'css/framework.' . $theme1 . '.css';

$modmap = [
    'wind'              => 'css/modules/wind.css',
    'barometer'         => 'css/modules/barometer.css',
    'rainfall'          => 'css/modules/rainfall.css',
    'temperature'       => 'css/modules/temperature.css',
    'indoor'            => 'css/modules/temperature.css',
    'moonphase'         => 'css/modules/moonphase.css',
    'sun'               => 'css/modules/sun.css',
    'lightning34'       => 'css/modules/lightning34.css',
    'conditions'        => 'css/modules/conditions.css',
    'forecast'          => 'css/modules/forecast.css',
    'forecastlarge'     => 'css/modules/forecast.css',
    'forecastdiscussion'=> 'css/modules/forecastdiscussion.css',
    'localforecast'     => 'css/modules/localforecast.css',
    'aurora'            => 'css/modules/aurora.css',
    'clock'             => 'css/modules/clock.css',
    'advisory'          => 'css/modules/advisory.css',
    'windgustyear'      => 'css/modules/topyears.css',
    'temperatureyear'   => 'css/modules/topyears.css',
    'top-lightning'     => 'css/modules/top-lightning.css',
    'rain-totals'       => 'css/modules/rain-totals.css',
    'airqualitymodule'  => 'css/modules/airqualitymodule.css',
    'radar'             => 'css/modules/radar.css',
    'solaruv'           => 'css/modules/solar.css',
];
$modcss = $modmap[$module] ?? '';

$phpfile = [
    'wind' => 'windspeeddirection.php',
    'barometer' => 'barometer.php',
    'rainfall' => 'rainfall.php',
    'temperature' => 'temperaturein.php',
    'indoor' => 'indoortemperature.php',
    'moonphase' => 'moonphase.php',
    'sun' => 'sun3.php',
    'lightning34' => 'lightning34.php',
    'conditions' => 'currentconditionsw34.php',
    'forecast' => 'forecast3om.php',
    'forecastlarge' => 'forecast3omlarge.php',
    'forecastdiscussion' => 'forecastdiscussion.php',
    'localforecast' => 'localforecast.php',
    'aurora' => 'aurora_module.php',
    'clock' => 'weather34clock.php',
    'advisory' => 'top_advisory_nws.php',
    'windgustyear' => 'top_windgustyear.php',
    'temperatureyear' => 'top_temperatureyear.php',
    'top-lightning' => 'top_lightning.php',
    'rain-totals' => 'top_rainfallfyearmonth.php',
    'airqualitymodule' => 'airqualitymodule.php',
    'radar' => 'radar_module.php',
    'solaruv' => 'solaruv.php',
];
$phpsrc = $phpfile[$module] ?? '';
?>
<!DOCTYPE html>
<html data-theme="<?php echo $theme1; ?>" style="background:#222;color:#ccc;">
<head>
<meta charset="utf-8">
<link href="<?php echo $css_main; ?>" rel="stylesheet">
<?php if ($modcss): ?><link href="<?php echo $modcss; ?>" rel="stylesheet"><?php endif; ?>
<style>
  body { margin:0; padding:0; }
  #harness-frame { width:320px; padding:0; background:#26262b; }
</style>
</head>
<body>
<div id="harness-frame" class="weather-item">
  <div class="moduletitle"><?php echo htmlspecialchars($module); ?></div>
  <br>
  <div id="grid_0">
    <?php
      if ($phpsrc && file_exists($phpsrc)) {
        if ($freeze) { ob_start(); include($phpsrc); $html = ob_get_clean(); }
        else         { include($phpsrc); }
      }
    ?>
  </div>
</div>
<?php if ($freeze): ?>
<script>
  // Normalize live clock text inside .updatedtime / .updatedtime1 for parity
  function w34Freeze() {
    document.querySelectorAll('[class*=updatedtime]').forEach(function(el) {
      el.textContent = '--';
      if (el.firstChild && el.firstChild.nodeType === 3) el.firstChild.nodeValue = '--';
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', w34Freeze);
  } else { w34Freeze(); }
</script>
<?php endif; ?>
</body>
</html>
