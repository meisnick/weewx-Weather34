<?php include_once('w34CombinedData.php');error_reporting(0);
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; }
$is_dark = ($theme !== 'light');
$bg = $is_dark ? 'rgba(30,33,36,1)' : '#ffffff';
?>
<!DOCTYPE html>
<html lang="<?php include_once __DIR__ . '/w34_i18n.php'; echo w34_html_lang(); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('DaylightMapTitle', 'Equirectangular Projection World Daylight Map'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      background-color: <?php echo $bg; ?>;
      overflow: hidden;
    }
    .roundcornerframe {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
      margin: 0;
      padding: 0;
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
  <div class="roundcornerframe">
    <iframe src="daylightmap/daylightmap.html?<?php echo http_build_query(array(
      't' => w34t('DaylightMapTitle', 'Equirectangular Projection World Daylight Map'),
      'u' => w34t('UsingLabel', 'Using'),
      'mo' => implode(',', array_map(function ($m) { return w34_datetext($m); }, array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec')))
    )); ?>" scrolling="no" frameborder="0"></iframe>
  </div>
</body>
</html>
