<?php include_once('w34_i18n.php'); ?>
<?php include_once('shared_core.php'); include('common.php'); include_once('aq_outlook.php');

// --- Load AQ data safely ---
$aq_file = 'jsondata/aq.txt';
$aqi = 0;
$aq_pm25 = null; $aq_pm10 = null; $aq_o3 = null; $aq_no2 = null;
$aq_dominant = '';
$data_ok = false;

if (file_exists($aq_file) && (time() - filemtime($aq_file) < 7200)) {
    $raw = file_get_contents($aq_file);
    $parsed = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($parsed['data'])) {
        $d = $parsed['data'];
        $aqi         = (int)($d['aqi'] ?? 0);
        $iaqi        = $d['iaqi'] ?? [];
        $aq_pm25     = isset($iaqi['pm25']['v']) ? round((float)$iaqi['pm25']['v']) : null;
        $aq_o3       = isset($iaqi['o3']['v'])   ? round((float)$iaqi['o3']['v'])   : null;
        $aq_dominant = strtolower($d['dominentpol'] ?? '');
        $data_ok     = true;
    }
}

// PM10 and NO2: the nearest monitor measures neither (WAQI's values for them aren't its readings), so they come
// from Open-Meteo's air-quality model at the station (jsondata/om.txt, the 'om' web service), as US AQI sub-indices
// like WAQI's, and the pills say they are estimates.
$om_file = 'jsondata/om.txt';
if (file_exists($om_file) && (time() - filemtime($om_file) < 10800)) {
    $om = json_decode(file_get_contents($om_file), true);
    $omc = $om['current'] ?? [];
    $aq_pm10 = isset($omc['us_aqi_pm10']) ? round((float)$omc['us_aqi_pm10']) : null;
    $aq_no2  = isset($omc['us_aqi_nitrogen_dioxide']) ? round((float)$omc['us_aqi_nitrogen_dioxide']) : null;
}

// Modifier class suffix for a given AQI sub-index value
function aq_level($val) {
    if ($val === null) return 'none';
    if ($val > 150) return 'unhealthy';
    if ($val > 100) return 'usg';
    if ($val > 50)  return 'moderate';
    return 'good';
}

// Main badge modifier class and icon based on composite AQI
if ($aqi > 300)      { $badge_lvl = 'hazard';    $icon = 'css/aqi/hazair.svg'; }
elseif ($aqi > 200)  { $badge_lvl = 'vu';        $icon = 'css/aqi/vhair.svg';  }
elseif ($aqi > 150)  { $badge_lvl = 'unhealthy'; $icon = 'css/aqi/uhair.svg';   }
elseif ($aqi > 100)  { $badge_lvl = 'usg';       $icon = 'css/aqi/uhfsair.svg'; }
elseif ($aqi > 50)   { $badge_lvl = 'moderate';  $icon = 'css/aqi/modair.svg';  }
else                 { $badge_lvl = 'good';      $icon = 'css/aqi/goodair.svg'; }

// AQI description text (shortened to fit the 85px badge)
if ($aqi > 300)      $aqi_desc = w34t('AqHazardous', 'Hazardous');
elseif ($aqi > 200)  $aqi_desc = w34t('AqVeryUnhealthy', 'V. Unhealthy');
elseif ($aqi > 150)  $aqi_desc = w34t('AqUnhealthy', 'Unhealthy');
elseif ($aqi > 100)  $aqi_desc = w34t('AqUnhealthyFS', 'Unhealthy FS');
elseif ($aqi > 50)   $aqi_desc = w34t('AqModerate', 'Moderate');
else                 $aqi_desc = w34t('AqGood', 'Good');

// Dominant pollutant display label and its level class
$dom_labels = ['pm25' => 'PM2.5', 'pm10' => 'PM10', 'o3' => 'O3', 'no2' => 'NO₂'];
$dom_label  = $dom_labels[$aq_dominant] ?? strtoupper($aq_dominant);
$dom_vals   = ['pm25' => $aq_pm25, 'pm10' => $aq_pm10, 'o3' => $aq_o3, 'no2' => $aq_no2];
$dom_lvl    = aq_level($dom_vals[$aq_dominant] ?? null);

$polls = [
    'PM2.5' => ['val' => $aq_pm25, 'slug' => 'pm25'],
    'PM10'  => ['val' => $aq_pm10, 'slug' => 'pm10', 'model' => true],
    'O3'    => ['val' => $aq_o3,   'slug' => 'o3'],
    'NO₂'   => ['val' => $aq_no2,  'slug' => 'no2', 'model' => true],
];
// The nearest monitor measures ozone only in its ozone season (Harrington Beach, WI: about mid/late March to
// 13 October, from AirNow's 2025-26 hourly files). An empty O3 pill outside it says so instead of looking broken.
// Set the window for your own monitor (MM-DD, inclusive; it wraps the new year).
$aq_o3_off = ['10-14', '03-31'];
$aq_md = date('m-d');
$aq_o3_offseason = ($aq_o3 === null) && ($aq_md >= $aq_o3_off[0] || $aq_md <= $aq_o3_off[1]);
// Cause line and agency forecast row (jsondata/aq_outlook.json); without it the card keeps its plain layout.
$aqx = aqx_load();
if ($aqx) list($aqx_kind, $aqx_title, $aqx_detail) = aqx_cause($data_ok ? $aqi : 0, $aq_dominant, $aqx);
$aq_model_tip = htmlspecialchars(w34t('AqModelTip', 'Estimate from the Open-Meteo air-quality model (CAMS): the nearest monitor does not measure this'));
?>
<div class="updatedtime"><span>
    <?php if ($data_ok):
        echo $online . ' ' . date($timeFormat, filemtime($aq_file));
    else:
        echo $offline . ' <offline>Offline</offline>';
    endif; ?>
</span></div>

<div class="mod-aq<?php if ($aqx) echo ' mod-aq--outlook'; ?>">

    <?php if ($aqx): ?>
    <!-- Cause: satellite smoke over the station and the dominant pollutant -->
    <div class="mod-aq-cause" title="<?php echo htmlspecialchars(strip_tags(aqx_why($data_ok ? $aqi : 0, $aq_dominant, $aqx))); ?>">
        <span class="ic mod-aq-cause--<?php echo $aqx_kind; ?>"><?php if ($aqx_kind === 'o3'): ?><svg viewBox="0 0 12 12" width="10" height="10"><circle cx="6" cy="6" r="2.6" fill="currentColor"/><g stroke="currentColor" stroke-width="1.2" stroke-linecap="round"><path d="M6 .8v1.6M6 9.6v1.6M.8 6h1.6M9.6 6h1.6M2.3 2.3l1.1 1.1M8.6 8.6l1.1 1.1M2.3 9.7l1.1-1.1M8.6 3.4l1.1-1.1"/></g></svg><?php else: ?><svg viewBox="0 0 12 10" width="11" height="9"><path d="M1 3c2-2 3 2 5 0s3 2 5 0M1 6c2-2 3 2 5 0s3 2 5 0M1 9c2-2 3 2 5 0s3 2 5 0" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg><?php endif; ?></span>
        <b class="mod-aq-cause--<?php echo $aqx_kind; ?>"><?php echo htmlspecialchars($aqx_title); ?></b><?php if ($aqx_detail !== ''): ?><span class="sep">&middot;</span><span><?php echo htmlspecialchars($aqx_detail); ?></span><?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Left: Main AQI Badge -->
    <div class="mod-aq-badge">
        <div class="mod-aq-badge-top mod-aq-bg--<?php echo $badge_lvl; ?>">
            <span class="lbl">AQI</span>
            <span class="val"><?php echo $aqi; ?></span>
        </div>
        <div class="mod-aq-badge-bot"><?php echo $aqi_desc; ?></div>
    </div>

    <!-- Right: Refined Pollutants Grid -->
    <div class="mod-aq-details">
        <div class="mod-aq-header">
            <span class="mod-aq-heading"><?php echo w34t('Pollutants', 'Pollutants');?></span>
            <?php if ($dom_label): ?>
            <span class="mod-aq-dominant-tag mod-aq-fg--<?php echo $dom_lvl; ?>"><?php echo htmlspecialchars($dom_label); ?> &#x29C9;</span>
            <?php endif; ?>
        </div>
        <div class="mod-aq-grid">
            <?php foreach ($polls as $label => $p):
                $lvl  = aq_level($p['val']);
                $disp = ($p['val'] !== null) ? $p['val'] : '--';
                $dom  = ($p['slug'] === $aq_dominant) ? ' dominant' : '';
            ?>
            <?php $off = ($p['slug'] === 'o3' && $aq_o3_offseason); ?>
            <div class="mod-aq-pill mod-aq-bg--<?php echo $lvl . $dom; ?>"<?php if (!empty($p['model'])) echo ' title="' . $aq_model_tip . '"'; elseif ($off) echo ' title="' . htmlspecialchars(w34t('AqOffSeasonTip', 'The nearest monitor measures ozone only in the ozone season (spring to mid-October)')) . '"'; ?>>
                <span class="val"><?php echo $disp; ?></span>
                <span class="lbl"><?php echo $label; if (!empty($p['model'])) echo ' <span class="est">' . w34t('AqEstShort', 'est') . '</span>'; elseif ($off) echo ' <span class="est">' . w34t('AqOffSeason', 'off season') . '</span>'; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (isset($aqx['forecast'])): ?>
    <!-- Agency AQI forecast (AirNow), today + 3 days -->
    <div class="mod-aq-fc">
        <div class="mod-aq-fc-lbl"><?php echo w34t('AqxForecast', 'AQI forecast'); ?></div>
        <?php foreach ($aqx['forecast']['days'] as $i => $d): list($lvl, $short, $long) = aqx_cat($d['cat']); ?>
        <div class="mod-aq-day mod-aq-bg--<?php echo $lvl . ($i === 0 ? ' today' : '') . (!empty($d['action']) ? ' action' : ''); ?>" title="<?php echo htmlspecialchars(aqx_day($d['date']) . ': ' . $long . ' (' . aqx_pol($d['pol']) . ')'); ?>">
            <span class="mod-aq-day-d"><?php echo aqx_day($d['date']); ?></span><span class="mod-aq-day-c"><?php echo $short; ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
