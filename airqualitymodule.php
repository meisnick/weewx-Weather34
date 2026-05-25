<?php include('shared.php'); include('common.php');

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

// Colour for a given AQI sub-index value
function aq_color($val) {
    if ($val === null) return 'var(--bg0)';
    if ($val > 150) return 'var(--red)';
    if ($val > 100) return 'var(--orange)';
    if ($val > 50)  return 'var(--amber)';
    return 'var(--green)';
}

// Main badge colour and icon based on composite AQI
if ($aqi > 300)      { $badge_col = '#8b3a8b'; $icon = 'css/aqi/hazair.svg'; }
elseif ($aqi > 200)  { $badge_col = '#a475cb'; $icon = 'css/aqi/vhair.svg';  }
elseif ($aqi > 150)  { $badge_col = 'var(--red)';    $icon = 'css/aqi/uhair.svg';   }
elseif ($aqi > 100)  { $badge_col = 'var(--orange)'; $icon = 'css/aqi/uhfsair.svg'; }
elseif ($aqi > 50)   { $badge_col = 'var(--amber)';  $icon = 'css/aqi/modair.svg';  }
else                 { $badge_col = 'var(--green)';  $icon = 'css/aqi/goodair.svg'; }

// AQI description text
if ($aqi > 300)     $aqi_desc = $lang['Hazordous']    ?? 'Hazardous';
elseif ($aqi > 200) $aqi_desc = $lang['VeryUnhealthy'] ?? 'Very Unhealthy';
elseif ($aqi > 150) $aqi_desc = $lang['Unhealthy']     ?? 'Unhealthy';
elseif ($aqi > 100) $aqi_desc = $lang['UnhealthyFS']   ?? 'Unhealthy for Some';
elseif ($aqi > 50)  $aqi_desc = $lang['Moderate']      ?? 'Moderate';
else                $aqi_desc = $lang['Good']           ?? 'Good';

// Dominant pollutant display label and its colour
$dom_labels = ['pm25' => 'PM2.5', 'pm10' => 'PM10', 'o3' => 'O3', 'no2' => 'NO₂'];
$dom_label  = $dom_labels[$aq_dominant] ?? strtoupper($aq_dominant);
$dom_vals   = ['pm25' => $aq_pm25, 'pm10' => $aq_pm10, 'o3' => $aq_o3, 'no2' => $aq_no2];
$dom_color  = aq_color($dom_vals[$aq_dominant] ?? null);

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
        <div class="mod-aq-badge-top" style="background-color:<?php echo $badge_col; ?>">
            <img class="mod-aq-icon" src="<?php echo $icon; ?>?ver=1.4" alt="AQI">
            <span class="val"><?php echo $aqi; ?></span>
        </div>
        <div class="mod-aq-badge-bot"><?php echo $aqi_desc; ?></div>
    </div>

    <!-- Right: Refined Pollutants Grid -->
    <div class="mod-aq-details">
        <div class="mod-aq-header">
            <span class="mod-aq-heading">Pollutants</span>
            <?php if ($dom_label): ?>
            <span class="mod-aq-dominant-tag" style="color:<?php echo $dom_color; ?>"><?php echo htmlspecialchars($dom_label); ?> &#x29C9;</span>
            <?php endif; ?>
        </div>
        <div class="mod-aq-grid">
            <?php foreach ($polls as $label => $p):
                $col  = aq_color($p['val']);
                $disp = ($p['val'] !== null) ? $p['val'] : '--';
                $dom  = ($p['slug'] === $aq_dominant) ? ' dominant' : '';
            ?>
            <div class="mod-aq-pill<?php echo $dom; ?>" style="background-color:<?php echo $col; ?>">
                <span class="val"><?php echo $disp; ?></span>
                <span class="lbl"><?php echo $label; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>
