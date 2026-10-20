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
//  weewx-Weather34 Template — originally by Ian Millard (Steepleian) and collaborators              			#
//  © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                          			#
//  Forecast data: Open-Meteo (open-meteo.com) — CC BY 4.0                                          			#
//  Issues: https://github.com/meisnick/weewx-Weather34/issues                                       			#
//###################################################################################################################
?>
<style>
* { box-sizing: border-box; }
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background: var(--pop-bg);
  color: var(--pop-ink);
  font-family: Arial, sans-serif;
}
body {
  display: flex;
  flex-direction: column;
}
.tab-bar {
  flex: none;
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  padding: 5px 50px 5px 6px;
  background: var(--pop-bg);
}
.tablink {
  background-color: var(--pop-tab-bg);
  color: var(--pop-tab-color);
  border: 1px solid var(--pop-tab-border);
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
  background-color: var(--pop-tab-hover-bg);
  color: var(--pop-tab-hover-color);
}
.tablink.active {
  background-color: #c2663a !important;
  color: #fff !important;
  border-color: #c2663a !important;
}
.tabcontent {
  display: none;
  width: 100%;
  flex: 1 1 auto;
  min-height: 0;
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
  <button class="tablink" onclick="openPage('Tab1', this)" id="defaultOpen"><?php echo w34t('Hourlyforecast', 'Hourly Forecast'); ?></button>
  <button class="tablink" onclick="openPage('Tab2', this)"><?php echo w34t('TabHourlyTable', 'Hourly Forecast Table'); ?></button>
  <button class="tablink" onclick="openPage('Tab3', this)"><?php echo w34t('TabDayNight', 'Day and Night Forecast'); ?></button>  
  <button class="tablink" onclick="openPage('Tab4', this)"><?php echo w34t('TabDayNightTable', 'Day and Night Forecast Table'); ?></button>  
</div>

<div id="Tab1" class="tabcontent">
  <iframe scrolling="auto" data-src="pop_forecast_hourly.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
</div>

<div id="Tab2" class="tabcontent">
  <iframe scrolling="auto" data-src="pop_forecast_hourly_table.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
</div>

<div id="Tab3" class="tabcontent">
  <iframe scrolling="auto" data-src="pop_forecast_daynight.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
</div>

<div id="Tab4" class="tabcontent">
  <iframe scrolling="auto" data-src="pop_forecast_daynight_table.php?theme=<?php echo $theme; ?>" frameborder="0"></iframe>
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
  var frame = document.getElementById(pageName).querySelector("iframe[data-src]");
  if (frame) { frame.src = frame.getAttribute("data-src"); frame.removeAttribute("data-src"); }
  elmnt.classList.add("active");
}
document.getElementById("defaultOpen").click();
</script>
</body>
</html>
