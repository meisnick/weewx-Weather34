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
    /* Load EXACTLY what index.php loads — framework.base + framework.$theme + the
     * modular css. The legacy monolith css/main.*.css is deliberately NOT loaded:
     * the modularized dashboard doesn't use it, and its stale .mod-* rules (e.g.
     * .weather34-barometerruler{margin-top:62}) would leak in and make the preview
     * lie about where things land on the real page. */
    echo '<link rel="stylesheet" href="css/framework.base.css?t=' . time() . '">';
    echo '<link rel="stylesheet" href="css/framework.' . $theme . '.css?t=' . time() . '">';
    foreach (glob('css/modules/*.css') as $s) {
        if (basename($s) === 'modules.bundle.css') { continue; }
        echo '<link rel="stylesheet" href="' . $s . '?t=' . time() . '">';
    }
    echo '<style id="liveoverride"></style>';
    echo '<style>html,body{margin:0}body{background:var(--page-bg,#15171a);display:flex;justify-content:center;padding:26px 10px}
      .sbx-ov{position:fixed;inset:0;pointer-events:none;z-index:99999;display:none;
        background-image:linear-gradient(rgba(120,170,255,.18) 1px,transparent 1px),linear-gradient(90deg,rgba(120,170,255,.18) 1px,transparent 1px);
        background-size:10px 10px}
      .sbx-hl{outline:1px dashed #ff5bd0 !important;outline-offset:0;cursor:move}
      .sbx-hidden{visibility:hidden !important}</style>';
    echo '</head><body>';
    if ($isTop) {
        echo '<div class="weather34box-toparea" style="width:250px;display:block">'
           . '<div class="weather34box" data-module="' . htmlspecialchars($module) . '">'
           . '<div class="title">&#9432; SANDBOX</div>'
           . '<div class="value"><div id="sbx">' . $frag . '</div></div></div></div>';
    } else {
        echo '<div class="weather-container" style="--w34-grid-cols:1;width:316px;display:block">'
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
  .arrange{display:flex;flex-wrap:wrap;gap:6px;padding:8px 12px;border-bottom:1px solid var(--line);background:var(--panel)}
  .arrange button{flex:1 1 42%;padding:6px 8px;font-size:12px}
  .arrange button.act{background:var(--accent);color:#0b1120;border-color:var(--accent)}
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
  <span class="hint">Click &rarr; drag / nudge &larr;&uarr;&darr;&rarr; (Shift=10px) · <b>Alt-click</b> digs through stacked elements · Center &amp; Hide in the panel</span>
</header>
<div class="wrap">
  <div class="side">
    <div class="sel" id="sel">No element selected</div>
    <div class="arrange" id="arrange" style="display:none"></div>
    <div class="panel empty" id="panel">Click any element in the preview to edit it.</div>
    <div class="cssout">
      <div class="lbl">Generated CSS</div>
      <textarea id="css" readonly spellcheck="false"></textarea>
    </div>
    <div class="foot">
      <button id="copy">Copy CSS</button>
      <button id="reset">Reset element</button>
      <button id="clear">Clear all</button>
      <button id="showall">Show all</button>
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
  // Move uses left/top (with position:relative for non-absolute elements) instead of
  // margins: works on inline elements (vertical margin is ignored on inline boxes) and
  // never shoves sibling elements. setProp() adds position:relative automatically.
  var CTRLS = {
    move: [['left','X','num',-200,320,1,'px'], ['top','Y','num',-200,320,1,'px']],
    size: [['width','W','num',0,340,1,'px'], ['height','H','num',0,240,1,'px']],
    text: [['font-size','Font','num',5,60,0.5,'px']],
    color:[['color','Text','color'], ['background-color','Fill','color']]
  };

  function theme(){ return themeBtn.dataset.theme; }
  function frameSrc(){ return base+'/sandbox.php?frame=1&module='+encodeURIComponent(moduleSel.value)+'&theme='+theme()+'&t='+Date.now(); }
  function load(){ curSel=null; curEl=null; buildArrange(); frame.src=frameSrc(); prevbar.textContent=moduleSel.value+'  ·  '+theme(); }

  function serialize(){
    var out='';
    for (var s in rules){ var props=rules[s]; var keys=Object.keys(props); if(!keys.length) continue;
      out+=s+' {\n'; keys.forEach(function(p){ out+='  '+p+': '+props[p]+' !important;\n'; }); out+='}\n\n'; }
    css.value=out; applyLive();
  }
  function applyLive(){ try{ var d=frame.contentDocument; var st=d&&d.getElementById('liveoverride'); if(st) st.textContent=css.value; }catch(e){} }
  // absolute/fixed elements move via their own left/top; everything else via position:relative + left/top
  function moveRelative(el){ try{ var p=frame.contentWindow.getComputedStyle(el).position; return p!=='absolute'&&p!=='fixed'; }catch(e){ return true; } }
  function setProp(sel,prop,val){
    rules[sel]=rules[sel]||{};
    if((prop==='left'||prop==='top') && curEl && sel===curSel && moveRelative(curEl)) rules[sel]['position']='relative';
    rules[sel][prop]=val; serialize();
  }
  function getProp(sel,prop){ return rules[sel]&&rules[sel][prop]; }

  // current left/top offset base (honours stored rule, else computed used-value; static → 0)
  function baseOff(prop){ var v=getProp(curSel,prop); if(v!=null) return px(v);
    try{ return px(frame.contentWindow.getComputedStyle(curEl)[prop]); }catch(e){ return 0; } }
  function moveTo(l,t){
    rules[curSel]=rules[curSel]||{};
    if(moveRelative(curEl)) rules[curSel]['position']='relative';
    rules[curSel]['left']=Math.round(l)+'px'; rules[curSel]['top']=Math.round(t)+'px';
    serialize();
  }
  function nudge(dx,dy){ if(!curEl) return; moveTo(baseOff('left')+dx, baseOff('top')+dy); buildPanel(); }
  function onKey(ev){
    if(!curEl) return;
    if(/^(input|select|textarea)$/i.test((ev.target&&ev.target.tagName)||'')) return; // let controls handle their own arrows
    var s=ev.shiftKey?10:1, dx=0, dy=0;
    if(ev.key==='ArrowLeft') dx=-s; else if(ev.key==='ArrowRight') dx=s;
    else if(ev.key==='ArrowUp') dy=-s; else if(ev.key==='ArrowDown') dy=s; else return;
    ev.preventDefault(); nudge(dx,dy);
  }

  // ── arrange helpers ──────────────────────────────────────────────────────
  function moduleBoxOf(el){
    var m=el.closest&&el.closest('[class*="mod-"]');
    if(!m){ try{ m=frame.contentDocument.getElementById('sbx'); }catch(e){} }
    return m;
  }
  function selectable(e){
    if(!e||e.nodeType!==1) return false;
    var tag=e.tagName.toLowerCase();
    if(tag==='html'||tag==='body') return false;
    if(e.id==='sbx'||e.id==='sbxov') return false;
    if(e.classList && (e.classList.contains('weather-item')||e.classList.contains('weather-container')||
       e.classList.contains('weather34box')||e.classList.contains('weather34box-toparea')||
       e.classList.contains('title')||e.classList.contains('value')||e.classList.contains('moduletitle'))) return false;
    return true;
  }
  function center(axis){
    if(!curEl) return;
    var m=moduleBoxOf(curEl); if(!m) return;
    var b=curEl.getBoundingClientRect(), mb=m.getBoundingClientRect();
    var l=baseOff('left'), t=baseOff('top');
    if(axis==='h') l+=(mb.width-b.width)/2-(b.left-mb.left);
    if(axis==='v') t+=(mb.height-b.height)/2-(b.top-mb.top);
    moveTo(l,t); buildPanel();
  }
  function toggleHide(){ if(curEl) curEl.classList.toggle('sbx-hidden'); }
  function showAll(){ try{ var d=frame.contentDocument;
    Array.prototype.forEach.call(d.querySelectorAll('.sbx-hidden'),function(e){e.classList.remove('sbx-hidden');});
  }catch(e){} }
  function digUnder(){                                  // select the next element beneath the current one
    if(!curEl) return; var d=frame.contentDocument;
    var b=curEl.getBoundingClientRect();
    var stack=d.elementsFromPoint(b.left+b.width/2, b.top+b.height/2).filter(selectable);
    if(!stack.length) return;
    var i=stack.indexOf(curEl);
    select(stack[(i+1)%stack.length]);
  }

  function segFor(el){                                  // one selector segment for a node
    var tag=el.tagName.toLowerCase();
    var cls=(el.className&&typeof el.className==='string')?el.className.trim().split(/\s+/).filter(Boolean):[];
    if(cls.length) return (tag.match(/^(div|span|a|b|li)$/)?'':tag)+'.'+cls.join('.');
    return tag;
  }
  function isShellRoot(el){
    if(!el) return true;
    if(el.id==='sbx') return true;
    return el.classList && (el.classList.contains('weather-item')||el.classList.contains('weather-container')||
      el.classList.contains('weather34box')||el.classList.contains('weather34box-toparea'));
  }
  // Build the SHORTEST descendant selector that is unique inside the module. Walks up
  // adding ancestor segments until querySelectorAll matches exactly one element, so a
  // class-less tag like <valuetext> gets scoped to its nearest classed parent
  // (e.g. ".barometerorange valuetext") instead of the ambiguous ".mod-barometer valuetext".
  function selectorFor(el){
    if(!el||el.nodeType!==1) return '';
    var d=frame.contentDocument;
    var parts=[], node=el;
    while(node && node.nodeType===1 && !isShellRoot(node)){
      parts.unshift(segFor(node));
      var sel=parts.join(' ');
      try{ if(d.querySelectorAll(sel).length===1){
        // ensure it's anchored to a mod-* class for a clean paste target
        if(/(^|\s)\.mod-/.test(sel)) return sel;
        var mod=el.closest&&el.closest('[class*="mod-"]');
        var mc=mod&&(''+mod.className).split(/\s+/).filter(function(c){return c.indexOf('mod-')===0;})[0];
        return mc ? '.'+mc+' '+sel : sel;
      } }catch(e){}
      node=node.parentElement;
    }
    return parts.join(' ') || el.tagName.toLowerCase();
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

  function buildArrange(){                              // fixed toolbar (not buried in the scroll panel)
    var bar=$('arrange');
    if(!curEl){ bar.style.display='none'; bar.innerHTML=''; return; }
    bar.style.display='flex'; bar.innerHTML='';
    [['Center ⇔',function(){center('h');}], ['Center ⇕',function(){center('v');}],
     ['Hide',toggleHide], ['Under ⤵',digUnder]].forEach(function(bt){
      var btn=document.createElement('button'); btn.textContent=bt[0];
      btn.onclick=function(){ bt[1](); buildArrange(); };
      if(bt[0]==='Hide' && curEl.classList.contains('sbx-hidden')) btn.className='act';
      bar.appendChild(btn);
    });
  }

  function select(el){
    if(curEl) curEl.classList.remove('sbx-hl');
    curEl=el; curSel=selectorFor(el); el.classList.add('sbx-hl');
    var mod=el.closest&&el.closest('[class*="mod-"]'); var b=el.getBoundingClientRect();
    var mb=mod?mod.getBoundingClientRect():{left:0,top:0};
    selBar.innerHTML='<code>'+curSel+'</code> &nbsp; <span class="box">x:'+Math.round(b.left-mb.left)+' y:'+Math.round(b.top-mb.top)+' '+Math.round(b.width)+'&times;'+Math.round(b.height)+' · '+frame.contentWindow.getComputedStyle(el).position+'</span>';
    buildPanel(); buildArrange();
  }

  var pickParam=(location.search.match(/[?&]pick=([^&]+)/)||[])[1];
  function wireFrame(){
    var d,w; try{ d=frame.contentDocument; w=frame.contentWindow; }catch(e){ return; }
    if(!d) return; applyLive();
    var ov=d.getElementById('sbxov'); if(ov) ov.style.display=gridOn?'block':'none';
    if(pickParam && !curEl){ try{ var pe=d.querySelector(decodeURIComponent(pickParam)); if(pe) select(pe); }catch(e){} }
    // Swallow clicks so anchors / chart popups can't navigate the preview away.
    d.addEventListener('click', function(ev){ ev.preventDefault(); ev.stopPropagation(); }, true);
    d.addEventListener('dragstart', function(ev){ ev.preventDefault(); }, true);
    var drag=null;
    d.addEventListener('mousedown', function(ev){
      var el=ev.target; ev.preventDefault();
      if(ev.altKey){                                   // Alt-click: dig through the stack at this point
        var stk=d.elementsFromPoint(ev.clientX, ev.clientY).filter(selectable);
        if(stk.length){ var st=stk.indexOf(curEl); select(stk[(st+1)%stk.length]); }
        return;
      }
      if(curEl && (el===curEl)){                       // drag the already-selected element
        drag={sx:ev.clientX, sy:ev.clientY, l:baseOff('left'), t:baseOff('top')};
      } else { select(el); }
    }, true);
    d.addEventListener('mousemove', function(ev){
      if(!drag) return;
      moveTo(drag.l+(ev.clientX-drag.sx), drag.t+(ev.clientY-drag.sy));
      buildPanel();
    }, true);
    d.addEventListener('mouseup', function(){ drag=null; }, true);
    d.addEventListener('keydown', onKey);          // arrow-nudge when focus is in the preview
  }

  moduleSel.onchange=load;
  themeBtn.onclick=function(){ themeBtn.dataset.theme=theme()==='dark'?'light':'dark'; themeBtn.textContent='Theme: '+theme(); load(); };
  gridBtn.onclick=function(){ gridOn=!gridOn; gridBtn.classList.toggle('on',gridOn); wireFrame(); };
  $('reload').onclick=load;
  $('copy').onclick=function(){ navigator.clipboard.writeText(css.value); status.textContent='copied ✓'; setTimeout(function(){status.textContent='';},1200); };
  $('reset').onclick=function(){ if(curSel){ delete rules[curSel]; serialize(); buildPanel(); } };
  $('clear').onclick=function(){ rules={}; serialize(); buildPanel(); };
  $('showall').onclick=showAll;
  frame.addEventListener('load', wireFrame);
  document.addEventListener('keydown', onKey);     // arrow-nudge when focus is on the editor side
  load();
})();
</script>
</body>
</html>
