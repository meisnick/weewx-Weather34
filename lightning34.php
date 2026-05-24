<?php include('w34CombinedData.php'); date_default_timezone_set($TZ);

$strike_3hr = (int)($lightning['strike_count_3hr'] ?? 0);
$distance   = $lightning['light_last_distance'] ?? null;
$energy     = rand(6452, 28864); // no energy sensor; preserved from original
$strikes_mo = str_replace(',', '', $weather['lightningmonth'] ?? '0');
$strikes_yr = str_replace(',', '', $weather['lightningyear']  ?? '0');
$last_time  = (isset($lightning['last_time']) && $lightning['last_time'] >= 1)
              ? date('j M, H:i', $lightning['last_time']) : null;

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
            <div class="mod-lt-badge" style="background-color:<?php echo $badge_col; ?>">
                <div class="mod-lt-badge-top">
                    <span class="lbl">Strikes</span>
                    <span class="val"><?php echo $strike_3hr; ?></span>
                </div>
                <div class="mod-lt-badge-bot">Last 3 Hrs</div>
            </div>
            <div class="mod-lt-distance-pill">
                <?php if ($distance !== null): ?>
                <span class="val"><?php echo htmlspecialchars((string)$distance); ?></span><span class="unit">km</span>
                <?php else: ?>
                <span class="val">--</span>
                <?php endif; ?>
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
            <div class="lt-item lt-full-width">
                <div class="lt-lbl">Last Strike</div>
                <div class="lt-pill">
                    <span class="val date-text"><?php echo $last_time ?? '--'; ?></span>
                </div>
            </div>
        </div>

    </div>

</div>
