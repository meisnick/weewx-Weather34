<?php include_once('w34_i18n.php'); ?>
<?php
//###################################################################################################################
//	weewx-Weather34 fork maintained by meisnick, based on the WeeWX skin by Ian Millard (Steepleian)    #
//	                                                                                                				#
//  Contains original legacy code (by agreement) created and developed by Brian Underdown (https://weather34.com)   # 
//  for the (now superseeded) original Weather34 Template which is no longer maintained by its creator              #
//  © weather34.com original CSS/SVG/PHP 2015-2019                                                                  #
// 	                                                                                                				#
//  Contains original code by Ian Millard and collaborators															#
//  © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                                         #
// 	                                                                                                				#
// 	Issues for weewx-Weather34 template should be addressed to https://github.com/meisnick/weewx-Weather34/issues #                                                                                              #
// 	                                                                                                				#
//###################################################################################################################
include_once('w34CombinedData.php'); include('settings1.php');
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; $theme1 = $theme; }

function w34_temp_row($val, $unit, $is_max, $label_key, $label_default, $time) {
    $badge_class = $is_max ? 'temp-max' : 'temp-min';
    $meta_class = $is_max ? 'meta-max' : 'meta-min';
    $unit_str = ($unit === '%') ? '%' : ('°' . $unit);
    echo '<div class="temp-row">';
    echo '<div class="temp-badge ' . $badge_class . '">' . $val . '<smalluvunit>' . $unit_str . '</smalluvunit></div>';
    echo '<div class="temp-meta"><span class="' . $meta_class . '"><b>' . w34t($label_key, $label_default) . '</b></span><br>' . $time . '</div>';
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('LinkTempAlmanac', 'Temperature Almanac and Derived Charts'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if ($theme === "dark") {
    echo '<style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype");}
html,body{font-size:13px;font-family:"weathertext2",Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;background:rgba(30,33,36,1);color:#f5f7fc;margin:0;padding:4px;overflow:hidden;}
.grid{display:grid;grid-template-columns:repeat(5,1fr);grid-gap:6px;align-items:stretch;color:white;padding:4px 44px 4px 4px;box-sizing:border-box;}
.grid > article{border:1px solid #212428;box-shadow:2px 2px 6px 0px rgba(0,0,0,0.3);padding:6px;font-size:0.8em;border-radius:4px;background:rgba(40,45,52,0.5);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;box-sizing:border-box;}
.grid1{display:block;color:#f5f7fc;margin-top:5px;padding:0 4px;box-sizing:border-box;}
.grid1 > articlegraph{border:1px solid rgba(245,247,252,0.05);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.6);padding:4px;font-size:0.8em;border-radius:4px;background:rgba(40,45,52,0.4);height:270px;box-sizing:border-box;display:block;}
a{color:white;text-decoration:none;}
.actualt{border-radius:3px;background:#555555;padding:2px 6px;font-family:Arial,Helvetica,sans-serif;width:max-content;font-size:0.75rem;color:white;margin-bottom:6px;text-align:left;}
.temp-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;}
.temp-badge{width:56px;height:18px;line-height:18px;border-radius:3px;font-family:weathertext2,Arial,sans-serif;font-size:0.85rem;text-align:center;color:#fff;box-sizing:border-box;white-space:nowrap;flex-shrink:0;border-bottom:4px solid rgba(56,56,60,1);}
.temp-max{background:rgba(255,124,57,1);}
.temp-min{background:rgba(68,166,181,1);}
smalluvunit{font-size:0.55rem;font-family:Arial,Helvetica,sans-serif;margin-left:2px;}
.temp-meta{font-size:8.5px;line-height:11px;text-align:right;flex-grow:1;padding-left:4px;color:#aaa;}
.meta-max{color:#ff7c39;}
.meta-min{color:#00a4b4;}
.lotemp{color:#888;font-size:8.5px;text-align:center;margin-top:4px;}
</style>';
} elseif ($theme === "light") {
    echo '<style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype");}
html,body{font-size:13px;font-family:"weathertext2",Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;background-color:#ffffff;color:#212428;margin:0;padding:4px;overflow:hidden;}
.grid{display:grid;grid-template-columns:repeat(5,1fr);grid-gap:6px;align-items:stretch;color:#212428;padding:4px 44px 4px 4px;box-sizing:border-box;}
.grid > article{border:1px solid rgba(0,0,0,0.12);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.06);padding:6px;font-size:0.8em;border-radius:4px;background:#f8f9fa;color:#212428;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;box-sizing:border-box;}
.grid1{display:block;color:#212428;margin-top:5px;padding:0 4px;box-sizing:border-box;}
.grid1 > articlegraph{border:1px solid rgba(0,0,0,0.12);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.06);padding:4px;font-size:0.8em;border-radius:4px;background:#ffffff;height:270px;box-sizing:border-box;display:block;}
a{color:black;text-decoration:none;}
.actualt{border-radius:3px;background:#e2e5e9;padding:2px 6px;font-family:Arial,Helvetica,sans-serif;width:max-content;font-size:0.75rem;color:#212428;border:1px solid rgba(0,0,0,0.1);margin-bottom:6px;text-align:left;}
.temp-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;}
.temp-badge{width:56px;height:18px;line-height:18px;border-radius:3px;font-family:weathertext2,Arial,sans-serif;font-size:0.85rem;text-align:center;color:white;box-sizing:border-box;white-space:nowrap;flex-shrink:0;border-bottom:4px solid #555555;}
.temp-max{background:rgba(255,124,57,1);}
.temp-min{background:rgba(68,166,181,1);}
smalluvunit{font-size:0.55rem;font-family:Arial,Helvetica,sans-serif;margin-left:2px;}
.temp-meta{font-size:8.5px;line-height:11px;text-align:right;flex-grow:1;padding-left:4px;color:#555;}
.meta-max{color:#d85c18;}
.meta-min{color:#007a8a;}
.lotemp{color:#777;font-size:8.5px;text-align:center;margin-top:4px;}
</style>';
}
?>
</head>
<body>
<main class="grid">
  <article>
    <div class="actualt"><?php echo w34t('Today', 'Today'); ?></div>
    <?php
    w34_temp_row($weather["tempdmax"], $weather["temp_units"], true, 'AlmTempMax', 'Temp Max', $weather["tempdmaxtime"]);
    w34_temp_row($weather["tempdmin"], $weather["temp_units"], false, 'AlmTempMin', 'Temp Min', $weather["tempdmintime"]);
    w34_temp_row($weather["dewmax"], $weather["temp_units"], true, 'AlmDewMax', 'Dew Max', $weather["dewmaxtime"]);
    w34_temp_row($weather["dewmin"], $weather["temp_units"], false, 'AlmDewMin', 'Dew Min', $weather["dewmintime"]);
    w34_temp_row($weather["humidity_max"], '%', true, 'AlmHumMax', 'Hum Max', $weather["humidity_maxtime"]);
    w34_temp_row($weather["humidity_min"], '%', false, 'AlmHumMin', 'Hum Min', $weather["humidity_mintime"]);
    ?>
  </article>

  <article>
    <div class="actualt"><?php echo w34t('Yesterday', 'Yesterday'); ?></div>
    <?php
    w34_temp_row($weather["tempydmax"], $weather["temp_units"], true, 'AlmTempMax', 'Temp Max', $weather["tempydmaxtime"]);
    w34_temp_row($weather["tempydmin"], $weather["temp_units"], false, 'AlmTempMin', 'Temp Min', $weather["tempydmintime"]);
    w34_temp_row($weather["dewydmax"], $weather["temp_units"], true, 'AlmDewMax', 'Dew Max', $weather["dewydmaxtime"]);
    w34_temp_row($weather["dewydmin"], $weather["temp_units"], false, 'AlmDewMin', 'Dew Min', $weather["dewydmintime"]);
    w34_temp_row($weather["humidity_ydmax"], '%', true, 'AlmHumMax', 'Hum Max', $weather["humidity_ydmaxtime"]);
    w34_temp_row($weather["humidity_ydmin"], '%', false, 'AlmHumMin', 'Hum Min', $weather["humidity_ydmintime"]);
    ?>
  </article>

  <article>
    <div class="actualt"><?php echo w34_date('F Y'); ?></div>
    <?php
    w34_temp_row($weather["tempmmax"], $weather["temp_units"], true, 'AlmTempMax', 'Temp Max', $weather["tempmmaxtime"]);
    w34_temp_row($weather["tempmmin"], $weather["temp_units"], false, 'AlmTempMin', 'Temp Min', $weather["tempmmintime"]);
    w34_temp_row($weather["dewmmax"], $weather["temp_units"], true, 'AlmDewMax', 'Dew Max', $weather["dewmmaxtime"]);
    w34_temp_row($weather["dewmmin"], $weather["temp_units"], false, 'AlmDewMin', 'Dew Min', $weather["dewmmintime"]);
    w34_temp_row($weather["humidity_mmax"], '%', true, 'AlmHumMax', 'Hum Max', $weather["humidity_mmaxtime"]);
    w34_temp_row($weather["humidity_mmin"], '%', false, 'AlmHumMin', 'Hum Min', $weather["humidity_mmintime"]);
    ?>
  </article>

  <article>
    <div class="actualt"><?php echo date('Y'); ?></div>
    <?php
    w34_temp_row($weather["tempymax"], $weather["temp_units"], true, 'AlmTempMax', 'Temp Max', $weather["tempymaxtime"]);
    w34_temp_row($weather["tempymin"], $weather["temp_units"], false, 'AlmTempMin', 'Temp Min', $weather["tempymintime"]);
    w34_temp_row($weather["dewymax"], $weather["temp_units"], true, 'AlmDewMax', 'Dew Max', $weather["dewymaxtime"]);
    w34_temp_row($weather["dewymin"], $weather["temp_units"], false, 'AlmDewMin', 'Dew Min', $weather["dewymintime"]);
    w34_temp_row($weather["humidity_ymax"], '%', true, 'AlmHumMax', 'Hum Max', $weather["humidity_ymaxtime"]);
    w34_temp_row($weather["humidity_ymin"], '%', false, 'AlmHumMin', 'Hum Min', $weather["humidity_ymintime"]);
    ?>
  </article>

  <article>
    <div class="actualt"><?php echo w34t('AllTime', 'All-Time'); ?></div>
    <?php
    w34_temp_row($weather["tempamax"], $weather["temp_units"], true, 'AlmTempMax', 'Temp Max', $weather["tempamaxtime"]);
    w34_temp_row($weather["tempamin"], $weather["temp_units"], false, 'AlmTempMin', 'Temp Min', $weather["tempamintime"]);
    w34_temp_row($weather["dewamax"], $weather["temp_units"], true, 'AlmDewMax', 'Dew Max', $weather["dewamaxtime"]);
    w34_temp_row($weather["dewamin"], $weather["temp_units"], false, 'AlmDewMin', 'Dew Min', $weather["dewamintime"]);
    w34_temp_row($weather["humidity_amax"], '%', true, 'AlmHumMax', 'Hum Max', $weather["humidity_amaxtime"]);
    w34_temp_row($weather["humidity_amin"], '%', false, 'AlmHumMin', 'Hum Min', $weather["humidity_amintime"]);
    ?>
  </article>
</main>

<main class="grid1">
  <articlegraph> 
    <iframe src="w34highcharts/<?php echo $theme1; ?>-charts.html?chart=tempsmallplot&span=yearly&temp=<?php echo $weather['temp_units']; ?>&pressure=<?php echo $weather['barometer_units']; ?>&wind=<?php echo $weather['wind_units']; ?>&rain=<?php echo $weather['rain_units']; ?>&height=270" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>   
  </articlegraph> 
  <div class="lotemp">
    <a href="https://highcharts.com" title="https://highcharts.com" target="_blank" style="color:inherit;text-decoration:none;font-size:8px;"><?php echo w34t('ChartsByHighcharts', 'Charts rendered and compiled using Highcharts'); ?></a>
    &bull; <?php echo w34t('CreditAdapted', 'Adapted by Steepleian for the WeeWX Weather34 skin'); ?> &copy; 2015-<?php echo date('Y');?>
  </div>
</main>
</body>
</html>
