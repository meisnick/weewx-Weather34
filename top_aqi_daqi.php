<?php
include_once('w34_i18n.php');
include_once('w34CombinedData.php');
include_once('top_year_helpers.php');
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set($TZ);
error_reporting(0);

$u = 'μg/m³';
$json_string = @file_get_contents('jsondata/aqiJson.txt');
$parsed_json = @json_decode($json_string, true);
$pm25 = round((float)($parsed_json['pm25'] ?? 0), 0);
$pm10 = round((float)($parsed_json['pm10'] ?? 0), 0);
list($cls25, $band25) = yt_daqi_info($pm25);
list($cls10, $band10) = yt_daqi_info($pm10);
?>
<div class="mod-topyear">
<?php
echo yt_side($cls25, 'PM<sub>2.5</sub>', $pm25, $u, $band25);
echo '<div class="yt-year"><a alt="DAQI air quality info" title="AQI air quality info" href="pop_aqinfo.php" data-lity>DAQI</a></div>';
echo yt_side($cls10, 'PM<sub>10</sub>', $pm10, $u, $band10);
?>
</div><!-- /mod-topyear -->
 
