<?php include('w34CombinedData.php'); include('top_year_helpers.php');
header('Content-type: text/html; charset=utf-8'); date_default_timezone_set($TZ);
if ($weather["wind_units"] == 'kts') { $weather["wind_units"] = 'kn'; }
$u = $weather["wind_units"];
?>
<div class="mod-topyear">
<?php
echo yt_side(yt_wind_class($weather["windmmax"], $u), date('M'), $weather["windmmax"], $u, $weather["windmmaxtime2"]);
echo '<div class="yt-year">' . date('Y') . '</div>';
echo yt_side(yt_wind_class($weather["windymax"], $u), date('Y'), $weather["windymax"], $u, $weather["windymaxtime2"]);
?>
</div><!-- /mod-topyear -->
