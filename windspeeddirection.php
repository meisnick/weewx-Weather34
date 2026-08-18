<?php 
//original weather34 script original css/svg/php by weather34 2015-2019 clearly marked as original by weather34//
require_once('w34CombinedData.php');require_once('common.php');?>
<meta http-equiv="Content-Type: text/html; charset=UTF-8" />
<style>
.mod-wind .thearrow2{-webkit-transform:rotate(<?php echo $weather["wind_direction"];?>deg);-moz-transform:rotate(<?php echo $weather["wind_direction"];?>deg);-o-transform:rotate(<?php echo $weather["wind_direction"];?>deg);-ms-transform:rotate(<?php echo $weather["wind_direction"];?>deg);transform:rotate(<?php echo $weather["wind_direction"];?>deg);position:absolute;z-index:200;top:0;left:50%;margin-left:-5px;width:10px;height:50%;-webkit-transform-origin:50% 100%;-moz-transform-origin:50% 100%;-o-transform-origin:50% 100%;-ms-transform-origin:50% 100%;transform-origin:50% 100%;-webkit-transition-duration:3s;-moz-transition-duration:3s;-o-transition-duration:3s;-ms-transition-duration:3s;transition-duration:3s}
.mod-wind .thearrow2:after{content:'';position:absolute;left:50%;top:0;height:10px;width:10px;background-color:NONE;width:0;height:0;border-style:solid;border-width:14px 9px 0 9px;border-color:RGBA(255,121,58,1) transparent transparent transparent;-webkit-transform:translate(-50%,-50%);-moz-transform:translate(-50%,-50%);-o-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);transform:translate(-50%,-50%);-webkit-transition-duration:3s;-moz-transition-duration:3s;-o-transition-duration:3s;-ms-transition-duration:3s;transition-duration:3s}
.mod-wind .thearrow2:before{content:'  o o o';color:rgba(255, 124, 57, 1);font-family:Arial, Helvetica, sans-serif;font-size:6px;width:6px;height:6px;position:absolute;z-index:9;left:2px;top:-5px;border:2px solid RGBA(255,255,255,0.8);-webkit-border-radius:100%;-moz-border-radius:100%;-o-border-radius:100%;-ms-border-radius:100%;border-radius:100%}
.mod-wind .thearrow1{-webkit-transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);-moz-transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);-o-transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);-ms-transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);position:absolute;z-index:150;top:0;left:50%;margin-left:-5px;-webkit-transform-origin:50% 100%;-moz-transform-origin:50% 100%;-o-transform-origin:50% 100%;-ms-transform-origin:50% 100%;transform-origin:50% 100%;-webkit-transition-duration:0s;-moz-transition-duration:0s;-o-transition-duration:0s;-ms-transition-duration:0s;transition-duration:0s;background:0}
.mod-wind .thearrow1:after{content:'';position:absolute;text-align:left;left:50%;font-size:8px;top:0;width:0;height:0;-webkit-border-radius:0;border-radius:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:9px solid rgb(144, 177, 42);border-bottom:0;-webkit-transform:translate(-50%,-50%);-moz-transform:translate(-50%,-50%);-o-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);transform:translate(-50%,-50%);-webkit-transition-duration:3s;-moz-transition-duration:3s;-o-transition-duration:3s;-ms-transition-duration:3s;transition-duration:3s;background:0}
.mod-wind .thearrow1:before{content:'  o o o o';color:rgb(144, 177, 42);font-family:Arial, Helvetica, sans-serif;font-size:4px;width:1px;height:1px;position:absolute;z-index:1;left:3px;top:-4px;border:2px dotted RGBA(255,255,255,0.8);-webkit-border-radius:100%;-moz-border-radius:100%;-o-border-radius:100%;-ms-border-radius:100%;border-radius:100%}
.mod-wind .avgw{ width:27px; height:27px;	margin-left:35px;-webkit-transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);-moz-transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);-o-transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);-ms-transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);transform:rotate(<?php echo $weather["wind_direction_avg"];?>deg);}
spancalm{postion:relative;font-family:weathertext,Arial;font-size:26px;}
</style>

<div class="mod-wind">

<span class="wind-time">
<?php if(file_exists($livedata)&&time()- filemtime($livedata)>300)echo $offline. '<offline> Offline </offline>';else echo $online." ".$weather["time"];?>
</span>

<span class="wind-speed-val">
<?php if ($weather["wind_speed"]<10){echo "&nbsp;".number_format($weather["wind_speed"],1);}else echo number_format($weather["wind_speed"],1);?>
</span>
<span class="wind-currently"><?php echo $lang['Currently'];?></span>
<span class="wind-speed-unit"><?php echo $weather["wind_units"]; ?></span>

<span class="wind-gust-val">
<?php 
if ($weather["wind_gust_speed"]*$toKnots>=26.9978){echo "<windred>".number_format($weather["wind_gust_speed"],1)."</windred>";}
else if ($weather["wind_gust_speed"]*$toKnots>=21.5983){echo "<windorange>".number_format($weather["wind_gust_speed"],1)."</windorange>";}
else if ($weather["wind_gust_speed"]*$toKnots>=16.1987){echo "<windgreen>".number_format($weather["wind_gust_speed"],1)."</windgreen>";}
else if ($weather["wind_gust_speed"]<10){echo "&nbsp;".number_format($weather["wind_gust_speed"],1);}
else echo number_format($weather["wind_gust_speed"],1);
?>
</span>
<span class="wind-gust-label"><?php echo $lang['Gust']; ?></span>
<span class="wind-gust-unit"><?php echo $weather["wind_units"]; ?></span>

<span class="wind-max-gust-label">
<?php echo "Max ".$lang['Gust']." (".$weather["winddmaxtime"].")";?>
</span>
<span class="wind-max-gust-val">
<?php echo number_format($weather["wind_gust_speed_max"],1);?>
</span>
<span class="wind-max-gust-unit">
<?php echo $weather["wind_units"];?>
</span>

<?php 
//weather34-convert kmh to mph
if ($weather["wind_units"]=="km/h" && $weather["wind_gust_speed"]*$toKnots>=26.9978){echo "<span class='wind-conv-val'><tred>".number_format($weather["wind_gust_speed"]*0.621371,1)."</tred></span><span class='wind-conv-unit'>mph</span>";}
else if ($weather["wind_units"]=="km/h" && $weather["wind_gust_speed"]*$toKnots>=21.5983){echo "<span class='wind-conv-val'><torange>".number_format($weather["wind_gust_speed"]*0.621371,1)."</torange></span><span class='wind-conv-unit'>mph</span>";}
else if ($weather["wind_units"]=="km/h" && $weather["wind_gust_speed"]*$toKnots>=16.1987){echo "<span class='wind-conv-val'><tgreen>".number_format($weather["wind_gust_speed"]*0.621371,1)."</tgreen></span><span class='wind-conv-unit'>mph</span>";}
else if ($weather["wind_units"]=="km/h" && $weather["wind_gust_speed"]*$toKnots<16.1987){echo "<span class='wind-conv-val'>".number_format($weather["wind_gust_speed"]*0.621371,1)."</span><span class='wind-conv-unit'>mph</span>";}
//weather34-convert mph to kmh
else if ($weather["wind_units"]=="mph" && $weather["wind_gust_speed"]*$toKnots>=26.9978){echo "<span class='wind-conv-val'><tred>".number_format($weather["wind_gust_speed"]*1.609343502101025,1)."</tred></span><span class='wind-conv-unit'>kmh</span>";}
else if ($weather["wind_units"]=="mph" && $weather["wind_gust_speed"]*$toKnots>=21.5983){echo "<span class='wind-conv-val'><torange>".number_format($weather["wind_gust_speed"]*1.609343502101025,1)."</torange></span><span class='wind-conv-unit'>kmh</span>";}
else if ($weather["wind_units"]=="mph" && $weather["wind_gust_speed"]*$toKnots>=16.1987){echo "<span class='wind-conv-val'>".number_format($weather["wind_gust_speed"]*1.609343502101025,1)."</span><span class='wind-conv-unit'>kmh</span>";}
else if ($weather["wind_units"]=="mph" && $weather["wind_gust_speed"]*$toKnots<16.1987){echo "<span class='wind-conv-val'><tgreen>".number_format($weather["wind_gust_speed"]*1.609343502101025,1)."</tgreen></span><span class='wind-conv-unit'>kmh</span>";}
//weather34-convert ms to kmh
else if ($weather["wind_units"]=="m/s" && $weather["wind_gust_speed"]*$toKnots>=26.9978){echo "<span class='wind-conv-val'><tred>".number_format($weather["wind_gust_speed"]*3.60000288,1)."</tred></span><span class='wind-conv-unit'>kmh</span>";}
else if ($weather["wind_units"]=="m/s" && $weather["wind_gust_speed"]*$toKnots>=21.5983){echo "<span class='wind-conv-val'><torange>".number_format($weather["wind_gust_speed"]*3.60000288,1)."</torange></span><span class='wind-conv-unit'>kmh</span>";}
else if ($weather["wind_units"]=="m/s" && $weather["wind_gust_speed"]*$toKnots>=16.1987){echo "<span class='wind-conv-val'>".number_format($weather["wind_gust_speed"]*3.60000288,1)."</span><span class='wind-conv-unit'>kmh</span>";}
else if ($weather["wind_units"]=="m/s" && $weather["wind_gust_speed"]*$toKnots<16.1987){echo "<span class='wind-conv-val'><tgreen>".number_format($weather["wind_gust_speed"]*3.60000288,1)."</tgreen></span><span class='wind-conv-unit'>kmh</span>";}
?>

<div class="homeweathercompass1"><div class="homeweathercompass-line1"><div class="thearrow2"></div><div class="thearrow1"></div></div></div>

<span class="wind-dir-deg"><?php echo $weather["wind_direction"],'&deg;';?></span>
<span class="wind-dir-text">
<?php  
if($weather["wind_direction"]<=11.25){echo $lang['Northdir'] ;}else if($weather["wind_direction"]<=33.75){echo $lang['NNEdir'];}else if($weather["wind_direction"]<=56.25){echo $lang['NEdir'];}else if($weather["wind_direction"]<=78.75){echo $lang['ENEdir'];}else if($weather["wind_direction"]<=101.25){echo $lang['Eastdir'];}else if($weather["wind_direction"]<=123.75){echo $lang['ESEdir'];}else if($weather["wind_direction"]<=146.25){echo $lang['SEdir'];}else if($weather["wind_direction"]<=168.75){echo $lang['SSEdir'];}else if($weather["wind_direction"]<=191.25){echo $lang['Southdir'];}  else if($weather["wind_direction"]<=213.75){echo $lang['SSWdir'];}else if($weather["wind_direction"]<=236.25){echo $lang['SWdir'];}else if($weather["wind_direction"]<=258.75){echo $lang['WSWdir'];}else if($weather["wind_direction"]<=281.25){echo $lang['Westdir'];}else if($weather["wind_direction"]<=303.75){echo $lang['WNWdir'];}else if($weather["wind_direction"]<=326.25){echo $lang['NWdir'];}else if($weather["wind_direction"]<=348.75){echo $lang['NWNdir'];}else {echo $lang['Northdir'];}
?>
</span> 

<span class="wind-run-label"><?php echo $windrunicon . ' ' . $lang['Wind Run'];?></span>
<span class="wind-run-val"><?php echo number_format($weather["windrun"],1);?></span>
<span class="wind-run-unit">
<?php if ($weather["wind_units"] == 'mph') echo 'mi'; else if ($weather["wind_units"] == 'm/s') echo 'km'; else if ($weather["wind_units"] == 'kts') echo 'mi';else echo 'km';?>
</span>

<span class="wind-bft-val <?php 
if ($weather["wind_speed_bft"] >= 12) { echo 'weather34beaufort6'; }
else if ($weather["wind_speed_bft"] >= 11) { echo 'weather34beaufort6'; }
else if ($weather["wind_speed_bft"] >= 10) { echo 'weather34beaufort6'; }
else if ($weather["wind_speed_bft"] >= 9) { echo 'weather34beaufort6'; }
else if ($weather["wind_speed_bft"] >= 8) { echo 'weather34beaufort6'; }
else if ($weather["wind_speed_bft"] >= 7) { echo 'weather34beaufort6'; }
else if ($weather["wind_speed_bft"] >= 6) { echo 'weather34beaufort6'; }
else if ($weather["wind_speed_bft"] >= 5) { echo 'weather34beaufort4-5'; }
else if ($weather["wind_speed_bft"] >= 4) { echo 'weather34beaufort4-5'; }
else if ($weather["wind_speed_bft"] >= 3) { echo 'weather34beaufort3-4'; }
else if ($weather["wind_speed_bft"] >= 2) { echo 'weather34beaufort1-3'; }
else if ($weather["wind_speed_bft"] >= 1) { echo 'weather34beaufort1-3'; }
else if ($weather["wind_speed_bft"] >= 0) { echo 'weather34beaufort1-3'; }
?>">
<?php
if ($weather["wind_speed_bft"] >= 12) { echo $beaufort12 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 11) { echo $beaufort11 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 10) { echo $beaufort10 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 9) { echo $beaufort9 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 8) { echo $beaufort8 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 7) { echo $beaufort7 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 6) { echo $beaufort6 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 5) { echo $beaufort5 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 4) { echo $beaufort4 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 3) { echo $beaufort3 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 2) { echo $beaufort2 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 1) { echo $beaufort1 . "&nbsp; " . $weather["wind_speed_bft"]; }
else if ($weather["wind_speed_bft"] >= 0) { echo $beaufort0 . "&nbsp; " . $weather["wind_speed_bft"]; }
?>
</span>
<span class="wind-bft-label">BFT</span>
<span class="wind-bft-text">
<?php
if ($weather["wind_speed_bft"] == 0) { echo $lang['Calm']; }
else if ($weather["wind_speed_bft"] == 1) { echo $lang['Lightair'] ?? 'Light Air'; }
else if ($weather["wind_speed_bft"] == 2) { echo $lang['Lightbreeze'] ?? 'Light Breeze'; }
else if ($weather["wind_speed_bft"] == 3) { echo $lang['Gentelbreeze'] ?? 'Gentle Breeze'; }
else if ($weather["wind_speed_bft"] == 4) { echo $lang['Moderatebreeze'] ?? 'Moderate Breeze'; }
else if ($weather["wind_speed_bft"] == 5) { echo $lang['Freshbreeze'] ?? 'Fresh Breeze'; }
else if ($weather["wind_speed_bft"] == 6) { echo $lang['Strongbreeze'] ?? 'Strong Breeze'; }
else if ($weather["wind_speed_bft"] == 7) { echo ($lang['Neargale'] ?? 'Near Gale') . " " . $alert; }
else if ($weather["wind_speed_bft"] == 8) { echo ($lang['Galeforce'] ?? 'Gale Force') . " " . $alert; }
else if ($weather["wind_speed_bft"] == 9) { echo ($lang['Stronggale'] ?? 'Strong Gale') . " " . $alert; }
else if ($weather["wind_speed_bft"] == 10) { echo ($lang['Storm'] ?? 'Storm Force') . " " . $alert; }
else if ($weather["wind_speed_bft"] == 11) { echo ($lang['Violentstorm'] ?? 'Violent Storm') . " " . $alert; }
else if ($weather["wind_speed_bft"] >= 12) { echo ($lang['Hurricane'] ?? 'Hurricane Force') . " " . $alert; }
?>
</span>

</div><!-- /mod-wind -->
