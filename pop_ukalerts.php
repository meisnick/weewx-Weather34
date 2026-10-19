<?php
//###################################################################################################################
//	weewx-Weather34 fork maintained by meisnick, based on the WeeWX skin by Ian Millard (Steepleian)    #
//	                                                                                                				#
//  Contains original code by Ian Millard and collaborators															#
//  © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                                         #
// 	                                                                                                				#
// 	Issues for weewx-Weather34 template should be addressed to https://github.com/meisnick/weewx-Weather34/issues #
// 	                                                                                                				#
//###################################################################################################################
include_once "w34CombinedData.php";
include ('settings1.php');
error_reporting(0);
if (isset($_GET['theme'])) {
    $theme = ($_GET['theme'] === 'light') ? 'light' : 'dark';
}
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('AerisWeatherAlerts', 'AerisWeather Alerts'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/tokens.css">
  <link href="css/popup.<?php echo $theme; ?>.css?version=<?php echo filemtime('css/popup.' . $theme . '.css'); ?>" rel="stylesheet prefetch">
  <style>
    body { height: auto; overflow: auto; padding: 10px 44px 10px 15px; }
    .weather34darkbrowser { margin-right: 0; }
    .weather34darkbrowser[url]::after { margin: 11px 44px 0 auto; }
    .alert-row { display: flex; flex-direction: row; height: 120px; padding: 10px 0; background-color: whitesmoke; }
    .alert-row-narrow { display: flex; flex-direction: row; padding: 10px; border-radius: 4px; margin-bottom: 8px; font-size: 12px; align-items: center; }
    .alert-text-container { font-family: Arial, Helvetica, sans-serif; font-size: 11px; display: flex; flex-direction: column; justify-content: center; margin-right: 10px; color: #000; }
  </style>
</head>
<body>
<?php
$forecastime = filemtime('jsondata/uk.txt'); ?>
<div class="weather34darkbrowser" style="color:<?php echo $text1 ?>;" url="Weather Alerts for <?php echo $stationName ?>
                                         <?php echo '&nbsp;';
echo "Risk Level Updated &nbsp;" . date($timeFormatShort, $forecastime); ?>"></div>  
 
<?php
$json_icon = file_get_contents("jsondata/lookupTable.json");
$parsed_icon = json_decode($json_icon, true);
// needs the php-xml extension and a Met Office feed in jsondata/uk.txt (nothing fetches one by default)
$xml = (function_exists('simplexml_load_file') && is_file("jsondata/uk.txt")) ? @simplexml_load_file("jsondata/uk.txt") : false;
if ($xml === false) {
    echo '<div style="text-align:center;padding:40px;color:var(--modal-meta-ink);font-size:13px;">UK weather alerts unavailable: no Met Office feed in jsondata/uk.txt</div></body></html>';
    exit;
}
$jsonData = json_encode($xml, JSON_PRETTY_PRINT);
$parsed_json = json_decode($jsonData, true);
if (($parsed_json['channel']['item'][0]['description']) !== null)
{
    $data = "multi";
    $datastream = "multi";
}
else if (($parsed_json['channel']['item']['description']) !== null)
{
    $data = "single";
    $datastream = "multi";
}
else $datastream = "none";
$favcolor = $datastream;

switch ($favcolor)
{

    case "multi":
        if ($data === "single")
        {
            $cnt = 1;
        }
        else if ($data === "multi")
        {
            $cnt = count($parsed_json['channel']['item']);
        }
        for ($i = 0;$i < $cnt;$i++)
        {
            if ($data === "single")
            {
                $description[$i] = $parsed_json['channel']['item']['description'];
            }
            else if ($data === "multi")
            {
                $description[$i] = $parsed_json['channel']['item'][$i]['description'];
            }
            $url[$i] = "https://www.metoffice.gov.uk/weather/warnings-and-advice/uk-warnings#";
            $validpos = strpos($description[$i], "valid");
            $validtext = substr($description[$i], $validpos);
            $datestring = explode(" ", $validtext);
            $hourFrom = substr($datestring[2], 0, 2);
            $hourTo = substr($datestring[7], 0, 2);
            $minFrom = substr($datestring[2], 2, 2);
            $minTo = substr($datestring[7], 2, 2);
            $dayFrom = substr($datestring[4], 0, 2);
            $dayTo = substr($datestring[9], 0, 2);
            $monthFrom = substr($datestring[5], 0, 3);
            $monthTo = substr($datestring[10], 0, 3);
            $fromTime = $hourFrom . ":" . $minFrom . " " . $dayFrom . " " . $monthFrom . " " . date("Y");
            $toTime = $hourTo . ":" . $minTo . " " . $dayTo . " " . $monthTo . " " . date("Y");
            $fromTimeStamp = strtotime($fromTime);
            $toTimeStamp = strtotime($toTime);

            $from = date_create($fromTime, new DateTimeZone("GMT"))->setTimezone(new DateTimeZone("Europe/London"))
                ->format("H:i l j F");
            $to = date_create($toTime, new DateTimeZone("GMT"))->setTimezone(new DateTimeZone("Europe/London"))
                ->format("H:i l j F");

            $fromto[$i] = "Valid from " . "$from" . " to " . "$to" . " ";

            $description[$i] = substr($description[$i], 0, ($validpos - 1)) . ".";

            if (strpos($description[$i], "Red") === 0)

            {
                $alertlevel[$i] = "red";
                $warntext = "The weather is very dangerous. Exceptionally intense meteorological phenomena have been forecast. Major damage and accidents are likely, in many cases with threat to life and limb, over a wide area. Keep frequently informed about detailed expected meteorological conditions and risks. Follow orders and any advice given by your authorities under all circumstances, be prepared for extraordinary measures.";
            }

            else if (strpos($description[$i], "Amber") === 0)
            {
                $alertlevel[$i] = "orange";
                $warntext = "The weather is dangerous. Unusual meteorological phenomena have been forecast. Damage and casualties are likely to happen. Be very vigilant and keep regularly informed about the detailed expected meteorological conditions. Be aware of the risks that might be unavoidable. Follow any advice given by your authorities.";
            }

            else if (strpos($description[$i], "Yellow") === 0)
            {
                $alertlevel[$i] = "yellow";
                $warntext = "The weather is potentially dangerous. The weather phenomena that have been forecast are not unusual, but be attentive if you intend to practice activities exposed to meteorological risks. Keep informed about the expected meteorological conditions and do not take any avoidable risk.";
            }

            if ($alertlevel[$i] == 'yellow' && strpos($description[$i], "wind") !== false)
            {
                $alerttype[$i] = 'Wind';

            }
            else if ($alertlevel[$i] == 'orange' && strpos($description[$i], "wind") !== false)
            {
                $alerttype[$i] = 'Wind';

            }
            else if ($alertlevel[$i] == 'red' && strpos($description[$i], "wind") !== false)
            {
                $alerttype[$i] = 'Wind';

            }
            else if ($alertlevel[$i] == 'yellow' && strpos($description[$i], "snow") !== false)
            {
                $alerttype[$i] = 'Snow';

            }
            else if ($alertlevel[$i] == 'orange' && strpos($description[$i], "snow") !== false)
            {
                $alerttype[$i] = 'Snow';

            }
            else if ($alertlevel[$i] == 'red' && strpos($description[$i], "snow") !== false)
            {
                $alerttype[$i] = 'Snow';

            }
            else if ($alertlevel[$i] == 'yellow' && strpos($description[$i], "ice") !== false)
            {
                $alerttype[$i] = 'Ice';

            }
            else if ($alertlevel[$i] == 'orange' && strpos($description[$i], "ice") !== false)
            {
                $alerttype[$i] = 'Ice';

            }
            else if ($alertlevel[$i] == 'red' && strpos($description[$i], "ice") !== false)
            {
                $alerttyp[$i] = 'Ice';

            }
            else if ($alertlevel[$i] == 'yellow' && strpos($description[$i], "fog") !== false)
            {
                $alerttype[$i] = 'Fog';

            }
            else if ($alertlevel[$i] == 'orange' && strpos($description[$i], "fog") !== false)
            {
                $alerttype[$i] = 'Fog';

            }
            else if ($alertlevel[$i] == 'red' && strpos($description[$i], "fog") !== false)
            {
                $alerttype[$i] = 'Fog';

            }
            else if ($alertlevel[$i] == 'yellow' && strpos($description[$i], "thunderstorm") !== false)
            {
                $alerttype[$i] = 'Thunderstorms';

            }
            else if ($alertlevel[$i] == 'orange' && strpos($description[$i], "thunderstorm") !== false)
            {
                $alerttype[$i] = 'Thunderstorms';

            }
            else if ($alertlevel[$i] == 'red' && strpos($description[$i], "thunderstorm") !== false)
            {
                $alerttype[$i] = 'Thunderstorms';

            }
            else if ($alertlevel[$i] == 'yellow' && strpos($description[$i], "rain") !== false)
            {
                $alerttype[$i] = 'Rain';

            }
            else if ($alertlevel[$i] == 'orange' && strpos($description[$i], "rain") !== false)
            {
                $alerttype[$i] = 'Rain';

            }
            else if ($alertlevel[$i] == 'red' && strpos($description[$i], "rain") !== false)
            {
                $alerttype[$i] = 'Rain';

            }
            else if ($alertlevel[$i] == 'yellow' && strpos($description[$i], "heat") !== false)
            {
                $alerttype[$i] = 'Extreme heat';

            }
            else if ($alertlevel[$i] == 'orange' && strpos($description[$i], "heat") !== false)
            {
                $alerttype[$i] = 'Extreme heat';

            }
            else if ($alertlevel[$i] == 'red' && strpos($description[$i], "heat") !== false)
            {
                $alerttype[$i] = 'Extreme heat';

            }
            $warnimage[$i] = "css/svg/" . $parsed_icon[$alertlevel[$i]][$alerttype[$i]];

?>
<main class="grid_MET"><articlegraph_MET class="alert-row-narrow" style="font-size:10px; background-color:<?php echo $alertlevel[$i] ?>">
                            <div class="alert-row" style="background-color:<?php echo $alertlevel[$i] ?>">
    <img src="<?php echo $warnimage[$i] ?>"style="width:70px; height:70px;">
    <div class="alert-text-container">
      <div><?php echo $fromto[$i] ?></br></br><?php echo $description[$i] ?></br></br><?php echo $warntext ?></a></div>
        
    
        
    </div>
</div>
</articlegraph_MET>
<?php
        }
        break;
    case "none": ?>
    
<p><main class="grid3"><articlegraph3 class="alert-row-narrow" style="background-color:white; font-size:10px;"><img src="css/svg/icon-warning-noalert-white.svg" style="width:75px; height:75px;"><ul><li><?php
        echo "NO WEATHER ALERTS in force for this location at the present time."
?></li></br><li><?php echo "The weather alerts used by this website are provided by AerisWeather using data supplied to them by meteoalarm.org and metoffice.gov.uk. An explanation of the severity of the alerts can be found in the Glossary below.";
?></li></ul></articlegraph3>
    <main class="grid1"><articlegraph class="alert-row-narrow" style="background-color:teal; font-size:12px;color:white;height:20px"><?php
        echo "<b>Glossary</b>";
?></articlegraph>

    
      
    <main class="grid3"><articlegraph3 class="alert-row-narrow" style="background-color:yellow; font-size:10px;"><img src="css/svg/icon-warning-generic-yellow.svg" style="width:75px; height:75px;"><ul><li><?php
        echo "YELLOW ALERT. Yellow warnings can be issued for a range of weather situations."
?></li></br><li><?php echo "It is important to read the content of yellow warnings to determine which weather situation is being covered by the warning.";
?></li></ul></articlegraph3>
    
    <main class="grid3"><articlegraph3 class="alert-row-narrow" style="background-color:orange; font-size:10px;"><img src="css/svg/icon-warning-generic-orange.svg" style="width:75px; height:75px;"><ul><li><?php
        echo "AMBER ALERT. There is an increased likelihood of impacts from severe weather, which could potentially disrupt your plans."
?></li></br><li><?php echo "This means there is the possibility of travel delays, road and rail closures, power cuts and the potential risk to life and property.";
?></li></ul></articlegraph3>
      
    <main class="grid3"><articlegraph3 class="alert-row-narrow" style="background-color:red; font-size:10px; color:white;"><img src="css/svg/icon-warning-generic-red.svg" style="width:75px; height:75px;"><ul><li><?php
        echo "RED ALERT. Dangerous weather is expected and, if you have not done so already, you should take action now to keep yourself and others safe from the impact of the severe weather."
?></li></br><li><?php echo "It is very likely that there will be a risk to life, with substantial disruption to travel, energy supplies and possibly widespread damage to property and infrastructure.";
?></li></ul></articlegraph3>
<?php
        break;

    }
?>
<main class="grid_FT">
<articlegraph_FT style="height:15px">  
  <div class="lotemp">
   <?php echo $info ?> CSS/SVG/PHP scripts by claydonsweather.org.uk &copy; 2021-<?php echo date('Y'); ?>  -  <a href="https://www.aerisweather.com/support/docs/api/reference/endpoints/alerts/" title="AerisWeather" target="_blank">Data © <?php echo date('Y'); ?>AerisWeather</a></span>
  </div>   
    
     
  </articlegraph_FT>
  
</main>



</body>
</html>
