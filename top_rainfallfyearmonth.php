<?php include('w34CombinedData.php'); date_default_timezone_set($TZ); error_reporting(0); ?>
<div class="mod mod-top">

  <div class="mod-primary" style="gap:16px;margin-top:4px">

    <div>
      <div class="mod-label"><?php echo date('M'); ?></div>
      <span class="mod-lg mod-cold">
        <?php echo ($weather['rain_month']>=1000) ? round($weather['rain_month'],0) : $weather['rain_month']; ?>
      </span>
      <span class="mod-unit"><?php echo $weather['rain_units']; ?></span>
      <div class="mod-label">Total</div>
    </div>

    <div>
      <div class="mod-label"><?php echo date('Y'); ?></div>
      <span class="mod-lg mod-cold">
        <?php echo ($weather['rain_year']>=1000) ? round($weather['rain_year'],0) : $weather['rain_year']; ?>
      </span>
      <span class="mod-unit"><?php echo $weather['rain_units']; ?></span>
      <div class="mod-label">Total</div>
    </div>

  </div>
</div>
