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

  <div style="display:flex;align-items:flex-start;gap:10px;flex:1">

    <!-- Strike count badge — keeps original .simsek yellow pill styling -->
    <div>
      <div class="simsekdata">Strikes</div>
      <div class="simsek" style="font-size:1.4rem;align-items:center;justify-content:center;color:#fff;border-bottom:10px solid rgba(0,0,0,.2)">
        <?php echo $lightning['strike_count_3hr']; ?>
      </div>
      <div class="mod-label" style="text-align:center;margin-top:2px">Last 3 Hrs</div>
    </div>

    <!-- Data rows -->
    <div class="mod-rows" style="flex:1;margin-top:4px">

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

</div>
