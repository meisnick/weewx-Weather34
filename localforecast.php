<?php
// localforecast.php — Hyperlocal Short-Term Forecast Card (LLM Analogue Matching)
include('shared_core.php');
include_once('settings1.php');
include('common.php');

$file_path = 'jsondata/local_forecast.json';
$data_ok   = false;
$forecast  = $lang['NoHyperlocalNowcast'];
$wind_out  = $lang['Calm'];
$rain_pct  = 0;
$rain_class = 'ym-quiet';
$rain_status = $lang['Dry'];

if (file_exists($file_path)) {
    $raw  = @file_get_contents($file_path);
    $data = @json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && !empty($data['forecast'])) {
        $forecast = $data['forecast'];
        
        // Wind Label
        $wind_label = $data['wind_label'] ?? '';
        if ($wind_label) {
            $wind_out = $wind_label . ' ' . $lang['Wind'];
        }
        
        // Time Window Calculation
        $gen_ts = $data['generated_ts'] ?? filemtime($file_path);
        $time_window = date('ga', $gen_ts) . '-' . date('ga', $gen_ts + (6 * 3600));
        
        // Rain classification
        $rain_pct = intval($data['rain_pct_6h'] ?? 0);
        if ($rain_pct === 0) {
            $rain_class = 'ym-quiet';
            $rain_status = $lang['Dry'];
        } elseif ($rain_pct < 30) {
            $rain_class = 'ym-minor';
            $rain_status = $lang['Slight'];
        } elseif ($rain_pct < 60) {
            $rain_class = 'ym-active';
            $rain_status = $lang['Chance'];
        } else {
            $rain_class = 'ym-storm';
            $rain_status = $lang['Likely'];
        }
        
        // Dynamic Winter Precipitation Check
        $is_freezing = false;
        $raw_temp = $data['current_temp_f'] ?? null;
        if ($raw_temp !== null && $raw_temp <= 32.0) {
            $is_freezing = true;
        }
        $badge_label = $is_freezing ? $lang['Snow6h'] : $lang['Rain6h'];
        
        $data_ok = true;
    }
}
?>
<!-- Status Time Indicator -->
<div class="updatedtime"><span>
    <?php if ($data_ok):
        echo $online . ' ' . date($timeFormat, filemtime($file_path));
    else:
        echo $offline . ' <offline>' . $lang['Offline'] . '</offline>';
    endif; ?>
</div>

<!-- Nowcast Content -->
<div class="mod-localforecast">
    <div class="mod-lf-header">
        <div class="mod-lf-header-left">
            <span class="mod-lf-term"><?php echo $lang['Nowcast6h']; ?><?php if(isset($time_window)) echo ' <span style="font-size: 0.75em; opacity: 0.8; font-weight: normal;">(' . $time_window . ')</span>'; ?></span>
            <span class="mod-lf-wind"><?php echo htmlspecialchars($wind_out); ?></span>
        </div>
        
        <div class="mod-lf-header-right">
            <!-- Style B (High-Contrast Solid-Bottom Badge, scaled for grid size) -->
            <div class="ym-badge <?php echo $rain_class; ?>">
                <div class="ym-badge-top">
                    <span class="ym-lbl"><?php echo $badge_label; ?></span>
                    <span class="ym-val"><?php echo $rain_pct; ?>%</span>
                </div>
                <div class="ym-badge-bot">
                    <span><?php echo $rain_status; ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Narrative Text Card -->
    <div class="nowcast-text-card">
        <span class="nowcast-quote">“</span>
        <span class="nowcast-sentence"><?php echo htmlspecialchars($forecast); ?></span>
        <span class="nowcast-quote">”</span>
    </div>
</div>
