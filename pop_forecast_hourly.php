<?php
//###################################################################################################################
//	weewx-Weather34 fork maintained by meisnick, based on the WeeWX skin by Ian Millard (Steepleian)    #
//	                                                                                                				#
//  Contains original legacy code (by agreement) created and developed by Brian Underdown (https://weather34.com)   #
//  for the (now superseeded) original Weather34 Template which is no longer maintained by its creator              #
//  © weather34.com original CSS/SVG/PHP 2015-2019                                                                  #
// 	                                                                                                				#
//  Contains original code by Ian Millard and collaborators															#
//  © claydonsweather.org original CSS/SVG/PHP 2020-2021                                                            #
// 	                                                                                                				#
// 	Issues for weewx-Weather34 template should be addressed to https://github.com/meisnick/weewx-Weather34/issues #                                                                                              #
// 	                                                                                                				#
//###################################################################################################################
include_once ('w34_i18n.php');
include_once ('settings.php');
include_once ('w34CombinedData.php');
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; }
$iconset = "icon";
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title>Hourly Forecast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="js/jquery.js"></script>
  <link rel="stylesheet" href="css/tokens.css">
  <style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(css/fonts/verbatim-regular.woff2) format("woff2"),url(css/fonts/verbatim-regular.ttf) format("truetype")}
html,body{background-color:var(--pop-bg);margin:0;padding:0;font-size:13px;font-family:"weathertext2",Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;color:var(--pop-ink);}
[data-theme="light"] html,[data-theme="light"] body{color:#000;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));grid-gap:5px;align-items:stretch;color:var(--pop-ink);padding:4px 44px 4px 4px;box-sizing:border-box}
[data-theme="light"] .grid{color:#000;}
.grid>article{border:1px solid rgba(245,247,252,.02);box-shadow:2px 2px 6px 0 rgba(0,0,0,.6);padding:5px;font-size:.8em;-webkit-border-radius:4px;border-radius:4px;background:hsla(233,12%,13%,.5);color:var(--pop-ink);}
[data-theme="light"] .grid>article{background-color:#fff;color:#000;border:1px solid rgba(0,0,0,.08);box-shadow:2px 2px 6px 0 rgba(0,0,0,.1);}
.grid>article img{max-width:100%}
.grid>article rainsnow{vertical-align:bottom;float:right}
.grid>article actualt{vertical-align:top;-webkit-border-radius:2px;border-radius:2px;background:0;font-family:Arial,Helvetica,sans-serif;padding:1px 3px 1px 3px;width:max-content;font-size:.9rem;color:#fff;align-items:center;justify-content:center;margin-bottom:10px;top:-2px;display:flex}
.grid>article actualtn{vertical-align:top;-webkit-border-radius:2px;border-radius:2px;background:0;font-family:Arial,Helvetica,sans-serif;padding:1px 3px 1px 3px;width:max-content;font-size:.9rem;color:#fff;align-items:center;justify-content:center;margin-bottom:10px;top:-2px;display:flex}
.grid>article tempicon{vertical-align:top;float:right;font-size:1.1em;margin-top:-20px;margin-right:20px}
a{color:#aaa;text-decoration:none}
[data-theme="light"] a{color:#007a8c;}
.weather34darkbrowser{position:relative;background:0;width:97%;height:30px;margin:auto;margin-top:-5px;margin-left:0;border-top-left-radius:5px;border-top-right-radius:5px;padding-top:10px}
.weather34darkbrowser[url]:after{content:attr(url);color:var(--pop-link);font-size:14px;text-align:center;position:absolute;left:0;right:0;top:0;padding:4px 15px;margin:11px 10px 0 auto;font-family:arial;height:20px}
blue{color:var(--pop-blue)}orange{color:var(--pop-cyan)}orange1{color:var(--pop-orange)}green{color:var(--pop-green-dim)}red{color:var(--pop-red)}red6{color:var(--pop-red-dark)}value{color:var(--pop-value)}yellow{color:var(--pop-yellow-dim)}purple{color:var(--pop-purple)}smalluvunit{font-size:.6rem;font-family:Arial,Helvetica,system}
.hitempy{position:relative;background:rgba(61,64,66,.5);color:#fff;font-size:12px;width:110px;padding:1px;-webit-border-radius:2px;border-radius:2px;margin-top:-44px;margin-left:72px;padding:2px;line-height:10px;font-size:9px}
.greydesc{color:var(--pop-value);margin-left:60px;margin-top:-35px;position:absolute;font-size:1em}
.none{float:none;margin-top:10px;position:absolute}
spantemp{font-size:.75em;color:#fff;font-family:weathertext2}
.tempicon{position:relative;font-family:weathertext2;margin-top:4px;margin-left:125px}
.uvforecast{font-size:.8rem;color:var(--pop-value);font-family:Arial,Helvetica;line-height:auto;margin-top:-15px;margin-bottom:5px}
.storm{font-size:.8rem;color:silver;font-family:Arial,Helvetica;line-height:auto;margin-top:5px;margin-bottom:2px}
.iconpos{margin-top:-4px;margin-bottom:0}
bluer{color:#fff;border-radius:2px;padding:0 2px 0 2px;align-items:center;justify-content:center;background:rgba(0,155,180,.6)}
bluet,blueu{background:#01a4b5}yellowt,yellowu{background:#e6a141}oranget,orangeu{background:#d05f2d}greent{background:#90b12a}greenu{background:#565f67}redt,redu{background:#cd5245}purplet,purpleu{background:rgba(151,88,190,.8)}
bluet,yellowt,oranget,greent,redt,purplet{-webkit-border-radius:2px;border-radius:2px;padding:2px;height:.9rem}
blueu,yellowu,orangeu,greenu,redu,purpleu{color:#fff;border-radius:2px;padding:0 3px 0 3px;align-items:center;justify-content:center}
summary{font-size:.9em;color:#aaa;display:none}blue1{color:var(--pop-cyan)}value{font-size:.95em;color:var(--pop-value)}
valuer{color:var(--pop-value);font-size:.9em}
thunder{font-size:.9em;color:#aaa}
wind{color:var(--pop-value);font-size:.9em}
.grid>article_small{border:1px solid rgba(245,247,252,.02);box-shadow:2px 2px 6px 0 rgba(0,0,0,.6);padding:5px;font-size:.8em;-webkit-border-radius:4px;border-radius:4px;background:hsla(233,12%,13%,.5)}
  bluet,blueu,greent,oranget,orangeu,yellowt,yellowu,wuunits{text-shadow:var(--badge-text-shadow)}
  </style>
</head>
<body>
<?php
$forecastime = filemtime('jsondata/forecast_hourly.txt'); ?>

  
<main class="grid">
  <?php
$jsonIcon = 'jsondata/lookupTable.json';
$jsonIcon = file_get_contents($jsonIcon);
$parsed_icon = json_decode($jsonIcon, true);
$forecastime = filemtime('jsondata/forecast_hourly.txt');
$json = 'jsondata/forecast_hourly.txt';
$json = file_get_contents($json);
$parsed_json = json_decode($json, true);
for ($k = 0;$k < 12;$k++)
{
    $pngicon[$k] = $parsed_json['response'][0]['periods'][$k]['icon'];
    $forecastIcon[$k] = $parsed_icon[$pngicon[$k]][$iconset];
    $forecastTime[$k] = date("H:i", $parsed_json['response'][0]['periods'][$k]['timestamp']);
    $forecastTempHigh[$k] = $parsed_json['response'][0]['periods'][$k]['avgTempC'];

    $forecastWindSpeed[$k] = $parsed_json['response'][0]['periods'][$k]['windSpeedKPH'];
    $forecastWindGust[$k] = $parsed_json['response'][0]['periods'][$k]['windGustKPH'] ?? $forecastWindSpeed[$k];

    $forecastWinddircardinal[$k] = $parsed_json['response'][0]['periods'][$k]['windDir'];

    $forecastprecipIntensity[$k] = $parsed_json['response'][0]['periods'][$k]['precipMM'];
    $forecastPrecipProb[$k] = $parsed_json['response'][0]['periods'][$k]['pop'];
    $forecastUV[$k] = $parsed_json['response'][0]['periods'][$k]['uvi'];
    $forecastsnow[$k] = $parsed_json['response'][0]['periods'][$k]['snowCM'];
    $forecastsummary[$k] = w34_cond($parsed_json['response'][0]['periods'][$k]['weatherPrimary']);
    $forecastnight[$k] = $parsed_json['response'][0]['periods'][$k]['isDay'];
    $forecastdesc[$k] = w34_cond($parsed_json['response'][0]['periods'][$k]['weatherPrimary']);
    $forecastheatindex[$k] = $parsed_json['response'][0]['periods'][$k]['avgFeelslikeC'];
    $forecasthumidity[$k] = $parsed_json['response'][0]['periods'][$k]['humidity'];

    //aw convert temps-rain
    //metric to F
    if ($tempunit == 'F')
    {
        $forecastTempHigh[$k] = round(($forecastTempHigh[$k] * 9 / 5) + 32, 0);
    }

    //heatindex
    if ($tempunit == 'F')
    {
        $wuskyheatindex[$k] = ($wuskyheatindex[$k] * 9 / 5) + 32;
    }

    //rain inches to mm
    if ($rainunit == 'in')
    {
        $forecastprecipIntensity[$k] = $forecastprecipIntensity[$k] * 0.0393701;
    }

    //kmh to ms
    if ($windunit == 'm/s')
    {
        $forecastWindGust[$k] = round((number_format($forecastWindGust[$k], 1) * 0.277778) , 0);
        $forecastWindSpeed[$k] = round((number_format($forecastWindSpeed[$k], 1) * 0.277778) , 0);
    }
    //kmh to mph
    if ($windunit == 'mph')
    {
        $forecastWindGust[$k] = round((number_format($forecastWindGust[$k], 1) * 0.621371) , 0);
        $forecastWindSpeed[$k] = round((number_format($forecastWindSpeed[$k], 1) * 0.621371) , 0);
    }
    //kmh to kts
    if ($windunit == 'kts')
    {
        $forecastWindGust[$k] = round($forecastWindGust[$k] * 0.539957, 0);
        $forecastWindSpeed[$k] = round($forecastWindSpeed[$k] * 0.539957, 0);
    }

    if ($forecastUV[$k] > 0)
    {
        $forecastnight[$k] = "D";
    }
    else $forecastnight[$k] = "N";

?>



  <article>  
   <actualtn>
 <?php //0  detailed forecast
    //temp
    if ($tempunit == 'F' && $forecastTempHigh[$k] < 44.6)
    {
        echo "<bluet>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($tempunit == 'F' && $forecastTempHigh[$k] > 80.6)
    {
        echo "<redt>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($tempunit == 'F' && $forecastTempHigh[$k] > 64.4)
    {
        echo "<oranget>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($tempunit == 'F' && $forecastTempHigh[$k] > 55)
    {
        echo "<yellowt>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($tempunit == 'F' && $forecastTempHigh[$k] >= 44.6)
    {
        echo "<greent>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($forecastTempHigh[$k] < 7)
    {
        echo "<bluet>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($forecastTempHigh[$k] > 27)
    {
        echo "<redt>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($forecastTempHigh[$k] > 19)
    {
        echo "<oranget>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($forecastTempHigh[$k] > 12.7)
    {
        echo "<yellowt>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    else if ($forecastTempHigh[$k] >= 7)
    {
        echo "<greent>" . $forecastTime[$k] . "h&nbsp;&nbsp;   " . number_format($forecastTempHigh[$k], 0);
    }
    echo "°" . $tempunit . "</actualtn>";

    //high temp icon
    if ($forecastnight == 'D')
    {
        if ($tempunit == 'F' && $forecastTempHigh[$k] >= 82)
        {
            echo "<img src='css/aqi/daywarm.svg' width='40px' class='tempalerticon'> ";
        }
        else if ($tempunit == 'C' && $forecastTempHigh[$k] >= 28)
        {
            echo "<img src='css/aqi/daywarm.svg' width='40px' class='tempalerticon'> ";
        }
    }
    if ($forecastnight == 'N')
    {
        if ($tempunit == 'F' && $forecastTempHigh[$k] >= 71)
        {
            echo "<img src='css/aqi/nightwarm.svg' width='40px' class='tempalerticon'> ";
        }
        if ($tempunit == 'C' && $forecastTempHigh[$k] >= 22)
        {
            echo "<img src='css/aqi/nightwarm.svg' width='40px' class='tempalerticon'> ";
        }
    }

    //icon
    echo "<div class=iconpos> ";

    //if ($forecastnight[$k] == 'D')
    //{
    echo '<img src="css/svg/' . $forecastIcon[$k] . '" width="19%" class="iconpos"></img></div>';
    //}
    //if ($forecastnight[$k] == 'N')
    //{
    //    echo '<img src="css/svg/' . $forecastIcon[$k] . '" width="19%" class="iconpos"></img></div>';
    //}
    //summary of icon
    echo '<div class=greydesc>' . $forecastdesc[$k] . '</div><br>';
    //humidity night
    if ($forecastnight[$k] == 'N')
    {
        echo '<div class=uvforecast><grey>';
        echo w34t('Humidity', 'Humidity') . ' ';
        if ($forecasthumidity[$k] >= 70)
        {
            echo "<blueu>" . $forecasthumidity[$k] . '%</blueu>';
        }
        else if ($forecasthumidity[$k] > 50)
        {
            echo "<yellowu>" . $forecasthumidity[$k] . '%</yellowu>';
        }
        else if ($forecasthumidity[$k] > 40)
        {
            echo "<greenu>" . $forecasthumidity[$k] . '%</greenu>';
        }
        else if ($forecasthumidity[$k] > 0)
        {
            echo "<redu>" . $forecasthumidity[$k] . '%</redu>';
        }
    }
    //uvi (the div is always opened: its </div> follows the rain line)
    else
    {
        echo '<div class=uvforecast><grey>';
        if ($forecastUV[$k] > 0) echo $sunlight . ' UVI ';
    }
    if ($forecastUV[$k] > 10)
    {
        echo "<purpleu>" . $forecastUV[$k] . '</purpleu><grey> ';
    }
    else if ($forecastUV[$k] > 7)
    {
        echo "<redu>" . $forecastUV[$k] . '</redu><grey> ';
    }
    else if ($forecastUV[$k] > 5)
    {
        echo "<orangeu>" . $forecastUV[$k] . '</orangeu><grey> ';
    }
    else if ($forecastUV[$k] > 2)
    {
        echo "<yellowu>" . $forecastUV[$k] . '</yellowu><grey> ';
    }
    else if ($forecastUV[$k] > 0)
    {
        echo "<greenu>" . $forecastUV[$k] . '</greenu><grey> ';
    }
    //snow
    if ($forecastsnow[$k] > 0)
    {
        echo '&nbsp;' . $snowflakesvg[$k] . '<valuer>' . w34t('CondSnow', 'Snow') . '  <bluer>' . $forecastsnow[$k] . 'cm</bluer>';
    }
    //rain — only show when there is a signal (amount or probability)
    else if ($forecastPrecipProb[$k] > 0 || $forecastprecipIntensity[$k] > 0)
    {
        echo '&nbsp;' . $rainsvg . '<valuer>';
        if ($forecastprecipIntensity[$k] > 0) {
            echo '<bluer>' . number_format($forecastprecipIntensity[$k], 2) . '&nbsp;' . $rainunit . '</bluer>&nbsp;&middot;&nbsp;';
        }
        echo '<grey>' . $forecastPrecipProb[$k] . '%&nbsp;' . w34t('ChanceLc', 'chance') . '</grey></valuer>';
    }
    echo "</div>";
    //wind and gust: the forecast's own values (the old "Gust" was the sustained speed x 1.625, printed twice for mph)
    $gust_warn = array('mph' => array(25, 30), 'kts' => array(22, 26), 'km/h' => array(40, 48), 'm/s' => array(11, 13));
    list($g_amber, $g_red) = isset($gust_warn[$windunit]) ? $gust_warn[$windunit] : $gust_warn['km/h'];
    echo "<wind>" . w34t('Wind', 'Wind') . " <orangeu>" . w34_dir($forecastWinddircardinal[$k]) . "</orangeu>&nbsp;<blueu>" . number_format($forecastWindSpeed[$k], 0) . "&nbsp;<wuunits>" . $windunit . "</wuunits></blueu>";
    if ($forecastWindGust[$k] > $forecastWindSpeed[$k])
    {
        $gtag = $forecastWindGust[$k] >= $g_red ? 'redu' : ($forecastWindGust[$k] >= $g_amber ? 'orangeu' : 'blueu');
        echo "&nbsp;" . w34t('Gust', 'Gust') . "&nbsp;<" . $gtag . ">" . number_format($forecastWindGust[$k], 0) . "</" . $gtag . ">";
    }
    echo '</wind>';

?>  
</article>
<?php
} ?>
 <!-- copyright needs to be kept please be ethical--->
  <article>
    <span style="font-size:9px;color:<?php echo $text1 ?>;">
  <?php echo $knfo ?> <?php echo w34f('CreditWeather34', 'CSS/SVG/PHP scripts were developed by %s for use in the weather34 template', '<a href="https://weather34.com" title="weather34.com" target="_blank" style="font-size:8px;">weather34.com</a>'); ?> &copy; 2015-<?php echo date('Y'); ?></span> <br>
    </article>
  <article>
    <span style="font-size:9px;color:<?php echo $text1 ?>;">
  <?php echo $knfo ?> <?php echo w34f('CreditSteepleian', 'CSS/SVG/PHP scripts were adapted by %s for use in the weewx-Weather34 skin', '<a href="https://claydonsweather.org.uk" title="Steepleian" target="_blank" style="font-size:8px;">Steepleian</a>'); ?> &copy; 2015-<?php echo date('Y'); ?></span> <br>
    </article>
  <article>
    <div style="font-size:9px;line-height:13px;color:<?php echo $text1 ?>;margin-bottom:3px;">
  <?php echo $knfo ?> <?php echo w34t('ForecastDataBy', 'Forecast data provided by'); ?> <a href="https://open-meteo.com/" title="Open-Meteo API" target="_blank" style="white-space:nowrap;">Open-Meteo</a> &mdash; <a href="https://creativecommons.org/licenses/by/4.0/" title="CC BY 4.0" target="_blank" style="font-size:8px;white-space:nowrap;">CC BY 4.0</a></div>
    <div style="font-size:9px;line-height:13px;color:<?php echo $text1 ?>;">
  <?php echo $knfo ?> <?php echo w34t('ForecastIconsBy', 'Forecast icons provided by'); ?> <a href="https://github.com/basmilius/weather-icons" title="basmilius/weather-icons" target="_blank" style="white-space:nowrap;">basmilius/weather-icons</a> &mdash; <a href="https://github.com/basmilius/weather-icons/blob/dev/LICENSE" target="_blank" style="font-size:8px;white-space:nowrap;">MIT</a></div>
    </article>
</main>
  </div>
  </body>
  </html>
