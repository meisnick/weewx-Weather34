<?php include_once('w34CombinedData.php'); error_reporting(0);
if (isset($_GET['theme'])) {
    $theme = ($_GET['theme'] === 'light') ? 'light' : 'dark';
}
?>
<!DOCTYPE html>
<html lang="<?php include_once __DIR__ . '/w34_i18n.php'; echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title>Webcam for <?php echo htmlspecialchars($stationlocation); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/tokens.css">
  <style>
    :root {
      --cam-border: var(--pop-cam-border);
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
      width: 100%;
      height: 100%;
      overflow: hidden;
      background-color: var(--pop-bg);
      color: var(--pop-ink);
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
      color: var(--pop-ink);
      font-size: 14px;
      font-family: Arial, Helvetica, sans-serif;
      text-align: center;
      position: absolute;
      left: 0;
      right: 44px;
      top: 6px;
      height: 20px;
    }
    .cam-container {
      position: relative;
      width: 100%;
      height: calc(100% - 32px);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      padding: 4px;
    }
    .webcamlarge {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      border-radius: 4px;
      border: 2px solid var(--pop-cam-border);
      display: block;
    }
    .streamframe {
      width: 100%;
      height: 100%;
      border: none;
      border-radius: 4px;
      display: block;
    }
  </style>
</head>
<body>
  <div class="weather34darkbrowser" url="Webcam for <?php echo htmlspecialchars($stationlocation); ?>"></div>
  <div class="cam-container">
<?php if (!empty($videoWeatherCamURL)): ?>
    <iframe src="<?php echo htmlspecialchars($videoWeatherCamURL); ?>"
            class="streamframe"
            allowfullscreen
            allow="autoplay"
            referrerpolicy="no-referrer"></iframe>
<?php else: ?>
    <img src="<?php echo htmlspecialchars($webcamurl); ?>?v=<?php echo time(); ?>" alt="weathercam" class="webcamlarge">
<?php endif; ?>
  </div>
</body>
</html>
