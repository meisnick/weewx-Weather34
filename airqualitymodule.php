<?php include_once('w34_i18n.php'); ?>
<?php include_once('shared_core.php'); include('common.php');

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
        $aq_pm10     = isset($iaqi['pm10']['v']) ? round((float)$iaqi['pm10']['v']) : null;
        $aq_o3       = isset($iaqi['o3']['v'])   ? round((float)$iaqi['o3']['v'])   : null;
        $aq_no2      = isset($iaqi['no2']['v'])  ? round((float)$iaqi['no2']['v'])  : null;
        $aq_dominant = strtolower($d['dominentpol'] ?? '');
        $data_ok     = true;
    }
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
    'PM10'  => ['val' => $aq_pm10, 'slug' => 'pm10'],
    'O3'    => ['val' => $aq_o3,   'slug' => 'o3'],
    'NO₂'   => ['val' => $aq_no2,  'slug' => 'no2'],
];
?>
<div class="updatedtime"><span>
    <?php if ($data_ok):
        echo $online . ' ' . date($timeFormat, filemtime($aq_file));
    else:
        echo $offline . ' <offline>Offline</offline>';
    endif; ?>
</span></div>

<div class="mod-aq">

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
            <div class="mod-aq-pill mod-aq-bg--<?php echo $lvl . $dom; ?>">
                <span class="val"><?php echo $disp; ?></span>
                <span class="lbl"><?php echo $label; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>
