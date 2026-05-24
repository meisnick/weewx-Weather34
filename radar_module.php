<?php
include('shared.php');
include_once('settings1.php');
?>
<div class="updatedtime"><span><?php echo $online; ?> Live</span></div>

<div class="mod-radar">
    <img class="mod-radar-img"
         src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
         alt="Radar Loop">
</div>
<script>
(function(){
  var img = document.querySelector('.mod-radar-img');
  var TW = 310, TH = 155;

  function getStation(){ return (localStorage.getItem('radar_station') || 'KMKX').toUpperCase(); }
  function updatePopLink(sta){
    var gridEl = img.closest('[id^="grid_"]');
    var card   = gridEl && gridEl.parentElement;
    var a      = card && card.querySelector('a[href="pop_radar.php"]');
    if (!a) return;
    a.childNodes.forEach(function(n){
      if (n.nodeType === 3) n.textContent = n.textContent.replace(/[A-Z]{3,4}/, sta);
    });
  }
  function makeSrc(sta){
    var cb = Math.floor(Date.now()/1000/300)*300;
    return 'https://radar.weather.gov/ridge/standard/'+sta+'_loop.gif?t='+cb;
  }
  function apply(){
    var z  = parseFloat(localStorage.getItem('radar_zoom')   || '0');
    var cx = parseFloat(localStorage.getItem('radar_crop_x') || '0');
    var cy = parseFloat(localStorage.getItem('radar_crop_y') || '0');
    if (!z || z < 0.01) return;
    img.style.transform = 'translate('+(-cx*z)+'px,'+(-cy*z)+'px) scale('+z+')';
    img.style.filter = localStorage.getItem('radar_invert') === '1' ? 'invert(1)' : '';
  }

  // Paint the current cropped/zoomed view onto #grid_N as a background-image.
  // #grid_N survives jQuery's innerHTML replacement, so this stays visible
  // while the new <img> is loading, eliminating the black-flash between reloads.
  function syncBackground(){
    var gridEl = img.closest('[id^="grid_"]');
    if (!gridEl || !img.naturalWidth) return;
    var z  = parseFloat(localStorage.getItem('radar_zoom')   || '1');
    var cx = parseFloat(localStorage.getItem('radar_crop_x') || '0');
    var cy = parseFloat(localStorage.getItem('radar_crop_y') || '0');
    var gr = gridEl.getBoundingClientRect();
    var mr = img.parentElement.getBoundingClientRect(); // .mod-radar
    var dy = mr.top - gr.top; // .mod-radar offset within #grid_N
    gridEl.style.backgroundImage    = 'url("' + img.src + '")';
    gridEl.style.backgroundRepeat   = 'no-repeat';
    gridEl.style.backgroundSize     = (img.naturalWidth*z)+'px '+(img.naturalHeight*z)+'px';
    gridEl.style.backgroundPosition = (-cx*z)+'px '+(-cy*z+dy)+'px';
  }

  function reveal(){ apply(); img.style.opacity = '1'; syncBackground(); }
  function setDefault(){
    var z = parseFloat(localStorage.getItem('radar_zoom') || '0');
    if (z > 0 && localStorage.getItem('radar_crop_x') !== null) return;
    var nw = img.naturalWidth, nh = img.naturalHeight;
    if (!nw) return;
    z = z > 0 ? z : TW / nw;
    localStorage.removeItem('radar_pan_x');
    localStorage.removeItem('radar_pan_y');
    localStorage.setItem('radar_zoom',   z);
    localStorage.setItem('radar_crop_x', 0);
    localStorage.setItem('radar_crop_y', Math.max(0, (nh - TH/z) / 2));
  }

  // JS owns the src — set from localStorage station
  var station = getStation();
  
  // Attach listeners BEFORE setting the src to avoid missing cached loads
  img.addEventListener('load',  function(){
    setDefault();
    if (typeof img.decode === 'function') {
      img.decode().then(function() {
        reveal();
      }).catch(function(err) {
        console.warn("Radar decode failed, falling back to instant reveal", err);
        reveal();
      });
    } else {
      reveal();
    }
  });
  img.addEventListener('error', function(){ img.style.opacity = '0.15'; });

  img.src = makeSrc(station);
  updatePopLink(station);

  // Preload the NEXT period's URL immediately and keep it warm near the boundary.
  // makeSrc() returns the CURRENT period; the module reload may land in the next
  // period, so that URL must already be cached to avoid a blank-frame flash.
  function preloadNext(){
    var nextCb = (Math.floor(Date.now()/1000/300) + 1) * 300;
    new Image().src = 'https://radar.weather.gov/ridge/standard/'+getStation()+'_loop.gif?t='+nextCb;
  }
  preloadNext(); // fire immediately on every module load
  if (window._radarPreloadTimer) clearInterval(window._radarPreloadTimer);
  window._radarPreloadTimer = setInterval(function(){
    // Refresh the preload during the 60s window before each boundary
    if ((Date.now()/1000 % 300) >= 240) preloadNext();
  }, 10000);

  function onStorage(e){
    if (e.key === 'radar_station'){
      localStorage.removeItem('radar_zoom');
      localStorage.removeItem('radar_crop_x');
      localStorage.removeItem('radar_crop_y');
      var sta = getStation();
      img.style.opacity = '0';
      img.src = makeSrc(sta);
      updatePopLink(sta);
    } else {
      apply();
    }
  }
  if (window._radarSH) window.removeEventListener('storage', window._radarSH);
  window._radarSH = onStorage;
  window.addEventListener('storage', onStorage);
})();
</script>
