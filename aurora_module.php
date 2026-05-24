<?php
include('shared.php');
include_once('settings1.php');

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
    $utc_today_ts = strtotime(gmdate('Y-m-d'));
    $local_date   = date('Y-m-d');
    foreach ([0, 1, 2] as $offset) {
        $target_ts = strtotime("+$offset day", $utc_today_ts);
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

// --- Helpers ---
function aurora_rsg_class(int $scale): string {
    if ($scale >= 4) return 'rsg-g-storm';
    if ($scale >= 2) return 'rsg-g-active';
    if ($scale >= 1) return 'rsg-g-minor';
    return 'rsg-g-none';
}

function aurora_hm_class(?float $kp): string {
    if ($kp === null) return 'hm-empty';
    if ($kp >= 5)     return 'hm-red';
    if ($kp >= 4)     return 'hm-orange';
    if ($kp >= 3)     return 'hm-yellow';
    return 'hm-green';
}

function aurora_pill_class(float $kp): string {
    if ($kp >= 5) return 'kpmax-high';
    if ($kp >= 4) return 'kpmax-mid';
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
        echo $offline . ' <offline>Offline</offline>';
    endif; ?>
</span></div>

<div class="aurora-card">

  <div class="aurora-header">Space Weather</div>

  <div class="aurora-body">
  <div class="aurora-left">
    <div class="aurora-sect">Latest Observed</div>
    <div class="aurora-rsg-row">
      <?php foreach (['R', 'S', 'G'] as $letter):
        $sc  = $rsg[$letter]['scale'];
        $cls = aurora_rsg_class($sc);
        $lbl = $sc > 0 ? ($letter . $sc) : '&mdash;';
      ?>
      <div class="aurora-rsg-box <?php echo $cls; ?>">
        <span class="aurora-rsg-letter"><?php echo $letter; ?></span>
        <span class="aurora-rsg-val"><?php echo $lbl; ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="aurora-rsg-labels">
      <span>Radio</span><span>Solar</span><span>Geo</span>
    </div>
  </div>

  <div class="aurora-vdivider"></div>

  <div class="aurora-right">
    <div class="aurora-sect">Kp Geomagnetic Forecast</div>
    <div class="aurora-fc-grid">
      <?php foreach ($forecast_days as $day): ?>
      <div class="aurora-day-col">
        <div class="aurora-day-name"><?php echo $day['label']; ?></div>
        <div class="aurora-kpmax <?php echo aurora_pill_class($day['max_kp']); ?>">
          Kp&nbsp;<?php echo number_format($day['max_kp'], 1); ?>
        </div>
        <div class="aurora-heatmap">
          <?php foreach ($day['slots'] as $kp): ?>
          <div class="aurora-hm-block <?php echo aurora_hm_class($kp); ?>"></div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  </div><!-- end aurora-body -->

</div><!-- end aurora-card -->
