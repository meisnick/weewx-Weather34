<?php include('w34CombinedData.php'); error_reporting(0); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden}
body{
  background:#1a1b1f;display:flex;flex-direction:column;
  padding:50px 12px 10px;font-family:Arial,sans-serif;color:silver;
}
<?php if($theme==='light'):?>body{background:#ddd;color:#333}<?php endif;?>

.rp-bar{
  display:flex;align-items:center;gap:12px;
  flex-shrink:0;margin-bottom:8px;flex-wrap:wrap;
}
.rp-station-group{display:flex;align-items:center;gap:6px;font-size:.7rem}
.rp-station-input{
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);
  color:#fff;font-size:.78rem;font-family:'Courier New',monospace;
  width:4.5rem;padding:3px 6px;border-radius:3px;text-transform:uppercase;
  letter-spacing:.1em;text-align:center;
}
.rp-station-input:focus{outline:none;border-color:#ffca32}
.rp-station-input.error{border-color:#f44336}
.rp-load{
  background:rgba(255,255,255,.12);border:none;color:silver;
  font-size:.65rem;padding:3px 9px;border-radius:3px;
  cursor:pointer;font-family:Arial,sans-serif;
}
.rp-load:hover{background:rgba(255,255,255,.22);color:#fff}
.rp-title{font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;flex:1;text-align:center}
.rp-controls{display:flex;align-items:center;gap:10px;font-size:.7rem;margin-left:auto}
.rp-controls input[type=range]{width:110px;cursor:pointer;accent-color:#ffca32}
.rp-invert-lbl{
  display:flex;align-items:center;gap:5px;font-size:.68rem;
  cursor:pointer;user-select:none;
}
.rp-invert-lbl input{cursor:pointer;accent-color:#ffca32}
.rp-reset{
  background:rgba(255,255,255,.1);border:none;color:silver;
  font-size:.65rem;padding:3px 9px;border-radius:3px;
  cursor:pointer;font-family:Arial,sans-serif;
}
.rp-reset:hover{background:rgba(255,255,255,.2);color:#fff}
.rp-save{
  background:#ffca32;border:none;color:#1a1b1f;
  font-size:.68rem;font-weight:bold;padding:4px 14px;border-radius:3px;
  cursor:pointer;font-family:Arial,sans-serif;
}
.rp-save:hover{background:#ffd966}
.rp-save.saved{background:#4caf50;color:#fff}
.rp-ws{
  position:relative;flex:1;overflow:hidden;
  display:flex;align-items:center;justify-content:center;
}
.rp-img{
  max-width:100%;max-height:100%;display:block;
  user-select:none;pointer-events:none;border-radius:3px;
  transition:opacity 0.25s ease;
}
.rp-crop{
  position:absolute;border:2px solid rgba(255,202,50,.95);
  box-shadow:0 0 0 9999px rgba(0,0,0,.45);border-radius:2px;cursor:move;
}
.rp-hint{
  flex-shrink:0;text-align:center;font-size:.62rem;color:#555;margin-top:6px;
}
</style>
</head>
<body>

<div class="rp-bar">
  <div class="rp-station-group">
    <span>Station</span>
    <input type="text" class="rp-station-input" id="rp-sta" maxlength="4" placeholder="KMKX">
    <button class="rp-load" id="rp-load">Load</button>
  </div>
  <span class="rp-title" id="rp-title">&mdash; Adjust Dashboard View</span>
  <div class="rp-controls">
    <span>Zoom</span>
    <input type="range" id="rp-sl" min="0.3" max="3" step="0.02">
    <span id="rp-zv">1.00&times;</span>
    <label class="rp-invert-lbl"><input type="checkbox" id="rp-inv"> Invert</label>
    <button class="rp-reset" id="rp-rst">Reset</button>
    <button class="rp-save" id="rp-sav">Save</button>
  </div>
</div>

<div class="rp-ws" id="rp-ws">
  <img class="rp-img" id="rp-img" src="" alt="Radar Loop" draggable="false">
  <div class="rp-crop" id="rp-crop" style="display:none"></div>
</div>

<div class="rp-hint">Drag the box to pan &nbsp;&bull;&nbsp; Slider or scroll wheel to zoom</div>

<script>
(function(){
  var TW=310, TH=155;
  var img   = document.getElementById('rp-img');
  var crop  = document.getElementById('rp-crop');
  var ws    = document.getElementById('rp-ws');
  var sl    = document.getElementById('rp-sl');
  var zv    = document.getElementById('rp-zv');
  var rst   = document.getElementById('rp-rst');
  var sav   = document.getElementById('rp-sav');
  var inv   = document.getElementById('rp-inv');
  var staIn = document.getElementById('rp-sta');
  var loadB = document.getElementById('rp-load');
  var title = document.getElementById('rp-title');

  var zoom=1, cropX=0, cropY=0, ps=1;
  var currentStation = (localStorage.getItem('radar_station') || 'KMKX').toUpperCase();

  function makeSrc(sta){
    var cb=Math.floor(Date.now()/1000/300)*300;
    return 'https://radar.weather.gov/ridge/standard/'+sta+'_loop.gif?t='+cb;
  }
  function setTitle(s){ title.textContent = s + ' — Adjust Dashboard View'; }

  function loadLS(){
    zoom  = parseFloat(localStorage.getItem('radar_zoom')   || '0');
    cropX = parseFloat(localStorage.getItem('radar_crop_x') || '0');
    cropY = parseFloat(localStorage.getItem('radar_crop_y') || '0');
  }
  function saveLS(){
    localStorage.setItem('radar_zoom',   zoom);
    localStorage.setItem('radar_crop_x', cropX);
    localStorage.setItem('radar_crop_y', cropY);
  }
  function updatePS(){
    var r=img.getBoundingClientRect();
    ps=(r.width>1&&img.naturalWidth)?r.width/img.naturalWidth:1;
  }
  function clamp(){
    var nw=img.naturalWidth,nh=img.naturalHeight,cw=TW/zoom,ch=TH/zoom;
    cropX=cw>=nw?-(cw-nw)/2:Math.max(0,Math.min(nw-cw,cropX));
    cropY=ch>=nh?-(ch-nh)/2:Math.max(0,Math.min(nh-ch,cropY));
  }
  function render(){
    var ir=img.getBoundingClientRect(),wr=ws.getBoundingClientRect();
    crop.style.left  =(ir.left-wr.left+cropX*ps)+'px';
    crop.style.top   =(ir.top-wr.top+cropY*ps)+'px';
    crop.style.width =(TW/zoom)*ps+'px';
    crop.style.height=(TH/zoom)*ps+'px';
    sl.value=zoom;
    zv.textContent=zoom.toFixed(2)+'×';
  }
  function setZoom(z){
    var cx0=cropX+TW/(2*zoom),cy0=cropY+TH/(2*zoom);
    zoom=Math.max(0.3,Math.min(3,z));
    cropX=cx0-TW/(2*zoom); cropY=cy0-TH/(2*zoom);
    clamp(); saveLS(); render();
  }
  function resetToDefault(){
    var nw=img.naturalWidth,nh=img.naturalHeight;
    if(!nw) return;
    zoom=TW/nw; cropX=0; cropY=Math.max(0,(nh-TH/zoom)/2);
    clamp(); saveLS(); render();
  }

  var dragging=false,dX,dY,dCX,dCY;
  crop.addEventListener('mousedown',function(e){
    dragging=true;dX=e.clientX;dY=e.clientY;dCX=cropX;dCY=cropY;e.preventDefault();
  });
  document.addEventListener('mousemove',function(e){
    if(!dragging) return;
    cropX=dCX+(e.clientX-dX)/ps; cropY=dCY+(e.clientY-dY)/ps;
    clamp(); saveLS(); render();
  });
  document.addEventListener('mouseup',function(){ dragging=false; });

  function applyInvert(on){
    img.style.filter = on ? 'invert(1)' : '';
    localStorage.setItem('radar_invert', on ? '1' : '0');
    try {
      var tImg = window.parent.document.querySelector('.mod-radar-img');
      if (tImg) tImg.style.filter = on ? 'invert(1)' : '';
    } catch(e){}
  }
  inv.checked = localStorage.getItem('radar_invert') === '1';
  img.style.filter = inv.checked ? 'invert(1)' : '';
  inv.addEventListener('change', function(){ applyInvert(this.checked); });

  sl.addEventListener('input',  function(){ setZoom(parseFloat(this.value)); });
  rst.addEventListener('click', resetToDefault);
  ws.addEventListener('wheel',function(e){
    e.preventDefault();
    setZoom(zoom+(e.deltaY<0?0.05:-0.05));
  },{passive:false});

  sav.addEventListener('click',function(){
    saveLS();
    try{
      var tImg=window.parent.document.querySelector('.mod-radar-img');
      if(tImg) tImg.style.transform=
        'translate('+(-cropX*zoom)+'px,'+(-cropY*zoom)+'px) scale('+zoom+')';
    }catch(e){}
    sav.textContent='Saved ✓'; sav.classList.add('saved');
    setTimeout(function(){ sav.textContent='Save'; sav.classList.remove('saved'); },1800);
  });

  // Station load
  function loadStation(s){
    s=s.toUpperCase().replace(/[^A-Z]/g,'');
    staIn.classList.remove('error');
    if(s.length!==4){ staIn.classList.add('error'); return; }
    currentStation=s;
    staIn.value=s;
    setTitle(s);
    localStorage.setItem('radar_station',s);
    localStorage.removeItem('radar_zoom');
    localStorage.removeItem('radar_crop_x');
    localStorage.removeItem('radar_crop_y');
    img.style.opacity='0';
    crop.style.display='none';
    var probe=new Image();
    probe.onload=function(){
      var onNewLoad = function(){
        img.removeEventListener('load', onNewLoad);
        var revealNew = function() {
          updatePS(); resetToDefault();
          crop.style.display='';
          img.style.filter = inv.checked ? 'invert(1)' : '';
          img.style.opacity='1';
        };
        if (typeof img.decode === 'function') {
          img.decode().then(revealNew).catch(revealNew);
        } else {
          revealNew();
        }
      };
      img.addEventListener('load', onNewLoad);
      img.src=makeSrc(s);
    };
    probe.onerror=function(){
      staIn.classList.add('error');
      img.style.opacity='1';
    };
    probe.src=makeSrc(s);
  }

  loadB.addEventListener('click',function(){ loadStation(staIn.value); });
  staIn.addEventListener('keydown',function(e){
    if(e.key==='Enter') loadStation(this.value);
  });
  staIn.addEventListener('input',function(){
    this.value=this.value.toUpperCase().replace(/[^A-Z]/g,'');
    this.classList.remove('error');
  });

  // Init
  function init(){
    updatePS(); loadLS();
    if(!zoom||zoom<0.01) resetToDefault();
    else{ clamp(); render(); }
    crop.style.display='';
    img.style.opacity='1';
  }
  staIn.value=currentStation;
  setTitle(currentStation);
  img.addEventListener('load', function(){
    var runInit = function() { requestAnimationFrame(init); };
    if (typeof img.decode === 'function') {
      img.decode().then(runInit).catch(runInit);
    } else {
      runInit();
    }
  });
  img.src=makeSrc(currentStation);
  window.addEventListener('resize',function(){ updatePS(); render(); });
})();
</script>
</body>
</html>
