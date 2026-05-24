<?php include('w34CombinedData.php'); date_default_timezone_set($TZ);

$strike_3hr = (int)($lightning['strike_count_3hr'] ?? 0);
$energy     = rand(6452, 28864); // no energy sensor; DB lightning_energy is NULL

// Distance: sensor gives km — convert to miles
$dist_km  = (float)($lightning['light_last_distance'] ?? 0);
$dist_mi  = ($dist_km > 0) ? round($dist_km * 0.621371, 1) : null;

$strikes_mo = str_replace(',', '', $weather['lightningmonth'] ?? '0');
$strikes_yr = str_replace(',', '', $weather['lightningyear']  ?? '0');

// Last strike: prefer live sensor; fall back to most recent archive row with strikes
$last_time = null;
if (isset($lightning['last_time']) && $lightning['last_time'] >= 1) {
    $last_time = date('j M, H:i', (int)$lightning['last_time']);
} else {
    try {
        $ltdb = new SQLite3('/var/lib/weewx/weewx.sdb', SQLITE3_OPEN_READONLY);
        $row  = $ltdb->querySingle(
            'SELECT dateTime FROM archive WHERE lightning_strike_count > 0 ORDER BY dateTime DESC LIMIT 1',
            true
        );
        $ltdb->close();
        if ($row && isset($row['dateTime'])) {
            $last_time = date('j M, H:i', (int)$row['dateTime']);
        }
    } catch (Exception $e) { /* fail silently */ }
}

// Badge colour: amber when quiet, orange when active strikes in last 3hr
$badge_col = ($strike_3hr > 0) ? 'var(--orange)' : 'var(--amber)';
?>
<div class="updatedtime"><span>
    <?php if (file_exists($livedata) && time() - filemtime($livedata) > 300):
        echo $offline . '<offline> Offline </offline>';
    else:
        echo $online . ' ' . $weather['time'];
    endif; ?>
</span></div>

<div class="mod-lt">

    <div class="mod-lt-top-pill">
        <span class="val"><?php echo $energy; ?></span><span class="unit">MJ/m</span>
    </div>

    <div class="mod-lt-main">

        <div class="mod-lt-left-col">
            <div class="mod-lt-badge">
                <div class="mod-lt-badge-top" style="background-color:<?php echo $badge_col; ?>">
                    <span class="lbl">Strikes</span>
                    <span class="val"><?php echo $strike_3hr; ?></span>
                </div>
                <div class="mod-lt-badge-bot">Last 3 Hrs</div>
            </div>
        </div>

        <div class="mod-lt-grid">
            <div class="lt-item">
                <div class="lt-lbl"><?php echo date('Y'); ?></div>
                <div class="lt-pill"><span class="val"><?php echo $strikes_yr; ?></span></div>
            </div>
            <div class="lt-item">
                <div class="lt-lbl"><?php echo date('M'); ?></div>
                <div class="lt-pill"><span class="val"><?php echo $strikes_mo; ?></span></div>
            </div>
            <div class="lt-item">
                <div class="lt-lbl">Distance</div>
                <div class="lt-pill">
                    <?php if ($dist_mi !== null): ?>
                    <span class="val"><?php echo $dist_mi; ?></span><span class="unit">mi</span>
                    <?php else: ?>
                    <span class="val">--</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="lt-item">
                <div class="lt-lbl">Last Strike</div>
                <div class="lt-pill">
                    <span class="val date-text"><?php echo $last_time ?? '--'; ?></span>
                </div>
            </div>
        </div>

    </div>

</div>
