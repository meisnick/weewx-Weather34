<?php
include('w34CombinedData.php');
include('settings.php');
date_default_timezone_set($TZ);
header('Content-type: text/html; charset=utf-8');
error_reporting(0);
?>
<div class="mod mod-top">

  <div class="mod-primary" style="margin-top:2px">
    <span class="mod-lg mod-warm"><?php echo $lightning['strike_count_3hr']; ?></span>
    <span class="mod-unit">strikes</span>
    <?php if ($lightning['strike_count_3hr'] > 0): ?>
      <img src="img/lightningalert.svg" width="14" height="14" style="margin-left:4px">
    <?php endif; ?>
  </div>
  <div class="mod-label"><a href="pop_lightningalmanac.php" data-lity style="color:inherit;text-decoration:none">Last 3 Hrs</a></div>

  <div class="mod-rows" style="margin-top:4px">
    <?php if ($lightning['last_time'] >= 1): ?>
    <div class="mod-row">
      <span class="mod-label">Last</span>
      <span class="mod-sm mod-warm"><?php echo date('j M Y', $lightning['last_time']); ?></span>
    </div>
    <?php endif; ?>
    <div class="mod-row">
      <span class="mod-label">Distance</span>
      <span class="mod-sm">
        <?php if ($windunit == 'mph'): ?>
          <span class="mod-warm"><?php echo number_format($lightning['light_last_distance']*0.621371,1); ?></span><span class="mod-unit"> mi</span>
        <?php else: ?>
          <span class="mod-warm"><?php echo $lightning['light_last_distance']; ?></span><span class="mod-unit"> km</span>
        <?php endif; ?>
      </span>
    </div>
    <div class="mod-row">
      <span class="mod-label">All-time</span>
      <span class="mod-sm mod-warm"><?php echo $lightning['strike_count']; ?></span>
    </div>
  </div>

</div>
