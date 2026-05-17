<?php
include('w34CombinedData.php');
include('settings.php');
date_default_timezone_set($TZ);
header('Content-type: text/html; charset=utf-8');
error_reporting(0);
?>
<body>
<?php
// Update time — sits top-right of the box title via absolute positioning
$isOffline = file_exists($livedata) && time() - filemtime($livedata) > 300;
echo '<span style="position:absolute;top:2px;right:5px;font:0.6em arial,helvetica;color:silver;line-height:1;white-space:nowrap;">';
if ($isOffline) echo '<offline>Offline</offline>';
else echo $online . ' ' . $weather["time"];
echo '</span>';
?>

<div class="wfstrike">
  <?php echo "<wfstriketoday>" . $lightning['strike_count_3hr']; ?>
</wfstriketoday>
</div>
<div class="minwordl">Strikes</div></div>
<div class="mintimedatex"><value>&nbsp;<a href="pop_lightningalmanac.php" data-lity style="color:inherit;text-decoration:none;">Last 3 Hrs</a></value></div>

<div class='wflaststrike'>
<?php
if ($lightning['last_time'] >= 1) {
    echo "<spanfeelstitle>Last Strike: <orange> " . date("j M Y", $lightning['last_time']) . " </orange></spanfeelstitle><br />";
}
if ($windunit == 'mph') {
    echo "<spanfeelstitle>Distance: <orange> " . number_format($lightning['light_last_distance'] * 0.621371, 1) . " </orange>mi</spanfeelstitle>";
} else {
    echo "<spanfeelstitle>Distance: <orange> " . $lightning['light_last_distance'] . " </orange>km</spanfeelstitle>";
}
echo "<br />";
echo "<spanfeelstitle>Month: <orange> " . str_replace(",", "", $weather["lightningmonth"]) . " </orange>";
echo "&nbsp; Year: <orange> " . str_replace(",", "", $weather["lightningyear"]) . " </orange>";
echo "&nbsp; All-time: <orange> " . $lightning['strike_count'] . " </orange></spanfeelstitle><br>";
if (!is_null($weather["solar"])) {
    echo "<spanfeelstitle>Solar: <orange> " . $weather["solar"] . " </orange>W/m&sup2;</spanfeelstitle>";
}
?>
</div>

<div class="lightningicon">
<?php
if ($lightning['strike_count_3hr'] > 0) {
    echo '<img src="img/lightningalert.svg" width="20" height="20" align="right"/>';
}
?>
</div>
