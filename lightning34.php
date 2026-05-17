<?php include('w34CombinedData.php');date_default_timezone_set($TZ);?>
<div class="updatedtime"><span><?php if(file_exists($livedata)&&time()- filemtime($livedata)>300)echo $offline. '<offline> Offline </offline>';else echo $online." ".$weather["time"];?></div>
<div class="simsekcontainer">
<div class="simsekdata">Strikes</div>
<?php
// Detected Lightning Last 3 Hours
echo '<div class=simsek>'.$lightning["strike_count_3hr"];?></div>
<div class="simsektoday"><valuetext>Last 3 Hrs</valuetext></div>
</div>
<div class="lightninginfo">Strikes Recorded
<?php
// Lightning Month Current
echo "<lightningannualx>".date('F Y').":<orange> " .str_replace(",","",$weather["lightningmonth"])." </orange></lightningannual>";?>
<?php
// Lightning Year Current
echo "<lightningannualx1> Total ".date('Y').":<orange> " .str_replace(",","",$weather["lightningyear"])." </orange>";?>
<?php
// Last Strike Detected
if ($lightning['last_time']>=1) echo "<timeago>Last Strike Detected<br> <agolightning><orange>".date('jS M H:i',$lightning['last_time'])." </orange> ";?></div>
<div class="rainconverter">
<?php
// Last Distance Detected
echo "<div class=tempconvertercircleyellow><orange> " .$lightning["light_last_distance"]."</orange><smallrainunit>&nbsp; km</smallrainunit>";?></div>
<?php
// Last Strike Energy (Random)
echo "<div class=tempconvertercircleyellow><orange> ";?><?php echo rand(6452,28864);?><?php echo "</orange><smallrainunit>&nbsp; MJ/m</smallrainunit>";?></div>
<lightningiconx>
<?php if ($lightning['strike_count_3hr'] > 0) echo '<img src="img/lightningalert.svg" width="20" height="20" align="right"/>';?>
</lightningiconx>
