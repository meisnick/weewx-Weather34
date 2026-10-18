<?php
//###################################################################################################################
//	weewx-Weather34 fork maintained by meisnick, based on the WeeWX skin by Ian Millard (Steepleian)    #
//	                                                                                                				#
//  Contains original code by Ian Millard and collaborators															#
//  © claydonsweather.org.uk original CSS/SVG/PHP 2020-2021                                                         #
// 	                                                                                                				#
// 	Issues for weewx-Weather34 template should be addressed to https://github.com/meisnick/weewx-Weather34/issues #                                                                                              
// 	                                                                                                				#
//###################################################################################################################
include_once('w34_i18n.php');
include_once('settings1.php');
include_once('common.php');
include('settings.php');

date_default_timezone_set($TZ);
$_weekdays = array_map('w34_datetext', array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"));
$_months   = array_map('w34_datetext', array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"));
?>
<div class="mod-clock">
  <div class="calendar34"><svg id="weather34clock-icon" width="60pt" height="60pt" viewBox="0 0 720 720" version="12-10-21"></svg></div>
  <span id="theTime"></span>
  <div class="clock">
    <div class="dot"></div>
    <div class="s-hand hand"></div>
    <div class="m-hand hand"></div>
    <div class="h-hand hand"></div>
    <div class="center"></div>
  </div>
</div><!-- /mod-clock -->
<script>
(function() {
  if (window._clockHandsTimer) { clearInterval(window._clockHandsTimer); }
  var yourTimeZoneFrom = <?php echo (float)$UTC_offset; ?>;
  var is12h = <?php echo ($clockformat == '12') ? 'true' : 'false'; ?>;
  var weekdays = <?php echo json_encode($_weekdays, JSON_UNESCAPED_UNICODE); ?>;
  var months   = <?php echo json_encode($_months, JSON_UNESCAPED_UNICODE); ?>;

  function getTime() {
    var now = new Date();
    var tzDifference = yourTimeZoneFrom * 60 + now.getTimezoneOffset();
    var offset = tzDifference * 60 * 1000;
    var now2 = new Date(now.getTime() + offset);

    var s = now2.getSeconds() + (now2.getMilliseconds() / 1000);
    var m = now2.getMinutes();
    var rawH = now2.getHours();
    var h = rawH % 12 || 12;

    var sEl = document.querySelector('.mod-clock .s-hand');
    var mEl = document.querySelector('.mod-clock .m-hand');
    var hEl = document.querySelector('.mod-clock .h-hand');
    if (sEl) sEl.style.transform = 'translate(-50%, -100%) rotate(' + (s * 6) + 'deg)';
    if (mEl) mEl.style.transform = 'translate(-50%, -100%) rotate(' + (m * 6) + 'deg)';
    if (hEl) hEl.style.transform = 'translate(-50%, -100%) rotate(' + (h * 30 + m * 0.5) + 'deg)';

    if (typeof window.UpdateClock !== 'function') {
      var el = document.getElementById('theTime');
      if (el) {
        var dispH = is12h ? (rawH % 12 || 12) : (rawH % 24 || 0);
        var amorpm = is12h ? (rawH < 12 ? ' am' : ' pm') : '';
        var pad = function(n) { return n < 10 ? '0' + n : '' + n; };
        var secInt = now2.getSeconds();
        el.innerHTML = "<div class='weatherclock34'> "
          + weekdays[now2.getDay()] + ' ' + now2.getDate() + ' ' + months[now2.getMonth()] + ' ' + now2.getFullYear()
          + "<div class='orangeclock'>" + pad(dispH) + ':' + pad(m) + ':' + pad(secInt) + amorpm + '</div></div>';
      }
    }
  }

  getTime();
  window._clockHandsTimer = setInterval(getTime, 50);
})();
</script>

