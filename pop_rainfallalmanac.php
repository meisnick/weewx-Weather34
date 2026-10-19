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

function w34_convert_rain($val, $unit) {
    if ($val === '' || $val === 'N/A') return '';
    $fval = floatval($val);
    if ($unit === 'mm') {
        return number_format($fval * 0.0393701, 2) . '<smalluvunit>in</smalluvunit>';
    }
    if ($unit === 'in') {
        return number_format($fval * 25.400013716, 2) . '<smalluvunit>mm</smalluvunit>';
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
  <meta charset="UTF-8">
  <title><?php echo w34t('LinkRainAlmanac', 'Rainfall Almanac and Charts'); ?></title>
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
blue{color:#01a4b4;font-weight:600;}
[data-theme="light"] blue{color:#007a8a;}
.badge-col{float:left;width:64px;}
.rainfalltoday1{font-family:weathertext2,Arial,sans-serif;width:64px;height:18px;border-radius:3px 3px 0 0;font-size:0.9rem;line-height:18px;padding-top:2px;color:#fff;background:rgba(68,166,181,1);display:block;text-align:center;box-sizing:content-box;white-space:nowrap;}
smalluvunit{font-size:0.55rem;font-family:Arial,Helvetica,sans-serif;margin-left:2px;}
.w34convertrain{font-size:0.65em;color:#fff;width:64px;text-align:center;line-height:14px;height:14px;white-space:nowrap;border-radius:0 0 3px 3px;background:rgba(56,56,60,1);display:block;}
[data-theme="light"] .w34convertrain{background:#555555;}
.lotemp{color:var(--pop-footer-ink);font-size:8.5px;text-align:center;margin-top:4px;}
.hitempy{margin-left:68px;color:var(--pop-meta-ink);line-height:13px;font-size:9px;text-align:right;min-height:34px;display:flex;flex-direction:column;justify-content:center;}
.actualt{border-radius:3px;background:var(--pop-pill-bg);padding:2px 6px;font-family:Arial,Helvetica,sans-serif;width:max-content;font-size:0.75rem;color:var(--pop-pill-ink);margin-bottom:6px;text-align:left;}
[data-theme="light"] .actualt{border:1px solid rgba(0,0,0,0.1);}
  [class^="rainfalltoday"]{text-shadow:var(--badge-text-shadow)}
  </style>
</head>
<body>
<main class="grid">
  <article>
    <div class="actualt"><?php echo w34f('AlmToday', '%s Today', w34t('Rainfall', 'Rainfall')); ?></div>
    <div class="badge-col">
      <div class="rainfalltoday1">
        <?php echo $weather["rain_today"]; ?><smalluvunit><?php echo $weather["rain_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_rain($weather["rain_today"], $weather["rain_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('LastHour', 'Last Hour'); ?><br><blue><?php echo $weather["rain_lasthour"] . ' ' . $weather["rain_units"]; ?></blue>
    </div>
  </article>

  <article>
    <div class="actualt"><?php echo w34f('AlmYesterday', '%s Yesterday', w34t('Rainfall', 'Rainfall')); ?></div>
    <div class="badge-col">
      <div class="rainfalltoday1">
        <?php echo $weather["rainydmax"]; ?><smalluvunit><?php echo $weather["rain_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_rain($weather["rainydmax"], $weather["rain_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('Last24Hours', 'Last 24 Hours'); ?><br><blue><?php echo $weather["rain_24hrs"] . ' ' . $weather["rain_units"]; ?></blue>
    </div>
  </article>

  <article>
    <div class="actualt"><?php echo w34t('Rainfall', 'Rainfall'); ?> <?php echo w34_date('M Y'); ?></div>
    <div class="badge-col">
      <div class="rainfalltoday1">
        <?php echo $weather["rain_month"]; ?><smalluvunit><?php echo $weather["rain_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_rain($weather["rain_month"], $weather["rain_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('LastRainfall', 'Last Rainfall'); ?><br>
      <blue><?php
        if (isset($meteobridgeapi[124]) && $meteobridgeapi[124] == '--') {
          echo w34t('Unknown', 'Unknown');
        } else if (isset($rainlasttime) && $rainlasttime == w34_date("jS M Y ")) {
          echo 'Today';
        } else if (isset($rainlasttime)) {
          echo $rainlasttime;
        } else {
          echo w34t('Unknown', 'Unknown');
        }
      ?></blue>
    </div>
  </article>

  <article>
    <div class="actualt"><?php echo w34t('Rainfall', 'Rainfall'); ?> <?php echo date("Y"); ?></div>
    <div class="badge-col">
      <div class="rainfalltoday1">
        <?php echo $weather["rain_year"]; ?><smalluvunit><?php echo $weather["rain_units"]; ?></smalluvunit>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_rain($weather["rain_year"], $weather["rain_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('Since', 'Since'); ?><br><blue><?php echo w34_datetext('Jan') . ' ' . date('Y'); ?></blue>
    </div>
  </article>

  <article>
    <div class="actualt"><?php echo w34f('AlmAllTime', '%s All-Time', w34t('Rainfall', 'Rainfall')); ?></div>
    <div class="badge-col">
      <div class="rainfalltoday1">
        <?php echo ($weather["rain_alltime"] === '' || $weather["rain_alltime"] === null) ? 'N/A' : ($weather["rain_alltime"] . '<smalluvunit>' . $weather["rain_units"] . '</smalluvunit>'); ?>
      </div>
      <div class="w34convertrain">
        <?php echo w34_convert_rain($weather["rain_alltime"], $weather["rain_units"]); ?>
      </div>
    </div>
    <div class="hitempy">
      <?php echo w34t('Since', 'Since'); ?><br><blue><?php echo $weather['rainStartTime']; ?></blue>
    </div>
  </article>
</main>

<main class="grid1">
  <articlegraph> 
    <iframe src="w34highcharts/<?php echo $theme1; ?>-charts.html?chart=rainsmallplot&span=yearly&temp=<?php echo $weather['temp_units']; ?>&pressure=<?php echo $weather['barometer_units']; ?>&wind=<?php echo $weather['wind_units']; ?>&rain=<?php echo $weather['rain_units']; ?>&height=380" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>
  </articlegraph> 
  <div class="lotemp">
    <a href="https://highcharts.com" title="https://highcharts.com" target="_blank" style="color:inherit;text-decoration:none;font-size:8px;"><?php echo w34t('ChartsByHighcharts', 'Charts rendered and compiled using Highcharts'); ?></a>
    &bull; <?php echo w34t('CreditAdapted', 'Adapted by Steepleian for the WeeWX Weather34 skin'); ?> &copy; 2015-<?php echo date('Y');?>
  </div>
</main>
</body>
</html>
