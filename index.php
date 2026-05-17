<?php
####################################################################################################################
#   weewx-Weather34 Template maintained by Ian Millard (Steepleian)                                              #
#   Contains original legacy code by Brian Underdown (https://weather34.com)                                     #
#   © weather34.com original CSS/SVG/PHP 2015-2019                                                               #
#   Contains original code by Ian Millard and collaborators                                                       #
#   © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                                      #
#   Issues: https://github.com/meisnick/weewx-Weather34/issues                                                   #
####################################################################################################################
if (!file_exists('settings1.php')) { copy('initial_settings1.php', 'settings1.php'); }
if (!file_exists('modules.php'))   { copy('modules.example.php', 'modules.php'); }
include_once('w34CombinedData.php');
include_once('common.php');
include_once('webserver_ip_address.php');
include('settings1.php');
include('settings.php');
include('modules.php');
date_default_timezone_set($TZ);
header('Content-type: text/html; charset=utf-8');
error_reporting(0);

// ── Module title lookup ──────────────────────────────────────────────────────
function moduleTitle($module, $weather, $lang) {
    switch ($module) {
        case 'temperaturein.php':
            return $lang['Temperature'] . ' (<valuetitleunit>&deg;' . $weather['temp_units'] . '</valuetitleunit>)';
        case 'forecast3om.php':
        case 'forecast3omlarge.php':
        case 'forecast3wu.php':
        case 'forecast3wularge.php':
            return 'Forecast (<valuetitleunit>&deg;' . $weather['temp_units'] . '</valuetitleunit>)';
        case 'currentconditionsw34.php':
            return $lang['Currentsky'];
        case 'windspeeddirection.php':
            $wu = $weather['wind_units'] === 'kts' ? 'kn' : $weather['wind_units'];
            return $lang['Direction'] . ' | ' . $lang['Windspeed'] . ' (<valuetitleunit>' . $wu . '</valuetitleunit>)';
        case 'barometer.php':
            return $lang['Barometer'] . ' (<valuetitleunit>' . $weather['barometer_units'] . '</valuetitleunit>)';
        case 'w34skymap.php':
            return $lang['Daylight'] . ' | ' . $lang['Darkness'];
        case 'rainfall.php':
            return $lang['Rainfalltoday'] . ' (<valuetitleunit>' . $weather['rain_units'] . '</valuetitleunit>)';
        case 'moonphase.php':
            return 'Moon Phase';
        case 'lightning34.php':
            return 'Lightning';
        case 'indoortemperature.php':
            return 'Indoor';
        case 'airqualitymodule.php':
        case 'purpleairqualitymodule.php':
        case 'ew_airqualitymodule.php':
            return 'Air Quality';
        case 'weather34uvsolar.php':
        case 'solaruv.php':
        case 'solaruvwu.php':
            return 'UV &amp; Solar';
        case 'webcamsmall.php':
            return 'Webcam';
        default:
            return '';
    }
}

// ── Module popup links lookup ─────────────────────────────────────────────────
function modulePopups($module, $vars) {
    extract($vars);
    $out = '';
    switch ($module) {
        case 'temperaturein.php':
            $out = '<span class="yearpopup"><a href="pop_menu_temperature.php" data-lity>' . $menucharticonpage . ' Temperature Almanac and Derived Charts</a></span>';
            break;
        case 'forecast3om.php':
        case 'forecast3omlarge.php':
            $out = '<span class="yearpopup"><a href="pop_menu_forecast.php" data-lity>' . $chartinfo . ' Forecasts</a></span>';
            break;
        case 'forecast3wu.php':
        case 'forecast3wularge.php':
            $out = '<span class="yearpopup"><a href="pop_outlookwu.php" data-lity>' . $chartinfo . ' Daily F.cast</a></span>';
            break;
        case 'currentconditionsw34.php':
            $metar_offline = (file_exists('jsondata/me.txt') && filesize('jsondata/me.txt') < 160) ? ' (<ored>Offline</ored>)' : '';
            $out  = '<span class="yearpopup"><a href="pop_metarnearby.php" data-lity>' . $chartinfo . ' Nearby Metar' . $metar_offline . '</a></span>';
            $out .= '<span class="monthpopup"><a href="pop_windyradar.php" data-lity>' . $chartinfo . ' Radar</a></span>';
            $out .= '<span class="monthpopup"><a href="pop_windywind.php" data-lity>' . $chartinfo . ' Wind Map</a></span>';
            $out .= '<span class="todaypopup"><a href="' . $chartsource . '/' . $theme1 . '-charts.html?chart=\'cloudcoverplot\'&span=\'yearly\'" data-lity>' . $menucharticonpage . ' Cloud Cover</a></span>';
            break;
        case 'windspeeddirection.php':
            $out = '<span class="yearpopup"><a href="pop_menu_wind.php" data-lity>' . $menucharticonpage . ' Wind Almanac and Charts</a></span>';
            break;
        case 'barometer.php':
            $out = '<span class="yearpopup"><a href="pop_menu_barometer.php" data-lity>' . $menucharticonpage . ' Barometer Almanac and Charts</a></span>';
            break;
        case 'w34skymap.php':
        case 'moonphase.php':
            $out  = '<span class="yearpopup"><a href="mooninfo.php" data-lity>' . $chartinfo . ' Moon Info</a></span>';
            $out .= '<span class="yearpopup"><a href="pop_meteorshowers.php" data-lity>' . $meteorinfo . ' Meteor Showers</a></span>';
            $kp_label = isset($kp) && $kp >= 5 ? ' <oorange>Active</oorange>' : '';
            $out .= '<span class="yearpopup"><a href="pop_aurora.php" data-lity>' . $info . ' Aurora' . $kp_label . '</a></span>';
            $out .= '<span class="yearpopup"><a href="pop_daylightmap.php" data-lity>' . $info . ' Daylight Map</a></span>';
            break;
        case 'rainfall.php':
            $out = '<span class="yearpopup"><a href="pop_menu_rain.php" data-lity>' . $menucharticonpage . ' Rainfall Almanac and Charts</a></span>';
            break;
        case 'lightning34.php':
            $out = '<span class="yearpopup"><a href="pop_lightningalmanac.php" data-lity>' . $chartinfo . ' Strike Almanac</a></span>';
            break;
        case 'webcamsmall.php':
            if (!empty($webcamurl) || !empty($videoWeatherCamURL)) {
                $out = '<span class="yearpopup"><a href="pop_cam.php" data-lity>' . $webcamicon . ' Timelapse Camera</a></span>';
            }
            $out .= '<span class="yearpopup"><a href="pop_homeindoor.php" data-lity>' . $chartinfo . ' Indoor Guide</a></span>';
            $out .= '<span class="yearpopup"><a href="pop_mooninfo.php" data-lity>' . $chartinfo . ' Moon Info</a></span>';
            break;
        case 'indoortemperature.php':
            $out  = '<span class="yearpopup"><a href="pop_cam.php" data-lity>' . $webcamicon . ' Timelapse Camera</a></span>';
            $out .= '<span class="yearpopup"><a href="pop_homeindoor.php" data-lity>' . $chartinfo . ' Indoor Guide</a></span>';
            $out .= '<span class="yearpopup"><a href="pop_mooninfo.php" data-lity>' . $chartinfo . ' Moon Info</a></span>';
            break;
        case 'airqualitymodule.php':
            $out = '<span class="yearpopup"><a href="aqipopup.php" data-lity>' . $chartinfo . ' Air Quality | Cloudbase</a></span>';
            break;
        case 'purpleairqualitymodule.php':
        case 'ew_airqualitymodule.php':
            $out = '<span class="yearpopup"><a href="pop_airqualityinfo.php" data-lity>' . $chartinfo . ' Air Quality | Cloudbase</a></span>';
            break;
        case 'weather34uvsolar.php':
        case 'solaruv.php':
            $out = '<span class="yearpopup"><a href="pop_menu_solar.php" data-lity>' . $chartinfo . ' UV and Solar Almanacs</a></span>';
            break;
        case 'solaruvwu.php':
            $out  = '<span class="yearpopup"><a href="uvindexwu.php" data-lity>' . $chartinfo . ' UV Guide</a></span>';
            $out .= '<span class="yearpopup"><a href="pop_solaralmanac.php" data-lity>' . $chartinfo . ' Solar Almanac</a></span>';
            break;
    }
    return $out;
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $stationlocation; ?> Weather Station</title>
<meta content="Home weather station providing current weather conditions for <?php echo $stationlocation; ?>" name="description">
<meta itemprop="name" content="Home Weather Station <?php echo $stationlocation; ?>">
<meta itemprop="description" content="Home weather station providing current weather conditions for <?php echo $stationlocation; ?>">
<meta itemprop="image" content="img/weather34_meta.png">
<meta content="place" property="og:type">
<meta content="weather34" name="author">
<meta content="INDEX,FOLLOW" name="robots">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name=apple-mobile-web-app-title content="HOME WEATHER STATION">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, viewport-fit=cover">
<link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
<link rel="manifest" href="manifest.php">
<meta name="theme-color" content="#ffffff">
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
<link href="favicon.ico" rel="icon" type="image/x-icon">
<link rel="preload" href="css/fonts/clock3-webfont.woff" as="font" type="font/woff" crossorigin>
<link rel="preload" href="css/fonts/verbatim-regular.woff" as="font" type="font/woff" crossorigin>
<link href="css/main.<?php echo $theme; ?>.css?version=<?php echo filemtime('css/main.' . $theme . '.css'); ?>" rel="stylesheet prefetch">
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js')
            .then(r => console.log('SW registered:', r.scope))
            .catch(e => console.error('SW failed:', e));
    });
}
</script>
</head>

<body>
<?php
// Variables passed into popup/title helpers
$popupVars = compact('chartinfo','menucharticonpage','info','webcamicon','webcamurl',
                     'videoWeatherCamURL','weather','kp','meteorinfo','meteor_default',
                     'purpleairhardware','lang','chartsource','theme1');
?>

<!-- ── TOP BAR ─────────────────────────────────────────────────────────────── -->
<div class="weather2-container">
<div class="container weather34box-toparea">
<?php foreach ($topbar_modules as $i => $mod): ?>
    <div class="weather34box">
        <div class="title"><?php echo $info; ?> <?php echo htmlspecialchars($mod['title']); ?></div>
        <div class="value"><div id="top_<?php echo $i; ?>"></div></div>
    </div>
<?php endforeach; ?>
</div></div>

<!-- ── GRID ────────────────────────────────────────────────────────────────── -->
<?php
// Wind unit fix
if ($weather['wind_units'] === 'kts') { $weather['wind_units'] = 'kn'; }

foreach ($grid_modules as $i => $mod):
    $file  = $mod['module'];
    $title = $mod['title'] !== '' ? $mod['title'] : moduleTitle($file, $weather, $lang);

    // Night substitution for webcam
    if ($file === 'webcamsmall.php' && $dayPartCivil === 'night') {
        $file  = 'moonphase.php';
        $title = 'Moonphase';
    }

    $popups = modulePopups($file, $popupVars);

    if ($i === 0): ?>
<div class="weather-container">
    <?php endif; ?>
    <div class="weather-item">
        <div class="chartforecast">
            <?php echo $popups; ?>
        </div>
        <span class='moduletitle'><?php echo $title; ?></span><br />
        <div id="grid_<?php echo $i; ?>"></div>
    </div>
    <?php if ($i === count($grid_modules) - 1): ?>
</div>
    <?php endif; ?>
<?php endforeach; ?>

<!-- ── FOOTER ──────────────────────────────────────────────────────────────── -->
<div class=weatherfooter-container><div class=weatherfooter-item>
<div class=hardwarelogo1>
<a href="http://weewx.com" alt="http://weewx.com" title="http://weewx.com">
<?php echo '<img src="img/icon-weewx.svg" alt="WeeWX" title="WeeWX" width="150px" height="55px"><div class=hardwarelogo1text></div>'; ?>
</a></div>
<div class=hardwarelogo2><?php
if ($weatherhardware == "Davis Vantage Vue")
    echo '<img src="img/designedfordavisvue.svg" width="160px" height="65px">';
elseif ($weatherhardware == "Davis Envoy8x")
    echo '<img src="img/designedfordavisenvoy8x.svg" width="160px" height="65px">';
elseif ($davis == "Yes")
    echo '<img src="img/designedfor-1.svg" width="160px" height="65px">';
elseif ($weatherhardware == 'Weatherflow Air-Sky')
    echo '<a href="http://weatherflow.com/" target="_blank"><img src="img/wflogo.svg" width="125px" height="65px"></a>';
else
    echo '<a href="https://weather34.com/homeweatherstation/" target="_blank"><br><img src="img/weather34logo.svg" width="40px" class="homeweatherstationlogo"><weather34>designed by weather34 2015-' . date('Y') . '</weather34></a>';
?></div>
<div class=footertext>&nbsp;<?php echo $info; ?>&nbsp;(<value><?php echo $templateversion; ?></value>)&nbsp;
<?php echo "WeeWX"; ?>-(<value><maxred><?php echo $weather["swversion"]; ?></maxred></value>)&nbsp;
<?php echo $info . "&nbsp;" . $weatherhardware; ?></div>
<div class=footertext><a href="https://github.com/meisnick/weewx-Weather34"><?php echo $github; ?>&nbsp; WeeWX Version Repository at https://github.com/meisnick/weewx-Weather34 &nbsp; <img src="img/flags/<?php echo $flag; ?>.svg" width="20px"></a></div>
<div class=footertext><a href="https://hjelp.yr.no/hc/en-us/articles/203786121-Weather-symbols-on-Yr">Weather Symbols by <img src="img/yr.svg" width="14px"></a></div>
</div></div>

<div id=lightningalert></div>
</body>
<?php include_once('updater.php'); include_once('menu.php'); ?>
</html>
