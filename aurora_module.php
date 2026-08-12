<?php
include('shared.php');
include_once('settings1.php');
include('common.php');

// --- NOAA current R/S/G scales ---
$rsg = [
    'R' => ['scale' => 0, 'text' => 'none'],
    'S' => ['scale' => 0, 'text' => 'none'],
    'G' => ['scale' => 0, 'text' => 'none'],
];
if (file_exists('jsondata/noaa_scales.json')) {
    $ns = json_decode(file_get_contents('jsondata/noaa_scales.json'), true);
    if ($ns && isset($ns['0'])) {
        foreach (['R', 'S', 'G'] as $k) {
            $rsg[$k]['scale'] = max(0, (int)($ns['0'][$k]['Scale'] ?? 0));
            $rsg[$k]['text']  = strtoupper($ns['0'][$k]['Text'] ?? 'none');
        }
    }
}

// --- 3-day Kp forecast from ki.txt ---
$forecast_days = [];
if (file_exists('jsondata/ki.txt')) {
    $entries = json_decode(file_get_contents('jsondata/ki.txt'), true);
    $by_date = [];
    foreach ((array)$entries as $e) {
        $date = substr($e['time_tag'], 0, 10);
        $slot = intdiv((int)substr($e['time_tag'], 11, 2), 3);
        if (!isset($by_date[$date])) {
            $by_date[$date] = array_fill(0, 8, null);
        }
        $by_date[$date][$slot] = (float)$e['kp'];
    }
    $local_today_ts = strtotime(date('Y-m-d'));
    $local_date   = date('Y-m-d');
    foreach ([0, 1, 2] as $offset) {
        $target_ts = strtotime("+$offset day", $local_today_ts);
        $d = date('Y-m-d', $target_ts);
        if (!isset($by_date[$d])) continue;
        $slots  = $by_date[$d];
        $valid  = array_filter($slots, fn($v) => $v !== null);
        $max_kp = $valid ? (float)max($valid) : 0.0;
        $label  = ($d === $local_date) ? 'Today' : date('j M', $target_ts);
        $forecast_days[] = [
            'label'  => $label,
            'max_kp' => $max_kp,
            'slots'  => $slots,
        ];
    }
}

// --- Real-time Aurora Probability from Ovation grid ---
$aurora_prob = null;
$prob_ok = false;
if (file_exists('jsondata/aurora_prob.json')) {
    $ap_json = json_decode(file_get_contents('jsondata/aurora_prob.json'), true);
    if ($ap_json && isset($ap_json['probability'])) {
        $aurora_prob = (int)$ap_json['probability'];
        $prob_ok = (time() - filemtime('jsondata/aurora_prob.json') < 1800); // 30 min freshness
    }
}

// --- Helpers ---
function aurora_prob_class(int $prob): string {
    if ($prob >= 50) return 'mod-aurora-prob-red';
    if ($prob >= 25) return 'mod-aurora-prob-orange';
    if ($prob >= 10) return 'mod-aurora-prob-amber';
    if ($prob > 0)   return 'mod-aurora-prob-green';
    return 'mod-aurora-prob-none';
}

function aurora_rsg_class(int $scale): string {
    if ($scale >= 5) return 'mod-aurora-rsg-scale-5';
    if ($scale == 4) return 'mod-aurora-rsg-scale-4';
    if ($scale == 3) return 'mod-aurora-rsg-scale-3';
    if ($scale == 2) return 'mod-aurora-rsg-scale-2';
    if ($scale == 1) return 'mod-aurora-rsg-scale-1';
    return 'mod-aurora-rsg-scale-0';
}

function aurora_hm_class(?float $kp): string {
    if ($kp === null) return 'mod-aurora-hm-empty';
    if ($kp >= 5)     return 'mod-aurora-hm-red';
    if ($kp >= 4)     return 'mod-aurora-hm-orange';
    if ($kp >= 3)     return 'mod-aurora-hm-yellow';
    return 'mod-aurora-hm-green';
}

function aurora_pill_class(float $kp): string {
    if ($kp >= 5) return 'mod-aurora-kpmax-high';
    if ($kp >= 4) return 'mod-aurora-kpmax-mid';
    return '';
}

// --- Freshness check ---
$scales_ok = file_exists('jsondata/noaa_scales.json') && (time() - filemtime('jsondata/noaa_scales.json') < 7200);
$ki_ok     = file_exists('jsondata/ki.txt')           && (time() - filemtime('jsondata/ki.txt') < 28800);
?>
<div class="updatedtime"><span>
    <?php if ($scales_ok && $ki_ok):
        echo $online . ' ' . date($timeFormat, filemtime('jsondata/noaa_scales.json'));
    else:
        echo $offline . ' <offline>'.$lang['Offline'].'</offline>';
    endif; ?>
</span></div>

<div class="mod-aurora">

  <div class="mod-aurora-body">
  <div class="mod-aurora-left">
    <div class="mod-aurora-sect"><?php echo $lang['LatestObserved'];?></div>
    <div class="mod-aurora-rsg-row">
      <?php foreach (['R', 'S', 'G'] as $letter):
        $sc  = $rsg[$letter]['scale'];
        $cls = aurora_rsg_class($sc);
        $lbl = $sc > 0 ? ($letter . $sc) : '&mdash;';
      ?>
      <div class="mod-aurora-rsg-box <?php echo $cls; ?>">
        <span class="mod-aurora-rsg-letter"><?php echo $letter; ?></span>
        <span class="mod-aurora-rsg-val"><?php echo $lbl; ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="mod-aurora-rsg-labels">
      <span><?php echo $lang['Radio'];?></span><span><?php echo $lang['Solar'];?></span><span><?php echo $lang['Geo'];?></span>
    </div>
  </div>

  <div class="mod-aurora-right">
    <div class="mod-aurora-sect"><?php echo $lang['KpGeomagneticForecast'];?></div>
    <div class="mod-aurora-fc-grid">
      <?php foreach ($forecast_days as $day): ?>
      <div class="mod-aurora-day-col">
        <div class="mod-aurora-day-name"><?php echo $day['label']; ?></div>
        <div class="mod-aurora-kpmax <?php echo aurora_pill_class($day['max_kp']); ?>">
          <?php echo $lang['Kp'];?>&nbsp;<?php echo number_format($day['max_kp'], 1); ?>
        </div>
        <div class="mod-aurora-heatmap">
          <?php foreach ($day['slots'] as $kp): ?>
          <div class="mod-aurora-hm-block <?php echo aurora_hm_class($kp); ?>"></div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  </div><!-- end mod-aurora-body -->

  <div class="mod-aurora-footer">
    <span class="mod-aurora-prob-lbl" title="30-minute real-time nowcast based on L1 solar wind measurements"><?php echo $lang['AuroraNowcast'];?></span>
    <span class="mod-aurora-prob-val <?php echo $prob_ok ? aurora_prob_class($aurora_prob) : 'mod-aurora-prob-none'; ?>">
      <?php echo $prob_ok ? $aurora_prob . '%' : '--'; ?>
    </span>
  </div>

</div><!-- end mod-aurora -->
