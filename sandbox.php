<?php
/* sandbox.php — live-CSS module editor (DEV ONLY).
 *
 * Renders ONE Weather34 module authentically (real framework/main/module CSS +
 * the real card shell, fetched over HTTP so it matches the dashboard exactly)
 * inside an iframe, with a live-CSS editor beside it. Type CSS -> instant preview
 * (no deploy, no cache). Click any element -> its selector + live bounding box.
 *
 * Kills the three slow-loop bottlenecks: deploy round-trip, browser cache desync,
 * and pixel-guessing. Iterate here, then paste the CSS into css/modules/<x>.css.
 *
 *   /weewx/weather34/sandbox.php               -> editor
 *   /weewx/weather34/sandbox.php?frame=1&module=barometer.php&theme=dark  -> preview
 */

$base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');           // /weewx/weather34
$module = preg_replace('/[^a-zA-Z0-9_.\-]/', '', $_GET['module'] ?? 'barometer.php');
$theme  = (($_GET['theme'] ?? 'dark') === 'light') ? 'light' : 'dark';
$isTop  = (strpos($module, 'top_') === 0);

/* ── FRAME MODE: one module, styled exactly like the dashboard ───────────────── */
if (isset($_GET['frame'])) {
    // Run the module in its own request so its header()/includes are clean.
    $url  = 'http://127.0.0.1' . $base . '/' . $module;
    $ctx  = stream_context_create(['http' => ['timeout' => 10, 'header' => "User-Agent: sandbox\r\n"]]);
    $frag = @file_get_contents($url, false, $ctx);
    if ($frag === false) { $frag = '<div style="color:#c33;padding:20px">Could not load ' . htmlspecialchars($module) . '</div>'; }

    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html data-theme="' . $theme . '"><head><meta charset="utf-8">';
    echo '<link rel="stylesheet" href="css/framework.base.css?t=' . time() . '">';
    echo '<link rel="stylesheet" href="css/framework.' . $theme . '.css?t=' . time() . '">';
    echo '<link rel="stylesheet" href="css/main.' . $theme . '.css?t=' . time() . '">';
    foreach (glob('css/modules/*.css') as $s) { echo '<link rel="stylesheet" href="' . $s . '?t=' . time() . '">'; }
    echo '<style id="liveoverride"></style>';
    echo '<style>html,body{margin:0}body{background:var(--page-bg,#15171a);display:flex;justify-content:center;padding:26px 10px}
      .sbx-grid{background:var(--panel-bg,transparent)}
      .sbx-ov{position:fixed;inset:0;pointer-events:none;z-index:99999;display:none;
        background-image:linear-gradient(rgba(120,170,255,.18) 1px,transparent 1px),linear-gradient(90deg,rgba(120,170,255,.18) 1px,transparent 1px);
        background-size:10px 10px}
      .sbx-hl{outline:1px solid #ff5bd0 !important;outline-offset:0}</style>';
    echo '</head><body>';
    if ($isTop) {
        echo '<div class="weather34box-toparea sbx-grid" style="width:250px;display:block">'
           . '<div class="weather34box" data-module="' . htmlspecialchars($module) . '">'
           . '<div class="title">&#9432; SANDBOX</div>'
           . '<div class="value"><div id="sbx">' . $frag . '</div></div></div></div>';
    } else {
        echo '<div class="weather-container sbx-grid" style="--w34-grid-cols:1;width:340px;display:block">'
           . '<div class="weather-item" data-module="' . htmlspecialchars($module) . '">'
           . '<span class="moduletitle">SANDBOX</span><br>'
           . '<div id="sbx">' . $frag . '</div></div></div>';
    }
    echo '<div class="sbx-ov" id="sbxov"></div>';
    echo '</body></html>';
    exit;
}

/* ── EDITOR MODE ─────────────────────────────────────────────────────────────── */
$mods = [];
foreach (glob('top_*.php') as $f) { if (strpos($f, 'top_year_helpers') === false) $mods[] = $f; }
foreach (['barometer.php','windspeeddirection.php','sun3.php','sun4.php','rainfall.php','temperaturein.php',
          'indoortemperature.php','moonphase.php','solaruv.php','currentconditionsw34.php'] as $f) {
    if (file_exists($f)) $mods[] = $f;
}
$mods = array_values(array_unique($mods));
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>W34 Module Sandbox</title>
<style>
  :root{--bg:#0f1115;--panel:#1a1d23;--line:#2a2e37;--ink:#e6e8ec;--muted:#8b93a1;--accent:#6ea8ff;--pink:#ff5bd0}
  *{box-sizing:border-box}
  body{margin:0;font:13px/1.45 ui-sans-serif,system-ui,Segoe UI,Roboto,sans-serif;background:var(--bg);color:var(--ink);height:100vh;display:flex;flex-direction:column}
  header{display:flex;gap:12px;align-items:center;padding:8px 14px;background:var(--panel);border-bottom:1px solid var(--line);flex-wrap:wrap}
  header h1{font-size:13px;margin:0;font-weight:600;letter-spacing:.3px}
  header .sp{flex:1}
  select,button{font:inherit;background:#232833;color:var(--ink);border:1px solid var(--line);border-radius:6px;padding:5px 9px;cursor:pointer}
  button:hover{border-color:var(--accent)}
  button.on{background:var(--accent);color:#0b1120;border-color:var(--accent)}
  .wrap{flex:1;display:flex;min-height:0}
  .side{width:420px;min-width:320px;display:flex;flex-direction:column;border-right:1px solid var(--line);background:var(--panel)}
  .pick{padding:8px 12px;border-bottom:1px solid var(--line);font-size:12px;color:var(--muted);min-height:58px}
  .pick b{color:var(--ink)}
  .pick code{color:var(--pink);font-family:ui-monospace,Menlo,Consolas,monospace;cursor:pointer}
  .pick .box{color:var(--accent);font-family:ui-monospace,monospace}
  textarea{flex:1;width:100%;border:0;background:#0b0d10;color:#d6e2ff;font:12.5px/1.5 ui-monospace,Menlo,Consolas,monospace;padding:12px;resize:none;outline:none}
  .foot{display:flex;gap:8px;padding:8px 12px;border-top:1px solid var(--line)}
  .prev{flex:1;display:flex;flex-direction:column;min-width:0}
  .prevbar{padding:6px 12px;color:var(--muted);font-size:12px;border-bottom:1px solid var(--line);background:var(--panel)}
  iframe{flex:1;width:100%;border:0;background:#15171a}
  .hint{color:var(--muted);font-size:11.5px}
</style>
</head>
<body>
<header>
  <h1>W34 Module Sandbox</h1>
  <select id="module"><?php foreach ($mods as $m) { $sel = $m === $module ? ' selected' : ''; echo '<option' . $sel . '>' . htmlspecialchars($m) . '</option>'; } ?></select>
  <button id="theme" data-theme="<?php echo $theme; ?>">Theme: <?php echo $theme; ?></button>
  <button id="grid">Grid overlay</button>
  <button id="reload">Reload</button>
  <span class="sp"></span>
  <span class="hint">Click any element in the preview to target it &middot; edits apply live</span>
</header>
<div class="wrap">
  <div class="side">
    <div class="pick" id="pick">Click an element in the preview&hellip;</div>
    <textarea id="css" spellcheck="false" placeholder="/* Live CSS — applies instantly to the preview.
   Click an element to insert a rule stub. Paste into css/modules/<module>.css when happy. */"></textarea>
    <div class="foot">
      <button id="copy">Copy CSS</button>
      <button id="clear">Clear</button>
      <span class="hint" id="status"></span>
    </div>
  </div>
  <div class="prev">
    <div class="prevbar" id="prevbar"></div>
    <iframe id="frame"></iframe>
  </div>
</div>
<script>
(function () {
  var base = <?php echo json_encode($base); ?>;
  var moduleSel = document.getElementById('module');
  var themeBtn  = document.getElementById('theme');
  var gridBtn   = document.getElementById('grid');
  var frame     = document.getElementById('frame');
  var css       = document.getElementById('css');
  var pick      = document.getElementById('pick');
  var prevbar   = document.getElementById('prevbar');
  var status    = document.getElementById('status');
  var gridOn    = false;

  function theme(){ return themeBtn.dataset.theme; }
  function frameSrc(){
    return base + '/sandbox.php?frame=1&module=' + encodeURIComponent(moduleSel.value) + '&theme=' + theme() + '&t=' + Date.now();
  }
  function load(){ frame.src = frameSrc(); prevbar.textContent = moduleSel.value + '  ·  ' + theme(); }

  // Build a stable-ish selector for an element (tag + classes, scoped to its .mod-* ancestor).
  function selectorFor(el){
    if (!el || el.nodeType !== 1) return '';
    var tag = el.tagName.toLowerCase();
    var cls = (el.className && typeof el.className === 'string')
      ? '.' + el.className.trim().split(/\s+/).filter(Boolean).join('.') : '';
    var self = cls ? (tag.match(/^(div|span|a|b)$/) ? cls : tag + cls) : tag;
    var mod = el.closest && el.closest('[class*="mod-"]');
    if (mod && mod !== el) {
      var mc = ('' + mod.className).split(/\s+/).filter(function(c){return c.indexOf('mod-')===0;})[0];
      if (mc) return '.' + mc + ' ' + self;
    }
    return self;
  }

  function applyLive(){
    try {
      var d = frame.contentDocument; if (!d) return;
      var s = d.getElementById('liveoverride'); if (s) s.textContent = css.value;
    } catch(e){}
  }

  function wireFrame(){
    var d;
    try { d = frame.contentDocument; } catch(e){ return; }
    if (!d) return;
    applyLive();
    // grid overlay state
    var ov = d.getElementById('sbxov'); if (ov) ov.style.display = gridOn ? 'block' : 'none';
    // element picker
    var last;
    d.addEventListener('click', function(ev){
      var el = ev.target; ev.preventDefault(); ev.stopPropagation();
      if (last) last.classList.remove('sbx-hl');
      el.classList.add('sbx-hl'); last = el;
      var b = el.getBoundingClientRect();
      var mod = el.closest && el.closest('[class*="mod-"]');
      var mb = mod ? mod.getBoundingClientRect() : {left:0, top:0};
      var sel = selectorFor(el);
      var rx = Math.round(b.left - mb.left), ry = Math.round(b.top - mb.top);
      pick.innerHTML = '<b>selector</b> <code id="ins" title="click to insert a rule stub">' + sel + '</code><br>'
        + '<span class="box">x:' + rx + ' y:' + ry + '  ' + Math.round(b.width) + '&times;' + Math.round(b.height)
        + '  ·  pos:' + getComputedStyle(el).position + '</span>';
      d0('ins', sel);
    }, true);
  }
  function d0(id, sel){
    var c = document.getElementById(id); if (!c) return;
    c.onclick = function(){
      var stub = sel + ' {\n  \n}\n';
      var at = css.selectionStart || css.value.length;
      css.value = css.value.slice(0, at) + stub + css.value.slice(at);
      css.focus(); css.selectionStart = css.selectionEnd = at + sel.length + 3;
      applyLive();
    };
  }

  moduleSel.onchange = load;
  themeBtn.onclick = function(){ themeBtn.dataset.theme = theme()==='dark'?'light':'dark'; themeBtn.textContent='Theme: '+theme(); load(); };
  gridBtn.onclick  = function(){ gridOn=!gridOn; gridBtn.classList.toggle('on',gridOn); wireFrame(); };
  document.getElementById('reload').onclick = load;
  css.addEventListener('input', applyLive);
  document.getElementById('copy').onclick  = function(){ navigator.clipboard.writeText(css.value); status.textContent='copied ✓'; setTimeout(function(){status.textContent='';},1200); };
  document.getElementById('clear').onclick = function(){ css.value=''; applyLive(); };
  frame.addEventListener('load', wireFrame);
  load();
})();
</script>
</body>
</html>
