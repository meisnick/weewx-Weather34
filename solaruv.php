<?php //weather34 solar and uvindex module 27th Jan 2017 //
include_once('w34CombinedData.php');include('common.php');include_once('w34_i18n.php');
$result = date_sun_info(time(), $lat, $lon); '<pre>'.time().print_r($result,true); $nextday = time() + 24*60*60; $result2 = date_sun_info($nextday,$lat, $lon); '<pre>'.print_r($result2,true);
$nextrise = $result['sunrise']; $now = time(); if ($now > $nextrise) { $nextrise = date('H:i',$result2['sunrise']);} else {$nextrise = date('H:i',$nextrise);}
$nextset = $result['sunset']; if ($now > $nextset) { $nextset = date('H:i',$result2['sunset']);} else {$nextset = date('H:i',$nextset);} $firstrise = $result['sunrise']; $secondrise = $result2['sunrise']; $firstset = $result ['sunset']; if ($now < $firstrise) { $time = $firstrise - $now; $hrs = gmdate ('G',$time); $min = gmdate ('i',$time);;} elseif ($now < $firstset) { $time = $firstset - $now; $hrs = gmdate ('G',$time); $min = gmdate ('i',$time); } else { $time = $secondrise - $now; $hrs = gmdate ('G',$time); $min = gmdate ('i',$time);}$sunset=date('Hi',$firstset);$sunrise=date('Gi',$firstrise);
$nextset = $result['sunset']; if ($now > $nextset) { $nextset = date('H:i',$result2['sunset']);}

$has_uv_hardware = isset($weather["uv"]) && is_numeric($weather["uv"]) && $weather["uv"] !== 'NULL' && $weather["uv"] !== '';
$sun_up = is_int($firstrise) && is_int($firstset) && $now >= $firstrise && $now <= $firstset;

// UV category: the WHO bands, keyed by their lower bound
$uvs_cat  = function ($u) { $u = round((float)$u); return $u >= 11 ? 11 : ($u >= 8 ? 8 : ($u >= 6 ? 6 : ($u >= 3 ? 3 : 1))); };
$uvs_name = function ($c) {
    return $c == 11 ? w34t('UvExtreme', 'Extreme') : ($c == 8 ? w34t('UvVeryHigh', 'Very High') : ($c == 6 ? w34t('UvHigh', 'High')
         : ($c == 3 ? w34t('UvModerate', 'Moderate') : w34t('UvLow', 'Low'))));
};
$uvs_num = function ($u) { return number_format((float)$u, 1); };

// UV day strip, sunrise to sunset: the hourly UV forecast (jsondata/uv_day.json, written with the forecast by
// nws_forecast_update.py) under the measured UV in 10-minute steps up to now. After sunset it shows tomorrow.
$uvs_tomorrow = is_int($firstset) && $now > $firstset;
$uvs_sun = $uvs_tomorrow ? $result2 : $result;
$uvs_ok = is_int($uvs_sun['sunrise']) && is_int($uvs_sun['sunset']);
$uvs_fc = array(); $uvs_obs = array(); $uvs_fpk = null; $uvs_pk = null;
if ($uvs_ok) {
    $uvs_sr = $uvs_sun['sunrise']; $uvs_ss = $uvs_sun['sunset'];
    $uvs_x0 = $uvs_sr - 1800; $uvs_x1 = $uvs_ss + 1800;
    $uvs_date = date('Y-m-d', $uvs_sr);
    $uvj = @json_decode(@file_get_contents('jsondata/uv_day.json'), true);
    if (is_array($uvj) && isset($uvj['uv']) && $now - (int)($uvj['generated'] ?? 0) < 2 * 86400) {
        foreach ($uvj['uv'] as $t => $u) {
            if (substr($t, 0, 10) != $uvs_date) continue;
            $ts = strtotime($t);
            if ($ts === false || $ts < $uvs_x0 - 1800 || $ts > $uvs_x1 + 1800) continue;
            $uvs_fc[$ts] = (float)$u;
            if ($uvs_fpk === null || $u > $uvs_fpk[0]) $uvs_fpk = array((float)$u, $ts);
        }
    }
    if (!$uvs_tomorrow && $has_uv_hardware) {
        try {
            $uvdb = new SQLite3('/var/lib/weewx/weewx.sdb', SQLITE3_OPEN_READONLY);
            $uvdb->busyTimeout(2000);
            $q = $uvdb->query('SELECT (dateTime / 600) * 600 AS b, MAX(UV) AS u FROM archive WHERE dateTime >= ' . (int)$uvs_sr
                            . ' AND dateTime <= ' . $now . ' AND UV IS NOT NULL GROUP BY b');
            while ($r = $q->fetchArray(SQLITE3_ASSOC)) $uvs_obs[(int)$r['b']] = (float)$r['u'];
            $r = $uvdb->querySingle('SELECT dateTime, UV FROM archive WHERE dateTime >= ' . strtotime('today', $now)
                                  . ' AND dateTime <= ' . $now . ' AND UV > 0 ORDER BY UV DESC, dateTime ASC LIMIT 1', true);
            if ($r) $uvs_pk = array((float)$r['UV'], (int)$r['dateTime']);
            $uvdb->close();
        } catch (Exception $e) { /* no archive: forecast only */ }
        // the live reading counts too: the archive lags by up to one interval
        if ($sun_up && (float)$weather["uv"] > 0) {
            $uvs_obs[intdiv($now, 600) * 600] = max($uvs_obs[intdiv($now, 600) * 600] ?? 0, (float)$weather["uv"]);
            if ($uvs_pk === null || (float)$weather["uv"] > $uvs_pk[0]) $uvs_pk = array((float)$weather["uv"], $now);
        }
    }
}

// UV pill: the live sensor while the sun is up; after dark (or with no UV sensor) the forecast peak the strip shows,
// else the daily forecast's
$forecasthourlyuv = 0;
$uv_label = isset($lang['Current']) ? $lang['Current'] : 'Current';
if ($has_uv_hardware && ($sun_up || $weather["uv"] > 0)) {
    $forecasthourlyuv = $weather["uv"];
} else {
    $forecast_uvi = $uvs_fpk ? $uvs_fpk[0] : 0;
    if ($forecast_uvi <= 0 && file_exists('jsondata/forecast_daily.txt')) {
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
?>
<div class="updatedtime"><span><?php if(isset($livedata2)&&file_exists($livedata2)&&time()- filemtime($livedata2)>300)echo $offline. '<offline> Offline </offline>';else echo $online." ".$weather["time"];?></span></div>
<div class="mod-solaruv<?php echo $uvs_ok ? ' mod-solaruv--strip' : ''; ?>">
<?php if ($uvs_ok):
    $uvs_pct = function ($t) use ($uvs_x0, $uvs_x1) { return round(max(0, min(1, ($t - $uvs_x0) / ($uvs_x1 - $uvs_x0))) * 100, 2); };
    $uvs_top = max(6, $uvs_fpk ? $uvs_fpk[0] : 0, $uvs_obs ? max($uvs_obs) : 0);
    $uvs_bar = function ($t0, $t1, $u, $cls) use ($uvs_pct, $uvs_top, $uvs_cat) {
        if ($u <= 0) return '';
        $h = round($u / $uvs_top * 15, 2);
        return '<rect class="' . $cls . ' mod-uvs-u' . $uvs_cat($u) . '" x="' . $uvs_pct($t0) * 10 . '" y="' . (16 - $h) . '" width="'
             . max(0, $uvs_pct($t1) * 10 - $uvs_pct($t0) * 10 - 2) . '" height="' . $h . '"/>';
    };
    // caption: the category now and the peak so far, or (sun down) the coming day's forecast peak
    $b = function ($u) use ($uvs_num) { return '<b>' . $uvs_num($u) . '</b>'; };
    if (!$sun_up) {
        $cap = w34t('BelowHorizon', 'Below Horizon');
        if ($uvs_fpk) $cap .= ' &middot; ' . ($uvs_tomorrow ? w34f('UvsTomorrowPeak', 'tomorrow’s peak %s at %s', $b($uvs_fpk[0]), date('H:i', $uvs_fpk[1]))
                                                          : w34f('UvsTodayPeak', 'today’s peak %s at %s', $b($uvs_fpk[0]), date('H:i', $uvs_fpk[1])));
    } else {
        $cap = '<b>' . $uvs_name($uvs_cat($weather["uv3"])) . '</b>';
        if ($uvs_pk && $uvs_fpk) $cap .= ' &middot; ' . w34f('UvsPeakFc', 'peak %s at %s &middot; forecast %s', $uvs_num($uvs_pk[0]), date('H:i', $uvs_pk[1]), $uvs_num($uvs_fpk[0]));
        else if ($uvs_pk)    $cap .= ' &middot; ' . w34f('UvsPeak', 'peak %s at %s', $uvs_num($uvs_pk[0]), date('H:i', $uvs_pk[1]));
        else if ($uvs_fpk)   $cap .= ' &middot; ' . w34f('UvsFcPeak', 'forecast peak %s at %s', $uvs_num($uvs_fpk[0]), date('H:i', $uvs_fpk[1]));
    }
?>
<div class="mod-uvs<?php echo ($uvs_obs && max($uvs_obs) > 0) ? '' : ' mod-uvs--fc'; ?>">
    <div class="mod-uvs-cap"><?php echo $cap; ?></div>
    <div class="mod-uvs-track"><svg viewBox="0 0 1000 16" preserveAspectRatio="none" aria-hidden="true"><?php
        echo '<rect class="mod-uvs-night" x="0" y="0" width="' . $uvs_pct($uvs_sr) * 10 . '" height="16"/>';
        echo '<rect class="mod-uvs-night" x="' . $uvs_pct($uvs_ss) * 10 . '" y="0" width="' . (1000 - $uvs_pct($uvs_ss) * 10) . '" height="16"/>';
        foreach ($uvs_fc as $t => $u) echo $uvs_bar($t - 1800, $t + 1800, $u, 'mod-uvs-f');
        foreach ($uvs_obs as $t => $u) if ($t + 600 > $uvs_x0 && $t < $uvs_x1) echo $uvs_bar($t, $t + 600, $u, 'mod-uvs-m');
    ?></svg></div>
    <?php if (!$uvs_tomorrow && $now > $uvs_x0 && $now < $uvs_x1): ?><span class="mod-uvs-mark" style="left:<?php echo $uvs_pct($now); ?>%"></span><?php endif; ?>
    <div class="mod-uvs-ticks"><?php
        // sunrise and sunset, and every third hour that does not crowd them
        echo '<span style="left:' . $uvs_pct($uvs_sr) . '%">' . date('G:i', $uvs_sr) . '</span>';
        $mid = strtotime('today', $uvs_sr);
        for ($h = 3; $h <= 21; $h += 3) {
            $t = $mid + $h * 3600;   // local clock hour (DST changes at 2 AM, outside the strip)
            if ($uvs_pct($t) - $uvs_pct($uvs_sr) > 9 && $uvs_pct($uvs_ss) - $uvs_pct($t) > 9) echo '<span style="left:' . $uvs_pct($t) . '%">' . $h . '</span>';
        }
        echo '<span style="left:' . $uvs_pct($uvs_ss) . '%">' . date('G:i', $uvs_ss) . '</span>';
    ?></div>
</div>
<?php endif; ?>
<?php
$solar_val = 0;
$solar_label = w34t('Solarradiation', 'Solar Radiation');
$has_solar_hardware = isset($weather["solar"]) && is_numeric($weather["solar"]) && $weather["solar"] !== 'NULL' && $weather["solar"] !== '';

if ($has_solar_hardware && $weather["solar"] > 0) {
    $solar_val = $weather["solar"];
} else {
    // Fall back to upcoming hourly forecast solar radiation for the current hour
    $forecast_solar = 0;
    if (file_exists('jsondata/forecast_hourly.txt')) {
        $forecast_data = json_decode(file_get_contents('jsondata/forecast_hourly.txt'), true);
        if (isset($forecast_data['response'][0]['periods']) && is_array($forecast_data['response'][0]['periods'])) {
            $current_hour = date('H');
            foreach ($forecast_data['response'][0]['periods'] as $period) {
                $period_hour = substr($period['validTime'], 11, 2);
                if ($period_hour == $current_hour) {
                    if (isset($period['solar'])) {
                        $forecast_solar = $period['solar'];
                    }
                    break;
                }
            }
        }
    }
    if ($forecast_solar > 0) {
        $solar_val = $forecast_solar;
        $solar_label = w34t('SolarForecast', 'Solar Forecast');
    }
}
// Clear sky: measured radiation against weewx's clear-sky radiation for the sun's height now (maxSolarRad).
// Below ~50 W/m2 of clear-sky radiation (sun near the horizon) the ratio is noise, so it shows "--".
$maxsolar = (float)str_replace(',', '', (string)($weather["maxsolar"] ?? ''));
$clearsky = ($has_solar_hardware && $sun_up && $maxsolar >= 50) ? min(100, round((float)$weather["solar"] / $maxsolar * 100)) : null;
$lux_title = (isset($weather["lux"]) && is_numeric($weather["lux"])) ? ' title="' . number_format((float)$weather["lux"]) . ' lux"' : '';
?>
<div class="weather34solarword"><valuetext>W/m&sup2 </valuetext> </div><div class="weather34solarvalue">
<div class="solartodaycontainer1"<?php echo $lux_title; ?>><?php
if ($solar_val==0){echo "<div class=solarluxtodaydark>".$solar_val;}
else if ($solar_val>0){echo "<div class=solarluxtoday>".$solar_val;}?></div></div></div>
<div class="solarluxtodayword"><valuetext><?php echo $solar_label;?></valuetext></div><div class="solarwrap"></div>


<div class="uvcontainer1"><?php
if ($weather["uv3"]>10) {echo '<div class=uvtoday11>'.number_format((float)$weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if ($weather["uv3"]>8) {echo '<div class=uvtoday9-10>'.number_format((float)$weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if ($weather["uv3"]>5) {echo '<div class=uvtoday6-8>'.number_format((float)$weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if ($weather["uv3"]>3) {echo '<div class=uvtoday4-5>'.number_format((float)$weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if ($weather["uv3"]>=1) {echo '<div class=uvtoday1-3>'.number_format((float)$weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else if (!$sun_up && $weather["uv3"]==0) {echo '<div class=uvtodaydark>'.number_format((float)$weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}
else {echo '<div class=uvtoday1-3>'.number_format((float)$weather["uv3"],1)."<smalluvunit> &nbsp;UVI";}?></smalluvunit></div></div>
<div class="uvtrend"><?php echo mb_strtoupper(w34t('UvIndex', 'UV Index'))?></div>
<div class="uvcaution"><value>UVI <?php echo $uv_label;?></value></div>

<div class="weather34luxword"><valuetext>%</valuetext></div> <div class="weather34luxvalue"><div class="luxtodaycontainer1">
<?php
if ($clearsky === null) echo "<div class=luxtodaydark>--";
else echo "<div class=luxtoday>" . $clearsky;?>
</div></div></div><div class="luxtodayword"><valuetext><?php echo w34t('ClearSky', 'Clear Sky');?></valuetext></div><div class="luxwrap"></div>
</div>
