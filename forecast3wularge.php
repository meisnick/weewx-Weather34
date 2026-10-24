<?php include_once('w34CombinedData.php');error_reporting(0); date_default_timezone_set($TZ);
//original weather34 script original css/svg/php by weather34 2015-2019 clearly marked as original by weather34//
	####################################################################################################
	#	HOME WEATHER STATION TEMPLATE by BRIAN UNDERDOWN 2016-17-18-19                                 #
	#	CREATED FOR HOMEWEATHERSTATION TEMPLATE at https://weather34.com/homeweatherstation/           # 
	# 	LARGE DAY WU WEATHER FORECAST:  original MAR 2019			   		                           #
	#   https://www.weather34.com 	                                                                   #
	####################################################################################################
//start the wu output
$json='jsondata/wu.txt';
$weather34wuurl=file_get_contents($json);
$parsed_weather34wujson = json_decode($weather34wuurl,false);
{    if (($parsed_weather34wujson->{'daypart'}[0]->{'iconCode'}[0] ?? null)==null){
	 $wuskydayIcon=$parsed_weather34wujson->{'daypart'}[0]->{'iconCode'}[1] ?? null;$wuskydayTime = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[1] ?? null;	
	 $wuskydayTempHigh = $parsed_weather34wujson->{'daypart'}[0]->{'temperature'}[1] ?? null;$wuskydayTempLow = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureWindChill'}[1] ?? null;	 
	 $wuskydayWindGust = $parsed_weather34wujson->{'daypart'}[0]->{'windSpeed'}[1] ?? null;$wuskydayWinddir = $parsed_weather34wujson->{'daypart'}[0]->{'windDirection'}[1] ?? null;
	 $wuskydayWinddircardinal = $parsed_weather34wujson->{'daypart'}[0]->{'windDirectionCardinal'}[1] ?? null;$wuskydayacumm = $parsed_weather34wujson->{'daypart'}[0]->{'snowRange'}[1] ?? null;
	 $wuskydayPrecipType = $parsed_weather34wujson->{'daypart'}[0]->{'precipType'}[1] ?? null;$wuskydayprecipIntensity = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[1] ?? null;
	 $wuskydayPrecipProb = $parsed_weather34wujson->{'daypart'}[0]->{'precipChance'}[1] ?? null;$wuskydayUV = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[1] ?? null;
	 $wuskydayUVdesc = $parsed_weather34wujson->{'daypart'}[0]->{'uvDescription'}[1] ?? null;$wuskydaysnow = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[1] ?? null;
	 $wuskydaysummary = $parsed_weather34wujson->{'daypart'}[0]->{'narrative'}[1] ?? null;$wuskydaynight = $parsed_weather34wujson->{'daypart'}[0]->{'dayOrNight'}[1] ?? null;
	 $wuskydesc = $parsed_weather34wujson->{'daypart'}[0]->{'wxPhraseLong'}[1] ?? null;$wuskythunderidx = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[1] ?? null;
	 $wuskyhumidity = $parsed_weather34wujson->{'daypart'}[0]->{'relativeHumidity'}[1] ?? null;
	 $wuskyheatindex = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[1] ?? null;
	 $wuskywindchill = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureWindChill'}[1] ?? null;
	 }	 
	 else {
	 $wuskydayIcon=$parsed_weather34wujson->{'daypart'}[0]->{'iconCode'}[0];$wuskydayTime = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[0];	
	 $wuskydayTempHigh = $parsed_weather34wujson->{'daypart'}[0]->{'temperature'}[0];$wuskydayTempLow = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureWindChill'}[0];	 
	 $wuskydayWindGust = $parsed_weather34wujson->{'daypart'}[0]->{'windSpeed'}[0];$wuskydayWinddir = $parsed_weather34wujson->{'daypart'}[0]->{'windDirection'}[0];
	 $wuskydayWinddircardinal = $parsed_weather34wujson->{'daypart'}[0]->{'windDirectionCardinal'}[0];$wuskydayacumm = $parsed_weather34wujson->{'daypart'}[0]->{'snowRange'}[0];
	 $wuskydayPrecipType = $parsed_weather34wujson->{'daypart'}[0]->{'precipType'}[0];$wuskydayprecipIntensity = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[0];
	 $wuskydayPrecipProb = $parsed_weather34wujson->{'daypart'}[0]->{'precipChance'}[0];$wuskydayUV = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[0];
	 $wuskydayUVdesc = $parsed_weather34wujson->{'daypart'}[0]->{'uvDescription'}[0];$wuskydaysnow = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[0];
	 $wuskydaysummary = $parsed_weather34wujson->{'daypart'}[0]->{'narrative'}[0];$wuskydaynight = $parsed_weather34wujson->{'daypart'}[0]->{'dayOrNight'}[0];
	 $wuskydesc = $parsed_weather34wujson->{'daypart'}[0]->{'wxPhraseLong'}[0];$wuskythunderidx = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[0];
	 $wuskyhumidity = $parsed_weather34wujson->{'daypart'}[0]->{'relativeHumidity'}[0];
	 $wuskyheatindex = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[0];
	 $wuskywindchill = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureWindChill'}[0];
	 }
	 //thunder
	 $wuskythunderidx1 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[1] ?? null;
	 $wuskythunderidx2 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[2] ?? null;
	 $wuskythunderidx3 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[3] ?? null;
	 $wuskythunderidx4 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[4] ?? null;
	 $wuskythunderidx5 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[5] ?? null;
	 $wuskythunderidx6 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[6] ?? null;
	 $wuskythunderidx7 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[7] ?? null;
	 $wuskythunderidx8 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[8] ?? null;
	 $wuskythunderidx9 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[9] ?? null;
	 $wuskythunderidx10 = $parsed_weather34wujson->{'daypart'}[0]->{'thunderIndex'}[10] ?? null;
	 //period
	 $wuskydayTime1 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[1] ?? null;
	 $wuskydayTime2 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[2] ?? null;
	 $wuskydayTime3 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[3] ?? null;
	 $wuskydayTime4 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[4] ?? null;
	 $wuskydayTime5 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[5] ?? null;
	 $wuskydayTime6 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[6] ?? null;
	 $wuskydayTime7 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[7] ?? null;
	 $wuskydayTime8 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[8] ?? null;
	 $wuskydayTime9 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[9] ?? null;
	 $wuskydayTime10 = $parsed_weather34wujson->{'daypart'}[0]->{'daypartName'}[10] ?? null;
	 //rain
	 $wuskyrain1 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[1] ?? null;
	 $wuskyrain2 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[2] ?? null;
	 $wuskyrain3 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[3] ?? null;
	 $wuskyrain4 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[4] ?? null;
	 $wuskyrain5 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[5] ?? null;
	 $wuskyrain6 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[6] ?? null;
	 $wuskyrain7 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[7] ?? null;
	 $wuskyrain8 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[8] ?? null;
	 $wuskyrain9 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[9] ?? null;
	 $wuskyrain10 = $parsed_weather34wujson->{'daypart'}[0]->{'qpf'}[10] ?? null;
			 
	 //snow
	 $wuskysnow1 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[1] ?? null;
	 $wuskysnow2 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[2] ?? null;
	 $wuskysnow3 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[3] ?? null;
	 $wuskysnow4 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[4] ?? null;
	 $wuskysnow5 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[5] ?? null;
	 $wuskysnow6 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[6] ?? null;
	 $wuskysnow7 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[7] ?? null; 
	 $wuskysnow8 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[8] ?? null;
	 $wuskysnow9 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[9] ?? null;
	 $wuskysnow10 = $parsed_weather34wujson->{'daypart'}[0]->{'qpfSnow'}[10] ?? null;
	  //heatindex
	 $wuskyheatindex1 = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[1] ?? null;
	 $wuskyheatindex2 = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[2] ?? null;
	 $wuskyheatindex3 = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[3] ?? null;
	 $wuskyheatindex4 = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[4] ?? null;
	 $wuskyheatindex5 = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[5] ?? null;
	 $wuskyheatindex6 = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[6] ?? null;
	 $wuskyheatindex7 = $parsed_weather34wujson->{'daypart'}[0]->{'temperatureHeatIndex'}[7] ?? null;
	
	  //uv index
	 $wuskydayUV1 = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[1] ?? null;
	 $wuskydayUV2 = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[2] ?? null;
	 $wuskydayUV3 = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[3] ?? null;
	 $wuskydayUV4 = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[4] ?? null;
	 $wuskydayUV5 = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[5] ?? null;
	 $wuskydayUV6 = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[6] ?? null;
	 $wuskydayUV7 = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[7] ?? null;
	 $wuskydayUV8 = $parsed_weather34wujson->{'daypart'}[0]->{'uvIndex'}[8] ?? null;
	 
	 }?>
<div class="updatedtime1"><?php $forecastime=filemtime('jsondata/wu.txt');$weather34wuurl = file_get_contents("jsondata/wu.txt");if(filesize('jsondata/wu.txt')<1){echo "".$offline. "";}else echo $online,"";echo " ",date($timeFormat,$forecastime);	?></div>
<div class="mod-forecast"><div class="wulargeforecasthome"><div class="wulargediv">
<?php //begin wu stuff 
//convert lightning index
if ( $wuskythunderidx==0 ){$wuskythunder=$lightningalert8.' &nbsp;No Thunder Storm';}else if ( $wuskythunderidx==1 ){$wuskythunder=$lightningalert8.' &nbsp;Thunder Storm Risk';}
else if ( $wuskythunderidx==2 ){$wuskythunder=$lightningalert8.' &nbsp;Thunder Storm';}else if ( $wuskythunderidx>=3 ){$wuskythunder=$lightningalert8.' &nbsp;Severe Thunderstorm';}
//wu convert temps-rain-wind
//metric to F
if ($tempunit=='F' && $wuapiunit=='m' ){$wuskydayTempHigh=($wuskydayTempHigh*9/5)+32;}
if ($tempunit=='F' && $wuapiunit=='m' ){$wuskyheatindex=($wuskyheatindex*9/5)+32;}
if ($tempunit=='F' && $wuapiunit=='m' ){$wuskywindchill=($wuskywindchill*9/5)+32;}
// metric to F UK
if ($tempunit=='F' && $wuapiunit=='h' ){$wuskydayTempHigh=($wuskydayTempHigh*9/5)+32;}
if ($tempunit=='F' && $wuapiunit=='h' ){$wuskyheatindex=($wuskyheatindex*9/5)+32;}
if ($tempunit=='F' && $wuapiunit=='h' ){$wuskywindchill=($wuskywindchill*9/5)+32;}
// ms non metric to c Scandinavia 
if ($tempunit=='F' && $wuapiunit=='s' ){$wuskydayTempHigh=($wuskydayTempHigh*9/5)+32;}
if ($tempunit=='F' && $wuapiunit=='s' ){$wuskyheatindex=($wuskyheatindex*9/5)+32;}
if ($tempunit=='F' && $wuapiunit=='s' ){$wuskywindchill=($wuskywindchill*9/5)+32;}
// non metric to c US
if ($tempunit=='C' && $wuapiunit=='e' ){$wuskydayTempHigh=($wuskydayTempHigh-32)/1.8;}
if ($tempunit=='C' && $wuapiunit=='e' ){$wuskyheatindex=($wuskyheatindex-32)/1.8;}
if ($tempunit=='C' && $wuapiunit=='e' ){$wuskywindchill=($wuskywindchill-32)/1.8;}
//wind
// mph to kmh US
if ($windunit=='km/h' && $wuapiunit=='e' ){$wuskydayWindGust=(number_format($wuskydayWindGust,1)*1.60934);}
// mph to kmh UK
if ($windunit=='km/h' && $wuapiunit=='h' ){$wuskydayWindGust=(number_format((float)$wuskydayWindGust,1)*1.60934);}
//mph to ms US
if ($windunit=='m/s' && $wuapiunit=='e' ){$wuskydayWindGust=(number_format($wuskydayWindGust,1)*0.44704);}
//mph to ms uk
if ($windunit=='m/s' && $wuapiunit=='h' ){$wuskydayWindGust=(number_format($wuskydayWindGust,1)*0.44704);}
//kmh to ms
if ($windunit=='m/s' && $wuapiunit=='m' ){$wuskydayWindGust=(number_format($wuskydayWindGust,1)*0.277778);}
//kmh to mph
if ($windunit=='mph' && $wuapiunit=='m' ){$wuskydayWindGust=(number_format($wuskydayWindGust,1)*0.621371);}	
//rain inches to mm
if ($rainunit=='mm' && $wuapiunit=='e' ){$wuskydayprecipIntensity=$wuskydayprecipIntensity*25.4;}
//rain mm to inches scandinavia
if ($rainunit=='in' && $wuapiunit=='s' ){$wuskydayprecipIntensity=$wuskydayprecipIntensity*0.0393701;}
//rain mm to inches uk
if ($rainunit=='in' && $wuapiunit=='h' ){$wuskydayprecipIntensity2=($wuskydayprecipIntensity2 ?? null)*0.0393701;}
//rain mm to inches metric
if ($rainunit=='in' && $wuapiunit=='m' ){$wuskydayprecipIntensity=$wuskydayprecipIntensity*0.0393701;}
//icon + day wu
echo '<div class="wulargeforecastinghome">';echo '<div class="wulargeweekdayhome">'.$wuskydayTime."'s Forecast</div><div class=\"wulargehomeicons\">";
if ($wuskydaynight=='D'){echo '<img src="css/wuicons/'.$wuskydayIcon.'.svg" width="50" height="40" alt="'.$wuskydesc.'" title="'.$wuskydesc.'">';}
if ($wuskydaynight=='N'){echo '<img src="css/wuicons/nt_'.$wuskydayIcon.'.svg" width="50" height="40" alt="'.$wuskydesc.'" title="'.$wuskydesc.'">';}
echo '</div><wulargetempdesc><value>'.$wuskydesc.'</value></wulargetempdesc><br><br>';
//temp non metric wu
if($tempunit=='F' && $wuskydayTempHigh<44.6){echo '<wulargetemphihome><bluewu>'.number_format($wuskydayTempHigh,0).'°<wuunits>F</wuunits></bluewu></wulargetemphihome>';}
else if($tempunit=='F' && $wuskydayTempHigh>104){echo '<wulargetemphihome><purplewu>'.number_format($wuskydayTempHigh,0).'°<wuunits>F</wuunits></purplewu></wulargetemphihome>';}
else if($tempunit=='F' && $wuskydayTempHigh>80.6){echo '<wulargetemphihome><redwu>'.number_format($wuskydayTempHigh,0).'°<wuunits>F</wuunits></redwu></wulargetemphihome>';}
else if($tempunit=='F' && $wuskydayTempHigh>64.4){echo '<wulargetemphihome><orangewu>'.number_format($wuskydayTempHigh,0).'°<wuunits>F</wuunits></orangewu></wulargetemphihome>';}
else if($tempunit=='F' && $wuskydayTempHigh>55){echo '<wulargetemphihome><yellowwu>'.number_format($wuskydayTempHigh,0).'°<wuunits>F</wuunits></yellowwu></wulargetemphihome>';}
else if($tempunit=='F' && $wuskydayTempHigh>=44.6){echo '<wulargetemphihome><greenwu>'.number_format($wuskydayTempHigh,0).'°<wuunits>F</wuunits></greenwu></wulargetemphihome>';}
//temp metric wu
else if($wuskydayTempHigh<7){echo '<wulargetemphihome><bluewu>'.number_format((float)$wuskydayTempHigh,0).'°<wuunits>C</wuunits></bluewu></wulargetemphihome>';}
else if($wuskydayTempHigh>40){echo '<wulargetemphihome><purplewu>'.number_format($wuskydayTempHigh,0).'°<wuunits>C</wuunits></purplewu></wulargetemphihome>';}
else if($wuskydayTempHigh>27){echo '<wulargetemphihome><redwu>'.number_format($wuskydayTempHigh,0).'°<wuunits>C</wuunits></redwu></wulargetemphihome>';}
else if($wuskydayTempHigh>18){echo '<wulargetemphihome><orangewu>'.number_format($wuskydayTempHigh,0).'°<wuunits>C</wuunits></orangewu></wulargetemphihome>';}
else if($wuskydayTempHigh>12.7){echo '<wulargetemphihome><yellowwu>'.number_format($wuskydayTempHigh,0).'°<wuunits>C</wuunits></yellowwu></wulargetemphihome>';}
else if($wuskydayTempHigh>=7){echo '<wulargetemphihome><greenwu>'.number_format($wuskydayTempHigh,0).'°<wuunits>C</wuunits></greenwu></wulargetemphihome>';}
//wind wu
echo "<div class='wulargewindspeedicon'> Winds from  <blueu>";
echo $wuskydayWinddircardinal; 
echo " </blueu>at ".$windalert2." <div class=wuwindspeed> ".number_format((float)$wuskydayWindGust,0),"&nbsp;<wuunits>".$windunit;echo  '</wuunits></div></div>';'<br><br>';
echo "<div class='wulargerain'>";
//snow wu
if ( $wuskydaysnow>0 && $rainunit=='in'){ echo ' Snowfall Accumulation '.$snowflakesvg.'&nbsp;<wulargetempwindhome><span class="ink-oblue">&nbsp;'.$wuskydaysnow.'</span><wuunits> in</wuunits></wulargetempwindhome>';}
else if ( $wuskydaysnow>0 && $rainunit=='mm'){ echo 'Snowfall Accumulation '.$snowflakesvg.'&nbsp;<wulargetempwindhome><span class="ink-oblue">&nbsp;'.$wuskydaysnow.'</span><wuunits> cm</wuunits></wulargetempwindhome>';}
//rain wu
else if ($wuskydayPrecipType='rain' && $rainunit=='in'){echo 'Rainfall Accumulation '.$rainsvg.'&nbsp;<div class=wurainfall>'. number_format((float)$wuskydayprecipIntensity,2).'&nbsp;<wuunits>'.$rainunit.'</wuunits></div>';}
else if ($wuskydayPrecipType='rain' && $rainunit=='mm'){echo 'Rainfall Accumulation '.$rainsvg.'&nbsp;<div class=wurainfall>'. number_format((float)$wuskydayprecipIntensity,1).'&nbsp;<wuunits>'.$rainunit.'</wuunits></div>';}
//uvindex wu
echo '<br><div class=wulargeuvindex>UV Index &nbsp;<wuuvicon>&#9788;</wuuvicon>';
if ($wuskydayUV>=10){echo 	"<purplewuv>".$wuskydayUV. '</purplewuv><wuinfo>'.$wuskydayUVdesc.'</wuinfo>';}
else  if ($wuskydayUV>7){echo 	"<redwuv>".$wuskydayUV. '</redwuv><wuinfo> '.$wuskydayUVdesc.'</wuinfo>';}
else if ($wuskydayUV>5){echo 	"<orangewuv>".$wuskydayUV. '</orangewuv><wuinfo> '.$wuskydayUVdesc.'</wuinfo>';}
else if ($wuskydayUV>2){echo 	"<yellowwuv>".$wuskydayUV. '</yellowwuv><wuinfo> '.$wuskydayUVdesc.'</wuinfo>';}
else if ($wuskydayUV>0){echo 	"<greenwuv>".$wuskydayUV. '</greenwuv><wuinfo>'.$wuskydayUVdesc.'</wuinfo>';}	
else if ($wuskydayUV==0){echo 	"<greywuv>".$wuskydayUV. '</greywuv><wuinfo> No Caution</wuinfo>';}echo '</div>';
///wuheat index F
echo "<div class=wulargeheatindex>";
if ($tempunit=='F' && $wuskyheatindex>=84.2){echo "Heat Index ".$heatindexwu."<heatindexwu>".number_format($wuskyheatindex,0). '°<wuunits>F</wuunits></heatindexwu>';}
//wu heat index C
if ($tempunit=='C' && $wuskyheatindex>=29){echo "Heat Index ".$heatindexwu." <heatindexwu>".number_format($wuskyheatindex,0). '°<wuunits>C</wuunits></heatindexwu>';}
//lightning wu
echo '</div><div class="wulargeheatindex wulargeheatindex--alert">';
if ($wuskythunderidx>0 )  {echo 'Thunderstorms expected '.$wuskydayTime;}
else if ($wuskythunderidx1>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime1. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx2>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime2. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx3>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime3. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx4>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime4. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx5>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime5. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx6>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime6. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx7>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime7. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx8>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime8. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx9>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime9. '&nbsp;'.$lightningalert8.'';}
else if ($wuskythunderidx10>0 )  {echo $infowu.'<span class="ink-ored">Thunder</span> '.$wuskydayTime10. '&nbsp;'.$lightningalert8.'';}
//snowfall wu
else if (($wuskysnow ?? null)>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime. '&nbsp;'.$freezing.'';}
else if ($wuskysnow1>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime1. '&nbsp;'.$freezing.'';}
else if ($wuskysnow2>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime2. '&nbsp;'.$freezing.'';}
else if ($wuskysnow3>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime3. '&nbsp;'.$freezing.'';}
else if ($wuskysnow4>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime4. '&nbsp;'.$freezing.'';}
else if ($wuskysnow5>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime5. '&nbsp;'.$freezing.'';}
else if ($wuskysnow6>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime6. '&nbsp;'.$freezing.'';}
else if ($wuskysnow7>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime7. '&nbsp;'.$freezing.'';}
else if ($wuskysnow8>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime8. '&nbsp;'.$freezing.'';}
else if ($wuskysnow9>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime9. '&nbsp;'.$freezing.'';}
else if ($wuskysnow10>0 )  {echo $infowu.'<blue>Snow</blue> '.$wuskydayTime10. '&nbsp;'.$freezing.'';}
//rainfall wu
else if (($wuskyrain ?? null)>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime. '&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain1>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime1.'&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain2>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime2.'&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain3>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime3. '&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain4>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime4. '&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain5>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime5. '&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain6>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime6. '&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain7>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime7. '&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain8>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime8. '&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain9>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime9. '&nbsp;'.$rainfallalert8.'';}
else if ($wuskyrain10>0 )  {echo $infowu.'<blue>Rain</blue> '.$wuskydayTime10. '&nbsp;'.$rainfallalert8.'';}
//heat index today caution wu
else if ($tempunit=='C' && $wuskyheatindex>29 )  {echo $infowu.'<wuheatindex>Heat Index </wuheatindex>High '.$wuskydayTime. '&nbsp;'.$lightningalert8.'';}
else if ($tempunit=='F' && $wuskyheatindex>84 )  {echo $infowu.'<wuheatindex>Heat Index </wuheatindex>High '.$wuskydayTime. '&nbsp;'.$lightningalert8.'';}
//uv index wu
else if ($wuskydayUV1>6 )  {echo $infowu.'<span class="ink-ored">High UV </span> '.$wuskydayTime1.'&nbsp;'.$lightningalert8.'';}
else if ($wuskydayUV2>6 )  {echo $infowu.'<span class="ink-ored">High UV </span> '.$wuskydayTime2.'&nbsp;'.$lightningalert8.'';}
else if ($wuskydayUV3>6 )  {echo $infowu.'<span class="ink-ored">High UV </span> '.$wuskydayTime3. '&nbsp;'.$lightningalert8.'';}
else if ($wuskydayUV4>6 )  {echo $infowu.'<span class="ink-ored">High UV </span> '.$wuskydayTime4. '&nbsp;'.$lightningalert8.'';}
else if ($wuskydayUV5>6 )  {echo $infowu.'<span class="ink-ored">High UV </span> '.$wuskydayTime5. '&nbsp;'.$lightningalert8.'';}
else if ($wuskydayUV6>6 )  {echo $infowu.'<span class="ink-ored">High UV </span> '.$wuskydayTime6. '&nbsp;'.$lightningalert8.'';}
else if ($wuskydayUV7>6 )  {echo $infowu.'<span class="ink-ored">High UV </span> '.$wuskydayTime7. '&nbsp;'.$lightningalert8.'';}
else if ($wuskydayUV8>6 )  {echo $infowu.'<span class="ink-ored">High UV </span> '.$wuskydayTime8. '&nbsp;'.$lightningalert8.'';}
else echo $lightningalert8. "&nbsp;No Cautions";
echo '</div></div>';?></div></div></div>
</div><!-- /mod-forecast -->
