<?php include_once('w34_i18n.php'); ?>
<?php include_once('w34CombinedData.php'); include('top_year_helpers.php');
header('Content-type: text/html; charset=utf-8'); date_default_timezone_set($TZ);
$mn = $weather["rain_month"] >= 1000 ? round($weather["rain_month"], 0) : $weather["rain_month"];
$yr = $weather["rain_year"]  >= 1000 ? round($weather["rain_year"], 0)  : $weather["rain_year"];
$u  = $weather["rain_units"];
?>
<div class="mod-rain-totals">
<?php
echo yt_side('yt-blue', w34_date('M'), $mn, $u, w34t('Total', 'Total'));
echo '<div class="yt-year">' . date('Y') . '</div>';
echo yt_side('yt-blue', date('Y'), $yr, $u, w34t('Total', 'Total'));
?>
</div><!-- /mod-rain-totals -->
