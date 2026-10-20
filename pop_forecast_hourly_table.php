<?php
//###################################################################################################################
//	weewx-Weather34 fork maintained by meisnick, based on the WeeWX skin by Ian Millard (Steepleian)    #
//	                                                                                                				#
//  Contains original code by Ian Millard and collaborators															#
//  © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                                            #
// 	                                                                                                				#
// 	Issues for weewx-Weather34 template should be addressed to https://github.com/meisnick/weewx-Weather34/issues #                                                                                              #
// 	                                                                                                				#
//###################################################################################################################
include_once ('settings.php');
include_once ('w34CombinedData.php');
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; }
function fc_close($open) { return $open ? str_replace('<', '</', $open) : ''; }
$forecastTime[0] = "0";
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title>Hourly Forecast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/tokens.css">
  <style>
  html, body { height: auto; }
  body { margin: 0 !important; padding: 0 10px 10px !important; background: var(--pop-bg) !important; color: var(--pop-ink); font-family: Arial, Helvetica, sans-serif; }
  .fc-wrap { display: block; }
  .fc-table { width: 100%; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: var(--pop-table-text); }
  .fc-table thead th { position: sticky; top: 0; z-index: 1; background: var(--pop-bg); color: var(--pop-table-th-text);
    font-weight: 600; font-size: 12px; padding: 12px 6px 7px; border-bottom: 1px solid var(--pop-table-th-border); text-align: center; white-space: nowrap; }
  .fc-table td { padding: 4px 6px; text-align: center; border-bottom: 1px solid var(--pop-table-td-border); white-space: nowrap; vertical-align: middle; }
  .fc-table th.fc-first, .fc-table td.fc-first { text-align: left; padding-left: 8px; }
  .fc-table th.fc-cond, .fc-table td.fc-cond { text-align: left; }
  .fc-ci { display: inline-flex; align-items: center; gap: 8px; }
  .fc-table td.fc-cond img { width: 28px; height: 20px; margin: 0 !important; display: block; flex: none; }
  [data-theme="light"] .fc-table td.fc-cond img { filter: drop-shadow(0 0 1px rgba(0,0,0,.45)); }
  .fc-u { font-size: 10px; margin-left: 2px; opacity: .85; }
  .fc-credit { font-size: 9px; color: #999; padding: 10px 8px 2px; }
  .fc-credit a { color: var(--pop-table-credit-link) !important; }
  [data-theme="light"] .fc-credit { color: #666; }
  @media screen and (max-width:768px) {
    main.fc-wrap {
      width: 100% !important;
      max-width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
  }
  </style>
</head>
<body>

<?php
$jsonIcon = 'jsondata/lookupTable.json';
$jsonIcon = file_get_contents($jsonIcon);
$parsed_icon = json_decode($jsonIcon, true);
$forecastime = filemtime('jsondata/forecast_hourly.txt');
$json = 'jsondata/forecast_hourly.txt';
$json = file_get_contents($json);
$parsed_json = json_decode($json, true);
for ($k = 0;$k < 24;$k++)
{

    $pngicon[$k] = $parsed_json['response'][0]['periods'][$k]['icon'];
    $forecastIcon[$k] = $parsed_icon[$pngicon[$k]]['icon'];
    $forecastTime[$k] = date("H:i", $parsed_json['response'][0]['periods'][$k]['timestamp']);

    $forecastTempHigh[$k] = $parsed_json['response'][0]['periods'][$k]['maxTempC'];
    $forecastTempLow[$k] = $parsed_json['response'][0]['periods'][$k]['minTempC'];
    if ($forecastTempHigh[$k] === null)
    {
        $forecastTempHigh[$k] = $forecastTempLow[$k];
    }
    $forecastWindSpeedMax[$k] = $parsed_json['response'][0]['periods'][$k]['windSpeedMaxKPH'];
    $forecastWindSpeedMin[$k] = $parsed_json['response'][0]['periods'][$k]['windSpeedMinKPH'];

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
    if ($forecastUV[$k] === 0 or $forecastUV[$k] === null)
    {
        $forecastUV[$k] = "-";
        $colorUV[$k] = "<greyt>";
    }

    if ($tempunit == 'F')
    {
        $forecastTempHigh[$k] = round(($forecastTempHigh[$k] * 9 / 5) + 32, 0);
    }

    //heatindex
    if ($tempunit == 'F')
    {
        $forecastheatindex[$k] = round(($forecastheatindex[$k] * 9 / 5) + 32, 0);
    }

    //rain — split amount and probability into distinct elements
    if ($forecastprecipIntensity[$k] > 0 && $rainunit == 'in')
    {
        $forecastprecipIntensity[$k] = number_format($forecastprecipIntensity[$k] * 0.0393701, 2);
        $forecastPrecip[$k] = "<bluet>" . $forecastprecipIntensity[$k] . "</bluet><span class=\"fc-u\">in</span> &middot; <greyt>" . $forecastPrecipProb[$k] . "%</greyt>";
    }
    else if ($forecastprecipIntensity[$k] > 0)
    {
        $forecastPrecip[$k] = "<bluet>" . $forecastprecipIntensity[$k] . "</bluet><span class=\"fc-u\">mm</span> &middot; <greyt>" . $forecastPrecipProb[$k] . "%</greyt>";
    }
    else if ($forecastPrecipProb[$k] > 0)
    {
        $forecastPrecip[$k] = "<greyt>" . $forecastPrecipProb[$k] . "%&nbsp;" . w34t('ChanceLc', 'chance') . "</greyt>";
    }
    else
    {
        $forecastPrecip[$k] = "<greyt>-</greyt>";
    }

    //kmh to ms
    if ($windunit == 'm/s')
    {
        $forecastWindSpeedMax[$k] = round((number_format($forecastWindSpeedMax[$k], 1) * 0.277778) , 0);
        $forecastWindSpeedMin[$k] = round((number_format($forecastWindSpeedMin[$k], 1) * 0.277778) , 0);
    }
    //kmh to mph
    if ($windunit == 'mph')
    {
        $forecastWindSpeedMax[$k] = round((number_format($forecastWindSpeedMax[$k], 1) * 0.621371) , 0);
        $forecastWindSpeedMin[$k] = round((number_format($forecastWindSpeedMin[$k], 1) * 0.621371) , 0);
    }

    if ($forecastnight[$k] !== false)
    {
        $forecastnight[$k] = "D";
    }
    else $forecastnight[$k] = "N";

    if ($forecastUV[$k] > 10)
    {
        $colorUV[$k] = "<purplet>";
    }
    else if ($forecastUV[$k] > 7)
    {
        $colorUV[$k] = "<redt>";
    }
    else if ($forecastUV[$k] > 5)
    {
        $colorUV[$k] = "<oranget>";
    }
    else if ($forecastUV[$k] > 2)
    {
        $colorUV[$k] = "<yellowt>";
    }
    else if ($forecastUV[$k] > 0)
    {
        $colorUV[$k] = "<greent>";
    }

    if ($forecasthumidity[$k] >= 70)
    {
        $colorHumidity[$k] = "<bluet>";
    }
    else if ($forecasthumidity[$k] > 50)
    {
        $colorHumidity[$k] = "<yellowt>";
    }
    else if ($forecasthumidity[$k] > 40)
    {
        $colorHumidity[$k] = "<greent>";
    }
    else if ($forecasthumidity[$k] > 0)
    {
        $colorHumidity[$k] = "<redt>";
    }
    if ($tempunit == 'F' && $forecastTempHigh[$k] < 44.6)
    {
        $colorTempHigh[$k] = "<bluet>";
    }
    else if ($tempunit == 'F' && $forecastTempHigh[$k] > 80.6)
    {
        $colorTempHigh[$k] = "<redt>";
    }
    else if ($tempunit == 'F' && $forecastTempHigh[$k] > 64.4)
    {
        $colorTempHigh[$k] = "<oranget>";
    }
    else if ($tempunit == 'F' && $forecastTempHigh[$k] > 55)
    {
        $colorTempHigh[$k] = "<yellowt>";
    }
    else if ($tempunit == 'F' && $forecastTempHigh[$k] >= 44.6)
    {
        $colorTempHigh[$k] = "<greent>";
    }
    else if ($forecastTempHigh[$k] < 7)
    {
        $colorTempHigh[$k] = "<bluet>";
    }
    else if ($forecastTempHigh[$k] > 27)
    {
        $colorTempHigh[$k] = "<redt>";
    }
    else if ($forecastTempHigh[$k] > 19)
    {
        $colorTempHigh[$k] = "<oranget>";
    }
    else if ($forecastTempHigh[$k] > 12.7)
    {
        $colorTempHigh[$k] = "<yellowt>";
    }
    else if ($forecastTempHigh[$k] >= 7)
    {
        $colorTempHigh[$k] = "<greent>";
    }

    if ($forecastnight[$k] !== false)
    {
        $forecastnight[$k] = "D";
    }
    else $forecastnight[$k] = "N";

?>



 
<?php
} ?>



<main class="fc-wrap">
<table class="fc-table">
  <thead><tr>
    <th class="fc-first"><?php echo w34t('Time', 'Time'); ?></th>
    <th class="fc-cond"><?php echo w34t('Conditions', 'Conditions'); ?></th>
    <th><?php echo w34t('Temperature', 'Temperature'); ?></th>
    <th><?php echo w34t('Precipitation', 'Precipitation'); ?></th>
    <th><?php echo w34t('WindSpeedLabel', 'Wind Speed'); ?></th>
    <th><?php echo w34t('Direction', 'Direction'); ?></th>
    <th><?php echo w34t('UvIndex', 'UV Index'); ?></th>
    <th><?php echo w34t('Humidity', 'Humidity'); ?></th>
  </tr></thead>
  <tbody>
<?php for ($k = 0; $k < 24; $k++) { ?>
  <tr>
    <td class="fc-first"><?php echo $forecastTime[$k]; ?></td>
    <td class="fc-cond"><span class="fc-ci"><img src="css/svg/<?php echo $forecastIcon[$k]; ?>" alt=""><?php echo $forecastsummary[$k]; ?></span></td>
    <td><?php echo $colorTempHigh[$k] . $forecastTempHigh[$k] . '&deg;' . $tempunit . fc_close($colorTempHigh[$k]); ?></td>
    <td><?php echo $forecastPrecip[$k]; ?></td>
    <td><?php echo $forecastWindSpeedMax[$k]; ?><span class="fc-u"><?php echo $windunit; ?></span></td>
    <td><?php echo w34_dir($forecastWinddircardinal[$k]); ?></td>
    <td><?php echo $colorUV[$k] . $forecastUV[$k] . fc_close($colorUV[$k]); ?></td>
    <td><?php echo $colorHumidity[$k] . $forecasthumidity[$k] . fc_close($colorHumidity[$k]); ?><span class="fc-u">%</span></td>
  </tr>
<?php } ?>
  </tbody>
</table>
<div class="fc-credit">
  <?php echo w34t('DataLabel', 'Data'); ?>: <a href="https://open-meteo.com/" target="_blank">Open-Meteo</a>
  (<a href="https://creativecommons.org/licenses/by/4.0/" target="_blank">CC BY 4.0</a>)
  &nbsp;&middot;&nbsp;
  <?php echo w34t('IconsLabel', 'Icons'); ?>: <a href="https://github.com/basmilius/weather-icons" target="_blank">basmilius/weather-icons</a> (MIT)
</div>
</main>
</body>
</html>
