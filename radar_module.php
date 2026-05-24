<?php
include('shared.php');
include_once('settings1.php');
?>
<div class="updatedtime"><span><?php echo $online; ?> Live</span></div>

<div class="mod-radar">
    <img class="mod-radar-img"
         src=""
         alt="Radar Loop"
         onerror="this.style.opacity='0.15'">
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
  }
  function reveal(){ apply(); img.style.opacity = '1'; }
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
  img.src = makeSrc(station);
  updatePopLink(station);
  img.addEventListener('load',  function(){ setDefault(); reveal(); });
  img.addEventListener('error', function(){ img.style.opacity = '0.15'; });

  // Proactive preload ~30s before each 5-min cache-bust boundary
  if (window._radarPreloadTimer) clearInterval(window._radarPreloadTimer);
  window._radarPreloadTimer = setInterval(function(){
    var secsLeft = 300 - (Date.now()/1000 % 300);
    if (secsLeft <= 35 && secsLeft > 25) new Image().src = makeSrc(getStation());
  }, 5000);

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
