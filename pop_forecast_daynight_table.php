<?php include_once('w34_i18n.php'); ?>
<?php
//###################################################################################################################
//	weewx-Weather34 fork maintained by meisnick, based on the WeeWX skin by Ian Millard (Steepleian)    #
//	                                                                                                				#
//  Contains original code by Ian Millard and collaborators															#
//  © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                                         #
// 	                                                                                                				#
// 	Issues for weewx-Weather34 template should be addressed to https://github.com/meisnick/weewx-Weather34/issues #                                                                                              #
// 	                                                                                                				#
//###################################################################################################################
include_once ('w34CombinedData.php');
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; }
if ($theme === "dark")
{
    echo '<style>.demo{border:0 solid #aaa;border-collapse:collapse;padding:50px;font-family:arial,helvetica,verdana,sans-serif;font-size:10px;margin-bottom:50px;margin-top:50px;width:100%;color:silver}.demo th{border-bottom:.5px solid #aaa;padding:5px;color:silver}.demo td{border:0 solid #aaa;padding:0;background:0;text-align:center}.demo td#CELL1{background-color:transparent;color:#000}.demo td#CELL2{background-color:#9cff9c}.demo td#CELL3{background-color:#31ff00}.demo td#CELL4{background-color:#31cf00}.demo td#CELL5{background-color:#ff0}.demo td#CELL6{background-color:#ffcf00}.demo td#CELL7{background-color:#ff9a00}.demo td#CELL8{background-color:#ff6464}.demo td#CELL9{background-color:red;color:#fff}.demo td#CELL10{background-color:#900;color:#fff}.demo td#CELL11{background-color:#ce30ff;color:#fff}img{margin-left:10px;margin-right:10px;width:60px;background-color:transparent}body{margin:8px 44px 8px 8px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;color:#fff;font-weight:400;background-color:rgba(30,33,36,1)}html{margin:0;padding:0}a:link,a:visited,a:hover,a:active{color:#fff}table{font-size:11px;vertical-align:bottom;width:auto}.grid3{display:grid;grid-template-columns:repeat(auto-fill,minmax(100%,1fr));grid-gap:20px;color:#000}.grid3>articlegraph3{border:1px solid rgba(245,247,252,.02);box-shadow:2px 2px 6px 0 rgba(0,0,0,.6);padding:5px;font-size:.8em;-webkit-border-radius:4px;border-radius:4px;background:0;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;width:100%;height:90%}</style>';
}
else if ($theme === "light")
{
    echo '<style>.demo{border:0 solid #aaa;border-collapse:collapse;padding:50px;font-family:arial,helvetica,verdana,sans-serif;font-size:10px;margin-bottom:50px;margin-top:50px;width:100%;color:#000}.demo th{border-bottom:1px solid #aaa;padding:5px;color:#000}.demo td{border:0 solid #aaa;padding:0;background:#FFF;text-align:center}.demo td#CELL1{background-color:transparent;color:#000}.demo td#CELL2{background-color:#9cff9c}.demo td#CELL3{background-color:#31ff00}.demo td#CELL4{background-color:#31cf00}.demo td#CELL5{background-color:#ff0}.demo td#CELL6{background-color:#ffcf00}.demo td#CELL7{background-color:#ff9a00}.demo td#CELL8{background-color:#ff6464}.demo td#CELL9{background-color:red;color:#fff}.demo td#CELL10{background-color:#900;color:#fff}.demo td#CELL11{background-color:#ce30ff;color:#fff}img{margin-left:10px;margin-right:10px;width:60px;background-color:transparent}body{margin:8px 44px 8px 8px;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;color:#222;font-weight:400;background-color:#fff}html{margin:0;padding:0}a:link,a:visited,a:hover,a:active{color:#000}table{font-size:11px;vertical-align:bottom;width:auto}.grid3{display:grid;grid-template-columns:repeat(auto-fill,minmax(100%,1fr));grid-gap:20px;color:#000}.grid3>articlegraph3{border:1px solid rgba(0,0,0,.08);box-shadow:2px 2px 6px 0 rgba(0,0,0,.1);padding:5px;font-size:.8em;-webkit-border-radius:4px;border-radius:4px;background:#fff;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;width:100%;height:90%}</style>';
}
$forecastTime[0] = "0";
?>
<!DOCTYPE html>
<html lang="<?php include_once __DIR__ . '/w34_i18n.php'; echo w34_html_lang(); ?>">
<head>
  <meta charset="UTF-8">
  <title>Day Night Forecast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


<?php
$forecastime = filemtime('jsondata/forecast_daily.txt');
$jsonIcon = 'jsondata/lookupTable.json';
$jsonIcon = file_get_contents($jsonIcon);
$parsed_icon = json_decode($jsonIcon, true);
$json = 'jsondata/forecast_daily.txt';
$json = file_get_contents($json);
$parsed_json = json_decode($json, true);
for ($k = 0;$k < 14;$k++)
{
    $pngicon[$k] = $parsed_json['response'][0]['periods'][$k]['icon'];
    $forecastIcon[$k] = $parsed_icon[$pngicon[$k]]['icon'];
    $Time[$k] = date("H", $parsed_json['response'][0]['periods'][$k]['timestamp']);
    $nameDay[$k] = w34_date("l", $parsed_json['response'][0]['periods'][$k]['timestamp']);

    if ($Time[0] === "07")
    {
        $forecastTime[0] = w34t('Today', 'Today');
        $forecastTime[1] = w34t('Tonight', 'Tonight');
        $forecastTime[2] = w34t('Tomorrow', 'Tomorrow');
        $forecastTime[3] = w34t('Tomorrownight', 'Tomorrow Night');
        $forecastTime[4] = $nameDay[4];
        $forecastTime[5] = $nameDay[5] . ' ' . w34t('Night', 'Night');
        $forecastTime[6] = $nameDay[6];
        $forecastTime[7] = $nameDay[7] . ' ' . w34t('Night', 'Night');
        $forecastTime[8] = $nameDay[8];
        $forecastTime[9] = $nameDay[9] . ' ' . w34t('Night', 'Night');
        $forecastTime[10] = $nameDay[10];
        $forecastTime[11] = $nameDay[11] . ' ' . w34t('Night', 'Night');
        $forecastTime[12] = $nameDay[12];
        $forecastTime[13] = $nameDay[13] . ' ' . w34t('Night', 'Night');
        $forecastTime[14] = $nameDay[14];
        $forecastTime[15] = $nameDay[15] . ' ' . w34t('Night', 'Night');
        $forecastTime[16] = $nameDay[16];
        $forecastTime[17] = $nameDay[17] . ' ' . w34t('Night', 'Night');
    }
    else if ($Time[0] === "19")
    {
        $forecastTime[0] = w34t('Tonight', 'Tonight');
        $forecastTime[1] = w34t('Tomorrow', 'Tomorrow');
        $forecastTime[2] = w34t('Tomorrownight', 'Tomorrow Night');
        $forecastTime[3] = $nameDay[3];
        $forecastTime[4] = $nameDay[4] . ' ' . w34t('Night', 'Night');
        $forecastTime[5] = $nameDay[5];
        $forecastTime[6] = $nameDay[6] . ' ' . w34t('Night', 'Night');
        $forecastTime[7] = $nameDay[7];
        $forecastTime[8] = $nameDay[8] . ' ' . w34t('Night', 'Night');
        $forecastTime[9] = $nameDay[9];
        $forecastTime[10] = $nameDay[10] . ' ' . w34t('Night', 'Night');
        $forecastTime[11] = $nameDay[11];
        $forecastTime[12] = $nameDay[12] . ' ' . w34t('Night', 'Night');
        $forecastTime[13] = $nameDay[13];
        $forecastTime[14] = $nameDay[14] . ' ' . w34t('Night', 'Night');
    }

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

    //rain: same format as the hourly table ("0.04 in · 30%", "30% chance" or "-").
    // The old `=== 0` test never matched the float 0.0, so every period read "0in 0%".
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
    else
    {
        $forecasthumidity[$k] = "-";
        $colorHumidity[$k] = "";
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



<?php
// Shared forecast-table layout (hourly + day/night). Replaces the legacy .demo/.grid3 sizing:
// no 50px gaps, one text size, sticky header row, the page scrolls inside the tab's frame.
$fcBg   = ($theme === 'light') ? '#ffffff' : 'rgba(30,33,36,1)';
$fcInk  = ($theme === 'light') ? '#222222' : '#c8c8c8';
$fcHead = ($theme === 'light') ? '#2d3a4b' : '#e6e8ec';
$fcLine = ($theme === 'light') ? '#e2e5ea' : 'rgba(255,255,255,.08)';
$fcRule = ($theme === 'light') ? '#c9ced6' : 'rgba(255,255,255,.22)';
function fc_close($open) { return $open ? str_replace('<', '</', $open) : ''; }
?>
<style>
  html, body { height: auto; }
  body { margin: 0 !important; padding: 0 10px 10px !important; background: <?php echo $fcBg; ?> !important; }
  .fc-wrap { display: block; }
  .fc-table { width: 100%; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: <?php echo $fcInk; ?>; }
  .fc-table thead th { position: sticky; top: 0; z-index: 1; background: <?php echo $fcBg; ?>; color: <?php echo $fcHead; ?>;
    font-weight: 600; font-size: 12px; padding: 12px 6px 7px; border-bottom: 1px solid <?php echo $fcRule; ?>; text-align: center; white-space: nowrap; }
  .fc-table td { padding: 5px 6px; text-align: center; border-bottom: 1px solid <?php echo $fcLine; ?>; white-space: nowrap; vertical-align: middle; }
  .fc-table th.fc-first, .fc-table td.fc-first { text-align: left; padding-left: 8px; }
  .fc-table th.fc-cond, .fc-table td.fc-cond { text-align: left; }
  .fc-table td.fc-cond img { width: 30px; height: 24px; margin: 0 8px 0 0 !important; vertical-align: middle; display: inline-block; }
  .fc-u { font-size: 10px; margin-left: 2px; opacity: .85; }
  .fc-credit { font-size: 9px; color: #999; padding: 10px 8px 2px; }
  .fc-credit a { color: #999 !important; }
</style>
<main class="fc-wrap">
<table class="fc-table">
  <thead><tr>
    <th class="fc-first"><?php echo w34t('Period', 'Period'); ?></th>
    <th class="fc-cond"><?php echo w34t('Conditions', 'Conditions'); ?></th>
    <th><?php echo w34t('Temperature', 'Temperature'); ?></th>
    <th><?php echo w34t('Precipitation', 'Precipitation'); ?></th>
    <th><?php echo w34t('WindSpeedLabel', 'Wind Speed'); ?></th>
    <th><?php echo w34t('Direction', 'Direction'); ?></th>
    <th><?php echo w34t('UvIndex', 'UV Index'); ?></th>
    <th><?php echo w34t('Humidity', 'Humidity'); ?></th>
  </tr></thead>
  <tbody>
<?php for ($k = 0; $k < 14; $k++) { ?>
  <tr>
    <td class="fc-first"><?php echo $forecastTime[$k]; ?></td>
    <td class="fc-cond"><img src="css/svg/<?php echo $forecastIcon[$k]; ?>" alt=""><?php echo $forecastsummary[$k]; ?></td>
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
