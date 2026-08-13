<?php
/* iso.php — render ONE card off-dashboard for explicit-geometry A/B (DEV ONLY).
 *   ?card=<name>&mode=ref|mod&theme=dark|light&w=<px>
 *   ref : reference — main.<theme>.css, card unwrapped (exactly how index.php styles it).
 *   mod : self-contained module — kernel.<theme>.css + css/modules/<card>.css only,
 *         card wrapped in .mod-<card> (how a finished module must stand on its own).
 * Emits a hidden <pre id="__LAYOUT__"> with every element's box + key computed styles,
 * so `chromium --dump-dom` yields structured geometry (read the layout, not pixels).
 */
$card  = preg_replace('/[^a-z0-9\-]/i', '', $_GET['card'] ?? 'temperature');
$mode  = ($_GET['mode'] ?? 'ref') === 'mod' ? 'mod' : 'ref';
$theme = ($_GET['theme'] ?? 'dark') === 'light' ? 'light' : 'dark';
$w     = (int)($_GET['w'] ?? 380); if ($w < 120 || $w > 1200) $w = 380;

$phpfile = [
  'wind'=>'windspeeddirection.php','barometer'=>'barometer.php','rainfall'=>'rainfall.php',
  'temperature'=>'temperaturein.php','indoor'=>'indoortemperature.php','moonphase'=>'moonphase.php',
  'sun'=>'sun3.php','lightning34'=>'lightning34.php','conditions'=>'currentconditionsw34.php',
  'forecast'=>'forecast3om.php','forecastdiscussion'=>'forecastdiscussion.php',
  'localforecast'=>'localforecast.php','aurora'=>'aurora_module.php','clock'=>'weather34clock.php',
  'advisory'=>'top_advisory_nws.php','windgustyear'=>'top_windgustyear.php',
  'temperatureyear'=>'top_temperatureyear.php','top-lightning'=>'top_lightning.php',
  'rain-totals'=>'top_rainfallfyearmonth.php','airqualitymodule'=>'airqualitymodule.php',
  'radar'=>'radar_module.php','solaruv'=>'solaruv.php',
];
$modcss = [
  'wind'=>'wind','barometer'=>'barometer','rainfall'=>'rainfall','temperature'=>'temperature',
  'indoor'=>'temperature','moonphase'=>'moonphase','sun'=>'sun','lightning34'=>'lightning34',
  'conditions'=>'conditions','forecast'=>'forecast','forecastdiscussion'=>'forecastdiscussion',
  'localforecast'=>'localforecast','aurora'=>'aurora','clock'=>'clock','advisory'=>'advisory',
  'windgustyear'=>'topyears','temperatureyear'=>'topyears','top-lightning'=>'top-lightning',
  'rain-totals'=>'rain-totals','airqualitymodule'=>'airqualitymodule','radar'=>'radar','solaruv'=>'solar',
];
$src  = $phpfile[$card] ?? '';
$modf = 'css/modules/' . ($modcss[$card] ?? $card) . '.css';
?>
<!DOCTYPE html>
<html data-theme="<?php echo $theme; ?>" style="background:#26262b;color:#ccc;">
<head><meta charset="utf-8">
<?php if ($mode === 'ref'): ?>
  <link href="css/main.<?php echo $theme; ?>.css" rel="stylesheet">
<?php else: ?>
  <link href="css/kernel.<?php echo $theme; ?>.css" rel="stylesheet">
  <link href="<?php echo $modf; ?>" rel="stylesheet">
<?php endif; ?>
<style>body{margin:0;padding:0}#grid_0{width:<?php echo $w; ?>px}</style>
</head>
<body>
<div id="grid_0" class="weather-item<?php echo $mode==='mod' ? ' mod-'.$card : ''; ?>">
<?php if ($src && file_exists($src)) { include($src); } ?>
</div>
<script>
function w34dump(){
  document.querySelectorAll('[class*=updatedtime]').forEach(e=>{e.textContent='--';});
  const root=document.getElementById('grid_0'), b=root.getBoundingClientRect(), out=[];
  root.querySelectorAll('*').forEach((el,i)=>{const r=el.getBoundingClientRect(),c=getComputedStyle(el);
    out.push({i,tag:el.tagName.toLowerCase(),cls:(typeof el.className==='string'?el.className:''),
      x:Math.round(r.left-b.left),y:Math.round(r.top-b.top),w:Math.round(r.width),h:Math.round(r.height),
      fs:c.fontSize,fw:c.fontWeight,col:c.color,bg:c.backgroundColor,pos:c.position,disp:c.display,ta:c.textAlign,
      txt:el.children.length?'':el.textContent.trim().slice(0,24)});});
  const p=document.createElement('pre');p.id='__LAYOUT__';p.style.display='none';
  p.textContent=JSON.stringify(out);document.body.appendChild(p);
}
if(document.readyState==='complete')setTimeout(w34dump,300);
else window.addEventListener('load',()=>setTimeout(w34dump,300));
</script>
</body></html>
