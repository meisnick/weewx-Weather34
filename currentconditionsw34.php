<?php
include('settings1.php');
include_once('w34CombinedData.php');
include_once('w34_i18n.php');
include('metar34sky.php');
$cloud_region = explode("/", $TZ);
error_reporting(0);

// Visibility from METAR (me.txt) instead of stale awc.txt
if ($windunit == 'mph' || $windunit == 'kts') {
    $visibility = $metar34vismiles . " mi";
} else {
    $visibility = $metar34viskm . " km";
}
// Thresholds in the display units. Rain rate (NWS intensity): moderate from 0.10 in/h (2.5 mm/h), heavy from
// 0.30 in/h (7.6 mm/h), flooding possible from 0.8 in/h (20 mm/h). Fog/mist: dewpoint spread under 0.5 / 0.8 C and
// air above 5 C, converted when the display is in F.
$rain_in    = ($weather["rain_units"] == 'in');
$rr_mod     = $rain_in ? 0.10 : 2.5;
$rr_heavy   = $rain_in ? 0.30 : 7.6;
$rr_flood   = $rain_in ? 0.8  : 20;
$temp_f     = ($weather["temp_units"] == 'F');
$fog_spread = $temp_f ? 0.9 : 0.5;
$mist_spread = $temp_f ? 1.4 : 0.8;
$fog_above  = $temp_f ? 41 : 5;
$dp_spread  = $weather["temp"] - $weather["dewpoint"];
?>
<div class="mod-conditions"><div class="updatedtimecurrent">
<?php // the report's observation time (the file is rewritten every 15 min whether or not the report changed)
echo $online; if ($_d) echo " ", date($timeFormat, strtotime($_d->{'observed'}));?></div>
<div class="cloudconverter">
<?php //cloudbase-weather34
$cloudcoverunit = '%';
$clouds = w34t('Cloudbase', 'Cloudbase');
if ($windunit =='mph' ||  $windunit =='kts'){$distance="ft";}
else if ($windunit =='km/h' ||  $windunit =='m/s'){$distance="m";}
if ($weather["cloudbase3"]>0){
if (($windunit =='mph' ||  $windunit =='kts') && $weather["cloudbase3"]>=1999){echo "<div class=cloudconvertercircle2000>".$clouds."<tyellow> ".$weather["cloudbase3"]."</tyellow><smalltempunit2> ".$distance."</smalltempunit2></div>" ;}
else if (($windunit =='mph' ||  $windunit =='kts') && $weather["cloudbase3"]<1999){echo "<div class=cloudconvertercircle>".$clouds."<tblue> ".$weather["cloudbase3"]."</tblue><smalltempunit2> ".$distance."</smalltempunit2></div>" ;}
else if (($windunit =='km/h' ||  $windunit =='m/s') && $weather["cloudbase3"]>=609){echo "<div class=cloudconvertercircle2000>".$clouds."<tyellow> ".$weather["cloudbase3"]."</tyellow><smalltempunit2> ".$distance."</smalltempunit2></div>" ;}
else if (($windunit =='km/h' ||  $windunit =='m/s') && $weather["cloudbase3"]<609){echo "<div class=cloudconvertercircle>".$clouds."<tblue> ".$weather["cloudbase3"]."</tblue><smalltempunit2> ".$distance."</smalltempunit2></div>" ;}
}
//nearby airport and its flight category, under the cloudbase pill; opens the METAR popup
$fltcat = w34_fltcat_badge($_d->{'flight_category'} ?? '', '8px');
if (!empty($_d->{'icao'}) && $fltcat !== '') {
  echo '<a class="metarpill" href="pop_metarnearby.php" data-lity><div class="cloudconvertercircle">' . htmlspecialchars($_d->{'icao'}) . ' ' . $fltcat . '</div></a>';
}?></div>
<style>
.mod-conditions .metarpill { display: block; margin-top: 4px; color: inherit; text-decoration: none; }
.mod-conditions .metarpill .cloudconvertercircle { display: flex; justify-content: center; gap: 5px; height: 1rem; line-height: 16px; letter-spacing: .3px; }
</style>
<div class="forecasticoncurrent"><span1>
<?php 
//homeweatherstation weather34 current conditions using hardware values
if ($windunit=='kts'){$windunit="kn";}       
//rain-weather34
if($weather["rain_rate"]>0 && $weather["wind_speed_avg"]>15){echo "<img rel='prefetch' src='css/svg/10w.svg' width='60px' height='55px' alt='weather34 windy rain icon'>";}
else if($weather["rain_rate"]>=$rr_heavy){echo "<img rel='prefetch' src='css/svg/10.svg' width='70px' height='60px' alt='weather34 heavy rain icon'>";}
else if($weather["rain_rate"]>0){echo "<img rel='prefetch' src='css/svg/09.svg' width='70px' height='60px' alt='weather34 rain icon'>";}
//fog-weather34
else if($dp_spread <$mist_spread  && $dayPartNatural == "night" && $weather["temp"]>$fog_above){echo "<img rel='prefetch' src='css/svg/fog.night.svg' width='70px' height='60px' alt='weather34 fog icon'>";}
else if($dp_spread <$mist_spread  && $weather["temp"]>$fog_above){echo "<img rel='prefetch' src='css/svg/fog.svg' width='70px' height='60px' alt='weather34 fog'>";}
//windy moderate-weather34
else if($weather["wind_speed_avg"]>=15 && $dayPartNatural == "night" && $weather["cloud_cover"]<20){echo "<img rel='prefetch' src='css/svg/01nw.svg' width='70px' height='60px' alt='weather34 windy icon'>";}
else if($weather["wind_speed_avg"]>=15 && $weather["cloud_cover"]<20){echo "<img rel='prefetch' src='css/svg/01dw.svg' width='70px' height='60px' alt='weather34 windy icon'>";}
//windy moderate-weather34
else if($weather["wind_speed_avg"]>=15 && $dayPartNatural == "night"){echo "<img rel='prefetch' src='css/svg/wind.svg' width='70px' height='60px' alt='weather34 windy icon'>";}
else if($weather["wind_speed_avg"]>=15){echo "<img rel='prefetch' src='css/svg/wind.svg' width='70px' height='60px' alt='weather34 windy icon'>";}

//metar sky icon
else if($_d === null){
echo "<img rel='prefetch' src='css/icons/offline.svg' width='70px' height='60px' alt='weather34 offline icon'>";}
else {echo "<img rel='prefetch' src='css/svg/".$sky_icon."' width='70px' height='60px' alt='".htmlspecialchars($sky_desc)."'>";}
?></span1></div>
<div class="forecastsummary"><span>
<?php echo '';
if ($windunit=='kts'){$windunit="kn";}
//rain-weather34
if($weather["rain_rate"]>0 && $weather["wind_speed_avg"]>15){echo w34_cond("Rain Showers"); echo '<br>';echo w34_cond("Windy Conditions");}
else if($weather["rain_rate"]>=$rr_flood){echo w34_cond("Heavy Rain"); echo '<br>';echo w34t('Flooding', 'Flooding Possible');}
else if($weather["rain_rate"]>=$rr_heavy){echo w34_cond("Heavy Rain<br>Showers");}
else if($weather["rain_rate"]>=$rr_mod){echo w34_cond("Moderate Rain<br>Showers");}
else if($weather["rain_rate"]>0){echo w34_cond("Light Rain<br>Showers");}
//fog-weather34
else if($dp_spread <$fog_spread  && $dayPartNatural == "night" && $weather["temp"]>$fog_above){echo w34_cond("Misty Fog<br>Conditions")." ".$alert."";}
else if($dp_spread <$fog_spread  && $weather["temp"]>$fog_above){echo w34_cond("Misty Fog<br>Conditions")." ".$alert."";}
//misty-weather34
else if($dp_spread <$mist_spread  && $dayPartNatural == "night" && $weather["temp"]>$fog_above){echo w34_cond("Fog Hazy<br>Conditions");}
else if($dp_spread <$mist_spread  && $weather["temp"]>$fog_above){echo w34_cond("Misty Hazy<br>Conditions");}
//windy-weather34
else if($weather["wind_speed_avg"]>=40){echo w34_cond("Strong Wind")." ".$alert."<br>".w34t('Conditions', 'Conditions') ;}
else if($weather["wind_speed_avg"]>=30){echo w34_cond("Very Windy")." ".$alert."<br>".w34t('Conditions', 'Conditions');}
else if($weather["wind_speed_avg"]>=22){echo w34_cond("Moderate Wind<br>Conditions");}
else if($weather["wind_speed_avg"]>=15){echo w34_cond("Breezy<br>Conditions");}
//metar sky description
else if($_d === null){echo "<uppercase>".w34_cond("Conditions<br>Not Available")."</uppercase>";}
else {echo nl2br(htmlspecialchars(w34_cond($sky_desc)));}
//oktas
// eighths of sky, rounded to the nearest (0 % is 0 oktas)
if (is_numeric($weather["cloud_cover"])) {
    $okta = (int)round(max(0, min(100, $weather["cloud_cover"])) / 12.5);
    $weather["cloud_oktas"] = $okta . ($okta == 1 ? " okta" : " oktas");
}
//metar conditions-weather34

?>
</span></div>
 <!-- weather34 generated Data--> 
<div class="forecastnexthours">
<?php //weather34 average station data
//echo "Average <span class=\"ink-oblue\">Cloud Cover</span> last 5 minutes <span class=\"ink-ogreen\">" .$weather["cloud_cover"]."</span><valuetext>".$cloudcoverunit. "(".$weather["cloud_oktas"].")";

if ($_d){echo "</br>".w34t('Visibility', 'Visibility')." <span class=\"ink-oorange\">".$visibility."</span></br>";}
if (is_numeric($weather["cloud_cover"])){
echo "<span class=\"ink-oblue\">".w34t('Cloudcover', 'Cloud Cover')."</span><span class=\"ink-ogreen\"> " .$weather["cloud_cover"]."</span><valuetext>".$cloudcoverunit. " (".$weather["cloud_oktas"].")</valuetext>";
}


echo "<br>".w34t('CcAvg', 'Average')." <span class=\"ink-oorange\">".w34t('Temperature', 'Temperature')."</span> ".w34t('Last60Min', 'last 60 minutes')." ";if($weather["temp_avg"]>=20){echo "<span class=\"ink-oorange\">" .$weather["temp_avg"]."</span>°<valuetext>".$weather['temp_units'];} else if($weather["temp_avg"]<=10){echo "<span class=\"ink-oblue\">" .$weather["temp_avg"]."</span>°<valuetext>".$weather['temp_units'];}else if($weather["temp_avg"]<20){echo "<span class=\"ink-ogreen\">" .$weather["temp_avg"]."</span>°<valuetext>".$weather['temp_units'];}echo "</valuetext><br>";
echo  w34t('MaxLabel', 'Max')." <span class=\"ink-oblue\">".w34t('WindGustLabel', 'Wind Gust')."</span> ";
$wu_display = $weather['wind_units'] === 'kts' ? 'kn' : $weather['wind_units'];
if($weather["wind_gust_60min"]>=50){echo "<span class=\"ink-ored\">" ,number_format(round((float)$weather["wind_gust_60min"],1))."</span> ".$wu_display;}
else if($weather["wind_gust_60min"]>=30){echo "<span class=\"ink-oorange\">" ,number_format(round((float)$weather["wind_gust_60min"],1))."</span><valuetext> ".$wu_display;}
else if($weather["wind_gust_60min"]>=0){echo "<span class=\"ink-ogreen\">" ,number_format(round((float)$weather["wind_gust_60min"],1))."</span><valuetext> ".$wu_display;}echo " </valuetext>".w34t('Last60Min', 'last 60 minutes')." ";
echo  " <br>".w34t('CcAvg', 'Average')." <span class=\"ink-oblue\">".w34t('WindSpeedLabel', 'Wind Speed')."</span> ".w34t('Last10Min', 'last 10 minutes')." ";if($weather["wind_speed_avg"]>=30){echo "<span class=\"ink-ored\">" ,number_format(round((float)$weather["wind_speed_avg"]))."</span> ".$wu_display;}else if($weather["wind_speed_avg"]>=20){echo "<span class=\"ink-oorange\">" .$weather["wind_speed_avg"]."</span><valuetext> ".$wu_display;}
else if($weather["wind_speed_avg"]>=0){echo "<span class=\"ink-ogreen\">" ,number_format(round((float)$weather["wind_speed_avg"]))."</span><valuetext> ".$wu_display;}
echo "</valuetext>";
if($weather["wind_direction_avg"]>0){echo "<br>".w34t('CcAvg', 'Average')." ".w34t('Direction', 'Direction')." <span class=\"ink-oorange\">"; if($weather["wind_direction_avg"]<=11.25){echo w34_nobr($lang['Northdir']);}else if($weather["wind_direction_avg"]<=33.75){echo w34_nobr($lang['NNEdir']);}else if($weather["wind_direction_avg"]<=56.25){echo w34_nobr($lang['NEdir']);}else if($weather["wind_direction_avg"]<=78.75){echo w34_nobr($lang['ENEdir']);}else if($weather["wind_direction_avg"]<=101.25){echo w34_nobr($lang['Eastdir']);}else if($weather["wind_direction_avg"]<=123.75){echo w34_nobr($lang['ESEdir']);}else if($weather["wind_direction_avg"]<=146.25){echo w34_nobr($lang['SEdir']);}
else if($weather["wind_direction_avg"]<=168.75){echo w34_nobr($lang['SSEdir']);}else if($weather["wind_direction_avg"]<=191.25){echo w34_nobr($lang['Southdir']);}else if($weather["wind_direction_avg"]<=213.75){echo w34_nobr($lang['SSWdir']);}else if($weather["wind_direction_avg"]<=236.25){echo w34_nobr($lang['SWdir']);}else if($weather["wind_direction_avg"]<=258.75){echo w34_nobr($lang['WSWdir']);}else if($weather["wind_direction_avg"]<=281.25){echo w34_nobr($lang['Westdir']);}else if($weather["wind_direction_avg"]<=303.75){echo w34_nobr($lang['WNWdir']);}else if($weather["wind_direction_avg"]<=326.25){echo w34_nobr($lang['NWdir']);}else if($weather["wind_direction_avg"]<=348.75){echo w34_nobr($lang['NWNdir']);}else{echo w34_nobr($lang['Northdir']);}
echo " </span><span class=\"ink-oblue\"> ".$weather["wind_direction_avg"]."</span>°";}
echo "<br><span class=\"ink-oblue\">".w34t('Rainfall', 'Rainfall')."</span> ".w34t('RainLast3h', 'for the last 3 hours')." <span class=\"ink-oblue\"> " .$weather["rain_last3hours"]."</span><valuetext> " .$weather['rain_units'];
?></valuetext></div>
</div><!-- /mod-conditions -->
