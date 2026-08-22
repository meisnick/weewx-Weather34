<?php
/* sandbox.php — visual module & Highcharts editor (DEV ONLY).
 *
 * Renders ONE Weather34 module or popout almanac authentically inside an iframe.
 * Supports live CSS adjustments and Highcharts parameter overrides (margins, spacing,
 * label offsets, legend) in real-time via iframe postMessage communication.
 *
 *   /weewx/weather34/sandbox.php               -> editor
 *   /weewx/weather34/sandbox.php?frame=1&module=barometer.php&theme=dark  -> preview
 */

$base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');           // /weewx/weather34
$module = preg_replace('/[^a-zA-Z0-9_.\-]/', '', $_GET['module'] ?? 'barometer.php');
$theme  = (($_GET['theme'] ?? 'dark') === 'light') ? 'light' : 'dark';
$isTop  = (strpos($module, 'top_') === 0);
$isPop  = (strpos($module, 'pop_') === 0);

/* ── FRAME MODE: one module / popout, styled exactly like dashboard ──────────── */
if (isset($_GET['frame'])) {
    $url  = 'http://127.0.0.1' . $base . '/' . $module;
    $ctx  = stream_context_create(['http' => ['timeout' => 10, 'header' => "User-Agent: sandbox\r\n"]]);
    $frag = @file_get_contents($url, false, $ctx);
    if ($frag === false) { $frag = '<div style="color:#c33;padding:20px">Could not load ' . htmlspecialchars($module) . '</div>'; }

    header('Content-Type: text/html; charset=utf-8');
    if ($isPop) {
        // Popouts are full standalone HTML documents. Inject liveoverride before </head> or <body>
        $inject = '<style id="liveoverride"></style>'
                . '<style>'
                . '.sbx-ov{position:fixed;inset:0;pointer-events:none;z-index:99999;display:none;'
                . 'background-image:linear-gradient(rgba(120,170,255,.18) 1px,transparent 1px),linear-gradient(90deg,rgba(120,170,255,.18) 1px,transparent 1px);'
                . 'background-size:10px 10px}'
                . '.sbx-hl{outline:1px dashed #ff5bd0 !important;outline-offset:0;cursor:move}'
                . '.sbx-hidden{visibility:hidden !important}</style>';
        $ov_div = '<div class="sbx-ov" id="sbxov"></div>';
        
        if (stripos($frag, '</head>') !== false) {
            $frag = str_ireplace('</head>', $inject . '</head>', $frag);
        } else {
            $frag = $inject . $frag;
        }
        if (stripos($frag, '</body>') !== false) {
            $frag = str_ireplace('</body>', $ov_div . '</body>', $frag);
        } else {
            $frag .= $ov_div;
        }
        echo $frag;
        exit;
    }

    echo '<!doctype html><html data-theme="' . $theme . '"><head><meta charset="utf-8">';
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
        echo '<div class="mod-box-topbar" style="width:250px;display:block">'
           . '<div class="mod-box" data-module="' . htmlspecialchars($module) . '">'
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
$top_mods = [];
foreach (glob('top_*.php') as $f) { if (strpos($f, 'top_year_helpers') === false) $top_mods[] = $f; }
sort($top_mods);

$card_mods = [];
foreach (['barometer.php','windspeeddirection.php','sun3.php','sun4.php','rainfall.php','temperaturein.php',
          'indoortemperature.php','moonphase.php','solaruv.php','currentconditionsw34.php','lightning34.php',
          'airqualitymodule.php','aurora_module.php','radar_module.php'] as $f) {
    if (file_exists($f)) $card_mods[] = $f;
}
sort($card_mods);

$pop_mods = [];
foreach (glob('pop_*.php') as $f) {
    // Exclude alerts / table only popups if desired, but keep all popouts
    $pop_mods[] = $f;
}
sort($pop_mods);

?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>W34 Module & Highcharts Sandbox</title>
<style>
  :root{--bg:#0f1115;--panel:#1a1d23;--line:#2a2e37;--ink:#e6e8ec;--muted:#8b93a1;--accent:#6ea8ff;--pink:#ff5bd0;--green:#4ecc80}
  *{box-sizing:border-box}
  body{margin:0;font:13px/1.45 ui-sans-serif,system-ui,Segoe UI,Roboto,sans-serif;background:var(--bg);color:var(--ink);height:100vh;display:flex;flex-direction:column}
  header{display:flex;gap:10px;align-items:center;padding:8px 14px;background:var(--panel);border-bottom:1px solid var(--line);flex-wrap:wrap}
  header h1{font-size:13px;margin:0;font-weight:600;letter-spacing:.3px}
  header .sp{flex:1}
  select,button{font:inherit;background:#232833;color:var(--ink);border:1px solid var(--line);border-radius:6px;padding:5px 9px;cursor:pointer}
  button:hover{border-color:var(--accent)} button.on{background:var(--accent);color:#0b1120;border-color:var(--accent)}
  .tab-nav{display:flex;border-bottom:1px solid var(--line);background:#14171d}
  .tab-btn{flex:1;padding:8px 12px;background:none;border:none;border-bottom:2px solid transparent;border-radius:0;color:var(--muted);font-weight:500;font-size:12px;cursor:pointer}
  .tab-btn:hover{color:var(--ink);border-color:transparent}
  .tab-btn.active{color:var(--accent);border-bottom-color:var(--accent);background:var(--panel)}
  .wrap{flex:1;display:flex;min-height:0}
  .side{width:360px;min-width:320px;display:flex;flex-direction:column;border-right:1px solid var(--line);background:var(--panel)}
  .tab-content{display:none;flex:1;flex-direction:column;min-height:0}
  .tab-content.active{display:flex}
  .sel{padding:8px 12px;border-bottom:1px solid var(--line);font-size:12px;color:var(--muted)}
  .sel code{color:var(--pink);font-family:ui-monospace,Menlo,Consolas,monospace} .sel .box{color:var(--accent);font-family:ui-monospace,monospace}
  .panel{padding:6px 12px 10px;overflow:auto;flex:1}
  .panel.empty{color:var(--muted);display:flex;align-items:center;justify-content:center;text-align:center;padding:24px}
  .grp{margin:10px 0 4px;font-size:10.5px;letter-spacing:.6px;text-transform:uppercase;color:var(--muted)}
  .row{display:grid;grid-template-columns:52px 1fr 58px;gap:8px;align-items:center;margin:6px 0}
  .row.hc-row{grid-template-columns:84px 1fr 52px;gap:6px;margin:5px 0}
  .row label{font-size:12px;color:var(--muted)}
  .row select{width:100%;background:#0b0d10;color:#d6e2ff;border:1px solid var(--line);border-radius:5px;padding:3px 5px;font:12px ui-sans-serif,sans-serif}
  .row .inline-val{font:12px ui-monospace,monospace;color:var(--muted);text-align:right}
  .row input[type=range]{width:100%}
  .row input[type=number]{width:100%;background:#0b0d10;color:#d6e2ff;border:1px solid var(--line);border-radius:5px;padding:3px 5px;font:12px ui-monospace,monospace}
  .row input[type=color]{width:100%;height:26px;background:#0b0d10;border:1px solid var(--line);border-radius:5px;padding:1px;cursor:pointer}
  .cssout{border-top:1px solid var(--line)}
  .cssout .lbl{padding:6px 12px 0;font-size:10.5px;text-transform:uppercase;letter-spacing:.6px;color:var(--muted)}
  textarea{width:100%;height:130px;border:0;background:#0b0d10;color:#d6e2ff;font:12px/1.5 ui-monospace,Menlo,Consolas,monospace;padding:10px 12px;resize:none;outline:none}
  .foot{display:flex;gap:8px;padding:8px 12px;border-top:1px solid var(--line);flex-wrap:wrap;align-items:center}
  .foot button.primary{background:#285da8;border-color:#3c7bd9;color:#fff}
  .foot button.primary:hover{background:#3575d3}
  .prev{flex:1;display:flex;flex-direction:column;min-width:0}
  .prevbar{padding:6px 12px;color:var(--muted);font-size:12px;border-bottom:1px solid var(--line);background:var(--panel)}
  iframe{flex:1;width:100%;border:0;background:#15171a; transition:all 0.3s} iframe.popout-mode{max-width:850px; margin:40px auto; height:calc(100% - 80px); flex:none; box-shadow:0 10px 40px rgba(0,0,0,0.5); border-radius:8px}
  .hint{color:var(--muted);font-size:11.5px}
  .arrange{display:flex;flex-wrap:wrap;gap:6px;padding:8px 12px;border-bottom:1px solid var(--line);background:var(--panel)}
  .arrange button{flex:1 1 42%;padding:6px 8px;font-size:12px}
  .arrange button.act{background:var(--accent);color:#0b1120;border-color:var(--accent)}
</style>
</head>
<body>
<header>
  <h1>W34 Sandbox</h1>
  <select id="module">
    <optgroup label="Dashboard Cards">
      <?php foreach ($card_mods as $m) { $sel = ($m === $module) ? ' selected' : ''; echo '<option value="' . htmlspecialchars($m) . '"' . $sel . '>' . htmlspecialchars($m) . '</option>'; } ?>
    </optgroup>
    <optgroup label="Top Modules">
      <?php foreach ($top_mods as $m) { $sel = ($m === $module) ? ' selected' : ''; echo '<option value="' . htmlspecialchars($m) . '"' . $sel . '>' . htmlspecialchars($m) . '</option>'; } ?>
    </optgroup>
    <optgroup label="Popouts & Almanacs">
      <?php foreach ($pop_mods as $m) { $sel = ($m === $module) ? ' selected' : ''; echo '<option value="' . htmlspecialchars($m) . '"' . $sel . '>' . htmlspecialchars($m) . '</option>'; } ?>
    </optgroup>
  </select>
  <button id="theme" data-theme="<?php echo $theme; ?>">Theme: <?php echo $theme; ?></button>
  <button id="grid">Grid</button>
  <button id="reload">Reload</button>
  <span class="sp"></span>
  <span class="hint">WYSIWYG CSS & Highcharts Live Control · Shift=10px · Instant postMessage re-render</span>
</header>
<div class="wrap">
  <div class="side">
    <div class="tab-nav">
      <button class="tab-btn active" id="tabbtn-css" onclick="switchTab('css')">CSS Layout</button>
      <button class="tab-btn" id="tabbtn-hc" onclick="switchTab('hc')">Highcharts Live</button>
    </div>

    <!-- TAB 1: CSS Visual Module Editor -->
    <div class="tab-content active" id="tabcontent-css">
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

    <!-- TAB 2: Highcharts Live Editor -->
    <div class="tab-content" id="tabcontent-hc">
      <div class="panel" id="hc-panel">
        <!-- Controls rendered by JS -->
      </div>
      <div class="cssout">
        <div class="lbl">Overrides JSON</div>
        <textarea id="hc-json" readonly spellcheck="false"></textarea>
      </div>
      <div class="foot">
        <button id="hc-save" class="primary">Save Overrides</button>
        <button id="hc-reset">Reset Highcharts</button>
        <span class="hint" id="hc-status"></span>
      </div>
    </div>
  </div>

  <div class="prev">
    <div class="prevbar" id="prevbar"></div>
    <iframe id="frame"></iframe>
  </div>
</div>

<script src="w34highcharts/scripts/sandbox-overrides.js?t=<?php echo time(); ?>"></script>
<script>
(function () {
  var base = <?php echo json_encode($base); ?>;
  var $ = function(id){ return document.getElementById(id); };
  var moduleSel=$('module'), themeBtn=$('theme'), gridBtn=$('grid'), frame=$('frame'),
      css=$('css'), selBar=$('sel'), panel=$('panel'), prevbar=$('prevbar'), status=$('status');
  var hcPanel=$('hc-panel'), hcJson=$('hc-json'), hcStatus=$('hc-status');
  var gridOn=false, curSel=null, curEl=null;
  var rules={};                       // selector -> { prop: value }  (source of truth for CSS)

  // Tab switching
  window.switchTab = function(t) {
    $('tabbtn-css').classList.toggle('active', t==='css');
    $('tabbtn-hc').classList.toggle('active', t==='hc');
    $('tabcontent-css').classList.toggle('active', t==='css');
    $('tabcontent-hc').classList.toggle('active', t==='hc');
  };

  // CSS control definitions: [cssprop, label, kind, min/options, max, step, unit]
  var CTRLS = {
    move: [['left','X','num',-200,320,1,'px'], ['top','Y','num',-200,320,1,'px']],
    size: [['width','W','num',0,500,1,'px'], ['height','H','num',0,500,1,'px']],
    padding: [['padding-top','Pad T','num',0,100,1,'px'], ['padding-right','Pad R','num',0,100,1,'px'],
              ['padding-bottom','Pad B','num',0,100,1,'px'], ['padding-left','Pad L','num',0,100,1,'px']],
    margin: [['margin-top','Mar T','num',-100,150,1,'px'], ['margin-right','Mar R','num',-100,150,1,'px'],
             ['margin-bottom','Mar B','num',-100,150,1,'px'], ['margin-left','Mar L','num',-100,150,1,'px']],
    layout: [['display','Display','select',['','block','inline-block','inline','flex','inline-flex','none']],
             ['position','Position','select',['','static','relative','absolute','fixed']]],
    flex: [['gap','Gap','num',0,100,1,'px'],
           ['align-items','Align','select',['','stretch','flex-start','center','flex-end','baseline']],
           ['justify-content','Justify','select',['','flex-start','center','flex-end','space-between','space-around','space-evenly']]],
    text: [['font-size','Font','num',5,60,0.5,'px'],
           ['line-height','Line H','num',0,80,1,'px'],
           ['text-align','Align','select',['','left','center','right']]],
    color:[['color','Text','color'], ['background-color','Fill','color']]
  };

  // Highcharts Config State
  var savedOverrides = window.w34HighchartsOverrides || {};
  var hcState = {
    containerHeight: 350,
    spacingTop: (savedOverrides.chart && savedOverrides.chart.spacingTop) || 10,
    spacingBottom: (savedOverrides.chart && savedOverrides.chart.spacingBottom) || 10,
    spacingLeft: (savedOverrides.chart && savedOverrides.chart.spacingLeft) || 10,
    spacingRight: (savedOverrides.chart && savedOverrides.chart.spacingRight) || 10,
    marginTop: (savedOverrides.chart && savedOverrides.chart.marginTop != null) ? savedOverrides.chart.marginTop : '',
    marginBottom: (savedOverrides.chart && savedOverrides.chart.marginBottom != null) ? savedOverrides.chart.marginBottom : '',
    legendMargin: (savedOverrides.legend && savedOverrides.legend.margin != null) ? savedOverrides.legend.margin : 25,
    legendY: (savedOverrides.legend && savedOverrides.legend.y != null) ? savedOverrides.legend.y : '',
    xAxisLabelY: (savedOverrides.xAxis && savedOverrides.xAxis[0] && savedOverrides.xAxis[0].labels && savedOverrides.xAxis[0].labels.y != null) ? savedOverrides.xAxis[0].labels.y : 20,
    yAxisLabelX: (savedOverrides.yAxis && savedOverrides.yAxis[0] && savedOverrides.yAxis[0].labels && savedOverrides.yAxis[0].labels.x != null) ? savedOverrides.yAxis[0].labels.x : -8
  };

  function theme(){ return themeBtn.dataset.theme; }
  function frameSrc(){ return base+'/sandbox.php?frame=1&module='+encodeURIComponent(moduleSel.value)+'&theme='+theme()+'&t='+Date.now(); }
  function load(){ frame.classList.toggle('popout-mode', moduleSel.value.startsWith('pop_')); curSel=null; curEl=null; buildArrange(); frame.src=frameSrc(); prevbar.textContent=moduleSel.value+'  ·  '+theme(); }

  /* ── CSS SERIALIZE & LIVE APPLY ────────────────────────────────────────── */
  function serialize(){
    var out='';
    for (var s in rules){ var props=rules[s]; var keys=Object.keys(props); if(!keys.length) continue;
      out+=s+' {\n'; keys.forEach(function(p){ out+='  '+p+': '+props[p]+' !important;\n'; }); out+='}\n\n'; }
    css.value=out; applyLive();
  }
  function applyLive(){
    try{
      var d=frame.contentDocument;
      var st=d&&d.getElementById('liveoverride');
      if(st) st.textContent=css.value;
      // Also check if preview has nested iframes for charts
      if (d) {
        var iframes = d.querySelectorAll('iframe');
        iframes.forEach(function(ifr) {
          try {
            var idoc = ifr.contentDocument;
            var ist = idoc && idoc.getElementById('liveoverride');
            if (!ist && idoc && idoc.head) {
              ist = idoc.createElement('style');
              ist.id = 'liveoverride';
              idoc.head.appendChild(ist);
            }
            if (ist) ist.textContent = css.value;
          } catch(e){}
        });
      }
    }catch(e){}
  }

  function moveRelative(el){ try{ var p=frame.contentWindow.getComputedStyle(el).position; return p!=='absolute'&&p!=='fixed'; }catch(e){ return true; } }
  function setProp(sel,prop,val){
    rules[sel]=rules[sel]||{};
    if((prop==='left'||prop==='top') && curEl && sel===curSel && moveRelative(curEl)) rules[sel]['position']='relative';
    if(val==='' || val==null) {
      delete rules[sel][prop];
      if(!Object.keys(rules[sel]).length) delete rules[sel];
    } else {
      rules[sel][prop]=val;
    }
    serialize();
  }
  function getProp(sel,prop){ return rules[sel]&&rules[sel][prop]; }

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
    if(/^(input|select|textarea)$/i.test((ev.target&&ev.target.tagName)||'')) return;
    var s=ev.shiftKey?10:1, dx=0, dy=0;
    if(ev.key==='ArrowLeft') dx=-s; else if(ev.key==='ArrowRight') dx=s;
    else if(ev.key==='ArrowUp') dy=-s; else if(ev.key==='ArrowDown') dy=s; else return;
    ev.preventDefault(); nudge(dx,dy);
  }

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
       e.classList.contains('mod-box')||e.classList.contains('mod-box-topbar')||
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
  function digUnder(){
    if(!curEl) return; var d=frame.contentDocument;
    var b=curEl.getBoundingClientRect();
    var stack=d.elementsFromPoint(b.left+b.width/2, b.top+b.height/2).filter(selectable);
    if(!stack.length) return;
    var i=stack.indexOf(curEl);
    select(stack[(i+1)%stack.length]);
  }

  function segFor(el){
    var tag=el.tagName.toLowerCase();
    var cls=(el.className&&typeof el.className==='string')?el.className.trim().split(/\s+/).filter(Boolean):[];
    if(cls.length) return (tag.match(/^(div|span|a|b|li)$/)?'':tag)+'.'+cls.join('.');
    return tag;
  }
  function isShellRoot(el){
    if(!el) return true;
    if(el.id==='sbx') return true;
    return el.classList && (el.classList.contains('weather-item')||el.classList.contains('weather-container')||
      el.classList.contains('mod-box')||el.classList.contains('mod-box-topbar'));
  }
  function selectorFor(el){
    if(!el||el.nodeType!==1) return '';
    var d=frame.contentDocument;
    var parts=[], node=el;
    while(node && node.nodeType===1 && !isShellRoot(node)){
      parts.unshift(segFor(node));
      var sel=parts.join(' ');
      try{ if(d.querySelectorAll(sel).length===1){
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
        } else if(kind==='select'){
          var stored=getProp(curSel,prop);
          var computed=cs.getPropertyValue(prop)||'';
          var selBox=document.createElement('select');
          var options=def[3];
          options.forEach(function(opt){
            var o=document.createElement('option');
            o.value=opt;
            o.textContent=opt==='' ? '(default / inherit)' : opt;
            if(stored!=null ? stored===opt : (opt!=='' && computed===opt)){
              o.selected=true;
            }
            selBox.appendChild(o);
          });
          selBox.onchange=function(){ setProp(curSel,prop,selBox.value); };
          var valLabel=document.createElement('span'); valLabel.className='inline-val';
          valLabel.textContent=stored?stored:computed;
          selBox.addEventListener('change',function(){ valLabel.textContent=selBox.value||computed; });
          row.appendChild(selBox); row.appendChild(valLabel);
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

  function buildArrange(){
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

  /* ── HIGHCHARTS LIVE ENGINE & POSTMESSAGE ─────────────────────────────── */
  function sendHighchartsUpdate(config) {
    if (!frame || !frame.contentWindow) return;
    // Dispatch to main preview window
    frame.contentWindow.postMessage({ type: 'HIGHCHARTS_UPDATE', config: config }, '*');
    // Also dispatch into any child iframes inside the preview
    try {
      var d = frame.contentDocument;
      if (d) {
        var iframes = d.querySelectorAll('iframe');
        iframes.forEach(function(ifr) {
          if (ifr.contentWindow) {
            ifr.contentWindow.postMessage({ type: 'HIGHCHARTS_UPDATE', config: config }, '*');
          }
        });
      }
    } catch(e){}
  }

  function buildHighchartsPayload() {
    var cfg = {};
    var chart = {};
    if (hcState.spacingTop !== '') chart.spacingTop = Number(hcState.spacingTop);
    if (hcState.spacingBottom !== '') chart.spacingBottom = Number(hcState.spacingBottom);
    if (hcState.spacingLeft !== '') chart.spacingLeft = Number(hcState.spacingLeft);
    if (hcState.spacingRight !== '') chart.spacingRight = Number(hcState.spacingRight);
    if (hcState.marginTop !== '') chart.marginTop = Number(hcState.marginTop);
    if (hcState.marginBottom !== '') chart.marginBottom = Number(hcState.marginBottom);
    if (Object.keys(chart).length) cfg.chart = chart;

    var legend = {};
    if (hcState.legendMargin !== '') legend.margin = Number(hcState.legendMargin);
    if (hcState.legendY !== '') legend.y = Number(hcState.legendY);
    if (Object.keys(legend).length) cfg.legend = legend;

    if (hcState.xAxisLabelY !== '') {
      cfg.xAxis = [{ labels: { y: Number(hcState.xAxisLabelY) } }, { labels: { y: Number(hcState.xAxisLabelY) } }];
    }
    if (hcState.yAxisLabelX !== '') {
      cfg.yAxis = [{ labels: { x: Number(hcState.yAxisLabelX) } }];
    }
    return cfg;
  }

  function syncHighcharts() {
    // 1. Container Height CSS rule
    var h = parseInt(hcState.containerHeight, 10);
    if (h > 0) {
      rules['.grid1 > articlegraph'] = rules['.grid1 > articlegraph'] || {};
      rules['.grid1 > articlegraph']['height'] = h + 'px';
      serialize();
    }

    // 2. Highcharts JS update
    var payload = buildHighchartsPayload();
    hcJson.value = JSON.stringify(payload, null, 2);
    sendHighchartsUpdate(payload);
  }

  var HC_DEFS = [
    { section: 'Container' },
    { key: 'containerHeight', label: 'Chart Height', min: 150, max: 800, step: 10, unit: 'px' },
    { section: 'Spacing' },
    { key: 'spacingTop', label: 'Spacing Top', min: 0, max: 100, step: 1, unit: 'px' },
    { key: 'spacingBottom', label: 'Spacing Btm', min: 0, max: 100, step: 1, unit: 'px' },
    { key: 'spacingLeft', label: 'Spacing Left', min: 0, max: 100, step: 1, unit: 'px' },
    { key: 'spacingRight', label: 'Spacing Right', min: 0, max: 100, step: 1, unit: 'px' },
    { section: 'Margins (Explicit)' },
    { key: 'marginTop', label: 'Margin Top', min: 0, max: 150, step: 1, unit: 'px', allowEmpty: true },
    { key: 'marginBottom', label: 'Margin Btm', min: 0, max: 150, step: 1, unit: 'px', allowEmpty: true },
    { section: 'Legend' },
    { key: 'legendMargin', label: 'Legend Margin', min: 0, max: 100, step: 1, unit: 'px' },
    { key: 'legendY', label: 'Legend Y Offset', min: -50, max: 50, step: 1, unit: 'px', allowEmpty: true },
    { section: 'Axes Offsets' },
    { key: 'xAxisLabelY', label: 'X-Axis Label Y', min: -30, max: 60, step: 1, unit: 'px' },
    { key: 'yAxisLabelX', label: 'Y-Axis Label X', min: -50, max: 50, step: 1, unit: 'px' }
  ];

  function buildHighchartsPanel() {
    hcPanel.innerHTML = '';
    HC_DEFS.forEach(function(item) {
      if (item.section) {
        var h = document.createElement('div'); h.className = 'grp'; h.textContent = item.section; hcPanel.appendChild(h);
        return;
      }
      var row = document.createElement('div'); row.className = 'row hc-row';
      var lab = document.createElement('label'); lab.textContent = item.label; row.appendChild(lab);

      var curVal = hcState[item.key] !== '' ? hcState[item.key] : (item.allowEmpty ? 0 : item.min);
      var rng = document.createElement('input'); rng.type = 'range'; rng.min = item.min; rng.max = item.max; rng.step = item.step; rng.value = curVal;
      var nin = document.createElement('input'); nin.type = 'number'; nin.step = item.step; nin.value = hcState[item.key];

      function onUpdate(v) {
        hcState[item.key] = v;
        rng.value = v !== '' ? v : 0;
        nin.value = v;
        syncHighcharts();
      }

      rng.oninput = function() { onUpdate(parseFloat(rng.value)); };
      nin.oninput = function() { onUpdate(nin.value !== '' ? parseFloat(nin.value) : ''); };

      row.appendChild(rng); row.appendChild(nin);
      hcPanel.appendChild(row);
    });
    hcJson.value = JSON.stringify(buildHighchartsPayload(), null, 2);
  }

  function wireFrame(){
    var d,w; try{ d=frame.contentDocument; w=frame.contentWindow; }catch(e){ return; }
    if(!d) return; applyLive();
    syncHighcharts();

    var ov=d.getElementById('sbxov'); if(ov) ov.style.display=gridOn?'block':'none';
    if(pickParam && !curEl){ try{ var pe=d.querySelector(decodeURIComponent(pickParam)); if(pe) select(pe); }catch(e){} }
    
    // Swallow clicks inside preview so navigation doesn't break editor
    d.addEventListener('click', function(ev){
      // Allow Highcharts range selector buttons to be clicked
      if (ev.target && ev.target.closest && ev.target.closest('.highcharts-range-selector-buttons')) return;
      ev.preventDefault(); ev.stopPropagation();
    }, true);
    d.addEventListener('dragstart', function(ev){ ev.preventDefault(); }, true);
    
    var drag=null;
    d.addEventListener('mousedown', function(ev){
      var el=ev.target;
      if (el && el.closest && (el.closest('.highcharts-container') || el.closest('.highcharts-range-selector-buttons'))) {
        return; // Don't block Highcharts internal interactive elements
      }
      ev.preventDefault();
      if(ev.altKey){
        var stk=d.elementsFromPoint(ev.clientX, ev.clientY).filter(selectable);
        if(stk.length){ var st=stk.indexOf(curEl); select(stk[(st+1)%stk.length]); }
        return;
      }
      if(curEl && (el===curEl)){
        drag={sx:ev.clientX, sy:ev.clientY, l:baseOff('left'), t:baseOff('top')};
      } else { select(el); }
    }, true);
    d.addEventListener('mousemove', function(ev){
      if(!drag) return;
      moveTo(drag.l+(ev.clientX-drag.sx), drag.t+(ev.clientY-drag.sy));
      buildPanel();
    }, true);
    d.addEventListener('mouseup', function(){ drag=null; }, true);
    d.addEventListener('keydown', onKey);
  }

  var pickParam=(location.search.match(/[?&]pick=([^&]+)/)||[])[1];

  moduleSel.onchange=load;
  themeBtn.onclick=function(){ themeBtn.dataset.theme=theme()==='dark'?'light':'dark'; themeBtn.textContent='Theme: '+theme(); load(); };
  gridBtn.onclick=function(){ gridOn=!gridOn; gridBtn.classList.toggle('on',gridOn); wireFrame(); };
  $('reload').onclick=load;
  $('copy').onclick=function(){ navigator.clipboard.writeText(css.value); status.textContent='copied ✓'; setTimeout(function(){status.textContent='';},1200); };
  $('reset').onclick=function(){ if(curSel){ delete rules[curSel]; serialize(); buildPanel(); } };
  $('clear').onclick=function(){ rules={}; serialize(); buildPanel(); };
  $('showall').onclick=showAll;

  // Highcharts Save Button
  $('hc-save').onclick=function(){
    var payload = buildHighchartsPayload();
    hcStatus.textContent = 'Saving...';
    fetch('highcharts_save.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) {
        hcStatus.textContent = 'Saved ✓';
        setTimeout(function(){ hcStatus.textContent = ''; }, 2000);
      } else {
        hcStatus.textContent = 'Error: ' + (d.error || 'Save failed');
      }
    })
    .catch(function(e){
      hcStatus.textContent = 'Save failed: ' + e;
    });
  };

  $('hc-reset').onclick=function(){
    hcState = {
      containerHeight: 350, spacingTop: 10, spacingBottom: 10, spacingLeft: 10, spacingRight: 10,
      marginTop: '', marginBottom: '', legendMargin: 25, legendY: '', xAxisLabelY: 20, yAxisLabelX: -8
    };
    buildHighchartsPanel();
    syncHighcharts();
  };

  frame.addEventListener('load', function() {
    wireFrame();
    // Re-sync Highcharts to the loaded iframe
    setTimeout(syncHighcharts, 500);
    setTimeout(syncHighcharts, 1500);
  });
  document.addEventListener('keydown', onKey);
  buildHighchartsPanel();
  load();
})();
</script>
</body>
</html>
