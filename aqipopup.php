<?php
include_once('w34CombinedData.php');error_reporting(0);
include_once('aq_outlook.php');
if($theme==="dark"){$text1="silver";}
else if($theme==="light"){$text1="black";}
$forecastime = filemtime ('jsondata/uk.txt');
?>
<link href="css/popup.<?php echo $theme; ?>.css?version=<?php echo filemtime('css/popup.' . $theme . '.css'); ?>" rel="stylesheet prefetch">
<link href="css/tokens.css?version=<?php echo filemtime('css/tokens.css'); ?>" rel="stylesheet">
<link href="css/aq_outlook.css?version=<?php echo filemtime('css/aq_outlook.css'); ?>" rel="stylesheet">

<body>
<div class="tabs-header">
  <button class="tablink" onclick="openTab('outlook-tab', this)" id="defaultOpen"><?php echo w34t('AqxTab', 'Outlook & Cause'); ?></button>
  <button class="tablink" onclick="openTab('historical-tab', this)"><?php echo w34t('TabWeeklyAq', 'Weekly Air Quality'); ?></button>
  <button class="tablink" onclick="openTab('yearly-tab', this)"><?php echo w34t('TabYearlyAq', 'Yearly Air Quality'); ?></button>
  <button class="tablink" onclick="openTab('help-tab', this)"><?php echo w34t('AqiMeasurementGuide', 'AQI Measurement Guide'); ?></button>
</div>

<!-- TAB 1: OUTLOOK & CAUSE (aq_outlook.php) -->
<?php
$aqx = aqx_load();
list($aqx_aqi, $aqx_dom) = aqx_now();
?>
<div id="outlook-tab" class="tabcontent active aqx aqx--<?php echo htmlspecialchars($theme); ?>">
<?php if (!$aqx): ?>
  <div class="aqx-box"><?php echo w34t('AqxNoData', 'No air-quality outlook yet: scripts/aq_outlook_update.py has not run.'); ?></div>
<?php else: $den = aqx_density($aqx); ?>
  <div class="aqx-row">
    <div class="aqx-box aqx-why"><h4><?php echo w34t('AqxWhyHead', 'Why the air is like this'); ?></h4><?php echo aqx_why($aqx_aqi, $aqx_dom, $aqx); ?></div>
    <?php if (isset($aqx['smoke'])): $sm = $aqx['smoke']; ?>
    <div class="aqx-box"><h4><?php echo w34t('AqxSmokeHead', 'Satellite smoke (NOAA HMS)'); ?></h4>
      <div class="aqx-big aqx-den--<?php echo $den ? strtolower($den) : 'none'; ?>"><?php echo $den ? w34t('AqxDen' . $den, $den) : w34t('AqxDenNone', 'None'); ?></div>
      <?php echo w34_date('j M', strtotime($sm['date'] . ' 12:00')); if (!empty($sm['start'])) echo ', ' . $sm['start'] . '&ndash;' . $sm['end'] . ' UTC'; if (!empty($sm['satellite'])) echo ' (' . htmlspecialchars($sm['satellite']) . ')'; ?>
      <?php if (isset($aqx['monitor']['bc'])): $m = $aqx['monitor']; ?>
      <br><?php echo w34t('AqxSoot', 'Soot (black carbon)'); ?>: <b><?php echo round($m['bc'], 2); ?> µg/m³</b><?php if (!empty($m['bc_ratio'])) echo ' &middot; ' . w34f('AqxTimesUsual', '%s× usual', round($m['bc_ratio'], 1)); ?>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if (isset($aqx['forecast'])): $fc = $aqx['forecast']; ?>
    <div class="aqx-box"><h4><?php echo w34t('AqxForecastHead', 'Official AQI forecast'); ?></h4>
      <table class="aqx-tbl">
        <?php foreach ($fc['days'] as $d): list($lvl, $short, $long) = aqx_cat($d['cat']); ?>
        <tr><td><?php echo aqx_day($d['date']); ?></td><td><span class="aqx-sw aqx-bg--<?php echo $lvl; ?>"></span><?php echo $long; if (!empty($d['action'])) echo ' <span class="aqx-action">' . w34t('AqxActionDay', 'Action Day') . '</span>'; ?></td><td><?php echo aqx_pol($d['pol']); ?></td></tr>
        <?php endforeach; ?>
      </table>
      <div class="aqx-src"><?php echo htmlspecialchars($fc['area'] . ' · ' . $fc['agency']); ?></div>
    </div>
    <?php endif; ?>
  </div>
  <?php if (isset($aqx['model'])): ?>
  <div id="aqx-chart"></div>
  <?php endif; ?>
  <div class="aqx-src"><?php echo w34t('AqxSources', 'Sources: NOAA Hazard Mapping System satellite smoke analysis · AirNow agency forecast and hourly monitor data · Open-Meteo air-quality model (CAMS)'); ?></div>
<?php endif; ?>
</div>
<?php if (isset($aqx['model'])): ?>
<script src="w34highcharts/scripts/highstock.js"></script>
<script>
(function () {
  var m = <?php echo json_encode($aqx['model']); ?>;
  var tab = document.getElementById('outlook-tab'), cs = getComputedStyle(tab), v = function (n) { return cs.getPropertyValue(n).trim(); };
  var col = { good: v('--aqx-good'), moderate: v('--aqx-moderate'), usg: v('--aqx-usg'), unhealthy: v('--aqx-unhealthy'), vu: v('--aqx-vu'), hazard: v('--aqx-hazard') };
  var ink = v('--aqx-ink'), grid = v('--aqx-grid');
  var lv = function (a) { return a > 300 ? col.hazard : a > 200 ? col.vu : a > 150 ? col.unhealthy : a > 100 ? col.usg : a > 50 ? col.moderate : col.good; };
  // model times are station-local wall clock; plotted as UTC so the axis shows them unchanged
  var utc = function (s) { return Date.UTC(+s.slice(0, 4), +s.slice(5, 7) - 1, +s.slice(8, 10), +s.slice(11, 13)); };
  var t = m.time.map(utc);
  var pts = function (k) { return t.map(function (x, i) { return [x, m[k][i]]; }).filter(function (p) { return p[1] !== null; }); };
  Highcharts.setOptions({ time: { useUTC: true }, lang: { shortWeekdays: <?php echo json_encode(array_map(function ($i) { return w34_date('D', strtotime('last Sunday +' . $i . ' days')); }, range(0, 6))); ?> } });
  Highcharts.chart('aqx-chart', {
    chart: { backgroundColor: 'transparent', style: { fontFamily: 'Arial, sans-serif' }, spacing: [6, 6, 4, 4] },
    title: { text: <?php echo json_encode(w34t('AqxChartTitle', 'Model AQI, 12 h back to 60 h ahead (Open-Meteo CAMS)')); ?>, style: { color: ink, fontSize: '12px' } },
    credits: { enabled: false }, exporting: { enabled: false },
    legend: { itemStyle: { color: ink, fontWeight: 'normal' }, itemHoverStyle: { color: ink } },
    xAxis: { type: 'datetime', lineColor: grid, tickColor: grid, labels: { style: { color: ink } },
      plotLines: [{ value: utc(<?php echo json_encode(date('Y-m-d\TH:00')); ?>), color: ink, width: 1, dashStyle: 'Dash', zIndex: 5,
        label: { text: <?php echo json_encode(w34t('AqxNow', 'now')); ?>, rotation: 0, y: 12, style: { color: ink, fontSize: '10px' } } }] },
    yAxis: { title: { text: null }, min: 0, softMax: 60, gridLineColor: grid, labels: { style: { color: ink } } },
    tooltip: { shared: true, xDateFormat: '%a %e %b %H:00' },
    plotOptions: { column: { borderWidth: 0, pointPadding: 0, groupPadding: 0 }, series: { animation: false, marker: { enabled: false } } },
    series: [
      { type: 'column', name: 'AQI', color: col.moderate, data: pts('aqi').map(function (p) { return { x: p[0], y: p[1], color: lv(p[1]) }; }) },
      { type: 'line', name: <?php echo json_encode(w34t('AqxSeriesPm', 'PM2.5 AQI')); ?>, color: v('--aqx-line'), lineWidth: 1.5, data: pts('pm25') },
      { type: 'line', name: <?php echo json_encode(w34t('AqxSeriesO3', 'Ozone AQI')); ?>, color: v('--aqx-o3'), lineWidth: 1.5, dashStyle: 'ShortDash', data: pts('o3') }
    ]
  });
})();
</script>
<?php endif; ?>

<!-- TAB 2: HISTORICAL TRENDS HIGHCHART (loaded when first opened) -->
<div id="historical-tab" class="tabcontent">
  <div class="chart-container">
    <iframe data-src="w34highcharts/<?php echo $theme; ?>-charts.html?chart='airqualityplot'&span='weekly'&temp='<?php echo $weather['temp_units'];?>'&pressure='<?php echo $weather['barometer_units'];?>'&wind='<?php echo $weather['wind_units'];?>'&rain='<?php echo $weather['rain_units'];?>'&height='410'" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>
  </div>
</div>

<!-- TAB 3: YEARLY HIGHCHART (loaded when first opened) -->
<div id="yearly-tab" class="tabcontent">
  <div class="chart-container">
    <iframe data-src="w34highcharts/<?php echo $theme; ?>-charts.html?chart='airqualityplot'&span='yearly'&temp='<?php echo $weather['temp_units'];?>'&pressure='<?php echo $weather['barometer_units'];?>'&wind='<?php echo $weather['wind_units'];?>'&rain='<?php echo $weather['rain_units'];?>'&height='410'" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>
  </div>
</div>
<style>#yearly-tab.tabcontent.active { display: flex; flex-direction: column; }</style>

<!-- TAB 4: MEASUREMENT GUIDE TABLES -->
<div id="help-tab" class="tabcontent">
  <div class="weather34darkbrowser" style="color:<?php echo $text1 ?>;" url="<?php echo htmlspecialchars(w34t('AqiEpaNote', 'The World AQI Indices are based on the US EPA (United States Environmental Protection Agency) Scale, latest 24 hour running mean for the current day.')); ?>"></div>  
  <br> 

  <table class="demo">
    <tbody>
      <tr>
        <th style="color:<?php echo $text1 ?>;" id="CELL1">WORLD (US EPA) AQI PM<sub>2.5</sub></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL2"><?php echo w34t('AqGood', 'Good'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL3"><?php echo w34t('AqModerate', 'Moderate'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL4"><?php echo w34t('AqUnhealthySensitive', 'Unhealthy for Sensitive Groups'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL5"><?php echo w34t('AqUnhealthy', 'Unhealthy'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL6"><?php echo w34t('VeryUnhealthy', 'Very Unhealthy'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL7"><?php echo w34t('AqHazardous', 'Hazardous'); ?></th>
      </tr>
      <tr>
        <td style="color:<?php echo $text1 ?>;" id="CELL1"><b><?php echo w34t('Range', 'Range'); ?><b></td>
        <td style="color:black;" id="CELL3">0-50</td>
        <td style="color:black;" id="CELL6">51-100</td>
        <td style="color:black;" id="CELL7">101-150</td>
        <td id="CELL9">151-200</td>
        <td id="CELL11">201-300</td>
        <td id="CELL10">300</td>
      </tr>
      <tr> 	 	 	 	 	 	 	 	 	
        <td style="color:<?php echo $text1 ?>;" id="CELL1"><b>µg/m³<b></td>
        <td style="color:black;" id="CELL3">0-12</td>
        <td style="color:black;" id="CELL6">12-35</td>
        <td style="color:black;" id="CELL7">35-55</td>
        <td id="CELL9">55-150</td>
        <td id="CELL11">150-250</td>
        <td id="CELL10">250</td>
      </tr>
    </tbody>
  </table>

  <div class="weather34darkbrowser" style="color:<?php echo $text1 ?>;" url="<?php echo htmlspecialchars(w34t('AqiDaqiNote', 'The Defra (UK Government Department for Environment and Rural Affairs) DAQI Indices are based on the daily mean concentration for historical data, latest 24 hour running mean for the current day.')); ?>"></div>  
  <br>

  <table class="demo">
    <thead>
      <tr>
        <th style="color:<?php echo $text1 ?>;" id="CELL1">UK DAQI</th>
        <th style="color:<?php echo $text1 ?>;" id="CELL2"><?php echo w34t('DaqiLow', 'Low'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL3"><?php echo w34t('DaqiLow', 'Low'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL4"><?php echo w34t('DaqiLow', 'Low'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL5"><?php echo w34t('AqModerate', 'Moderate'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL6"><?php echo w34t('AqModerate', 'Moderate'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL7"><?php echo w34t('AqModerate', 'Moderate'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL8"><?php echo w34t('DaqiHigh', 'High'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL9"><?php echo w34t('DaqiHigh', 'High'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL10"><?php echo w34t('DaqiHigh', 'High'); ?></th>
        <th style="color:<?php echo $text1 ?>;" id="CELL11"><?php echo w34t('DaqiVeryHigh', 'Very High'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="color:<?php echo $text1 ?>;" id="CELL1"><b><?php echo w34t('Band', 'Band'); ?><b></td>
        <td style="color:black;" id="CELL2">1</td>
        <td style="color:black;" id="CELL3">2</td>
        <td style="color:black;" id="CELL4">3</td>
        <td style="color:black;" id="CELL5">4</td>
        <td style="color:black;" id="CELL6">5</td>
        <td style="color:black;" id="CELL7">6</td>
        <td style="color:black;" id="CELL8">7</td>
        <td id="CELL9">8</td>
        <td id="CELL10">9</td>
        <td id="CELL11">10</td>
      </tr>
      <tr>
        <td style="color:<?php echo $text1 ?>;" id="CELL1"><b>PM<sub>2.5 </sub>µg/m³<b></td>
        <td style="color:black;" id="CELL2">0-11</td>
        <td style="color:black;" id="CELL3">12-23</td>
        <td style="color:black;" id="CELL4">24-35</td>
        <td style="color:black;" id="CELL5">36-41</td>
        <td style="color:black;" id="CELL6">42-47</td>
        <td style="color:black;" id="CELL7">48-53</td>
        <td style="color:black;" id="CELL8">54-58</td>
        <td id="CELL9">59-64</td>
        <td id="CELL10">65-70</td>
        <td id="CELL11"><?php echo w34f('OrMore', '%s or more', '71'); ?></td>
      </tr>
      <tr> 	 	 	 	 	 	 	 	 	
        <td style="color:<?php echo $text1 ?>;" id="CELL1"><b>PM<sub>10 </sub>µg/m³<b></td>
        <td style="color:black;" id="CELL2">0-16</td>
        <td style="color:black;" id="CELL3">17-33</td>
        <td style="color:black;" id="CELL4">34-50</td>
        <td style="color:black;" id="CELL5">51-58</td>
        <td style="color:black;" id="CELL6">59-66</td>
        <td style="color:black;" id="CELL7">67-75</td>
        <td style="color:black;" id="CELL8">76-83</td>
        <td id="CELL9">84-91</td>
        <td id="CELL10">92-100</td>
        <td id="CELL11"><?php echo w34f('OrMore', '%s or more', '101'); ?></td>
      </tr>      
    </tbody>
  </table>
</div>

<script>
function openTab(tabId, btn) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].classList.remove("active");
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].classList.remove("active");
  }
  document.getElementById(tabId).classList.add("active");
  var frame = document.getElementById(tabId).querySelector("iframe[data-src]");
  if (frame) { frame.src = frame.getAttribute("data-src"); frame.removeAttribute("data-src"); }
  btn.classList.add("active");
}
document.getElementById("defaultOpen").click();
</script>
</body>
