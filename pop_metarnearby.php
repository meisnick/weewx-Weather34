<?php include_once('w34_i18n.php'); ?>
<?php include('metar34get.php');
//weather34 original metarnearby script 201-2019//
if (isset($_GET['theme'])) {
    $theme = ($_GET['theme'] === 'light') ? 'light' : 'dark';
}
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('LinkNearbyMetar', 'Nearby Metar'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/tokens.css">
<style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype")}
html,body
  {
    font-size:13px;
    font-family: "weathertext2", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;	
    -moz-osx-font-smoothing: grayscale;
    background-color:var(--pop-bg);
    color:var(--pop-ink);
  }
a:link,a:visited,a:hover,a:active {
  color: var(--pop-ink);
}
[data-theme="light"] a:link,[data-theme="light"] a:visited,[data-theme="light"] a:hover,[data-theme="light"] a:active {
  color: var(--pop-cyan);
}
.grid { 
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 2fr));
  grid-gap: 4px;
  align-items: stretch;
  color:var(--pop-ink);
  padding: 4px 44px 4px 4px;
  box-sizing: border-box;
}
[data-theme="light"] .grid {
  color:#000;
}
.grid > article {
  border: 1px solid rgba(245, 247, 252, .02);
  box-shadow: 2px 2px 6px 0px rgba(0,0,0,0.6);
  padding:5px;
  font-size:0.8em;
  -webkit-border-radius:4px;
  border-radius:4px;
  background:hsla(233,12%,13%,.5);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
[data-theme="light"] .grid > article {
  background:#ffffff;
  border: 1px solid rgba(0,0,0,0.08);
  box-shadow: 2px 2px 6px 0px rgba(0,0,0,0.1);
}
.grid > article img {
  max-width: 100%;
}
.weather34darkbrowser {
  position: relative;
  width: 97%;
  height: 30px;
  margin: auto;
  margin-top: -5px;
  margin-left: 0px;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  padding-top: 10px;
  margin-right: 44px;
  color: var(--pop-ink);
}
[data-theme="light"] .weather34darkbrowser {
  color: #000;
}

.weather34darkbrowser[url]:after {
  content: attr(url);
  font-size: 14px;
  text-align: center;
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  padding: 4px 15px;
  margin: 11px 44px 0 auto;
  font-family: arial;
  height: 20px;
} 
  blue{color:var(--pop-blue)}orange{color:var(--pop-cyan)}orange1{position:relative;color:var(--pop-cyan);margin:0 auto;text-align:center;margin-left:5%;font-size:1.1rem}green{color:var(--pop-green-dim)}red{color:var(--pop-red)}red6{color:var(--pop-red-dark)}value{color:var(--pop-value)}yellow{color:var(--pop-yellow-dim)}purple{color:var(--pop-purple)}
.metar34compass1>.metar34compass-line1,.metar34compass>.metar34compass-line{right:25px;-webkit-clip-path:polygon(100%0,100%100%,100%100%,0100%,00);-ms-clip-path:polygon(100%0,100%100%,100%100%,0100%,00);-webkit-border-radius:100%;-moz-border-radius:100%;-ms-border-radius:100%}
.text1{font-family:weathertext2,Arial;font-size:20px;margin-left:3px;}
.text1{color:var(--pop-green-dim)}
.metar34compass1{position:relative;width:165px;height:165px;margin:0 auto;z-index:1;text-align:center;align-items:center;justify-content:center;}
.directiontext{display:flex;font-size:1.2em;position:absolute;text-align:center;align-items:center;justify-content:center;vertical-align: middle;top:35%;width:100%;left:0;right:0}

.directiontext span{color:var(--pop-cyan)}
text1{z-index:10;text-align:center;margin:5px 0 auto}
.metar34compass1>.metar34compass-line1{position:absolute;z-index:10;left:24px;top:25px;bottom:25px;-o-border-radius:100%;border-radius:100%;border-left:8px solid rgba(95,96,97,.5);border-top:8px solid rgba(95,96,97,.8);border-right:8px solid rgba(95,96,97,.5);border-bottom:8px solid rgba(95,96,97,.8);}
.thearrow2{-webkit-transform:rotate(<?php echo $metar34windir;?>deg);-moz-transform:rotate(<?php echo $metar34windir;?>deg);-o-transform:rotate(<?php echo $metar34windir;?>deg);-ms-transform:rotate(<?php echo $metar34windir;?>deg);transform:rotate(<?php echo $metar34windir;?>deg);position:absolute;z-index:200;top:0;left:50%;margin-left:-5px;width:10px;height:50%;-webkit-transform-origin:50% 100%;-moz-transform-origin:50% 100%;-o-transform-origin:50% 100%;-ms-transform-origin:50% 100%;transform-origin:50% 100%;-webkit-transition-duration:3s;-moz-transition-duration:3s;-o-transition-duration:3s;-ms-transition-duration:3s;transition-duration:3s}.thearrow2:after{content:'';position:absolute;left:50%;top:0;height:10px;width:10px;background-color:NONE;width:0;height:0;border-style:solid;border-width:14px 9px 0 9px;border-color:RGBA(255,121,58,1.00) transparent transparent transparent;-webkit-transform:translate(-50%,-50%);-moz-transform:translate(-50%,-50%);-o-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);transform:translate(-50%,-50%);-webkit-transition-duration:3s;-moz-transition-duration:3s;-o-transition-duration:3s;-ms-transition-duration:3s;transition-duration:3s}.thearrow2:before{content:'';width:3px;height:3px;position:absolute;z-index:9;left:2px;top:-5px;border:1px solid RGBA(255,255,255,0.8);-webkit-border-radius:100%;-moz-border-radius:100%;-o-border-radius:100%;-ms-border-radius:100%;border-radius:100%}
spancalm{postion:relative;font-family:weathertext2,Arial;font-size:16px;}
.metartempcontainer1{left:70px;top:0}
.metarwindcontainer1{margin-top:0;margin-left:5px;position:relative}
/*kts*/
.metarwindcontainer2{margin-top:10px;margin-left:0px;position:relative}
/*mph*/
.metarwindcontainer3{margin-top:-95px;margin-left:85px;position:relative}
/*ms*/
.metarwindcontainer4{margin-top:10px;margin-left:85px;position:relative}
.metartemptoday0,.metartemptoday5,.metartemptoday10,.metartemptoday20,.metartemptoday25,.metartemptoday30{font-family:weathertext2,Arial,Helvetica,system;width:4.5rem;height:2.5rem;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;display:flex}.metartemptoday0,.metartemptoday5,.metartemptoday10,.metartemptoday20,.metartemptoday25,.metartemptoday30{font-size:1.1rem;padding-top:0;color:var(--pop-badge-text);border-bottom:12px solid rgba(56,56,60,1);align-items:center;justify-content:center;border-radius:3px;margin-bottom:21px;}
.metartemptoday0{background:rgba(68, 166, 181, 1.000)}.metartemptoday5{background:rgba(144, 177, 42, 1.000)}.metartemptoday10{background:rgba(230, 161, 65, 1.000)}.metartemptoday20{background:rgba(255, 124, 57, 1.000)}.metartemptoday25{background:rgba(255, 124, 57, 0.7)}.metartemptoday30{background:rgba(211, 93, 78, 1.000)}.metardewcontainer1{left:70px;margin-top:10px}
.metardewtoday0,.metardewtoday5,.metardewtoday10,.metardewtoday20,.metardewtoday25,.metardewtoday30{font-family:weathertext2,Arial,Helvetica,system;width:4.5rem;height:2.5rem;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;display:flex}.metardewtoday0,.metardewtoday5,.metardewtoday10,.metardewtoday20,.metardewtoday25,.metardewtoday30{font-size:1.1rem;padding-top:0;color:var(--pop-badge-text);border-bottom:12px solid rgba(56,56,60,1);align-items:center;justify-content:center;border-radius:3px;margin-bottom:21px;}
.metardewtoday0{background:rgba(68, 166, 181, 1.000)}.metardewtoday5{background:rgba(144, 177, 42, 1.000)}.metardewtoday10{background:rgba(230, 161, 65, 1.000)}.metardewtoday20{background:rgba(255, 124, 57, 1.000)}.metardewtoday25{background:rgba(255, 124, 57, 0.7)}.metardewtoday30{background:rgba(211, 93, 78, 1.000)}
.metarhumcontainer1{position:relative;top:-100px;font-size:.7rem;z-index:1;color:var(--pop-badge-text);margin-left:92px;display:inline-block;}
.humword{position:relative;top:-90px;font-size:.65rem;z-index:1;color:var(--pop-badge-text);margin-left:102px}
.metarhumtoday0-35,.metarhumtoday35-70,.metarhumtoday70-85,.metarhumtoday85-100{font-family:weathertext2,Arial,Helvetica,system;width:4.5rem;height:2.5rem;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;display:flex}
.metarhumtoday0-35,.metarhumtoday35-70,.metarhumtoday70-85,.metarhumtoday85-100{font-size:1.1rem;padding-top:2px;color:var(--pop-badge-text);border-bottom:12px solid rgba(56,56,60,1);align-items:center;justify-content:center;border-radius:3px;margin-bottom:-70px;}
.metarhumtoday0-35{background:rgba(211, 93, 78, 1.000)}.metarhumtoday35-70{background:rgba(230, 161, 65, 1.000)}.metarhumtoday70-85{background:rgba(230, 161, 65, 1.000)}.metarhumtoday85-100{background:rgba(68, 166, 181, 1.000)}
.dewword,.tword{position:absolute;margin-top:-33px;font-size:.7rem;z-index:1;color:var(--pop-badge-text)}
.dewword{margin-left:8px}.tword{margin-left:20px}.tword2{position:absolute;margin-top:-32px;font-size:.65rem;z-index:1;color:var(--pop-badge-text)}
.dewword2{position:absolute;margin-top:33px;font-size:.65rem;z-index:1;color:var(--pop-badge-text);margin-left:75px}.tword2{margin-left:70px}
.metarwindtoday10,.metarwindtoday20,.metarwindtoday25,.metarwindtoday30{font-family:weathertext2,Arial,Helvetica,system;width:5rem;height:2.5rem;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;display:flex}
.metarwindtoday10,.metarwindtoday20,.metarwindtoday25,.metarwindtoday30{font-size:1.1rem;padding-top:0;color:var(--pop-badge-text);border-bottom:10px solid rgba(56,56,60,1);align-items:center;justify-content:center;border-radius:3px;display:flex}
.actualt{position:relative;left:5px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:rgba(74, 99, 111, 0.1);
padding:5px;font-family:Arial, Helvetica, sans-serif;width:100px;height:0.8em;font-size:0.8rem;padding-top:2px;color:var(--pop-green-dim);
align-items:center;justify-content:center;margin-bottom:10px;top:0}
.actualw{position:relative;left:5px;-webkit-border-radius:3px;-moz-border-radius:3px;-o-border-radius:3px;border-radius:3px;background:rgba(74, 99, 111, 0.1);
padding:5px;font-family:Arial, Helvetica, sans-serif;width:100px;height:0.8em;font-size:0.8rem;padding-top:2px;color:var(--pop-green-dim);
align-items:center;justify-content:center;margin-bottom:10px;top:0}
.metarwindtoday10{background:rgba(230, 161, 65, 1.000)}
.metarwindtoday20{background:rgba(255, 124, 57, 1.000)}
.metarwindtoday25{background:rgba(255, 124, 57, 0.7)}
.metarwindtoday30{background:rgba(211, 93, 78, 1.000)}
smalluvunit{font-size:.8rem;font-family:Arial,Helvetica,system;}
valuecalm{font-size:.8em;font-family:weathertext2;}
stationid{font-size:1.4em;font-family:weathertext2;color:var(--pop-cyan)}[data-theme="light"] orange,[data-theme="light"] stationid,[data-theme="light"] .directiontext span{color:#007a8a}
.hitemp,.lotemp{font-size:9px;}
.iconcondition{float:left;margin-bottom:65px;}
.icontext{position:absolute;float:left;margin-top:60px;text-align:left;}
.pressure{position:absolute;float:left;margin-top:60px;text-align:left;}
[class^="metartemptoday"],[class^="metarhumtoday"],[class^="metarwindtoday"],.tword,.humword,.dewword{text-shadow:var(--badge-text-shadow)}
</style>
<div class="weather34darkbrowser" url="<?php echo htmlspecialchars(w34t('NearbyAirportConditions', 'Nearby Airport Conditions')); ?>"></div>
  
<main class="grid">

 <article>
  <div class=actualt style="background:teal;color:white;">&nbsp;&nbsp <?php echo w34t('Currentsky', 'Current Conditions'); ?> </div>
  <div class="iconcondition"><?php echo "<img rel='prefetch' src='css/svg/".$sky_icon."' width='60px'>";?></div>
  <div class="icontext"><?php  echo w34_cond($sky_desc); ?> </div>
<br><br><br>
<div class="pressure">
<blue><?php echo w34t('Pressure', 'Pressure'); ?></blue> <br>
<?php
if ($pressureunit == 'mb' || $pressureunit == 'hPa' || $pressureunit == 'kPa' ) {
	echo $metar34pressuremb ," (".$pressureunit.")";
} else {
	echo $metar34pressurehg ," (inHG)";
}
?> - 
<?php
if ($pressureunit == 'mb' || $pressureunit == 'hPa' || $pressureunit == 'kPa') {
	echo $metar34pressurehg ," (inHG)";
} else {
	echo $metar34pressuremb ," (mb)";
}
?>
<blue><br><?php echo w34t('Visibility', 'Visibility'); ?></blue> <br>
<?php
if ($distanceunit == 'mi') {
	echo $metar34vismiles  ," (", w34t('Miles', 'miles'), ")";
    echo $metar34cloudbasefeet;
} else {
	echo $metar34viskm ," (km)";
}
?> - 
<?php
if ($distanceunit =='mi') {
	echo $metar34viskm  ," (km)";
} else {
	echo $metar34vismiles ," (", w34t('Miles', 'miles'), ")";
}
?>
</div>
  </article> 
  
  



  <article>       
<div class=actualt style="background:teal;color:white;">&nbsp;&nbsp <?php echo w34t('Temperature', 'Temperature'); ?> </div>   

 <div class="metartempcontainer1"><?php
 if ($tempunit == 'C') {
	if ($metar34temperaturec >30) {echo '<div class=metartemptoday30>'.$metar34temperaturec."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34temperaturec >25) {echo '<div class=metartemptoday25>'.$metar34temperaturec."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34temperaturec >20) {echo '<div class=metartemptoday20>'.$metar34temperaturec."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34temperaturec >10) {echo '<div class=metartemptoday10>'.$metar34temperaturec."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34temperaturec >5) {echo '<div class=metartemptoday5>'.$metar34temperaturec."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34temperaturec >-50) {echo '<div class=metartemptoday0>'.$metar34temperaturec."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34temperaturec =='') {echo '<div class=metartemptoday0>'.$metar34temperaturec."<smalluvunit> N/A";}
 } else {
	 if ($metar34temperaturef >86) {echo '<div class=metartemptoday30>'.$metar34temperaturef."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34temperaturef >77) {echo '<div class=metartemptoday25>'.$metar34temperaturef."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34temperaturef >68) {echo '<div class=metartemptoday20>'.$metar34temperaturef."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34temperaturef >50) {echo '<div class=metartemptoday10>'.$metar34temperaturef."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34temperaturef >41) {echo '<div class=metartemptoday5>'.$metar34temperaturef."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34temperaturef >-50) {echo '<div class=metartemptoday0>'.$metar34temperaturef."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34temperaturef =='') {echo '<div class=metartemptoday0>'.$metar34temperaturef."<smalluvunit> N/A";}
 }
?></smalluvunit></div></div>
<div class="tword"><?php if ($tempunit == 'F') {echo $metar34temperaturec."&deg;C";} else if ($tempunit == 'C'){echo $metar34temperaturef."&deg;F";}?></div>
</div>






	 
<div class="lotemp">

<div class="metardewcontainer1"><?php
if ($tempunit == 'C') {
	if ($metar34dewpointc >30) {echo '<div class=metardewtoday30>'.$metar34dewpointc."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34dewpointc >25) {echo '<div class=metardewtoday25>'.$metar34dewpointc."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34dewpointc >20) {echo '<div class=metardewtoday20>'.$metar34dewpointc."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34dewpointc >10) {echo '<div class=metardewtoday10>'.$metar34dewpointc."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34dewpointc >5) {echo '<div class=metardewtoday5>'.$metar34dewpointc."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34dewpointc >-50) {echo '<div class=metardewtoday0>'.$metar34dewpointc."<smalluvunit> &nbsp;&deg;C";}
	else if ($metar34dewpointc=='') {echo '<div class=metartemptoday0>'.$metar34dewpointc."<smalluvunit> N/A";}
} else {
	if ($metar34dewpointf>86) {echo '<div class=metartemptoday30>'.$metar34dewpointf."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34dewpointf>77) {echo '<div class=metartemptoday25>'.$metar34dewpointf."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34dewpointf>68) {echo '<div class=metartemptoday20>'.$metar34dewpointf."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34dewpointf>50) {echo '<div class=metartemptoday10>'.$metar34dewpointf."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34dewpointf>41) {echo '<div class=metartemptoday5>'.$metar34dewpointf."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34dewpointf>-50) {echo '<div class=metartemptoday0>'.$metar34dewpointf."<smalluvunit> &nbsp;&deg;F";}
	else if ($metar34dewpointf=='') {echo '<div class=metartemptoday0>'.$metar34dewpointf."<smalluvunit> N/A";}
}
?></smalluvunit></div></div> 
 <div class="dewword"><?php echo w34t('Dewpoint', 'Dewpoint'); ?></div>

 <div class="metarhumcontainer1"><?php 
if ($metar34humidity >85) {echo '<div class=metarhumtoday85-100>'.$metar34humidity ."<smalluvunit> &nbsp;%";}
else if ($metar34humidity >70) {echo '<div class=metarhumtoday70-85>'.$metar34humidity ."<smalluvunit> &nbsp;%";}
else if ($metar34humidity  >35) {echo '<div class=metarhumtoday35-70>'.$metar34humidity ."<smalluvunit> &nbsp;%";}
else if ($metar34humidity >=0) {echo '<div class=metarhumtoday0-35>'.$metar34humidity ."<smalluvunit> &nbsp;%";}
else if ($metar34humidity=='') {echo '<div class=metarhumtoday0-35><smalluvunit> N/A';}
?></smalluvunit></div></div> 
<div class="humword">&nbsp;<?php echo w34t('Humidity', 'Humidity'); ?></div>
</div>
  
</article>
  
   
  <article>
  <div class=actualw style="background:teal;color:white;">&nbsp;&nbsp <?php echo w34t('WindSpeedLabel', 'Wind Speed'); ?></div>   
   <?php
//set windspeed variables
if ($windunit == 'km/h') {
	$metarwind1 = 'kmh';
	$metarwind2 = 'kts';
	$metarwind3 = 'mph';
	$metarwind4 = 'ms';
} else if ($windunit == 'mph') {
	$metarwind1 = 'mph';
	$metarwind2 = 'kts';
	$metarwind3 = 'kmh';
	$metarwind4 = 'ms';
} else if ($windunit == 'kts') {
	$metarwind1 = 'kts';
	$metarwind2 = 'mph';
	$metarwind3 = 'kmh';
	$metarwind4 = 'ms';
} else {
	$metarwind1 = 'ms';
	$metarwind2 = 'mph';
	$metarwind3 = 'kmh';
	$metarwind4 = 'kts';
}
	if ($metar34windspeedkmh >=50) {$metarkmh = '<div class=metarwindtoday30>'.$metar34windspeedkmh."<smalluvunit> &nbsp;km/h";}
	else if ($metar34windspeedkmh >=40) {$metarkmh = '<div class=metarwindtoday25>'.$metar34windspeedkmh."<smalluvunit>&nbsp; km/h";}
	else if ($metar34windspeedkmh >=30) {$metarkmh = '<div class=metarwindtoday20>'.$metar34windspeedkmh."<smalluvunit>&nbsp; km/h";}
	else if ($metar34windspeedkmh >0) {$metarkmh = '<div class=metarwindtoday10>'.$metar34windspeedkmh."<smalluvunit>&nbsp; km/h";}
	else {$metarkmh = '<div class=metarwindtoday10>'.'0'."<smalluvunit>&nbsp; km/h";}
	if ($metar34windspeedmph >=31.06) {$metarmph = '<div class=metarwindtoday30>'.$metar34windspeedmph."<smalluvunit> &nbsp;mph";}
	else if ($metar34windspeedmph >=24.85) {$metarmph = '<div class=metarwindtoday25>'.$metar34windspeedmph."<smalluvunit> &nbsp;mph";}
	else if ($metar34windspeedmph >=18.6) {$metarmph = '<div class=metarwindtoday20>'.$metar34windspeedmph."<smalluvunit> &nbsp;mph";}
	else if ($metar34windspeedmph >0) {$metarmph = '<div class=metarwindtoday10>'.$metar34windspeedmph."<smalluvunit> &nbsp;mph";}
	else {$metarmph = '<div class=metarwindtoday10>'.'0'."<smalluvunit> &nbsp;mph";}
	if ($metar34windspeedkts >=26.9) {$metarkts = '<div class=metarwindtoday30>'.$metar34windspeedkts."<smalluvunit> &nbsp;kts";}
	else if ($metar34windspeedkts >=21.5) {$metarkts = '<div class=metarwindtoday25>'.$metar34windspeedkts."<smalluvunit> &nbsp;kts";}
	else if ($metar34windspeedkts >=16.19) {$metarkts = '<div class=metarwindtoday20>'.$metar34windspeedkts."<smalluvunit> &nbsp;kts";}
	else if ($metar34windspeedkts >0) {$metarkts = '<div class=metarwindtoday10>'.$metar34windspeedkts."<smalluvunit> &nbsp;kts";}
	else {$metarkts = '<div class=metarwindtoday10>'.'0'."<smalluvunit> &nbsp;kts";}
	if ($metar34windspeedms >=13.8) {$metarms = '<div class=metarwindtoday30>'.$metar34windspeedms."<smalluvunit> &nbsp;m/s";}
	else if ($metar34windspeedms >=11.1) {$metarms = '<div class=metarwindtoday25>'.$metar34windspeedms."<smalluvunit> &nbsp;m/s";}
	else if ($metar34windspeedms >=8.3) {$metarms = '<div class=metarwindtoday20>'.$metar34windspeedms."<smalluvunit> &nbsp;m/s";}
	else if ($metar34windspeedms >0) {$metarms = '<div class=metarwindtoday10>'.$metar34windspeedms."<smalluvunit> &nbsp;m/s";}
	else {$metarms = '<div class=metarwindtoday10>'.'0'."<smalluvunit> &nbsp;m/s";}
$metarspot1 = 'metar'.$metarwind1;
$metarspot2 = 'metar'.$metarwind2;
$metarspot3 = 'metar'.$metarwind3;
$metarspot4 = 'metar'.$metarwind4;
?></div></div></div></div></smalluvunit>

<div class="metarwindcontainer1">
<?php
//wind1 kph
echo $$metarspot1;
?>
</div>
<div class="metarwindcontainer2">
<?php 
//wind2 mph
echo $$metarspot2;
?></smalluvunit></div>
</div>
<div class="metarwindcontainer3">
<?php 
//wind3 kts
echo $$metarspot3;
?></smalluvunit></div>
</div>
<div class="metarwindcontainer4">
<?php 
//wind4 ms
echo $$metarspot4;
?></smalluvunit></div>
</div>
</div>
</article>


<article>
<div class=actualw style="background:teal;color:white;">&nbsp;&nbsp <?php echo w34t('Winddirection', 'Wind Direction'); ?></div> 
</div>
</div> 
<div class="metar34compass1">
<div class=directiontext>
<?php 
if( $metar34windir==0){echo w34t('Calm', 'Calm');}else echo "&nbsp". $metar34windir,"&deg;";?>
<br>
<?php 
if($metar34windir<=11.25){echo w34t('Northdir', 'Due <span>North<br></span>');}
else if($metar34windir<=33.75){echo w34t('NNEdir', 'North North <br><span>East</span>');}
else if($metar34windir<=56.25){echo w34t('NEdir', 'North <span> East<br></span>');}
else if($metar34windir<=78.75){echo w34t('ENEdir', 'East North<br><span>East</span>');}
else if($metar34windir<=101.25){echo w34t('Eastdir', 'Due <span> East<br></span>');}
else if($metar34windir<=123.75){echo w34t('ESEdir', 'East South<br><span>East</span>');}
else if($metar34windir<=146.25){echo w34t('SEdir', 'South <span> East</span>');}
else if($metar34windir<=168.75){echo w34t('SSEdir', 'South South<br><span>East</span>');}
else if($metar34windir<=191.25){echo w34t('Southdir', 'Due <span> South</span>');}
else if($metar34windir<=213.75){echo w34t('SSWdir', 'South South<br><span>West</span>');}
else if($metar34windir<=236.25){echo w34t('SWdir', 'South <span> West</span>');}
else if($metar34windir<=258.75){echo w34t('WSWdir', 'West South<br><span>West</span>');}
else if($metar34windir<=281.25){echo w34t('Westdir', 'Due <span> West</span>');}
else if($metar34windir<=303.75){echo w34t('WNWdir', 'West North<br><span>West</span>');}
else if($metar34windir<=326.25){echo w34t('NWdir', 'North <span> West</span>');}
else if($metar34windir<=348.75){echo w34t('NWNdir', 'North North<br><span>West</span>');}
else{echo w34t('Northdir', 'Due <span>North<br></span>');}?></div>

<div class="metar34compass-line1">
<div class="thearrow2">



</div></div>


</div>
  </article> 
 
  <article>
  <div class=actualt style="background:teal;color:white;">&nbsp;&nbsp <?php echo w34t('AirportData', 'Airport Data'); ?> </div>   
  <stationid><?php echo $metar34stationid ; ?></stationid> <?php echo w34_fltcat_badge($metar34fltcat, '11px'); ?><br>
  <div class="lotemp">
   <?php


if ($distanceunit == 'km') {$metdist1 = round($airport1dist,0,PHP_ROUND_HALF_UP); $metdist2 = round($airport1dist / 1.609,0,PHP_ROUND_HALF_UP); $metunit1 = 'km'; $metunit2 = 'mi';}
else if ($distanceunit == 'mi') {$metdist1 = round($airport1dist / 1.609,0,PHP_ROUND_HALF_UP); $metdist2 = round($airport1dist,0,PHP_ROUND_HALF_UP); $metunit1 = 'mi'; $metunit2 = 'km';}
echo w34t('Location', 'Location'), " <orange>",$metar34stationname  ;
echo '</orange> <orange>'.$metdist1.'</orange> '.$metunit1.' (<orange>';
echo $metdist2;
echo '</orange>'.$metunit2.')';
    ?>
 <div class="lotemp">
<?php //metar raw
echo "METAR: " .$metar34raw."";?>
</div>
<div class="hitemp">
<?php //update timestamp
if (!empty($TZ)) {
    date_default_timezone_set($TZ);
}
$metar_ts = null;
if (!empty($metar34raw) && preg_match('/\b(\d{2})(\d{2})(\d{2})Z\b/', $metar34raw, $m)) {
    $day = (int)$m[1];
    $hour = (int)$m[2];
    $min = (int)$m[3];
    $utc_now = time();
    $year = (int)gmdate('Y', $utc_now);
    $month = (int)gmdate('m', $utc_now);
    $cur_day = (int)gmdate('d', $utc_now);
    if ($day > $cur_day + 1) {
        $month--;
        if ($month < 1) {
            $month = 12;
            $year--;
        }
    }
    $metar_ts = gmmktime($hour, $min, 0, $month, $day, $year);
}
if (!$metar_ts && !empty($metar34time)) {
    $metar_ts = strtotime($metar34time);
}
$fmt = !empty($timeFormatShort) ? $timeFormatShort : 'H:i';
if ($metar_ts) {
    echo w34_date('D jS F ' . $fmt, $metar_ts);
    // older than 2 h: not current conditions (the sky description above then reads Data Offline)
    if (!empty($metar34stale) && $metar34age !== null) {
        echo ' <span class="ink-ored">(' . w34f('MetarHoursOld', '%s h old', round($metar34age / 3600)) . ')</span>';
    }
}
?> </div></div>

  </article> 
  
  <article>
  <div class=actualt style="background:teal;color:white;">&nbsp;&nbsp <?php echo w34t('RawMetarInfo', 'Raw METAR Info'); ?></div>  
  <div class="lotemp">
  <?php echo $info?> <?php echo w34t('RawMetarExplain', 'Raw METAR is the most common format in the world for the transmission of observational weather data. It is highly standardized through the International Civil Aviation Organization (ICAO), which allows it to be understood throughout most of the world.'); ?></span></div>
  </article> 
  
  <article>
  <div class=actualt style="background:teal;color:white;">&nbsp;&nbsp <?php echo w34t('ApiInfo', 'API Info'); ?></div>  
  <div class="lotemp">
  <?php echo $info?> <?php echo w34t('DataProvidedBy', 'Data Provided by'); ?> </span><a href="https://aviationweather.gov/data/metar/?ids=<?php echo $icao1;?>" title="aviationweather.gov" target="_blank"><br><img src=img/noaa.svg width=130px alt="aviationweather.gov"></a></span></div>
  </article> 
  
  
  <article>
  <div class=actualt style="background:teal;color:white;">&nbsp;&nbsp &copy; <?php echo w34t('Information', 'Information'); ?></div>  
  <div class="lotemp">
  <?php echo $info?> <?php echo w34f('CreditWeather34', 'CSS/SVG/PHP scripts were developed by %s for use in the weather34 template', '<a href="https://weather34.com" title="weather34.com" target="_blank" style="font-size:9px;">weather34.com</a>'); ?> &copy; 2015-<?php echo date('Y');?>
  <br><br>
  <?php echo $info?> <?php echo w34t('GuideInfoBy', 'Guide information provided by'); ?> <a href="https://en.wikipedia.org/wiki/METAR" title="https://en.wikipedia.org/wiki/METAR" target="_blank" style="font-size:9px;">Metar-Wikipedia </a>  
  </div></article> 
   
</main>
