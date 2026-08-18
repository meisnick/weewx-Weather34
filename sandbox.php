<?php
/* sandbox.php — visual module editor (DEV ONLY).
 *
 * Renders ONE Weather34 module authentically (real framework/main/module CSS +
 * the real card shell, fetched over HTTP so it matches the dashboard exactly)
 * inside an iframe. Click an element to select it, then adjust it with controls
 * (drag to move, sliders for size/spacing, colour pickers) — the tool writes the
 * CSS for you. Instant preview, no deploy, no browser cache.
 *
 * Kills the three slow-loop bottlenecks: deploy round-trip, cache desync, and
 * pixel-guessing. Tune here, then Copy CSS into css/modules/<x>.css.
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
      .sbx-ov{position:fixed;inset:0;pointer-events:none;z-index:99999;display:none;
        background-image:linear-gradient(rgba(120,170,255,.18) 1px,transparent 1px),linear-gradient(90deg,rgba(120,170,255,.18) 1px,transparent 1px);
        background-size:10px 10px}
      .sbx-hl{outline:1px dashed #ff5bd0 !important;outline-offset:0;cursor:move}</style>';
    echo '</head><body>';
    if ($isTop) {
        echo '<div class="weather34box-toparea" style="width:250px;display:block">'
           . '<div class="weather34box" data-module="' . htmlspecialchars($module) . '">'
           . '<div class="title">&#9432; SANDBOX</div>'
           . '<div class="value"><div id="sbx">' . $frag . '</div></div></div></div>';
    } else {
        echo '<div class="weather-container" style="--w34-grid-cols:1;width:340px;display:block">'
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
  header{display:flex;gap:10px;align-items:center;padding:8px 14px;background:var(--panel);border-bottom:1px solid var(--line);flex-wrap:wrap}
  header h1{font-size:13px;margin:0;font-weight:600;letter-spacing:.3px}
  header .sp{flex:1}
  select,button{font:inherit;background:#232833;color:var(--ink);border:1px solid var(--line);border-radius:6px;padding:5px 9px;cursor:pointer}
  button:hover{border-color:var(--accent)} button.on{background:var(--accent);color:#0b1120;border-color:var(--accent)}
  .wrap{flex:1;display:flex;min-height:0}
  .side{width:340px;min-width:300px;display:flex;flex-direction:column;border-right:1px solid var(--line);background:var(--panel)}
  .sel{padding:8px 12px;border-bottom:1px solid var(--line);font-size:12px;color:var(--muted)}
  .sel code{color:var(--pink);font-family:ui-monospace,Menlo,Consolas,monospace} .sel .box{color:var(--accent);font-family:ui-monospace,monospace}
  .panel{padding:6px 12px 10px;overflow:auto;flex:1}
  .panel.empty{color:var(--muted);display:flex;align-items:center;justify-content:center;text-align:center;padding:24px}
  .grp{margin:10px 0 4px;font-size:10.5px;letter-spacing:.6px;text-transform:uppercase;color:var(--muted)}
  .row{display:grid;grid-template-columns:52px 1fr 58px;gap:8px;align-items:center;margin:6px 0}
  .row label{font-size:12px;color:var(--muted)}
  .row input[type=range]{width:100%}
  .row input[type=number]{width:100%;background:#0b0d10;color:#d6e2ff;border:1px solid var(--line);border-radius:5px;padding:3px 5px;font:12px ui-monospace,monospace}
  .row input[type=color]{width:100%;height:26px;background:#0b0d10;border:1px solid var(--line);border-radius:5px;padding:1px;cursor:pointer}
  .cssout{border-top:1px solid var(--line)}
  .cssout .lbl{padding:6px 12px 0;font-size:10.5px;text-transform:uppercase;letter-spacing:.6px;color:var(--muted)}
  textarea{width:100%;height:150px;border:0;background:#0b0d10;color:#d6e2ff;font:12px/1.5 ui-monospace,Menlo,Consolas,monospace;padding:10px 12px;resize:none;outline:none}
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
  <button id="grid">Grid</button>
  <button id="reload">Reload</button>
  <span class="sp"></span>
  <span class="hint">Click an element &rarr; adjust with the controls (or drag it in the preview)</span>
</header>
<div class="wrap">
  <div class="side">
    <div class="sel" id="sel">No element selected</div>
    <div class="panel empty" id="panel">Click any element in the preview to edit it.</div>
    <div class="cssout">
      <div class="lbl">Generated CSS</div>
      <textarea id="css" readonly spellcheck="false"></textarea>
    </div>
    <div class="foot">
      <button id="copy">Copy CSS</button>
      <button id="reset">Reset element</button>
      <button id="clear">Clear all</button>
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
  var $ = function(id){ return document.getElementById(id); };
  var moduleSel=$('module'), themeBtn=$('theme'), gridBtn=$('grid'), frame=$('frame'),
      css=$('css'), selBar=$('sel'), panel=$('panel'), prevbar=$('prevbar'), status=$('status');
  var gridOn=false, curSel=null, curEl=null;
  var rules={};                       // selector -> { prop: value }  (source of truth)

  // control definitions: [cssprop, label, kind, min, max, step, unit]
  var CTRLS = {
    move: [['margin-left','X','num',-120,320,1,'px'], ['margin-top','Y','num',-120,320,1,'px']],
    size: [['width','W','num',0,340,1,'px'], ['height','H','num',0,240,1,'px']],
    text: [['font-size','Font','num',5,60,0.5,'px']],
    color:[['color','Text','color'], ['background-color','Fill','color']]
  };

  function theme(){ return themeBtn.dataset.theme; }
  function frameSrc(){ return base+'/sandbox.php?frame=1&module='+encodeURIComponent(moduleSel.value)+'&theme='+theme()+'&t='+Date.now(); }
  function load(){ curSel=null; curEl=null; frame.src=frameSrc(); prevbar.textContent=moduleSel.value+'  ·  '+theme(); }

  function serialize(){
    var out='';
    for (var s in rules){ var props=rules[s]; var keys=Object.keys(props); if(!keys.length) continue;
      out+=s+' {\n'; keys.forEach(function(p){ out+='  '+p+': '+props[p]+' !important;\n'; }); out+='}\n\n'; }
    css.value=out; applyLive();
  }
  function applyLive(){ try{ var d=frame.contentDocument; var st=d&&d.getElementById('liveoverride'); if(st) st.textContent=css.value; }catch(e){} }
  function setProp(sel,prop,val){ (rules[sel]=rules[sel]||{})[prop]=val; serialize(); }
  function getProp(sel,prop){ return rules[sel]&&rules[sel][prop]; }

  function selectorFor(el){
    if(!el||el.nodeType!==1) return '';
    var tag=el.tagName.toLowerCase();
    var cls=(el.className&&typeof el.className==='string')?'.'+el.className.trim().split(/\s+/).filter(Boolean).join('.'):'';
    var self=cls?(tag.match(/^(div|span|a|b)$/)?cls:tag+cls):tag;
    var mod=el.closest&&el.closest('[class*="mod-"]');
    if(mod&&mod!==el){ var mc=(''+mod.className).split(/\s+/).filter(function(c){return c.indexOf('mod-')===0;})[0]; if(mc) return '.'+mc+' '+self; }
    return self;
  }
  function rgb2hex(c){ var m=(c||'').match(/(\d+),\s*(\d+),\s*(\d+)/); if(!m) return '#000000';
    return '#'+[1,2,3].map(function(i){return ('0'+parseInt(m[i]).toString(16)).slice(-2);}).join(''); }
  function px(v){ var n=parseFloat(v); return isNaN(n)?0:Math.round(n*10)/10; }

  function buildPanel(){
    if(!curEl){ panel.className='panel empty'; panel.textContent='Click any element in the preview to edit it.'; return; }
    panel.className='panel'; panel.innerHTML='';
    var cs=frame.contentWindow.getComputedStyle(curEl);
    Object.keys(CTRLS).forEach(function(grp){
      var h=document.createElement('div'); h.className='grp'; h.textContent=grp; panel.appendChild(h);
      CTRLS[grp].forEach(function(def){
        var prop=def[0], label=def[1], kind=def[2];
        var row=document.createElement('div'); row.className='row';
        var lab=document.createElement('label'); lab.textContent=label; row.appendChild(lab);
        if(kind==='color'){
          var stored=getProp(curSel,prop);
          var val=stored?stored:rgb2hex(cs[prop.replace('-color','Color').replace(/-([a-z])/g,function(_,c){return c.toUpperCase();})]||cs.getPropertyValue(prop));
          var ci=document.createElement('input'); ci.type='color'; ci.value=/^#/.test(val)?val:rgb2hex(val);
          ci.oninput=function(){ setProp(curSel,prop,ci.value); };
          var spacer=document.createElement('span'); row.appendChild(ci); row.appendChild(spacer);
        } else {
          var cur=getProp(curSel,prop); var num=cur!=null?px(cur):px(cs.getPropertyValue(prop));
          var rng=document.createElement('input'); rng.type='range'; rng.min=def[3]; rng.max=def[4]; rng.step=def[5]; rng.value=num;
          var nin=document.createElement('input'); nin.type='number'; nin.step=def[5]; nin.value=num;
          function push(v){ rng.value=v; nin.value=v; setProp(curSel,prop,v+def[6]); }
          rng.oninput=function(){ push(parseFloat(rng.value)); };
          nin.oninput=function(){ push(parseFloat(nin.value)); };
          row.appendChild(rng); row.appendChild(nin);
        }
        panel.appendChild(row);
      });
    });
  }

  function select(el){
    if(curEl) curEl.classList.remove('sbx-hl');
    curEl=el; curSel=selectorFor(el); el.classList.add('sbx-hl');
    var mod=el.closest&&el.closest('[class*="mod-"]'); var b=el.getBoundingClientRect();
    var mb=mod?mod.getBoundingClientRect():{left:0,top:0};
    selBar.innerHTML='<code>'+curSel+'</code> &nbsp; <span class="box">x:'+Math.round(b.left-mb.left)+' y:'+Math.round(b.top-mb.top)+' '+Math.round(b.width)+'&times;'+Math.round(b.height)+' · '+frame.contentWindow.getComputedStyle(el).position+'</span>';
    buildPanel();
  }

  var pickParam=(location.search.match(/[?&]pick=([^&]+)/)||[])[1];
  function wireFrame(){
    var d,w; try{ d=frame.contentDocument; w=frame.contentWindow; }catch(e){ return; }
    if(!d) return; applyLive();
    var ov=d.getElementById('sbxov'); if(ov) ov.style.display=gridOn?'block':'none';
    if(pickParam && !curEl){ try{ var pe=d.querySelector(decodeURIComponent(pickParam)); if(pe) select(pe); }catch(e){} }
    var drag=null;
    d.addEventListener('mousedown', function(ev){
      var el=ev.target; ev.preventDefault();
      if(curEl && (el===curEl)){                       // drag the already-selected element
        var cs=w.getComputedStyle(curEl);
        drag={sx:ev.clientX, sy:ev.clientY, ml:px(getProp(curSel,'margin-left')!=null?getProp(curSel,'margin-left'):cs.marginLeft), mt:px(getProp(curSel,'margin-top')!=null?getProp(curSel,'margin-top'):cs.marginTop)};
      } else { select(el); }
    }, true);
    d.addEventListener('mousemove', function(ev){
      if(!drag) return;
      var nx=Math.round(drag.ml+(ev.clientX-drag.sx)), ny=Math.round(drag.mt+(ev.clientY-drag.sy));
      rules[curSel]=rules[curSel]||{}; rules[curSel]['margin-left']=nx+'px'; rules[curSel]['margin-top']=ny+'px'; serialize();
      buildPanel();
    }, true);
    d.addEventListener('mouseup', function(){ drag=null; }, true);
  }

  moduleSel.onchange=load;
  themeBtn.onclick=function(){ themeBtn.dataset.theme=theme()==='dark'?'light':'dark'; themeBtn.textContent='Theme: '+theme(); load(); };
  gridBtn.onclick=function(){ gridOn=!gridOn; gridBtn.classList.toggle('on',gridOn); wireFrame(); };
  $('reload').onclick=load;
  $('copy').onclick=function(){ navigator.clipboard.writeText(css.value); status.textContent='copied ✓'; setTimeout(function(){status.textContent='';},1200); };
  $('reset').onclick=function(){ if(curSel){ delete rules[curSel]; serialize(); buildPanel(); } };
  $('clear').onclick=function(){ rules={}; serialize(); buildPanel(); };
  frame.addEventListener('load', wireFrame);
  load();
})();
</script>
</body>
</html>
