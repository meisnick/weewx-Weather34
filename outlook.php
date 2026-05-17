<?php
include_once('settings.php');include('w34CombinedData.php');
	####################################################################################################
	#	HOME WEATHER STATION TEMPLATE by BRIAN UNDERDOWN 2016-2019                                     #
	#	CREATED FOR HOMEWEATHERSTATION TEMPLATE at https://weather34.com/homeweatherstation/index.html # 
	# 	                                                                                               #
	# 	                                                                                               #
	# 	FORECAST WEATHER UNDERGROUND WEATHER FORECAST: FEB 2109  			                           #
	# 	                                                                                               #
	#   https://www.weather34.com 	                                                                   #
	####################################################################################################
	//original weather34 script original css/svg/php by weather34 2015-2019 clearly marked as original by weather34//
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo "${stationName}";?> <?php echo 'Forecast' ;?> </title>
		
		<style>
		@font-face{font-family:system;font-style:normal;src:local(".SFNSText-Light"),local("Arial")}
		@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(css/fonts/verbatim-regular.woff2) format("woff2"),url(css/fonts/verbatim-regular.ttf) format("truetype")}
body{background:rgba(11, 12, 12, 0.4)}		
.forecastforecasting{float:left;display:block;margin-right:0;width:40%;border-radius:4px;margin:2px;margin-top:5px;font-family:weathertext2;margin-left:5px;height:210px;padding:5px;background-color:rgba(253, 166, 16, 1.000);border:1px solid rgba(153,155,156,0.3);color:#aaa;font-size:0.6rem;color:#aaa;font-family:weathertext2;line-height:12px}
forecastweekday{position:absolute;margin:3px;background-color:rgba(253, 166, 16, 1.000);text-align:center;padding:5px;color:#aaa;font-family:weathertext2;font-size:11px;margin-bottom:20px;border-radius:4px;font-size:0.6rem;color:#aaa;font-family:weathertext2;line-height:15px}forecasttemphi{margin-top:5px;font-size:0.6rem;color:rgba(255,124,57,1);font-family:weathertext2;margin-left:10%}forecasttemphi span{font-size:0.6rem;color:#aaa}forecasttemplo{margin-top:5px;font-size:10px;color:#00a4b4;font-family:weathertext2}forecasttemplo span{font-size:10px;color:#aaa;font-family:weathertext2}forecastsummary{font-size:0.5rem;color:#aaa;font-family:weathertext2;line-height:11px}forecastwindspeed{font-size:10px;color:#aaa;font-family:weathertext2;line-height:11px}.forecastwindspeedicon{position:absolute;font-size:0.6rem;color:#aaa;font-family:weathertext2;line-height:11px;margin-top:-55px;margin-left:67px}.forecastwindgust{position:absolute;font-size:0.6rem;color:#aaa;font-family:weathertext2;line-height:11px;margin-top:-55px;margin-left:97px}.forecastdiv{position:relative;width:700px;overflow:hidden!important;height:365px;float:none;margin-left:2%;margin-top:5px}
.forecastforecastinghome{font-size:10px;float:left;display:inline;margin-right:0;width:21%;border-radius:4px;margin:5px;font-family:weathertext2,system;margin-left:0;height:160px;padding:5px;
background: rgba(29, 32, 34, 1.000);background: linear-gradient(to bottom, rgba(97, 106, 114, 1.000) 12%,rgba(29, 32, 34, 0) 11%,rgba(29, 32, 34, 0) 100%,rgba(229, 77, 11, 0) 0%);
background: -webkit-linear-gradient(to bottom, rgba(97, 106, 114, 1.000) 12%,rgba(29, 32, 34, 0) 11%,rgba(29, 32, 34,0) 100%,rgba(229, 77, 11, 0) 0%);
background: -moz-linear-gradient(to bottom, rgba(97, 106, 114, 1.000) 12%,rgba(29, 32, 34, 0) 11%,rgba(29, 32, 34, 0) 100%,rgba(229, 77, 11, 0) 0%);
background: -o-linear-gradient(to bottom, rgba(97, 106, 114, 1.000) 12%,rgba(29, 32, 34,0) 11%,rgba(29, 32, 34, 0) 100%,rgba(229, 77, 11, 0) 0%);
border:0;color:#aaa;overflow:hidden!important;margin-bottom:5px;border:solid 1px #444;border-bottom:solid 1px #444;border-top:1px solid rgba(97, 106, 114, 1.000);}
.forecastweekdayhome{postion:absolutue;text-align:center;padding:0;color:#fff;font-family:weathertext2;font-size:0.6rem;margin:0;background:0;margin-bottom:12px;}
.forecastforecasthome forecasttemphihome{font-size:0.6rem;color:#ff7c39;font-family:weathertext2;line-height:10px}.forecastforecasthome forecasttemphihome span{font-size::0.6rem;color:#ff7c39;font-family:weathertext2;line-height:10px}.forecastforecasthome forecasttemplohome{font-size:0.6rem;color:#ff7c39;font-family:weathertext2;line-height:10px}.forecastforecasthome forecasttemplohome span{font-size:0.6rem;color:#01a4b5;font-family:weathertext2}.forecastforecasthome forecasttempwindhome{font-size:10px;color:#aaa;font-family:weathertext2;line-height:10px}.forecastforecasthome forecasttempwindhome span{font-size:0.6rem;color:#aaa;font-family:weathertext2;line-height:10px}.forecastforecasthome forecasttempwindhome span2{font-size:0.6rem;color:#aaa;font-family:weathertext2;line-height:10px;margin-top:3px}.forecasticoncurrent img{position:relative;width:80px;margin-top:-50px;margin-left:0;margin-bottom:-10px;margin-right:0;padding-right:0;float:left}.forecastnexthours{line-height:12px}.forecastnexthours span2{line-height:12px}body{line-height:11px}grey{color:#aaa}blue1{color:#01a4b5;text-transform:capitalize}orange1{color:#ff7c39}green{color:rgba(144,177,42,1)}a{font-size:10px;color:#aaa;text-decoration:none!important;font-family:weathertext2}.forecastupdated{position:absolute;font-size:10px;color:#aaa;font-family:weathertext2;bottom:25px;float:right;margin-left:575px}	
.weather34darkbrowser{font-family:weathertext2, Helvetica, sans-serif;position:relative;background:0;width:103%;max-height:30px;margin:auto;margin-top:-15px;margin-left:-20px;border-top-left-radius:5px;border-top-right-radius:5px;padding-top:45px;background-image:radial-gradient(circle,#EB7061 6px,transparent 8px),radial-gradient(circle,#F5D160 6px,transparent 8px),radial-gradient(circle,#81D982 6px,transparent 8px),radial-gradient(circle,rgba(97,106,114,1) 2px,transparent 2px),radial-gradient(circle,rgba(97,106,114,1) 2px,transparent 2px),radial-gradient(circle,rgba(97,106,114,1) 2px,transparent 2px),linear-gradient(to bottom,rgba(59,60,63,0.4) 40px,transparent 0);background-position:left top,left top,left top,right top,right top,right top,0 0;background-size:50px 45px,90px 45px,130px 45px,50px 30px,50px 45px,50px 60px,100%;background-repeat:no-repeat,no-repeat}
.weather34darkbrowser[url]:after{content:attr(url);color:#aaa;font-size:11px;position:absolute;left:0;right:0;top:0;padding:5px 15px;margin:11px 50px 0 90px;border-radius:3px;background:rgba(97, 106, 114, 0.3);height:20px;box-sizing:border-box}precip{position:relative;top:5px;padding:2px;border-radius:3px;background:rgba(97,106,114,0.2);}value{font-size:.8em;font-family:weathertext2}value1{font-size:1em;font-family:weathertext2}
bluetds,greentds,orangetds,purpletds,redtds,yellowtds{color:#fff;text-transform:capitalize;border-radius:2px;width:35px;padding:0 3px;font-size:11px;}
bluetds{background:#01a4b5}yellowtds{background:#e6a141}orangetds{background:#d05f2d}greentds{background:#90b12a}redtds{background:-webkit-linear-gradient(90deg,#d86858,rgba(211,93,78,.7));background:linear-gradient(90deg,#d86858,rgba(211,93,78,.7))}purpletds{background:-webkit-linear-gradient(90deg,#d86858,rgba(157,59,165,.4));background:linear-gradient(90deg,#d86858,rgba(157,59,165,.4))}
blueu,greenu,orangeu,purpleu,redu,yellowu,zerou{color:#fff;border-radius:2px;width:35px;font-size:11px;padding:0 3px}
blueu{background:#01a4b5}zerou{color:#777}yellowu{background:#e6a141}orangeu{background:#d05f2d}greenu{background:#90b12a}redu{background:#cd5245}purpleu{background:#b600b0}zerou{background:#4a636f}

</style>
</head>
<body>
<div class="weather34darkbrowser" url="<?php echo "${stationName} \n";?> Forecast  (<?php echo $weather["temp_units"]?>&deg;)"></div>
		<div style="position:absolute;width:725px;background:none;margin:0 auto;margin-left:7%;margin-top:5px;">
			
        <br>
		<script src="js/jquery.js"></script>
		 <div class="forecastforecasthome">
		<div class="forecastdiv"><value>
<?php
        
        foreach ($forecastdayCond as $cond) {
            $forecastdayTime = $cond['time'];
            $forecastdaySummary = $cond['summary'];
            $forecastdayIcon = $cond['icon'];
            if ($weather["temp_units"]=='F'){ $forecastdayTempHigh = round(32 +(9*$cond['temperatureMax']/5));}else $forecastdayTempHigh = round($cond['temperatureMax']);
			if ($weather["temp_units"]=='F'){ $forecastdayTempLow = round(32 +(9*$cond['temperatureMin']/5));}else $forecastdayTempLow = round($cond['temperatureMin']);
			$forecastdayWinddir = $cond['windBearing'];
			$forecastdayClouds = $cond['cloudCover']*100;
            $forecastdayHumidity = $cond['humidity']*100;
			$forecastdayUV = $cond['uvIndex'];
			$forecastdayPrecipProb = $cond['precipProbability']*100;
			
           if (isset($cond['precipType'])){$forecastdayPrecipType = $cond['precipType'];}
if ($rainunit=='in'){ $forecastdayprecipIntensity=number_format($cond['precipIntensity'],2);} 
else $forecastdayprecipIntensity = number_format($cond['precipIntensity']*25.4,1);
if ($rainunit=='in'){$forecastdayacumm=round($cond['precipAccumulation']*0.393701,1);}
else {$forecastdayacumm=round($cond['precipAccumulation'],1);}
//convert all the scenarios
if ($weather["temp_units"]=='C' && $forecastunit=='us'){ $forecastdayTempHigh = round($cond['temperatureMax']-32)*5/9;}
else if ($weather["temp_units"]=='F' && $forecastunit=='si'){ $forecastdayTempHigh = round(32 +(9*$cond['temperatureMax']/5));}
else if ($weather["temp_units"]=='F' && $forecastunit=='uk2'){ $forecastdayTempHigh = round(32 +(9*$cond['temperatureMax']/5));}
else if ($weather["temp_units"]=='F' && $forecastunit=='ca'){ $forecastdayTempHigh = round(32 +(9*$cond['temperatureMax']/5));}
else $forecastdayTempHigh = round($cond['temperatureMax']);
if ($weather["temp_units"]=='C' && $forecastunit=='us'){ $forecastdayTempLow = round($cond['temperatureMin']-32)*5/9;}
else if ($weather["temp_units"]=='F' && $forecastunit=='si'){ $forecastdayTempLow = round(32 +(9*$cond['temperatureMin']/5));}
else if ($weather["temp_units"]=='F' && $forecastunit=='uk2'){ $forecastdayTempLow = round(32 +(9*$cond['temperatureMin']/5));}
else if ($weather["temp_units"]=='F' && $forecastunit=='ca'){ $forecastdayTempLow = round(32 +(9*$cond['temperatureMin']/5));}
else $forecastdayTempLow = round($cond['temperatureMin']);


   //si wind is m/s
if ($weather["wind_units"] == 'mph' && $forecastunit=='si') {$windspeedconversion =2.23694;} 
else if ($weather["wind_units"] == 'km/h' && $forecastunit=='si') {$windspeedconversion = 3.6000059687997;} 
else if ($weather["wind_units"] == 'm/s' && $forecastunit=='si') {$windspeedconversion = 1;}
//ca wind is m/s
if ($weather["wind_units"] == 'mph' && $forecastunit=='ca') {$windspeedconversion = 2.23694;} 
else if ($weather["wind_units"] == 'km/h' && $forecastunit=='ca') {$windspeedconversion = 3.6000059687997;} 
else if ($weather["wind_units"] == 'm/s' && $forecastunit=='ca') {$windspeedconversion = 1;} 
//us wind is mph
if ($weather["wind_units"] == 'mph' && $forecastunit=='us') {$windspeedconversion =1;} 
else if ($weather["wind_units"] == 'km/h' && $forecastunit=='us') {$windspeedconversion = 1.6093466682922179523;} 
else if ($weather["wind_units"] == 'm/s' && $forecastunit=='us') {$windspeedconversion = 0.4470407411923185137;} 
//uk2 wund is mph
if ($weather["wind_units"] == 'mph' && $forecastunit=='uk2') {$windspeedconversion =1;} 
else if ($weather["wind_units"] == 'km/h' && $forecastunit=='uk2') {$windspeedconversion = 1.6093466682922179523;} 
else if ($weather["wind_units"] == 'm/s' && $forecastunit=='uk2') {$windspeedconversion = 0.4470407411923185137;}     
$forecastdayWindSpeed = round($cond['windSpeed']*$windspeedconversion,0);
$forecastdayWindGust = round($cond['windGust']*$windspeedconversion,0);
            	  echo '<div class="forecastforecastinghome">';  
                  echo '<div class="forecastweekdayhome">'.strftime("%a %b %e", $forecastdayTime).'</div>';  
				  if ($forecastdayacumm>0 ){echo '<img src="css/forecasticons/snow.svg" width="40"></img><br>';} 
				  else if ($forecastdayIcon == 'partly-cloudy-night'){echo '<img src="css/forecasticons/partly-cloudy-day.svg" width="40"></img><br>';}  
				  else echo '<img src="css/forecasticons/'.$forecastdayIcon.'.svg" width="40"></img><br>';	  
				  
				  echo '<forecasttemphihome><span>';
				  
				   echo " <hilo> </hilo>";
if($tempunit=='F' && $forecastdayTempHigh<44.6){echo '<bluetds>'.number_format($forecastdayTempHigh,0).'°</bluetds>';}
else if($tempunit=='F' && $forecastdayTempHigh>104){echo '<purpletds>'.number_format($forecastdayTempHigh,0).'°</purpletds>';}
else if($tempunit=='F' && $forecastdayTempHigh>80.6){echo '<forecasttemphihome><redtds>'.number_format($forecastdayTempHigh,0).'°</redtds>';}
else if($tempunit=='F' && $forecastdayTempHigh>64){echo '<forecasttemphihome><orangetds>'.number_format($forecastdayTempHigh,0).'°</orangetds>';}
else if($tempunit=='F' && $forecastdayTempHigh>55){echo '<forecasttemphihome><yellowtds>'.number_format($forecastdayTempHigh,0).'°</yellowtds>';}
else if($tempunit=='F' && $forecastdayTempHigh>=44.6){echo '<forecasttemphihome><greentds>'.number_format($forecastdayTempHigh,0).'°</greentds>';}
//temp metric
else if($forecastdayTempHigh<7){echo '<bluetds>'.number_format($forecastdayTempHigh,0).'°</bluet>';}
else if($forecastdayTempHigh>40){echo '<purpletsd>'.number_format($forecastdayTempHigh,0).'°</purpletds';}
else if($forecastdayTempHigh>27){echo '<redtds>'.number_format($forecastdayTempHigh,0).'°</redtds>';}
else if($forecastdayTempHigh>17.7){echo '<orangetds>'.number_format($forecastdayTempHigh,0).'°</orangetds>';}
else if($forecastdayTempHigh>12.7){echo '<yellowtds>'.number_format($forecastdayTempHigh,0).'°</yellowtds>';}
else if($forecastdayTempHigh>=7){echo '<greentds>'.number_format($forecastdayTempHigh,0).'°</greentds>';}

'°<grey> | </grey></span></forecasttemphihome>';
				   
				   
				   
				  echo '<forecasttemplohome><span>';
				  
			 echo " <hilo> </hilo>";
if($tempunit=='F' && $forecastdayTempLow<44.6){echo '<bluetds>'.number_format($forecastdayTempLow,0).'°</bluetds>';}
else if($tempunit=='F' && $forecastdayTempLow>104){echo '<purpletds>'.number_format($forecastdayTempLow,0).'°</purpletds>';}
else if($tempunit=='F' && $forecastdayTempLow>80.6){echo '<forecasttemphihome><redtds>'.number_format($forecastdayTempLow,0).'°</redtds>';}
else if($tempunit=='F' && $forecastdayTempLow>64){echo '<forecasttemphihome><orangetds>'.number_format($forecastdayTempLow,0).'°</orangetds>';}
else if($tempunit=='F' && $forecastdayTempLow>55){echo '<forecasttemphihome><yellowtds>'.number_format($forecastdayTempLow,0).'°</yellowtds>';}
else if($tempunit=='F' && $forecastdayTempLow>=44.6){echo '<forecasttemphihome><greentds>'.number_format($forecastdayTempLow,0).'°</greentds>';}
//temp metric
else if($forecastdayTempLow<7){echo '<bluetds>'.number_format($forecastdayTempLow,0).'°</bluet>';}
else if($forecastdayTempLow>40){echo '<purpletsd>'.number_format($forecastdayTempLow,0).'°</purpletds';}
else if($forecastdayTempLow>27){echo '<redtds>'.number_format($forecastdayTempLow,0).'°</redtds>';}
else if($forecastdayTempLow>17.7){echo '<orangetds>'.number_format($forecastdayTempLow,0).'°</orangetds>';}
else if($forecastdayTempLow>12.7){echo '<yellowtds>'.number_format($forecastdayTempLow,0).'°</yellowtds>';}
else if($forecastdayTempLow>=7){echo '<greentds>'.number_format($forecastdayTempLow,0).'°</greentds>';}

echo '</span></forecasttemplohome>';  
//uvindex
echo '<forecasttemplohome><grey> '.$sunlight.' UVI <orange1>';
if ($forecastdayUV>=10){echo "<purpleu>".$forecastdayUV;}
else if ($forecastdayUV>7){echo "<redu>".$forecastdayUV;}
else if ($forecastdayUV>5){echo "<orangeu>".$forecastdayUV;}
else if ($forecastdayUV>2){echo "<yellowu>".$forecastdayUV;}
else if ($forecastdayUV>0){echo "<greenu>".$forecastdayUV;}	
else if ($forecastdayUV==0){echo "<zerou>".$forecastdayUV;}				  
echo '</orange1></grey></forecasttemplohome><br>';  
//wind			  
echo "<br><div class='forecastwindspeedicon'><img src = 'css/windicons/avgw.svg' width='20' style='-webkit-transform:rotate(".$forecastdayWinddir."deg);-moz-transform:rotate(".$forecastdayWinddir."deg);-o-transform:rotate(".$forecastdayWinddir."deg);transform:rotate(".$forecastdayWinddir."deg)'>					   
				   ";			
				   
				   echo '&nbsp;&nbsp;&nbsp;';
				 if ($forecastdayWinddir <15 ) echo 'North';
				  elseif ($forecastdayWinddir <45 ) echo 'NNE';
				  elseif ($forecastdayWinddir <90 ) echo 'ENE';
				  elseif ($forecastdayWinddir <110 ) echo 'East';
				  elseif ($forecastdayWinddir <150 )  echo 'SE';
				  elseif ($forecastdayWinddir <170 )  echo 'SSE';
				  elseif ($forecastdayWinddir <190 ) echo 'South';
				  elseif ($forecastdayWinddir <220 ) echo 'SSW';
				  elseif ($forecastdayWinddir <250 ) echo 'SW';
				  elseif ($forecastdayWinddir <270 ) echo 'West';
				  elseif ($forecastdayWinddir <300 ) echo 'NW';
				  elseif ($forecastdayWinddir <340 ) echo 'NWN';
				  elseif ($forecastdayWinddir <360 ) echo 'North';
				  echo  '</div>';					   	 
				  echo "<div class='forecastwindgust'>".$forecastdayWindGust	." ".$windunit."</div>";
				  echo '<forecasttempwindhome><span>'.$forecastdaySummary.' </forecastwindhome></span><br>';
				  if ( $forecastdayacumm>0 && $weather['temp_units']=='F'){
				  echo ''.$snowflakesvg.'&nbsp;<forecasttempwindhome><span>Snow <blue1>&nbsp;'.$forecastdayacumm.'</blue1> in</forecastwindhome><br></span>';}  
				  else if ( $forecastdayacumm>0 && $weather['temp_units']=='C'){
				  echo ''.$snowflakesvg.'&nbsp;<forecasttempwindhome><span>Snow <blue1>&nbsp;'.$forecastdayacumm.'</blue1> cm</forecastwindhome><br></span>';}  				  
				  else if ($forecastdayPrecipType='rain'){
				  echo ''.$rainsvg.'&nbsp;<forecasttempwindhome><span>Rain <blue1>&nbsp;'. $forecastdayprecipIntensity.'</blue1>&nbsp;'.$weather["rain_units"].'&nbsp;<blue1>'.$forecastdayPrecipProb.'</blue1>%</forecastwindhome></span>';}  
				   
				  echo  '</div>';}?></div></div></div>                
                  
 <div style="position:absolute;bottom:5px;z-index:9999;font-weight:normal;font-size:10px;color:#aaa;text-decoration:none !important;float:right;font-family:weathertext2;">
  
   &nbsp;&nbsp;data provided by <a href="https://forecast.net/about" title="https://forecast.net/about" target="_blank">DarkSky</a> -- <?php echo $info;?><a href="https://weather34.com" title="weather34.com" target="_blank">weather34 original CSS/SVG/PHP Script</a></div>
  </body>
  </html>
