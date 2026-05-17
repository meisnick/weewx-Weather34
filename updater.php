<!-- begin updater.php -->
<?php
include_once('settings1.php');
include_once('settings.php');
include_once('common.php');
date_default_timezone_set($TZ);
if (!file_exists('modules.php')) { copy('modules.example.php', 'modules.php'); }
include('modules.php');
?>
<script src="js/jquery.js"></script>
<script>
<?php
// Build unified module list with div IDs
$all_modules = [];
foreach ($topbar_modules as $i => $mod) {
    $all_modules[] = ['id' => 'top_' . $i, 'module' => $mod['module'], 'refresh' => (int)$mod['refresh']];
}
foreach ($grid_modules as $i => $mod) {
    $file = $mod['module'];
    // Night substitution for webcam
    if ($file === 'webcamsmall.php' && $dayPartCivil === 'night') { $file = 'moonphase.php'; }
    $all_modules[] = ['id' => 'grid_' . $i, 'module' => $file, 'refresh' => (int)$mod['refresh']];
}

foreach ($all_modules as $mod):
    $ms = $mod['refresh'] * 1000;
?>
(function($){ $(document).ready(function(){
    var el = $("#<?php echo $mod['id']; ?>");
    el.load("<?php echo $mod['module']; ?>");
    <?php if ($ms > 0): ?>setTimeout(function f(){ el.load("<?php echo $mod['module']; ?>"); setTimeout(f, <?php echo $ms; ?>); }, <?php echo $ms; ?>);<?php endif; ?>
}); })(jQuery);
<?php endforeach; ?>

// Station data / realtime heartbeat
(function($){ $(document).ready(function(){
    var c = $("#blank");
    setInterval(function(){ $.ajax({ cache:false, url:"" }); }, <?php echo (isset($wuupdate) && $wuupdate > 0) ? 1000*$wuupdate : 30000; ?>);
}); })(jQuery);

</script>
<?php
$topbar_modules_files = array_column($topbar_modules, 'module');
if (in_array('weather34clock.php', $topbar_modules_files)):
?>
<script>
var clockID;
var yourTimeZoneFrom = <?php echo $UTC_offset; ?>;
var d = new Date();
var weekdays = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
var months   = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
var tzDifference = yourTimeZoneFrom * 60 + d.getTimezoneOffset();
var offset = tzDifference * 60 * 1000;
function UpdateClock() {
    var e = new Date(new Date().getTime() + offset);
    var c = e.getHours()<?php echo ($clockformat == '12') ? ' % 12 || 12' : ' % 24 || 0'; ?>;
    <?php echo ($clockformat == '12') ? "if(e.getHours()<12){amorpm=' am'}else{amorpm=' pm'}" : "amorpm='';"; ?>
    var a = e.getMinutes(), g = e.getSeconds(), f = e.getFullYear();
    var h = months[e.getMonth()], b = e.getDate(), i = weekdays[e.getDay()];
    if (a < 10) a = "0" + a;
    if (g < 10) g = "0" + g;
    if (c < 10) c = "0" + c;
    document.getElementById("theTime").innerHTML =
        "<div class='weatherclock34'> " + i + " " + b + " " + h + " " + f +
        "<div class='orangeclock'>" + c + ":" + a + ":" + g + amorpm;
}
function StartClock() { clockID = setInterval(UpdateClock, 500); }
function KillClock()  { clearTimeout(clockID); }
window.onload = function() { StartClock(); };
</script>
<?php endif; ?>
<!-- end updater.php -->
