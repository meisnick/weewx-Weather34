<?php include_once('w34_i18n.php'); ?>
<?php include('w34CombinedData.php'); include('top_year_helpers.php');
header('Content-type: text/html; charset=utf-8'); date_default_timezone_set($TZ);
$u = $weather["temp_units"];
?>
<div class="mod-topyear">
<?php
echo yt_side(yt_temp_class($weather["tempymin"], $u), w34t('MinLabel', 'Min'), $weather["tempymin"] . '&deg;', $u, $weather["tempymintime2"]);
echo '<div class="yt-year">' . date('Y') . '</div>';
echo yt_side(yt_temp_class($weather["tempymax"], $u), w34t('MaxLabel', 'Max'), $weather["tempymax"] . '&deg;', $u, $weather["tempymaxtime2"]);
?>
</div><!-- /mod-topyear -->
