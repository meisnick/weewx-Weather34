<?php
include_once('w34CombinedData.php');error_reporting(0);
include_once('w34_i18n.php');
if (isset($_GET['theme'])) { $theme = $_GET['theme'] === 'light' ? 'light' : 'dark'; }
// Satellite popout, from the Radar card's chin. Two tabs of NASA GIBS map tiles (free, keyless):
//   Live      GOES-East ABI imagery every 10 minutes (GeoColor, clean infrared, air mass) as a 1, 2 or 4 hour loop
//             ending on the newest frame GIBS has; local or wide view
//   Close-up  the latest clear Landsat 8/9 or Sentinel-2 pass (NASA HLS, 30 m) around the station, from
//             jsondata/sat_passes.json (scripts/sat_passes_update.py), over that day's VIIRS image so lakes and
//             the swath edge are not blank
$sp = @json_decode(@file_get_contents('jsondata/sat_passes.json'), true);
$passes = is_array($sp) && !empty($sp['passes']) ? $sp['passes'] : array();
$tab = ($_GET['tab'] ?? '') === 'close' && $passes ? 'close' : 'live';
?>
<!DOCTYPE html>
<html lang="<?php echo w34_html_lang(); ?>" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
<meta charset="UTF-8">
<title><?php echo w34t('SatTitle', 'Satellite'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/popup.<?php echo $theme; ?>.css?version=<?php echo filemtime('css/popup.' . $theme . '.css'); ?>" rel="stylesheet prefetch">
<link href="css/tokens.css?version=<?php echo filemtime('css/tokens.css'); ?>" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" rel="stylesheet">
<link href="css/satellite.css?version=<?php echo filemtime('css/satellite.css'); ?>" rel="stylesheet">
</head>
<body class="sat sat--<?php echo htmlspecialchars($theme); ?>">
<div class="tabs-header">
  <button class="tablink<?php echo $tab == 'live' ? ' active' : ''; ?>" data-tab="live"><?php echo w34t('SatLive', 'Live · GOES-East'); ?></button>
<?php if ($passes): ?>
  <button class="tablink<?php echo $tab == 'close' ? ' active' : ''; ?>" data-tab="close"><?php echo w34t('SatClose', 'Close-up · Landsat / Sentinel-2'); ?></button>
<?php endif; ?>
</div>
<div class="sat-body">
  <div class="sat-tools" data-for="live"<?php echo $tab == 'live' ? '' : ' hidden'; ?>>
    <span class="sat-lab"><?php echo w34t('SatView', 'View'); ?></span>
    <span class="sat-group" data-set="layer"><button data-v="geocolor" class="on"><?php echo w34t('SatGeoColor', 'GeoColor'); ?></button><button data-v="ir"><?php echo w34t('SatIR', 'Infrared'); ?></button><button data-v="airmass"><?php echo w34t('SatAirMass', 'Air mass'); ?></button></span>
    <span class="sat-sp"></span>
    <span class="sat-lab"><?php echo w34t('SatLoop', 'Loop'); ?></span>
    <span class="sat-group" data-set="hours"><button data-v="1">1 h</button><button data-v="2" class="on">2 h</button><button data-v="4">4 h</button></span>
    <button class="sat-play" title="<?php echo htmlspecialchars(w34t('SatPause', 'Pause')); ?>" data-pause="<?php echo htmlspecialchars(w34t('SatPause', 'Pause')); ?>" data-play="<?php echo htmlspecialchars(w34t('SatPlay', 'Play')); ?>">&#10074;&#10074;</button>
    <span class="sat-group" data-set="extent"><button data-v="local" class="on"><?php echo w34t('SatLocal', 'Local'); ?></button><button data-v="wide"><?php echo w34t('SatWide', 'Wide'); ?></button></span>
  </div>
<?php if ($passes): ?>
  <div class="sat-tools" data-for="close"<?php echo $tab == 'close' ? '' : ' hidden'; ?>>
    <span class="sat-lab"><?php echo w34t('SatPasses', 'Clear passes'); ?></span>
    <span class="sat-group" data-set="pass"><?php foreach ($passes as $i => $p):
        $tip = $p['sat'] . ($p['cloud'] > 0.05 ? ' · ' . w34f('SatCloud', '%s%% cloud or snow', round($p['cloud'] * 100)) : ''); ?><button data-v="<?php echo $i; ?>"<?php echo $i ? '' : ' class="on"'; ?> title="<?php echo htmlspecialchars($tip); ?>"><?php
        echo w34_date('M j', strtotime($p['date'])); ?><small><?php echo $p['src'] == 'S30' ? 'S2' : 'L'; ?></small></button><?php endforeach; ?></span>
    <span class="sat-sp"></span>
    <span class="sat-group" data-set="zoom"><button data-v="13">13</button><button data-v="12" class="on">12</button><button data-v="10">10</button></span>
  </div>
<?php endif; ?>
  <div class="sat-map">
    <div id="sat-map"></div>
    <div class="sat-bar" id="sat-bar"></div>
    <div class="sat-stamp" id="sat-stamp"><?php echo w34t('SatLoading', 'Loading…'); ?></div>
    <div class="sat-scale" id="sat-scale" hidden></div>
  </div>
  <div class="sat-src" data-for="live"<?php echo $tab == 'live' ? '' : ' hidden'; ?>><?php echo w34t('SatSrcLive', 'NOAA GOES-East ABI via NASA GIBS, every 10 minutes, about an hour behind real time. GeoColor at night: city lights under infrared cloud.'); ?></div>
  <div class="sat-src" data-for="close"<?php echo $tab == 'close' ? '' : ' hidden'; ?>><?php echo w34t('SatSrcClose', 'Harmonized Landsat 8/9 and Sentinel-2 (NASA HLS, 30 m) via NASA GIBS, over that day\'s VIIRS image. A pass every two to three days; clear ones listed.'); ?></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
(function () {
  if (!window.L) { document.getElementById('sat-stamp').textContent = ''; return; }
  var LAT = <?php echo (float)$lat; ?>, LON = <?php echo (float)$lon; ?>, TZ = <?php echo json_encode($TZ ?? 'UTC'); ?>;
  var LANG = document.documentElement.lang || 'en';
  var PASSES = <?php echo json_encode($passes, JSON_UNESCAPED_SLASHES); ?>;
  var T = <?php echo json_encode(array('ago' => w34t('SatMinAgo', '%s min ago'), 'days' => w34t('SatDaysAgo', '%s days ago'),
      'km' => w34t('SatKmAcross', '%s km across'), 'none' => w34t('SatNoFrames', 'No imagery available right now.')), JSON_UNESCAPED_UNICODE); ?>;
  var GIBS = 'https://gibs.earthdata.nasa.gov/wmts/epsg3857/best/';
  var LAYERS = {geocolor: ['GOES-East_ABI_GeoColor', 7], ir: ['GOES-East_ABI_Band13_Clean_Infrared', 6], airmass: ['GOES-East_ABI_Air_Mass', 6]};
  var url = function (id, time, level, ext) { return GIBS + id + '/default/' + time + '/GoogleMapsCompatible_Level' + level + '/{z}/{y}/{x}.' + (ext || 'png'); };
  var fmt = function (s, v) { return s.replace('%s', v); };

  var map = L.map('sat-map', {zoomControl: false, attributionControl: false, fadeAnimation: false, zoomSnap: 1});
  L.control.zoom({position: 'bottomright'}).addTo(map);
  L.marker([LAT, LON], {icon: L.divIcon({className: 'sat-pin', iconSize: null}), interactive: false, zIndexOffset: 1000}).addTo(map);
  var stamp = document.getElementById('sat-stamp'), bar = document.getElementById('sat-bar'), scale = document.getElementById('sat-scale');
  var st = {tab: <?php echo json_encode($tab); ?>, layer: 'geocolor', hours: 2, extent: 'local', pass: 0, zoom: 12, playing: true};
  var frames = [], timer = null, k = 0, group = L.layerGroup().addTo(map), token = 0;

  function clearAll() { if (timer) clearInterval(timer); timer = null; group.clearLayers(); frames = []; bar.innerHTML = ''; }

  // ---- Live: the frames GIBS actually has in the loop's window, from its DescribeDomains list, newest last. GOES has
  // the odd missing 10-minute frame, and asking for one gives a black frame. The 4 hour loop takes every other frame.
  function minutes(dur) { var m = /PT(?:(\d+)H)?(?:(\d+)M)?/.exec(dur || ''); return m ? (+(m[1] || 0) * 60 + +(m[2] || 0)) || 10 : 10; }
  var iso = function (t) { return new Date(t).toISOString().slice(0, 19) + 'Z'; };
  function domain(def, from, to) {   // the frame times (ms) GIBS has for a layer between two times
    return fetch(GIBS + '1.0.0/' + def[0] + '/default/GoogleMapsCompatible_Level' + def[1] + '/all/' + iso(from) + '--' + iso(to) + '.xml')
      .then(function (r) { return r.ok ? r.text() : ''; }, function () { return ''; })
      .then(function (xml) {
        var m = /<Domain>([^<]*)<\/Domain>/.exec(xml), times = [];
        (m ? m[1].split(',') : []).forEach(function (part) {   // "start/end/PT10M" runs, or single times
          var p = part.trim().split('/'), a = Date.parse(p[0]), b = p[1] ? Date.parse(p[1]) : a, step = minutes(p[2]) * 60000;
          for (var t = a; t <= b; t += step) times.push(t);
        });
        return times;
      });
  }
  function live() {
    var my = ++token, def = LAYERS[st.layer];
    clearAll(); scale.hidden = true; stamp.textContent = '…';
    place(st.extent == 'local' ? [LAT, LON] : [LAT - 1, LON - 1.5], st.extent == 'local' ? 7 : 5);
    var now = Date.now(), from = now - (st.hours * 60 + 120) * 60000;   // the newest frame runs about an hour behind
    // air mass is drawn over infrared (below), which has gaps of its own: only where it has the frame
    Promise.all([domain(def, from, now), st.layer == 'airmass' ? domain(LAYERS.ir, from, now) : []])
      .then(function (got) {
        if (my != token) return;
        var times = got[0], under = got[1];
        if (!times.length) { stamp.textContent = T.none; return; }
        var last = times[times.length - 1];
        times = times.filter(function (t) { return t > last - st.hours * 3600000; });
        if (st.hours > 2) times = times.filter(function (t, i) { return (times.length - 1 - i) % 2 == 0; });
        frames = times.map(function (t) { return new Date(t); });
        frames.forEach(function (t) {
          var at = iso(t), o = {maxNativeZoom: def[1], maxZoom: 10, opacity: 0}, parts = [];
          // GIBS leaves the air mass product's coldest cloud tops transparent: the same time's infrared shows through
          if (under.indexOf(t.getTime()) >= 0) parts.push(L.tileLayer(url(LAYERS.ir[0], at, LAYERS.ir[1]), o));
          parts.push(L.tileLayer(url(def[0], at, def[1]), o));
          group.addLayer(L.layerGroup(parts));
        });
        bar.innerHTML = frames.map(function () { return '<i></i>'; }).join('');
        k = frames.length - 1; show(k);
        if (st.playing) play();
      });
  }
  function show(i) {
    var ls = group.getLayers();
    ls.forEach(function (g, j) { g.eachLayer(function (l) { l.setOpacity(j == i ? 1 : 0); }); });
    [].forEach.call(bar.children, function (e, j) { e.className = j <= i ? 'on' : ''; });
    var t = frames[i];
    stamp.innerHTML = t.toLocaleString(LANG, {timeZone: TZ, weekday: 'short', hour: '2-digit', minute: '2-digit', hour12: false})
      + ' <b>&middot; ' + fmt(T.ago, Math.round((Date.now() - t) / 60000)) + '</b>';
  }
  function play() {
    if (timer) clearInterval(timer);
    var hold = 0;
    timer = setInterval(function () {   // 0.5 s a frame, the newest held for 2 s
      if (k == frames.length - 1 && hold++ < 3) return;
      hold = 0; k = (k + 1) % frames.length; show(k);
    }, 500);
  }

  // ---- Close-up: the chosen pass over that day's VIIRS true colour (NOAA-20), which fills lakes and the swath edge
  function close() {
    ++token; clearAll();
    var p = PASSES[st.pass]; if (!p) return;
    group.addLayer(L.tileLayer(url('VIIRS_NOAA20_CorrectedReflectance_TrueColor', p.date, 9, 'jpg'), {maxNativeZoom: 9, maxZoom: 15}));
    group.addLayer(L.tileLayer(url('HLS_' + p.src + '_Nadir_BRDF_Adjusted_Reflectance', p.date, 12), {maxNativeZoom: 12, maxZoom: 15}));
    place([LAT, LON], st.zoom);
    var d = new Date(p.date + 'T17:00:00Z');
    stamp.innerHTML = d.toLocaleDateString(LANG, {timeZone: TZ, weekday: 'short', month: 'short', day: 'numeric'}) + ' &middot; ' + p.sat
      + ' <b>&middot; ' + fmt(T.days, Math.round((Date.now() - d) / 86400000)) + '</b>';
    km();
  }
  function km() {   // the map's real width, for the zoom buttons and the corner label
    var w = map.getSize().x, m0 = 156543.03 * Math.cos(LAT * Math.PI / 180);   // metres a pixel at zoom 0
    var across = function (z) { return Math.round(w * m0 / Math.pow(2, z) / 1000); };
    document.querySelectorAll('[data-set=zoom] button').forEach(function (b) {
      b.textContent = across(+b.dataset.v) + ' km';
      b.classList.toggle('on', +b.dataset.v == map.getZoom());   // follows wheel and +/- zooming too
    });
    scale.textContent = fmt(T.km, across(map.getZoom())); scale.hidden = false;
  }

  // Each tab opens centred on the station; once panned or zoomed, the map stays where it was put while the pass,
  // view or loop changes, and each tab keeps its own place. The Local / Wide buttons recentre the Live tab.
  var views = {};
  function place(center, zoom) {
    var v = views[st.tab];
    map.setView(v ? v[0] : center, v ? v[1] : zoom, {animate: false});
  }
  map.on('moveend', function () { views[st.tab] = [map.getCenter(), map.getZoom()]; });

  function render() { (st.tab == 'live' ? live : close)(); }

  document.querySelector('.tabs-header').addEventListener('click', function (e) {
    var b = e.target.closest('.tablink'); if (!b) return;
    st.tab = b.dataset.tab;
    document.querySelectorAll('.tablink').forEach(function (x) { x.classList.toggle('active', x == b); });
    document.querySelectorAll('[data-for]').forEach(function (x) { x.hidden = x.dataset.for != st.tab; });
    render();
  });
  document.querySelectorAll('.sat-group').forEach(function (g) {
    g.addEventListener('click', function (e) {
      var b = e.target.closest('button'); if (!b) return;
      g.querySelectorAll('button').forEach(function (x) { x.classList.toggle('on', x == b); });
      var v = b.dataset.v; st[g.dataset.set] = isNaN(v) ? v : +v;
      if (g.dataset.set == 'zoom') { map.setZoom(st.zoom); return; }
      if (g.dataset.set == 'extent') delete views.live;
      render();
    });
  });
  var pb = document.querySelector('.sat-play');
  pb.addEventListener('click', function () {
    st.playing = !st.playing;
    pb.innerHTML = st.playing ? '&#10074;&#10074;' : '&#9654;'; pb.title = pb.dataset[st.playing ? 'pause' : 'play'];
    if (st.playing) play(); else if (timer) { clearInterval(timer); timer = null; }
  });
  map.on('zoomend resize', function () { if (st.tab == 'close') km(); });
  render();
})();
</script>
</body>
</html>
