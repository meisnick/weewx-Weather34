<?php
include_once('w34_i18n.php');
include_once('settings.php');
include_once('w34CombinedData.php');
date_default_timezone_set($TZ);
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; $theme1 = $theme; }
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/tokens.css">
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
?>
<style>
* { box-sizing: border-box; }
:root, [data-theme="dark"] {
  --tab-bg: #383c44;
  --tab-border: rgba(255, 255, 255, 0.12);
  --tab-color: #f5f7fc;
}
[data-theme="light"] {
  --tab-bg: #e2e5e9;
  --tab-border: rgba(0, 0, 0, 0.12);
  --tab-color: #212428;
}
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background: var(--pop-bg);
  color: var(--pop-ink);
  font-family: Arial, sans-serif;
}
.tab-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  padding: 5px 50px 5px 6px;
  background: var(--pop-bg);
}
.tablink {
  background-color: var(--tab-bg);
  color: var(--tab-color);
  border: 1px solid var(--tab-border);
  border-radius: 4px;
  outline: none;
  cursor: pointer;
  padding: 5px 8px;
  font-size: 10.5px;
  font-family: Arial, sans-serif;
  transition: background-color 0.2s, color 0.2s;
  white-space: nowrap;
}
.tablink:hover {
  background-color: #666;
  color: #fff;
}
.tablink.active {
  background-color: #c2663a !important;
  color: #fff !important;
  border-color: #c2663a !important;
}
.tabcontent {
  display: none;
  width: 100%;
  height: calc(100vh - 36px);
  background-color: var(--pop-bg);
}
.tabcontent iframe {
  width: 100%;
  height: 100%;
  display: block;
  border: 0;
}
</style>
</head>
<body>
<div class="tab-bar">
  <button class="tablink" onclick="openPage('Tab1', this)" id="defaultOpen"><?php echo w34t('TabTempAlmanac', 'Temp | Dew | Hum Almanac'); ?></button>  
  <button class="tablink" onclick="openPage('Tab2', this)"><?php echo w34t('TabYearlyTemp', 'Yearly Temperature'); ?></button>
  <button class="tablink" onclick="openPage('Tab3', this)"><?php echo w34t('TabYearlyFeels', 'Yearly Feels Like'); ?></button>
  <button class="tablink" onclick="openPage('Tab4', this)"><?php echo w34t('TabYearlyHum', 'Yearly Humidity'); ?></button>  
  <button class="tablink" onclick="openPage('Tab5', this)"><?php echo w34t('TabWeeklyTemp', 'Weekly Temperature'); ?></button>  
  <button class="tablink" onclick="openPage('Tab6', this)"><?php echo w34t('TabWeeklyFeels', 'Weekly Feels Like'); ?></button>  
  <button class="tablink" onclick="openPage('Tab7', this)"><?php echo w34t('TabWeeklyHum', 'Weekly Humidity'); ?></button>
</div>
  
<div id="Tab1" class="tabcontent">
  <iframe scrolling="no" src="pop_tempalmanac.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
</div>
  
<div id="Tab2" class="tabcontent">
  <iframe scrolling="no" src="<?php echo $chartsource;?>/<?php echo $theme1;?>-charts.html?chart=temperatureplot&span=yearly&temp=<?php echo $weather['temp_units'];?>&pressure=<?php echo $weather['barometer_units'];?>&wind=<?php echo $weather['wind_units'];?>&rain=<?php echo $weather['rain_units'];?>" frameborder="0"></iframe>
</div>

<div id="Tab3" class="tabcontent">
  <iframe scrolling="no" src="<?php echo $chartsource;?>/<?php echo $theme1;?>-charts.html?chart=tempderivedplot&span=yearly&temp=<?php echo $weather['temp_units'];?>&pressure=<?php echo $weather['barometer_units'];?>&wind=<?php echo $weather['wind_units'];?>&rain=<?php echo $weather['rain_units'];?>" frameborder="0"></iframe>
</div>
  
<div id="Tab4" class="tabcontent">
  <iframe scrolling="no" src="<?php echo $chartsource;?>/<?php echo $theme1;?>-charts.html?chart=humidityplot&span=yearly&temp=<?php echo $weather['temp_units'];?>&pressure=<?php echo $weather['barometer_units'];?>&wind=<?php echo $weather['wind_units'];?>&rain=<?php echo $weather['rain_units'];?>" frameborder="0"></iframe>
</div>
  
<div id="Tab5" class="tabcontent">
  <iframe scrolling="no" src="<?php echo $chartsource;?>/<?php echo $theme1;?>-charts.html?chart=tempallplot&span=weekly&temp=<?php echo $weather['temp_units'];?>&pressure=<?php echo $weather['barometer_units'];?>&wind=<?php echo $weather['wind_units'];?>&rain=<?php echo $weather['rain_units'];?>" frameborder="0"></iframe>
</div>
  
<div id="Tab6" class="tabcontent">
  <iframe scrolling="no" src="<?php echo $chartsource;?>/<?php echo $theme1;?>-charts.html?chart=tempderivedplot&span=weekly&temp=<?php echo $weather['temp_units'];?>&pressure=<?php echo $weather['barometer_units'];?>&wind=<?php echo $weather['wind_units'];?>&rain=<?php echo $weather['rain_units'];?>" frameborder="0"></iframe>
</div>
  
<div id="Tab7" class="tabcontent">
  <iframe scrolling="no" src="<?php echo $chartsource;?>/<?php echo $theme1;?>-charts.html?chart=humidityplot&span=weekly&temp=<?php echo $weather['temp_units'];?>&pressure=<?php echo $weather['barometer_units'];?>&wind=<?php echo $weather['wind_units'];?>&rain=<?php echo $weather['rain_units'];?>" frameborder="0"></iframe>
</div>

<script>
function openPage(pageName, elmnt) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].classList.remove("active");
  }
  document.getElementById(pageName).style.display = "block";
  elmnt.classList.add("active");
}
document.getElementById("defaultOpen").click();
</script>
</body>
</html>
