<?php
include('settings.php');
$t       = htmlspecialchars($theme          ?? 'dark');
$station = htmlspecialchars($stationlocation ?? 'Weather Station');
?><!DOCTYPE html>
<html lang="en" class="theme-<?= $t ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $station ?></title>
  <link rel="stylesheet" href="css/w34-module.css">
  <link rel="stylesheet" href="css/theme.<?= $t ?>.css">
  <link rel="stylesheet" href="css/layout.css">
</head>
<body>

<div class="w34-dashboard">

  <header class="w34-topbar">
    <div data-module="clock"       data-refresh="1"></div>
    <div data-module="lightning"   data-refresh="60"></div>
    <div data-module="rain-totals" data-refresh="60"></div>
    <div data-module="temp-year"   data-refresh="600"></div>
    <div data-module="wind-year"   data-refresh="600"></div>
  </header>

  <main class="w34-grid">
    <div data-module="temperature" data-refresh="60"></div>
    <div data-module="wind"        data-refresh="4"></div>
    <div data-module="rainfall"    data-refresh="60"></div>
    <div data-module="barometer"   data-refresh="300"></div>
    <div data-module="solar"       data-refresh="300"></div>
    <div data-module="indoor"      data-refresh="300"></div>
  </main>

</div>

<script src="js/w34-core.js"></script>
<script src="modules/clock/clock.js"></script>
<script src="modules/lightning/lightning.js"></script>
<script src="modules/rain-totals/rain-totals.js"></script>
<script src="modules/temp-year/temp-year.js"></script>
<script src="modules/wind-year/wind-year.js"></script>
<script src="modules/temperature/temperature.js"></script>
<script src="modules/wind/wind.js"></script>
<script src="modules/rainfall/rainfall.js"></script>
<script src="modules/barometer/barometer.js"></script>
<script src="modules/solar/solar.js"></script>
<script src="modules/indoor/indoor.js"></script>
<script>W34.init();</script>

</body>
</html>
