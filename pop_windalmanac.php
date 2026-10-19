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

if ($weather["wind_units"] == 'kts') { $weather["wind_units"] = "kn"; }

function w34_wind_badge_class($speed, $unit) {
    if ($unit === 'mph') {
        if ($speed >= 37.2) return 'windtoday60';
        if ($speed >= 24.85) return 'windtoday40';
        if ($speed >= 18.64) return 'windtoday30';
        if ($speed >= 6.2) return 'windtoday10';
        return 'windtoday';
    }
    if ($unit === 'kn') {
        if ($speed >= 32.40) return 'windtoday60';
        if ($speed >= 21.60) return 'windtoday40';
        if ($speed >= 16.20) return 'windtoday30';
        if ($speed >= 5.40) return 'windtoday10';
        return 'windtoday';
    }
    if ($unit === 'm/s') {
        if ($speed >= 16.66) return 'windtoday60';
        if ($speed >= 11.11) return 'windtoday40';
        if ($speed >= 8.33) return 'windtoday30';
        if ($speed >= 2.77) return 'windtoday10';
        return 'windtoday';
    }
    // km/h default
    if ($speed >= 60) return 'windtoday60';
    if ($speed >= 40) return 'windtoday40';
    if ($speed >= 30) return 'windtoday30';
    if ($speed >= 10) return 'windtoday10';
    return 'windtoday';
}

function w34_convert_wind($speed, $unit) {
    if ($unit === 'km/h') return number_format($speed * 0.621371, 1) . ' <smalluvunit>mph</smalluvunit>';
    if ($unit === 'mph') return number_format($speed * 1.60934, 1) . ' <smalluvunit>km/h</smalluvunit>';
    if ($unit === 'm/s') return number_format($speed * 3.5999988862317131577, 1) . ' <smalluvunit>km/h</smalluvunit>';
    if ($unit === 'kn') return number_format($speed * 1.8519994254280931489, 1) . ' <smalluvunit>km/h</smalluvunit>';
    return '';
}
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('LinkWindAlmanac', 'Wind Almanac and Charts'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/tokens.css">
  <style>
@font-face{font-family:weathertext2;src:url(css/fonts/verbatim-regular.woff) format("woff"),url(fonts/verbatim-regular.woff2) format("woff2"),url(fonts/verbatim-regular.ttf) format("truetype");}
html,body{font-size:13px;font-family:"weathertext2",Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;background:var(--pop-bg);color:var(--pop-ink);margin:0;padding:4px;overflow:hidden;}
.grid{display:grid;grid-template-columns:repeat(5,1fr);grid-gap:6px;align-items:stretch;color:var(--pop-ink);padding:4px 44px 4px 4px;box-sizing:border-box;}
.grid > article{border:1px solid var(--pop-article-border);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.3);padding:6px;font-size:0.8em;border-radius:4px;background:var(--pop-article-bg);-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;box-sizing:border-box;min-height:80px;}
[data-theme="light"] .grid > article{box-shadow:2px 2px 6px 0px rgba(0,0,0,0.06);}
.grid1{display:block;color:var(--pop-ink);margin-top:5px;padding:0 4px;box-sizing:border-box;}
.grid1 > articlegraph{border:1px solid var(--pop-graph-border);box-shadow:2px 2px 6px 0px rgba(0,0,0,0.6);padding:4px;font-size:0.8em;border-radius:4px;background:var(--pop-graph-bg);height:380px;box-sizing:border-box;display:block;}
[data-theme="light"] .grid1 > articlegraph{box-shadow:2px 2px 6px 0px rgba(0,0,0,0.06);}
a{color:var(--pop-link);text-decoration:none;}
[data-theme="light"] a{color:#007a8c;}
blue{color:#01a4b4;font-weight:600;}
[data-theme="light"] blue{color:#007a8a;}
orange{color:#009bb4;}
orange1{color:rgba(255,131,47,1);}
green{color:#aaa;}
[data-theme="light"] green{color:#555;}
red{color:#f37867;}
red6{color:#d65b4a;}
value{color:#fff;}
yellow{color:#cc0;}
purple{color:#916392;}
.badge-col{float:left;width:60px;}
.windtoday,.windtoday10,.windtoday30,.windtoday40,.windtoday60{font-family:weathertext2,Arial,Helvetica,sans-serif;width:60px;height:18px;border-radius:3px 3px 0 0;font-size:0.9rem;line-height:18px;padding-top:2px;color:#fff;display:block;text-align:center;box-sizing:content-box;white-space:nowrap;}
.windtoday{background:#9aba2f;}
.windtoday10{background:rgba(230,161,65,1);}
.windtoday30{background:rgba(255,124,57,0.8);}
.windtoday40{background:rgba(255,124,57,0.8);}
.windtoday60{background:rgba(211,93,78,1);}
smalluvunit{font-size:0.55rem;font-family:Arial,Helvetica,sans-serif;margin-left:2px;}
.w34convertrain{font-size:0.65em;color:#fff;width:60px;text-align:center;line-height:14px;height:14px;white-space:nowrap;border-radius:0 0 3px 3px;background:rgba(56,56,60,1);display:block;}
[data-theme="light"] .w34convertrain{background:#555555;}
.lotemp{color:var(--pop-footer-ink);font-size:8.5px;text-align:center;margin-top:4px;}
.hitempy{margin-left:64px;color:var(--pop-meta-ink);line-height:13px;font-size:9px;text-align:right;min-height:34px;display:flex;flex-direction:column;justify-content:center;}
.actualt{border-radius:3px;background:var(--pop-pill-bg);padding:2px 6px;font-family:Arial,Helvetica,sans-serif;width:max-content;font-size:0.75rem;color:var(--pop-pill-ink);margin-bottom:6px;text-align:left;}
[data-theme="light"] .actualt{padding:1px 6px;border:1px solid rgba(0,0,0,0.1);}
  [class^="windtoday"]{text-shadow:var(--badge-text-shadow)}
  </style>
</head>
<body>
<main class="grid">
  <article>
    <div class="actualt"><?php echo w34f('AlmToday', '%s Today', w34t('MaxLabel', 'Max')); ?></div>
    <div class="badge-col">
      <div class="<?php echo w34_wind_badge_class($weather['winddmax'], $weather['wind_units']); ?>">
        <?php echo $weather["winddmax"]; ?><smalluvunit><?php echo $weather["wind_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_wind($weather["winddmax"], $weather["wind_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('MaxRecorded', 'Max Recorded'); ?><br><blue><?php echo $weather["winddmaxtime"]; ?></blue>
    </div>
  </article>

  <article>
    <div class="actualt"><?php echo w34f('AlmYesterday', '%s Yesterday', w34t('MaxLabel', 'Max')); ?></div>
    <div class="badge-col">
      <div class="<?php echo w34_wind_badge_class($weather['windydmax'], $weather['wind_units']); ?>">
        <?php echo $weather["windydmax"]; ?><smalluvunit><?php echo $weather["wind_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_wind($weather["windydmax"], $weather["wind_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('MaxRecorded', 'Max Recorded'); ?><br><blue><?php echo $weather["windydmaxtime"]; ?></blue>
    </div>
  </article>

  <article>
    <div class="actualt"><?php echo w34t('MaxLabel', 'Max'); ?> <?php echo w34_date('F Y'); ?></div>
    <div class="badge-col">
      <div class="<?php echo w34_wind_badge_class($weather['windmmax'], $weather['wind_units']); ?>">
        <?php echo $weather["windmmax"]; ?><smalluvunit><?php echo $weather["wind_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_wind($weather["windmmax"], $weather["wind_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('MaxRecorded', 'Max Recorded'); ?><br><blue><?php echo $weather["windmmaxtime"]; ?></blue>
    </div>
  </article>

  <article>
    <div class="actualt"><?php echo w34t('MaxLabel', 'Max'); ?> <?php echo date('Y'); ?></div>
    <div class="badge-col">
      <div class="<?php echo w34_wind_badge_class($weather['windymax'], $weather['wind_units']); ?>">
        <?php echo $weather["windymax"]; ?><smalluvunit><?php echo $weather["wind_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_wind($weather["windymax"], $weather["wind_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('MaxRecorded', 'Max Recorded'); ?><br><blue><?php echo $weather["windymaxtime"]; ?></blue>
    </div>
  </article>

  <article>
    <div class="actualt"><?php echo w34f('AlmAllTime', '%s All-Time', w34t('MaxLabel', 'Max')); ?></div>
    <div class="badge-col">
      <div class="<?php echo w34_wind_badge_class($weather['windamax'], $weather['wind_units']); ?>">
        <?php echo $weather["windamax"]; ?><smalluvunit><?php echo $weather["wind_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_wind($weather["windamax"], $weather["wind_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('MaxRecorded', 'Max Recorded'); ?><br><blue><?php echo $weather["windamaxtime"]; ?></blue>
    </div>
  </article>
</main>

<main class="grid1">
  <articlegraph> 
    <iframe src="w34highcharts/<?php echo $theme1?>-charts.html?chart='windsmallplot'&span='yearly'&temp=<?php echo $weather['temp_units'];?>&pressure=<?php echo $weather['barometer_units'];?>&wind=<?php echo $weather['wind_units'];?>&rain=<?php echo $weather['rain_units'];?>&height=380" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>
  </articlegraph> 
  <div class="lotemp">
    <a href="https://highcharts.com" title="https://highcharts.com" target="_blank" style="color:inherit;text-decoration:none;font-size:8px;"><?php echo w34t('ChartsByHighcharts', 'Charts rendered and compiled using Highcharts'); ?></a>
    &bull; <?php echo w34t('CreditAdapted', 'Adapted by Steepleian for the WeeWX Weather34 skin'); ?> &copy; 2015-<?php echo date('Y');?>
  </div>
</main>
</body>
</html>
