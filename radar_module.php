<?php
include('shared_core.php');
include_once('settings1.php');
?>
<div class="updatedtime"><span><?php echo $online; ?> <span class="radar-time-text">Live</span></span></div>

<div class="mod-radar">
    <img class="mod-radar-img img-active"
         src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
         alt="Radar Loop">
    <img class="mod-radar-img img-buffer"
         src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
         alt="Radar Loop">
</div>
<script>
(function(){
  var container = document.querySelector('.mod-radar');
  var img1 = container.querySelector('.img-active');
  var img2 = container.querySelector('.img-buffer');
  var activeImg = img1;
  var bufferImg = img2;
  var TW = 310, TH = 155;
  var clockFormat = <?php echo json_encode($clockformat ?? '24'); ?>;

  function formatTime(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    var ampm = '';
    if (clockFormat === '12') {
      ampm = hours >= 12 ? ' pm' : ' am';
      hours = hours % 12;
      hours = hours ? hours : 12;
    }
    if (minutes < 10) minutes = '0' + minutes;
    if (seconds < 10) seconds = '0' + seconds;
    if (hours < 10 && clockFormat === '24') hours = '0' + hours;
    return hours + ':' + minutes + ':' + seconds + ampm;
  }

  function getStation(){ return (localStorage.getItem('radar_station') || 'KMKX').toUpperCase(); }
  function updatePopLink(sta){
    var gridEl = container.closest('[id^="grid_"]');
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
  function apply(img){
    var z  = parseFloat(localStorage.getItem('radar_zoom')   || '0');
    var cx = parseFloat(localStorage.getItem('radar_crop_x') || '0');
    var cy = parseFloat(localStorage.getItem('radar_crop_y') || '0');
    if (!z || z < 0.01) return;
    img.style.transform = 'translate('+(-cx*z)+'px,'+(-cy*z)+'px) scale('+z+')';
    img.style.filter = localStorage.getItem('radar_invert') === '1' ? 'invert(1)' : '';
  }

  function setDefault(img){
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

  function loadRadar(sta) {
    var srcUrl = makeSrc(sta);
    
    var onBufferLoad = function() {
      bufferImg.removeEventListener('load', onBufferLoad);
      setDefault(bufferImg);
      apply(bufferImg);
      
      var swap = function() {
        bufferImg.style.opacity = '1';
        activeImg.style.opacity = '0';
        
        var temp = activeImg;
        activeImg = bufferImg;
        bufferImg = temp;

        var timeText = container.parentElement.querySelector('.radar-time-text');
        if (timeText) {
          timeText.textContent = formatTime(new Date());
        }
      };
      
      if (typeof bufferImg.decode === 'function') {
        bufferImg.decode().then(swap).catch(swap);
      } else {
        swap();
      }
    };
    
    bufferImg.addEventListener('load', onBufferLoad);
    bufferImg.addEventListener('error', function onError() {
      bufferImg.removeEventListener('error', onError);
      bufferImg.style.opacity = '0.15';
    });
    
    bufferImg.src = srcUrl;
    updatePopLink(sta);
  }

  // Initial load
  var station = getStation();
  loadRadar(station);

  var lastCb = Math.floor(Date.now()/1000/300)*300;
  var updateInterval = setInterval(function(){
    var currentCb = Math.floor(Date.now()/1000/300)*300;
    if (currentCb > lastCb) {
      lastCb = currentCb;
      loadRadar(getStation());
    }
  }, 15000);

  function onStorage(e){
    if (e.key === 'radar_station' || e.key === 'radar_zoom' || e.key === 'radar_crop_x' || e.key === 'radar_crop_y' || e.key === 'radar_invert'){
      if (e.key === 'radar_station') {
        localStorage.removeItem('radar_zoom');
        localStorage.removeItem('radar_crop_x');
        localStorage.removeItem('radar_crop_y');
      }
      apply(activeImg);
      if (e.key === 'radar_station') {
        loadRadar(getStation());
      }
    }
  }

  if (window._radarSH) window.removeEventListener('storage', window._radarSH);
  window._radarSH = onStorage;
  window.addEventListener('storage', onStorage);

  function preloadNext(){
    var nextCb = (Math.floor(Date.now()/1000/300) + 1) * 300;
    new Image().src = 'https://radar.weather.gov/ridge/standard/'+getStation()+'_loop.gif?t='+nextCb;
  }
  preloadNext();
  if (window._radarPreloadTimer) clearInterval(window._radarPreloadTimer);
  window._radarPreloadTimer = setInterval(function(){
    if ((Date.now()/1000 % 300) >= 240) preloadNext();
  }, 10000);

  if (window._radarUpdateInterval) clearInterval(window._radarUpdateInterval);
  window._radarUpdateInterval = updateInterval;
})();
</script>
