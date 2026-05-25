<?php //weather34 solar and uvindex module 27th Jan 2017 //
include_once('w34CombinedData.php');include('common.php');
// Prefer live sensor during daytime; fall back to daily forecast max at night (when live is 0) or if hardware is missing
$forecasthourlyuv = 0;
$uv_label = isset($lang['Current']) ? $lang['Current'] : 'Current';
$has_uv_hardware = isset($weather["uv"]) && is_numeric($weather["uv"]) && $weather["uv"] !== 'NULL' && $weather["uv"] !== '';

if ($has_uv_hardware && $weather["uv"] > 0) {
    $forecasthourlyuv = $weather["uv"];
} else {
    // Fall back to upcoming daytime forecast UV index at night or if hardware is missing
    $forecast_uvi = 0;
    if (file_exists('jsondata/forecast_daily.txt')) {
        $forecast_data = json_decode(file_get_contents('jsondata/forecast_daily.txt'), true);
        if (isset($forecast_data['response'][0]['periods']) && is_array($forecast_data['response'][0]['periods'])) {
            foreach ($forecast_data['response'][0]['periods'] as $period) {
                if (isset($period['uvi']) && $period['uvi'] > 0) {
                    $forecast_uvi = $period['uvi'];
                    break;
                }
            }
        }
    }
    if ($forecast_uvi > 0) {
        $forecasthourlyuv = $forecast_uvi;
        $uv_label = isset($lang['Forecast']) ? $lang['Forecast'] : 'Forecast';
    }
}
$weather["uv3"] = $forecasthourlyuv;
$result = date_sun_info(time(), $lat, $lon); '<pre>'.time().print_r($result,true); $nextday = time() + 24*60*60; $result2 = date_sun_info($nextday,$lat, $lon); '<pre>'.print_r($result2,true); 
$nextrise = $result['sunrise']; $now = time(); if ($now > $nextrise) { $nextrise = date('H:i',$result2['sunrise']);} else {$nextrise = date('H:i',$nextrise);} 
$nextset = $result['sunset']; if ($now > $nextset) { $nextset = date('H:i',$result2['sunset']);} else {$nextset = date('H:i',$nextset);} $firstrise = $result['sunrise']; $secondrise = $result2['sunrise']; $firstset = $result ['sunset']; if ($now < $firstrise) { $time = $firstrise - $now; $hrs = gmdate ('G',$time); $min = gmdate ('i',$time);;} elseif ($now < $firstset) { $time = $firstset - $now; $hrs = gmdate ('G',$time); $min = gmdate ('i',$time); } else { $time = $secondrise - $now; $hrs = gmdate ('G',$time); $min = gmdate ('i',$time);}$sunset=date('Hi',$firstset);$sunrise=date('Gi',$firstrise);
$nextset = $result['sunset']; if ($now > $nextset) { $nextset = date('H:i',$result2['sunset']);}?>
<div class="updatedtime"><span><?php if(file_exists($livedata2)&&time()- filemtime($livedata2)>300)echo $offline. '<offline> Offline </offline>';else echo $online." ".$weather["time"];?></div>  
<div class="weather34solarword"><valuetext>W/m&sup2 </valuetext> </div><div class="weather34solarvalue">
<div class="solartodaycontainer1"><?php 
if ($weather["solar"]==0){echo "<div class=solarluxtodaydark>".$weather["solar"];}
else if ($weather["solar"]>0){echo "<div class=solarluxtoday>".$weather["solar"];}?></div></div></div>
<div class="solarluxtodayword"><valuetext>Solar Radiation</valuetext></div><div class="solarwrap"></div>


<div class="uvcontainer1"><?php 
if ($weather["uv3"]>10) {echo '<div class=uvtoday11>'.number_format($weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if ($weather["uv3"]>8) {echo '<div class=uvtoday9-10>'.number_format($weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if ($weather["uv3"]>5) {echo '<div class=uvtoday6-8>'.number_format($weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if ($weather["uv3"]>3) {echo '<div class=uvtoday4-5>'.number_format($weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if (date('Hi')>$sunset && $weather["uv3"]==0) {echo '<div class=uvtodaydark>'.number_format($weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if (date('Gi')<$sunrise && $weather["uv3"]==0) {echo '<div class=uvtodaydark>'.number_format($weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if ($weather["uv3"]>=0) {echo '<div class=uvtoday1-3>'.number_format($weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}?></smallrainunit></div></div>
<div class="uvtrend"><?php echo "UV INDEX"?></div>  
<div class="uvcaution"><value>&nbsp;&nbsp;UVI <?php echo $uv_label;?><value></div>

<div class="weather34luxword"><valuetext>Lux</valuetext></div> <div class="weather34luxvalue"><div class="luxtodaycontainer1">
<?php 
if ($weather["lux"]>99999) {echo "<div class=luxtoday>".number_format($weather["lux"]/1000,0). "K";}
else if($weather["lux"]==0) echo "<div class=luxtodaydark>".$weather["lux"];
else echo "<div class=luxtoday>".$weather["lux"];?> 
</div></div></div><div class="luxtodayword"><valuetext>Brightness<valuetext></div><div class="luxwrap"></div>

<div class="uvcautionbig"><?php if ($weather["uv"]>=10) {echo $uviclear.'<span>UVI</span> Extreme';}else if ($weather["uv"]>=8) {echo $uviclear.'<span>UVI</span> Very High';}else if ($weather["uv"]>=6) {echo $uviclear.'<span>UVI</span> High';}else if ($weather["uv"]>=3) {echo $uviclear.'<span>UVI</span> Moderate';}
else if (date('Hi')>$sunset && $weather["uv"]>=0 ) {echo $uviclear,"Below Horizon";}else if (date('Gi')<$sunrise && $weather["uv"]>=0 ) {echo $uviclear,"Below Horizon";}else if ($weather["uv"]>=0 ) {echo $uviclear,'<span>UVI</span> Low';}else if ($weather["uv"]>=0 ) {echo $uviclear,'<span>UVI</span> Very Low';}?></div>

<script>
(function () {
    var container = document.querySelector('.uvcontainer1');
    if (!container) return;
    var innerDiv = container.querySelector('.uvtodaydark');
    if (!innerDiv) return;
    var attempts = 0;
    var timer = setInterval(function () {
        var uvEl = document.querySelector('.forecastforecasthome forecasttemplohome uv');
        if (uvEl || ++attempts > 20) {
            clearInterval(timer);
            if (!uvEl) return;
            var uvSpan = uvEl.querySelector('uvspan');
            if (!uvSpan) return;
            var colorEl = uvSpan.querySelector('purpleu,redu,orangeu,yellowu,greenu');
            if (!colorEl) return;
            var uvVal = parseFloat(colorEl.textContent);
            if (isNaN(uvVal)) return;
            var cls = uvVal > 10 ? 'uvtoday11'
                    : uvVal > 8  ? 'uvtoday9-10'
                    : uvVal > 5  ? 'uvtoday6-8'
                    : uvVal > 3  ? 'uvtoday4-5'
                    : uvVal >= 1 ? 'uvtoday1-3'
                    :              'uvtodaydark';
            innerDiv.className = cls;
            innerDiv.innerHTML = uvVal.toFixed(1) + '<smalluvunit>&nbsp;fcst UVI</smalluvunit>';
            var lbl = document.querySelector('.uvcaution');
            if (lbl) lbl.innerHTML = '<value>&nbsp;&nbsp;UVI Forecast</value>';
        }
    }, 500);
})();
</script>