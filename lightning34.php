<?php include('w34CombinedData.php'); date_default_timezone_set($TZ); error_reporting(0); ?>
<div class="mod">

  <div class="mod-header">
    <span class="mod-time">
      <?php if(file_exists($livedata)&&time()-filemtime($livedata)>300) echo '<span class="mod-offline">Offline</span>'; else echo $online.' '.$weather['time']; ?>
    </span>
    <?php if ($lightning['strike_count_3hr'] > 0): ?>
      <img src="img/lightningalert.svg" width="14" height="14">
    <?php endif; ?>
  </div>

  <div class="mod-primary">
    <span class="mod-xl mod-warm"><?php echo $lightning['strike_count_3hr']; ?></span>
    <span class="mod-unit">strikes</span>
  </div>
  <div class="mod-label" style="margin-top:-4px;margin-bottom:6px">Last 3 hrs</div>

  <div class="mod-rows">

    <?php if ($lightning['last_time'] >= 1): ?>
    <div class="mod-row">
      <span class="mod-label">Last Strike</span>
      <span class="mod-sm mod-warm"><?php echo date('j M H:i', $lightning['last_time']); ?></span>
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
      <span class="mod-label"><?php echo date('F Y'); ?></span>
      <span class="mod-sm mod-warm"><?php echo str_replace(',','',$weather['lightningmonth']); ?></span>
    </div>

    <div class="mod-row">
      <span class="mod-label">Total <?php echo date('Y'); ?></span>
      <span class="mod-sm mod-warm"><?php echo str_replace(',','',$weather['lightningyear']); ?></span>
    </div>

    <div class="mod-row">
      <span class="mod-label">All-time</span>
      <span class="mod-sm"><?php echo $lightning['strike_count']; ?></span>
    </div>

  </div>
</div>
