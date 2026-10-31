<?php
include_once('w34CombinedData.php');error_reporting(0);
include_once('w34_i18n.php');
// Storm Outlook popout: the NWS Storm Prediction Center's convective outlooks for Days 1 to 8 at the station, from
// jsondata/spc_outlook.json (scripts/spc_outlook_update.py), with a regional map drawn from the outlook polygons
// over js/na_basemap.json.
$spc = @json_decode(@file_get_contents('jsondata/spc_outlook.json'), true);
$days = array();
if (is_array($spc) && !empty($spc['days'])) foreach ($spc['days'] as $d) $days[(int)$d['day']] = $d;
ksort($days);
$spc_age = is_array($spc) && !empty($spc['fetched']) ? time() - strtotime($spc['fetched']) : null;

$CATS = array('TSTM', 'MRGL', 'SLGT', 'ENH', 'MDT', 'HIGH');
$cat_name = function ($c) {
    $n = array('TSTM' => w34t('SpcTSTM', 'General thunderstorms'), 'MRGL' => w34t('SpcMRGL', 'Marginal risk'),
               'SLGT' => w34t('SpcSLGT', 'Slight risk'), 'ENH' => w34t('SpcENH', 'Enhanced risk'),
               'MDT' => w34t('SpcMDT', 'Moderate risk'), 'HIGH' => w34t('SpcHIGH', 'High risk'));
    return $n[$c] ?? w34t('SpcNone', 'No thunderstorms');
};
$cat_desc = function ($c) {
    $n = array('TSTM' => w34t('SpcTSTMd', 'Thunderstorms possible, none expected to be severe'),
               'MRGL' => w34t('SpcMRGLd', 'Isolated severe storms possible'),
               'SLGT' => w34t('SpcSLGTd', 'Scattered severe storms possible'),
               'ENH' => w34t('SpcENHd', 'Numerous severe storms possible'),
               'MDT' => w34t('SpcMDTd', 'Widespread severe storms likely'),
               'HIGH' => w34t('SpcHIGHd', 'Widespread, long-lived, intense severe storms expected'));
    return $n[$c] ?? w34t('SpcNoned', 'Outside the thunderstorm outlook');
};
$miles = (($windunit ?? 'mph') == 'mph');
$dist = function ($km) use ($miles) { return $miles ? round($km * 0.621371) . ' mi' : $km . ' km'; };
$when = function ($iso) { return $iso ? w34_date('D H:i', strtotime($iso)) : ''; };
$dayname = function ($d) { return isset($d['valid']) ? w34_date('D j', strtotime($d['valid'])) : ''; };
// probability at the station; 0 means outside the lowest contour SPC draws for that hazard
$pct = function ($v, $floor) { return $v > 0 ? $v . '%' : '&lt;' . $floor . '%'; };

// tab chip: the risk at the station (Days 1-3) or the any-severe probability (Days 4-8)
$chip = function ($d) {
    $p = $d['point'] ?? array();
    if (!empty($p['cat'])) return 'spc-c-' . $p['cat'];
    if (($p['prob'] ?? 0) >= 30) return 'spc-c-ENH';
    if (($p['prob'] ?? 0) >= 15) return 'spc-c-SLGT';
    if (($p['prob'] ?? 0) >= 5) return 'spc-c-MRGL';
    return 'spc-c-none';
};
// WPC Excessive Rainfall Outlook levels: the least chance that rain exceeds flash-flood guidance within 25 miles
$ero_pct = array('MRGL' => 5, 'SLGT' => 15, 'MDT' => 40, 'HIGH' => 70);
// the day the flood layer opens on without ?day= (the Rainfall card's chin link): the first day with the highest level
$ero_day = 1; $ero_top = 0;
foreach ($days as $n => $d) {
    $e = $d['point']['ero'] ?? '';
    if ($e && $ero_pct[$e] > $ero_top) { $ero_top = $ero_pct[$e]; $ero_day = $n; }
}
$map_days = array();
foreach ($days as $n => $d) $map_days[$n] = array('layers' => $d['layers'] ?? array());
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
<meta charset="UTF-8">
<title><?php echo w34t('SpcTitle', 'Storm Outlook'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/popup.<?php echo $theme; ?>.css?version=<?php echo filemtime('css/popup.' . $theme . '.css'); ?>" rel="stylesheet prefetch">
<link href="css/tokens.css?version=<?php echo filemtime('css/tokens.css'); ?>" rel="stylesheet">
<link href="css/spc_outlook.css?version=<?php echo filemtime('css/spc_outlook.css'); ?>" rel="stylesheet">
</head>
<body class="spc spc--<?php echo htmlspecialchars($theme); ?>">
<div class="tabs-header">
<?php foreach (array(1, 2, 3) as $n): if (!isset($days[$n])) continue; ?>
  <button class="tablink" data-day="<?php echo $n; ?>"><i class="spc-dot <?php echo $chip($days[$n]); ?>"></i><?php echo w34f('SpcDay', 'Day %s', $n) . ' &middot; ' . $dayname($days[$n]); ?></button>
<?php endforeach; ?>
<?php if (isset($days[4])): ?>
  <button class="tablink" data-day="4"><i class="spc-dot <?php
    $top = 'spc-c-none';
    foreach (array(4, 5, 6, 7, 8) as $n) if (isset($days[$n]) && $chip($days[$n]) != 'spc-c-none') { $top = $chip($days[$n]); break; }
    echo $top; ?>"></i><?php echo w34t('SpcDays48', 'Days 4–8'); ?></button>
<?php endif; ?>
</div>

<?php if (!$days): ?>
<div class="spc-empty"><?php echo w34t('SpcNoData', 'The storm outlook is not available yet.'); ?></div>
<?php else: ?>
<div class="spc-body">
  <div class="spc-info">
    <div class="spc-sub" hidden><?php foreach (array(4, 5, 6, 7, 8) as $n): if (!isset($days[$n])) continue; ?>
      <button class="spc-subday" data-day="<?php echo $n; ?>"><i class="spc-dot <?php echo $chip($days[$n]); ?>"></i><?php echo $dayname($days[$n]); ?></button><?php endforeach; ?>
    </div>
<?php foreach ($days as $n => $d):
    $p = $d['point'] ?? array();
    $c = $p['cat'] ?? '';
?>
    <section class="spc-day" data-day="<?php echo $n; ?>" hidden>
<?php if ($n <= 3): ?>
      <div class="spc-risk"><span class="spc-badge <?php echo $chip($d); ?>"><?php echo $cat_name($c); ?></span><?php
        if ($c && $c != 'TSTM') echo ' <span class="spc-level">' . w34f('SpcLevel', 'level %s of 5', array_search($c, $CATS)) . '</span>'; ?></div>
      <p class="spc-desc"><?php echo $cat_desc($c); ?></p>
<?php else:
    $pr = (int)($p['prob'] ?? 0); ?>
      <div class="spc-risk"><span class="spc-badge <?php echo $chip($d); ?>"><?php echo $pr ? w34f('SpcAnyPct', '%s%% chance of severe storms', $pr) : w34t('SpcLow48', 'No severe area outlined'); ?></span></div>
      <p class="spc-desc"><?php echo $pr ? w34t('SpcProb48d', 'Within 25 miles (40 km) of the station') : w34t('SpcLow48d', 'Severe storms not expected, or too uncertain to outline this far ahead'); ?></p>
<?php endif; ?>
      <p class="spc-when"><?php echo w34f('SpcValid', 'Valid %s – %s', $when($d['valid'] ?? ''), $when($d['expire'] ?? '')); ?></p>
<?php if ($n <= 2): ?>
      <h4><?php echo w34t('SpcChance', 'Chance within 25 mi (40 km)'); ?></h4>
      <table class="spc-haz">
        <tr><td><?php echo w34t('SpcTornado', 'Tornado'); ?></td><td><?php echo $pct($p['torn'] ?? 0, 2); ?></td><td><?php echo !empty($p['sigtorn']) ? '<span class="spc-sig">' . w34t('SpcSigTorn', 'EF2+ possible') . '</span>' : ''; ?></td></tr>
        <tr><td><?php echo w34t('SpcWind', 'Damaging wind'); ?></td><td><?php echo $pct($p['wind'] ?? 0, 5); ?></td><td><?php echo !empty($p['sigwind']) ? '<span class="spc-sig">' . w34t('SpcSigWind', '75 mph+ gusts possible') . '</span>' : ''; ?></td></tr>
        <tr><td><?php echo w34t('SpcHail', 'Large hail'); ?></td><td><?php echo $pct($p['hail'] ?? 0, 5); ?></td><td><?php echo !empty($p['sighail']) ? '<span class="spc-sig">' . w34t('SpcSigHail', '2 in+ hail possible') . '</span>' : ''; ?></td></tr>
      </table>
<?php elseif ($n == 3): ?>
      <h4><?php echo w34t('SpcChance', 'Chance within 25 mi (40 km)'); ?></h4>
      <table class="spc-haz">
        <tr><td><?php echo w34t('SpcAnySevere', 'Any severe'); ?></td><td><?php echo $pct($p['prob'] ?? 0, 5); ?></td><td><?php echo !empty($p['sigprob']) ? '<span class="spc-sig">' . w34t('SpcSigAny', 'significant severe possible') . '</span>' : ''; ?></td></tr>
      </table>
<?php endif; ?>
<?php if (!empty($p['near'])): ?>
      <p class="spc-near"><?php echo w34f('SpcNear', 'Nearest severe risk: %s, %s %s', '<b>' . $cat_name($p['near']['cat']) . '</b>', $dist($p['near']['km']), w34_compass($p['near']['bearing'])); ?></p>
<?php endif; ?>
<?php if (isset($p['ero'])): $e = $p['ero']; ?>
      <h4><?php echo w34t('SpcFlood', 'Flash flood (WPC)'); ?></h4>
      <p class="spc-flood"><i class="spc-dot spc-c-<?php echo $e ?: 'none'; ?>"></i> <b><?php echo $e ? $cat_name($e) : w34t('SpcFloodNone', 'No risk area'); ?></b> &middot; <?php
        echo $e ? w34f('SpcFloodPct', 'at least %s%% chance rain exceeds flash-flood guidance within 25 mi', $ero_pct[$e])
                : w34t('SpcFloodLow', 'under 5% chance rain exceeds flash-flood guidance within 25 mi'); ?></p>
<?php if (!empty($p['ero_near'])): ?>
      <p class="spc-near"><?php echo w34f('SpcFloodNear', 'Nearest flash-flood risk: %s, %s %s', '<b>' . $cat_name($p['ero_near']['cat']) . '</b>', $dist($p['ero_near']['km']), w34_compass($p['ero_near']['bearing'])); ?></p>
<?php endif; ?>
<?php endif; ?>
<?php if (!empty($d['summary'])): ?>
      <h4><?php echo w34t('SpcSummary', 'Forecaster summary (SPC)'); ?></h4>
      <p class="spc-sum" title="<?php echo htmlspecialchars($d['summary']); ?>"><?php echo htmlspecialchars($d['summary']); ?></p>
<?php endif; ?>
<?php if (!empty($d['issue'])): ?>
      <p class="spc-issued"><?php echo w34f('SpcIssued', 'Issued %s', $when($d['issue'])) . (!empty($d['forecaster']) ? ' &middot; ' . htmlspecialchars($d['forecaster']) : ''); ?></p>
<?php endif; ?>
    </section>
<?php endforeach; ?>
  </div>
  <div class="spc-mapcol">
    <div class="spc-tools">
      <span class="spc-layers"></span>
      <span class="spc-views"><button data-view="region" class="on"><?php echo w34t('SpcRegion', 'Region'); ?></button><button data-view="us"><?php echo w34t('SpcUS', 'US'); ?></button></span>
    </div>
    <div class="spc-map"><svg id="spc-svg" preserveAspectRatio="xMidYMid meet" aria-label="<?php echo htmlspecialchars(w34t('SpcTitle', 'Storm Outlook')); ?>"></svg><div class="spc-maptxt" hidden><?php echo w34t('SpcNoAreas', 'No areas outlined'); ?></div></div>
    <div class="spc-legend"></div>
  </div>
</div>
<div class="spc-src"><?php echo w34t('SpcSource', 'NOAA/NWS Storm Prediction Center convective and Weather Prediction Center excessive-rainfall outlooks, checked twice an hour. Map: Natural Earth.');
  if ($spc_age !== null && $spc_age > 3 * 3600) echo ' <span class="spc-stale">' . w34f('SpcStale', 'Last update %s hours ago.', round($spc_age / 3600)) . '</span>'; ?></div>
<?php endif; ?>

<script>
(function () {
  var DAYS = <?php echo json_encode($map_days, JSON_UNESCAPED_SLASHES); ?>;
  var STATION = [<?php echo (float)$lon; ?>, <?php echo (float)$lat; ?>];
  var T = <?php echo json_encode(array(
      'cat' => w34t('SpcRisk', 'Risk'), 'torn' => w34t('SpcTornado', 'Tornado'), 'wind' => w34t('SpcWind', 'Damaging wind'),
      'hail' => w34t('SpcHail', 'Large hail'), 'prob' => w34t('SpcAnySevere', 'Any severe'), 'ero' => w34t('SpcFloodLayer', 'Flash flood'), 'sig' => w34t('SpcSigLegend', 'Significant (hatched)'),
      'TSTM' => w34t('SpcTSTM', 'General thunderstorms'), 'MRGL' => w34t('SpcMRGL', 'Marginal risk'), 'SLGT' => w34t('SpcSLGT', 'Slight risk'),
      'ENH' => w34t('SpcENH', 'Enhanced risk'), 'MDT' => w34t('SpcMDT', 'Moderate risk'), 'HIGH' => w34t('SpcHIGH', 'High risk'),
      'station' => w34t('SpcStation', 'Station')), JSON_UNESCAPED_UNICODE); ?>;
  var svg = document.getElementById('spc-svg');
  if (!svg) return;
  var NS = 'http://www.w3.org/2000/svg';

  // Lambert conformal conic, standard parallels 33 and 45 N, centred on 96 W (as the NWS draws the US)
  var r = Math.PI / 180, p1 = 33 * r, p2 = 45 * r, p0 = 39 * r, l0 = -96 * r;
  var n = Math.log(Math.cos(p1) / Math.cos(p2)) / Math.log(Math.tan(Math.PI / 4 + p2 / 2) / Math.tan(Math.PI / 4 + p1 / 2));
  var F = Math.cos(p1) * Math.pow(Math.tan(Math.PI / 4 + p1 / 2), n) / n;
  var rho0 = F / Math.pow(Math.tan(Math.PI / 4 + p0 / 2), n);
  function proj(lon, lat) {
    var rho = F / Math.pow(Math.tan(Math.PI / 4 + lat * r / 2), n), t = n * (lon * r - l0);
    return [rho * Math.sin(t) * 1000, -(rho0 - rho * Math.cos(t)) * 1000];
  }
  function ringPath(flat) {
    var d = '';
    for (var i = 0; i < flat.length; i += 2) {
      var q = proj(flat[i], flat[i + 1]);
      d += (i ? 'L' : 'M') + q[0].toFixed(1) + ' ' + q[1].toFixed(1);
    }
    return d + 'Z';
  }
  function el(tag, attrs, parent) {
    var e = document.createElementNS(NS, tag);
    for (var k in attrs) e.setAttribute(k, attrs[k]);
    (parent || svg).appendChild(e);
    return e;
  }

  // layers drawn into groups; the basemap once
  var defs = el('defs', {});
  var pat = el('pattern', {id: 'spc-hatch', width: 6, height: 6, patternUnits: 'userSpaceOnUse', patternTransform: 'rotate(45)'}, defs);
  var hatch = el('line', {x1: 0, y1: 0, x2: 0, y2: 6, class: 'spc-hatchline'}, pat);
  var gLand = el('g', {class: 'spc-land'}), gLakes = el('g', {class: 'spc-lake'}), gOut = el('g', {class: 'spc-out'}),
      gStates = el('g', {class: 'spc-states'}), gMark = el('g', {class: 'spc-mark'});
  var st = proj(STATION[0], STATION[1]);
  var mark1 = el('circle', {cx: st[0], cy: st[1], class: 'spc-mark-ring'}, gMark), mark2 = el('circle', {cx: st[0], cy: st[1], class: 'spc-mark-dot'}, gMark);
  var title = el('title', {}, gMark); title.textContent = T.station;
  fetch('js/na_basemap.json').then(function (x) { return x.json(); }).then(function (b) {
    b.land.forEach(function (f) { el('path', {d: ringPath(f)}, gLand); });
    b.lakes.forEach(function (f) { el('path', {d: ringPath(f)}, gLakes); });
    b.states.forEach(function (f) { el('path', {d: ringPath(f)}, gStates); });
  });

  // ?layer=ero (the Rainfall card's Flood Outlook link) opens on that layer, on ?day= or the worst flood day
  var q = new URLSearchParams(location.search);
  var view = 'region', day = 1, want = q.get('layer') || 'cat', lay = want;
  function setView() {
    var box = svg.parentNode.getBoundingClientRect(), asp = (box.height || 300) / (box.width || 400), x0, y0, w, h;
    if (view == 'us') {
      var a = proj(-122, 24), c = proj(-70, 24), top = proj(-96, 50.5);
      x0 = a[0] - 10; w = c[0] - a[0] + 20; y0 = top[1] - 10; h = Math.max(a[1] - top[1] + 20, w * asp);
      if (h > w * asp) { var w2 = h / asp; x0 -= (w2 - w) / 2; w = w2; }
    } else {
      w = 300; h = w * asp; x0 = st[0] - w / 2; y0 = st[1] - h / 2;   // about 1900 km across
    }
    svg.setAttribute('viewBox', [x0, y0, w, h].map(function (v) { return v.toFixed(1); }).join(' '));
    var s = w / (box.width || 400);   // svg units per css pixel
    mark1.setAttribute('r', 6 * s); mark2.setAttribute('r', 2.5 * s);
    pat.setAttribute('width', 6 * s); pat.setAttribute('height', 6 * s); hatch.setAttribute('y2', 6 * s);   // hatching 6 px apart in either view
  }

  function draw() {
    while (gOut.firstChild) gOut.removeChild(gOut.firstChild);
    var L = (DAYS[day] || {}).layers || {};
    var all = (L[lay] || []).slice().sort(function (a, b) { return a.dn - b.dn; });
    var named = lay == 'cat' || lay == 'ero';   // levels by name; the rest are probabilities
    var plain = function (f) { return named || !isNaN(parseFloat(f.label)); };
    var feats = all.filter(plain), sig = (L['sig' + lay] || []).concat(all.filter(function (f) { return !plain(f); }));
    var legend = [];
    feats.forEach(function (f) {
      var d = f.polys.map(function (poly) { return poly.map(ringPath).join(''); }).join('');
      var p = el('path', {d: d, 'fill-rule': 'evenodd', class: 'spc-poly'}, gOut);
      p.style.fill = f.fill; p.style.stroke = f.stroke;
      var name = named ? (T[f.label] || f.label2) : Math.round(parseFloat(f.label) * 100) + '%';
      var key = '<span><i style="background:' + f.fill + ';border-color:' + f.stroke + '"></i>' + name + '</span>';
      if (legend.indexOf(key) < 0) legend.push(key);   // an area drawn in several pieces gets one key
    });
    sig.forEach(function (f) {
      var d = f.polys.map(function (poly) { return poly.map(ringPath).join(''); }).join('');
      el('path', {d: d, 'fill-rule': 'evenodd', class: 'spc-sigpoly'}, gOut);
    });
    if (sig.length) legend.push('<span><i class="spc-hatchkey"></i>' + T.sig + '</span>');
    document.querySelector('.spc-legend').innerHTML = legend.join('');
    document.querySelector('.spc-maptxt').hidden = feats.length > 0;
  }

  function tools() {
    var L = (DAYS[day] || {}).layers || {}, keys = ['cat', 'torn', 'wind', 'hail', 'prob', 'ero'].filter(function (k) { return k in L; });
    lay = keys.indexOf(want) >= 0 ? want : (keys[0] || 'cat');   // the chosen layer wherever the day has it
    var h = keys.length > 1 ? keys.map(function (k) { return '<button data-layer="' + k + '"' + (k == lay ? ' class="on"' : '') + '>' + T[k] + '</button>'; }).join('') : '';
    document.querySelector('.spc-layers').innerHTML = h;
  }

  function show(d) {
    day = d;
    document.querySelectorAll('.spc-day').forEach(function (s) { s.hidden = +s.dataset.day != d; });
    document.querySelectorAll('.tablink').forEach(function (b) { b.classList.toggle('active', +b.dataset.day == Math.min(d, 4)); });
    document.querySelector('.spc-sub').hidden = d < 4;
    document.querySelectorAll('.spc-subday').forEach(function (b) { b.classList.toggle('on', +b.dataset.day == d); });
    tools(); draw();
  }

  document.querySelector('.tabs-header').addEventListener('click', function (e) {
    var b = e.target.closest('.tablink'); if (b) show(+b.dataset.day);
  });
  document.querySelector('.spc-sub').addEventListener('click', function (e) {
    var b = e.target.closest('.spc-subday'); if (b) show(+b.dataset.day);
  });
  document.querySelector('.spc-tools').addEventListener('click', function (e) {
    var b = e.target.closest('button'); if (!b) return;
    if (b.dataset.layer) { want = b.dataset.layer; tools(); draw(); }
    if (b.dataset.view) {
      view = b.dataset.view; setView();
      document.querySelectorAll('.spc-views button').forEach(function (x) { x.classList.toggle('on', x == b); });
    }
  });
  window.addEventListener('resize', setView);
  setView(); show(Math.min(8, Math.max(1, parseInt(q.get('day'), 10) || (want == 'ero' ? <?php echo (int)$ero_day; ?> : 1))));
})();
</script>
</body>
</html>
