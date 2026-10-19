<?php include_once('w34_i18n.php'); ?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
//###################################################################################################################
//  weewx-Weather34 Template — originally by Ian Millard (Steepleian) and collaborators              			#
//  © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                          			#
//  Forecast data: Open-Meteo (open-meteo.com) — CC BY 4.0                                          			#
//  Issues: https://github.com/meisnick/weewx-Weather34/issues                                       			#
//###################################################################################################################

include_once ('settings.php');
include_once ('w34CombinedData.php');
date_default_timezone_set($TZ);
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; $theme1 = $theme; }
if ($theme === "light") {
    $background = "#ffffff";
    $text = "#212428";
    $tab_bg = "#e2e5e9";
    $tab_border = "rgba(0,0,0,0.12)";
    $tab_color = "#212428";
} else {
    $background = "rgba(30, 33, 36, 1)";
    $text = "#f5f7fc";
    $tab_bg = "#383c44";
    $tab_border = "rgba(255,255,255,0.12)";
    $tab_color = "#f5f7fc";
}
?>  
<style>
* { box-sizing: border-box; }
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background: <?php echo $background; ?>;
  color: <?php echo $text; ?>;
  font-family: Arial, sans-serif;
}
.tab-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  padding: 5px 50px 5px 6px;
  background: <?php echo $background; ?>;
}
.tablink {
  background-color: <?php echo $tab_bg; ?>;
  color: <?php echo $tab_color; ?>;
  border: 1px solid <?php echo $tab_border; ?>;
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
  background-color: <?php echo $background; ?>;
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
  <button class="tablink" onclick="openPage('Tab1', this)" id="defaultOpen"><?php echo w34t('Hourlyforecast', 'Hourly Forecast'); ?></button>
  <button class="tablink" onclick="openPage('Tab2', this)"><?php echo w34t('TabHourlyTable', 'Hourly Forecast Table'); ?></button>
  <button class="tablink" onclick="openPage('Tab3', this)"><?php echo w34t('TabDayNight', 'Day and Night Forecast'); ?></button>  
  <button class="tablink" onclick="openPage('Tab4', this)"><?php echo w34t('TabDayNightTable', 'Day and Night Forecast Table'); ?></button>  
</div>

<div id="Tab1" class="tabcontent">
  <iframe scrolling="auto" src="pop_forecast_hourly.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
</div>

<div id="Tab2" class="tabcontent">
  <iframe scrolling="auto" src="pop_forecast_hourly_table.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
</div>

<div id="Tab3" class="tabcontent">
  <iframe scrolling="auto" src="pop_forecast_daynight.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
</div>

<div id="Tab4" class="tabcontent">
  <iframe scrolling="auto" src="pop_forecast_daynight_table.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
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
