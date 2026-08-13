<?php
/* iso.php — render ONE card off-dashboard for explicit-geometry A/B (DEV ONLY).
 *   ?card=<name>&mode=ref|mod&theme=dark|light&w=<px>
 *   ref : exactly what index.php serves for this card — main.<theme>.css +
 *         ALL css/modules/*.css (the template self-scopes with its own .mod-X).
 *   mod : exactly what index2.php serves for this card — framework.<theme>.css +
 *         ALL css/modules/*.css.
 * The ONLY difference between the two modes is main vs framework, which is the
 * entire point: it isolates the kernel reduction's effect on this one card.
 * Emits a hidden <pre id="__LAYOUT__"> with every element's box + key computed
 * styles, so `chromium --dump-dom` yields structured geometry (not pixels).
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
$src = $phpfile[$card] ?? '';

// All module sheets, exactly like index.php / index2.php glob-loader. Card CSS is
// reached through the template's own self-scoped .mod-X wrapper.
$mods = '';
foreach (glob("css/modules/*.css") as $sheet) {
    $mods .= '<link href="' . $sheet . '" rel="stylesheet">' . "\n";
}
?>
<!DOCTYPE html>
<html data-theme="<?php echo $theme; ?>" style="background:#26262b;color:#ccc;">
<head><meta charset="utf-8">
<?php if ($mode === 'ref'): ?>
  <link href="css/main.<?php echo $theme; ?>.css" rel="stylesheet">
<?php else: ?>
  <link href="css/framework.<?php echo $theme; ?>.css" rel="stylesheet">
<?php endif; ?>
<?php echo $mods; ?>
<style>body{margin:0;padding:0}#grid_0{width:<?php echo $w; ?>px}</style>
</head>
<body>
<div id="grid_0" class="weather-item">
<?php if ($src && file_exists($src)) { include($src); } ?>
</div>
<script>
function w34dump(){
  // Normalize live/volatile leaf values so ref and mod compare STRUCTURAL geometry,
  // not the width of whatever number happened to be displayed in each capture.
  // (Same intent as blanking .updatedtime; does not loosen the diff tolerance.)
  document.querySelectorAll('[class*=updatedtime]').forEach(e=>{e.textContent='--';});
  document.querySelectorAll('#grid_0 *').forEach(el=>{
    if(el.children.length) return;                     // leaf only
    const t=(el.textContent||'').trim();
    if(/^-?\d+(\.\d+)?$/.test(t)) el.textContent='000'; // fixed-width numeric placeholder
  });
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
