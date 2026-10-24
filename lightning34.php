<?php include_once('w34_i18n.php'); ?>
<?php include_once('w34CombinedData.php'); include('common.php'); date_default_timezone_set($TZ);

$strike_3hr = (int)($lightning['strike_count_3hr'] ?? 0);

// Distance in km (archivedata.php gives km), shown in miles when the dashboard shows mph
$lt_miles = (($windunit ?? 'mph') == 'mph');
$lt_unit  = $lt_miles ? 'mi' : 'km';
$lt_fmt   = function ($km) use ($lt_miles) { return round($lt_miles ? $km * 0.621371 : $km, 1); };
$dist_km  = (float)($lightning['light_last_distance'] ?? 0);
$dist_mi  = ($dist_km > 0) ? $lt_fmt($dist_km) : null;

// Strike-distance strip: strikes in the last 24 h by the WH57's distance steps (km), the latest strike's
// distance, and the last strike ever for the quiet caption. The archive's unit system says miles or km.
$lt_steps = array(1, 5, 6, 8, 10, 12, 14, 17, 20, 24, 27, 31, 34, 37, 40);
$lt_bins = array(); $lt_total = 0; $lt_latest = null; $lt_last = null;
try {
    $ltdb = new SQLite3('/var/lib/weewx/weewx.sdb', SQLITE3_OPEN_READONLY);
    $ltdb->busyTimeout(2000);
    $to_km = function ($d, $us) { return ($us == 1) ? $d * 1.609344 : (float)$d; };
    $q = $ltdb->query('SELECT lightning_distance AS d, SUM(lightning_strike_count) AS n, MIN(usUnits) AS us FROM archive '
                    . 'WHERE dateTime > ' . (time() - 86400) . ' AND lightning_strike_count > 0 AND lightning_distance IS NOT NULL '
                    . 'GROUP BY lightning_distance');
    while ($r = $q->fetchArray(SQLITE3_ASSOC)) {
        $km = $to_km($r['d'], $r['us']);
        $step = $lt_steps[0];
        foreach ($lt_steps as $s) { if (abs($s - $km) < abs($step - $km)) $step = $s; }
        $lt_bins[$step] = ($lt_bins[$step] ?? 0) + (int)$r['n'];
        $lt_total += (int)$r['n'];
    }
    $r = $ltdb->querySingle('SELECT dateTime, lightning_distance AS d, usUnits AS us FROM archive WHERE lightning_strike_count > 0 '
                          . 'AND lightning_distance IS NOT NULL ORDER BY dateTime DESC LIMIT 1', true);
    if ($r) {
        $lt_last = array('ts' => (int)$r['dateTime'], 'km' => $to_km($r['d'], $r['us']));
        if ($lt_last['ts'] > time() - 86400) $lt_latest = $lt_last['km'];
    }
    $ltdb->close();
} catch (Exception $e) { /* no strip data: the strip shows empty */ }

$strikes_mo = str_replace(',', '', $weather['lightningmonth'] ?? '0');
$strikes_yr = str_replace(',', '', $weather['lightningyear']  ?? '0');

// Last strike: prefer live sensor; fall back to most recent archive row with strikes
$last_time = null;
if (isset($lightning['last_time']) && $lightning['last_time'] >= 1) {
    $last_time = w34_date('j M, H:i', (int)$lightning['last_time']);
} else {
    try {
        $ltdb = new SQLite3('/var/lib/weewx/weewx.sdb', SQLITE3_OPEN_READONLY);
        $row  = $ltdb->querySingle(
            'SELECT dateTime FROM archive WHERE lightning_strike_count > 0 ORDER BY dateTime DESC LIMIT 1',
            true
        );
        $ltdb->close();
        if ($row && isset($row['dateTime'])) {
            $last_time = w34_date('j M, H:i', (int)$row['dateTime']);
        }
    } catch (Exception $e) { /* fail silently */ }
}

// Badge colour band by strikes in the last 3 hours: quiet teal at 0, then yellow -> amber -> orange
// -> red -> purple as activity builds (same ramp as the UV badge)
if ($strike_3hr >= 200)     { $badge_cls = ' mod-lt-badge-top--200'; }
else if ($strike_3hr >= 50) { $badge_cls = ' mod-lt-badge-top--50'; }
else if ($strike_3hr >= 10) { $badge_cls = ' mod-lt-badge-top--10'; }
else if ($strike_3hr >= 1)  { $badge_cls = ' mod-lt-badge-top--1'; }
else                        { $badge_cls = ' mod-lt-badge-top--0'; }
?>
<div class="updatedtime"><span>
    <?php if (file_exists($livedata) && time() - filemtime($livedata) > 300):
        echo $offline . '<offline> Offline </offline>';
    else:
        echo $online . ' ' . $weather['time'];
    endif; ?>
</span></div>

<div class="mod-lt">

    <?php
    // strip geometry: 0-40 km (the sensor's range); each step's cell runs between the midpoints to its neighbours
    $lt_pct = function ($km) { return round(max(0, min(40, $km)) / 40 * 100, 2); };
    ?>
    <div class="mod-lt-strip">
        <div class="mod-lt-strip-cap"><?php
        if ($lt_total > 0) {
            echo w34t('LtByDistance', 'Strikes by distance, 24 h') . ' &middot; <b>' . $lt_total . '</b>';
            if ($lt_latest !== null) echo ' &middot; ' . w34t('LtLatest', 'latest') . ' ' . $lt_fmt($lt_latest) . ' ' . $lt_unit;
        } else {
            echo w34t('LtNone24h', 'No strikes in 24 h');
            if ($lt_last) echo ' &middot; ' . w34t('LtLatest', 'latest') . ' ' . w34_date('j M', $lt_last['ts']) . ', ' . $lt_fmt($lt_last['km']) . ' ' . $lt_unit;
        }
        ?></div>
        <div class="mod-lt-strip-track"><?php
        foreach ($lt_steps as $i => $s) {
            if (empty($lt_bins[$s])) continue;
            $n = $lt_bins[$s];
            $lo = $i == 0 ? 0 : ($lt_steps[$i - 1] + $s) / 2;
            $hi = $i == count($lt_steps) - 1 ? 40 : ($s + $lt_steps[$i + 1]) / 2;
            $c = $n >= 200 ? 200 : ($n >= 50 ? 50 : ($n >= 10 ? 10 : 1));
            echo '<span class="mod-lt-strip-cell mod-lt-strip-c' . $c . '" style="left:' . $lt_pct($lo) . '%;width:' . round($lt_pct($hi) - $lt_pct($lo), 2)
               . '%" title="' . $lt_fmt($s) . ' ' . $lt_unit . ': ' . $n . '"></span>';
        }
        ?></div>
        <?php if ($lt_latest !== null): ?><span class="mod-lt-strip-mark" style="left:<?php echo $lt_pct($lt_latest); ?>%"></span><?php endif; ?>
        <div class="mod-lt-strip-ticks"><?php
        $ticks = $lt_miles ? array(0, 5, 10, 15, 20, 25) : array(0, 10, 20, 30, 40);
        foreach ($ticks as $t) {
            $km = $lt_miles ? $t * 1.609344 : $t;
            echo '<span style="left:' . $lt_pct($km) . '%">' . $t . ($t == end($ticks) ? ' ' . $lt_unit : '') . '</span>';
        }
        ?></div>
    </div>

    <div class="mod-lt-main">

        <div class="mod-lt-left-col">
            <div class="mod-lt-badge">
                <div class="mod-lt-badge-top<?php echo $badge_cls; ?>">
                    <span class="lbl"><?php echo $lang['Strikes']; ?></span>
                    <span class="val"><?php echo $strike_3hr; ?></span>
                </div>
                <div class="mod-lt-badge-bot"><?php echo $lang['Last3Hrs']; ?></div>
            </div>
        </div>

        <div class="mod-lt-grid">
            <div class="mod-lt-item">
                <div class="mod-lt-lbl"><?php echo date('Y'); ?></div>
                <div class="mod-lt-pill"><span class="val"><?php echo $strikes_yr; ?></span></div>
            </div>
            <div class="mod-lt-item">
                <div class="mod-lt-lbl"><?php echo w34_date('M'); ?></div>
                <div class="mod-lt-pill"><span class="val"><?php echo $strikes_mo; ?></span></div>
            </div>
            <div class="mod-lt-item">
                <div class="mod-lt-lbl"><?php echo $lang['Distance']; ?></div>
                <div class="mod-lt-pill">
                    <?php if ($dist_mi !== null): ?>
                    <span class="val"><?php echo $dist_mi; ?></span><span class="unit"><?php echo $lt_unit; ?></span>
                    <?php else: ?>
                    <span class="val">--</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mod-lt-item">
                <div class="mod-lt-lbl"><?php echo $lang['LastStrike']; ?></div>
                <div class="mod-lt-pill">
                    <span class="val date-text"><?php echo $last_time ?? '--'; ?></span>
                </div>
            </div>
        </div>

    </div>

</div>
