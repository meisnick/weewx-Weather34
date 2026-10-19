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
include_once("w34CombinedData.php");
include("settings1.php");
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; $theme1 = $theme; }

function w34_solar_class($val) {
    if ($val >= 1000) return 'solar-extreme';
    if ($val >= 500) return 'solar-veryhigh';
    if ($val >= 300) return 'solar-high';
    return 'solar-normal';
}

function w34_solar_row($val, $time) {
    $cls = w34_solar_class(floatval($val));
    echo '<div class="solar-row">';
    echo '<div class="solar-badge ' . $cls . '">' . $val . '<smalluvunit> W/m<sup>2</sup></smalluvunit></div>';
    echo '<div class="solar-meta"><span class="meta-label"><b>' . w34t('MaxRecorded', 'Max Recorded') . '</b></span><br><blue>' . $time . '</blue></div>';
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('LinkSolarAlmanac', 'Solar Almanac'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if ($theme === "dark") {
    echo '<style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype");}
html,body{font-size:13px;font-family:"weathertext2",Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;background:rgba(30,33,36,1);color:#f5f7fc;margin:0;padding:4px;overflow:hidden;}
.grid{display:grid;grid-template-columns:repeat(5,1fr);grid-gap:6px;align-items:stretch;color:white;padding:4px 44px 4px 4px;box-sizing:border-box;}
.grid > article{border:1px solid #212428;box-shadow:2px 2px 6px 0px rgba(0,0,0,0.3);padding:6px;font-size:0.8em;border-radius:4px;background:rgba(40,45,52,0.5);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;box-sizing:border-box;min-height:80px;}
.grid1{display:block;color:#f5f7fc;margin-top:5px;padding:0 4px;box-sizing:border-box;}
.grid1 > articlegraph{border:1px solid rgba(245,247,252,0.05);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.6);padding:4px;font-size:0.8em;border-radius:4px;background:rgba(40,45,52,0.4);height:380px;box-sizing:border-box;display:block;}
a{color:white;text-decoration:none;}
blue{color:#01a4b4;font-weight:600;}
.actualt{border-radius:3px;background:#555555;padding:2px 6px;font-family:Arial,Helvetica,sans-serif;width:max-content;font-size:0.75rem;color:white;margin-bottom:6px;text-align:left;}
.solar-row{display:flex;align-items:center;justify-content:space-between;margin-top:4px;}
.solar-badge{width:70px;height:24px;line-height:24px;border-radius:3px;font-family:weathertext2,Arial,sans-serif;font-size:0.88rem;text-align:center;color:#fff;box-sizing:border-box;white-space:nowrap;flex-shrink:0;border-bottom:4px solid rgba(56,56,60,1);}
.solar-normal{background:#9aba2f;}
.solar-high{background:#ff7c39;}
.solar-veryhigh{background:#efa80f;}
.solar-extreme{background:#d05f2d;}
smalluvunit{font-size:0.55rem;font-family:Arial,Helvetica,sans-serif;margin-left:2px;}
.solar-meta{font-size:8.5px;line-height:12px;text-align:right;flex-grow:1;padding-left:4px;color:#aaa;}
.meta-label{color:#aaa;}
.lotemp{color:#888;font-size:8.5px;text-align:center;margin-top:4px;}
</style>';
} elseif ($theme === "light") {
    echo '<style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype");}
html,body{font-size:13px;font-family:"weathertext2",Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;background-color:#ffffff;color:#212428;margin:0;padding:4px;overflow:hidden;}
.grid{display:grid;grid-template-columns:repeat(5,1fr);grid-gap:6px;align-items:stretch;color:#212428;padding:4px 44px 4px 4px;box-sizing:border-box;}
.grid > article{border:1px solid rgba(0,0,0,0.12);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.06);padding:6px;font-size:0.8em;border-radius:4px;background:#f8f9fa;color:#212428;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;box-sizing:border-box;min-height:80px;}
.grid1{display:block;color:#212428;margin-top:5px;padding:0 4px;box-sizing:border-box;}
.grid1 > articlegraph{border:1px solid rgba(0,0,0,0.12);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.06);padding:4px;font-size:0.8em;border-radius:4px;background:#ffffff;height:380px;box-sizing:border-box;display:block;}
a{color:black;text-decoration:none;}
blue{color:#007a8a;font-weight:600;}
.actualt{border-radius:3px;background:#e2e5e9;padding:2px 6px;font-family:Arial,Helvetica,sans-serif;width:max-content;font-size:0.75rem;color:#212428;border:1px solid rgba(0,0,0,0.1);margin-bottom:6px;text-align:left;}
.solar-row{display:flex;align-items:center;justify-content:space-between;margin-top:4px;}
.solar-badge{width:70px;height:24px;line-height:24px;border-radius:3px;font-family:weathertext2,Arial,sans-serif;font-size:0.88rem;text-align:center;color:white;box-sizing:border-box;white-space:nowrap;flex-shrink:0;border-bottom:4px solid #555555;}
.solar-normal{background:#9aba2f;}
.solar-high{background:#ff7c39;}
.solar-veryhigh{background:#efa80f;}
.solar-extreme{background:#d05f2d;}
smalluvunit{font-size:0.55rem;font-family:Arial,Helvetica,sans-serif;margin-left:2px;}
.solar-meta{font-size:8.5px;line-height:12px;text-align:right;flex-grow:1;padding-left:4px;color:#555;}
.meta-label{color:#555;}
.lotemp{color:#777;font-size:8.5px;text-align:center;margin-top:4px;}
</style>';
}
?>
</head>
<body>
<main class="grid">
  <article>
    <div class="actualt"><?php echo w34t('Today', 'Today'); ?></div>
    <?php w34_solar_row($weather["solardmax"], $weather["solardmaxtime"]); ?>
  </article>

  <article>
    <div class="actualt"><?php echo w34t('Yesterday', 'Yesterday'); ?></div>
    <?php w34_solar_row($weather["solarydmax"], $weather["solarydmaxtime"]); ?>
  </article>

  <article>
    <div class="actualt"><?php echo w34_date("F Y"); ?></div>
    <?php w34_solar_row($weather["solarmmax"], $weather["solarmmaxtime"]); ?>
  </article>

  <article>
    <div class="actualt"><?php echo date("Y"); ?></div>
    <?php w34_solar_row($weather["solarymax"], $weather["solarymaxtime"]); ?>
  </article>

  <article>
    <div class="actualt"><?php echo w34t('AllTime', 'All-Time'); ?></div>
    <?php w34_solar_row($weather["solaramax"], $weather["solaramaxtime"]); ?>
  </article>
</main>

<main class="grid1">
  <articlegraph> 
    <iframe src="w34highcharts/<?php echo $theme1; ?>-charts.html?chart=radsmallplot&span=yearly&temp=<?php echo $weather['temp_units']; ?>&pressure=<?php echo $weather['barometer_units']; ?>&wind=<?php echo $weather['wind_units']; ?>&rain=<?php echo $weather['rain_units']; ?>&height=380" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>
  </articlegraph> 
  <div class="lotemp">
    <a href="https://highcharts.com" title="https://highcharts.com" target="_blank" style="color:inherit;text-decoration:none;font-size:8px;"><?php echo w34t('ChartsUsingHighcharts', 'Charts rendered using Highcharts.'); ?></a>
    &bull; <?php echo w34t('CreditAdapted', 'Adapted by Steepleian for the WeeWX Weather34 skin'); ?> &copy; 2015-<?php echo date('Y');?>
  </div>
</main>
</body>
</html>
