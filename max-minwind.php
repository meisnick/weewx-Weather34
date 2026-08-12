<?php include('livedata.php');include('common.php');header('Content-type: text/html; charset=utf-8');?>
<div class="hometemperatureindoor">
<?php 
 //weather34 wind kmh
 if ($weather["wind_speed_max"]>-50){echo "<div class=\"circlemaxwind\">", number_format($weather["wind_speed_max"],1) ;
 echo " <spanmaxwind>". $weather["wind_units"]."</spanmaxwind> <spanwindtitle> ".$lang['Wind']." </spanwindtitle>
 </div> " ;}  
?>
<?php 
 if ($weather["wind_gust_speed_max"]>-50){echo "<div class=\"circlemaxgust\">", number_format($weather["wind_gust_speed_max"],1) ;
 echo " <spanmaxwind>". $weather["wind_units"]."</spanmaxwind> <spanwindtitle> ".$lang['Gust']." </spanwindtitle> </div> " ;}   
?>
</div>