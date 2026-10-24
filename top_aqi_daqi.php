<?php
include_once('w34_i18n.php');
include_once('w34CombinedData.php');
include_once('top_year_helpers.php');
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set($TZ);
error_reporting(0);

// US AQI sub-indices (the bands in yt_daqi_info are the US AQI ones): PM2.5 measured at the nearest WAQI monitor,
// PM10 from the Open-Meteo air-quality model (that monitor has no PM10), as on the Air Quality card.
$u = 'AQI';
$aq = @json_decode(@file_get_contents('jsondata/aq.txt'), true);
$om = @json_decode(@file_get_contents('jsondata/om.txt'), true);
$fresh = function ($f) { return file_exists($f) && time() - filemtime($f) < 10800; };
$pm25 = $fresh('jsondata/aq.txt') && isset($aq['data']['iaqi']['pm25']['v']) ? round((float)$aq['data']['iaqi']['pm25']['v']) : null;
$pm10 = $fresh('jsondata/om.txt') && isset($om['current']['us_aqi_pm10']) ? round((float)$om['current']['us_aqi_pm10']) : null;
list($cls25, $band25) = $pm25 === null ? ['', '--'] : yt_daqi_info($pm25);
list($cls10, $band10) = $pm10 === null ? ['', '--'] : yt_daqi_info($pm10);
?>
<div class="mod-topyear">
<?php
echo yt_side($cls25, 'PM<sub>2.5</sub>', $pm25 ?? '--', $u, $band25);
echo '<div class="yt-year"><a alt="DAQI air quality info" title="AQI air quality info" href="pop_aqinfo.php" data-lity>DAQI</a></div>';
echo yt_side($cls10, 'PM<sub>10</sub> <span title="' . htmlspecialchars(w34t('AqModelTip', 'Estimate from the Open-Meteo air-quality model (CAMS): the nearest monitor does not measure this')) . '">' . w34t('AqEstShort', 'est') . '</span>', $pm10 ?? '--', $u, $band10);
?>
</div><!-- /mod-topyear -->
 
