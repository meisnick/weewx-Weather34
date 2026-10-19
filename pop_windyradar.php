<?php include_once('w34CombinedData.php'); error_reporting(0);
if (isset($_GET['theme'])) {
    $theme = ($_GET['theme'] === 'light') ? 'light' : 'dark';
}
$is_dark = ($theme !== 'light');
$bg = $is_dark ? 'rgba(30,33,36,1)' : '#ffffff';
$txt_col = $is_dark ? '#f5f7fc' : '#333333';
?>
<!DOCTYPE html>
<html lang="<?php include_once __DIR__ . '/w34_i18n.php'; echo w34_html_lang(); ?>">
<head>
  <meta charset="UTF-8">
  <title>Weather Radar for <?php echo htmlspecialchars($stationlocation); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
      width: 100%;
      height: 100%;
      overflow: hidden;
      background-color: <?php echo $bg; ?>;
      font-family: Arial, Helvetica, sans-serif;
    }
    .weather34darkbrowser {
      position: relative;
      width: 100%;
      height: 32px;
      padding-top: 6px;
      box-sizing: border-box;
    }
    .weather34darkbrowser[url]:after {
      content: attr(url);
      color: <?php echo $txt_col; ?>;
      font-size: 14px;
      font-family: Arial, Helvetica, sans-serif;
      text-align: center;
      position: absolute;
      left: 0;
      right: 44px;
      top: 6px;
      height: 20px;
    }
    .roundcornerframe {
      position: relative;
      width: 100%;
      height: calc(100% - 32px);
      overflow: hidden;
    }
    iframe {
      width: 100%;
      height: 100%;
      border: 0;
      display: block;
    }
  </style>
</head>
<body>
  <div class="weather34darkbrowser" url="Weather Radar for <?php echo htmlspecialchars($stationlocation); ?>"></div>
  <div class="roundcornerframe">
    <iframe scrolling="no" src="https://embed.windy.com/embed2.html?lat=<?php echo urlencode($lat); ?>&lon=<?php echo urlencode($lon); ?>&zoom=8&level=surface&overlay=radar&menu=&message=true&marker=&calendar=&pressure=&type=map&location=coordinates&detail=&detailLat=<?php echo urlencode($lat); ?>&detailLon=<?php echo urlencode($lon); ?>&metricWind=<?php echo urlencode($weather['wind_units']); ?>&metricTemp=%C2%B0<?php echo urlencode($weather['temp_units']); ?>&metricRain=<?php echo urlencode($weather['rain_units']); ?>&radarRange=-1" frameborder="0"></iframe>
  </div>
</body>
</html>
