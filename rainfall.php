<?php include_once('w34_i18n.php'); ?>
<?php  //weather34 rain module
include_once('w34CombinedData.php');

// Flood pill: the WPC Excessive Rainfall Outlook at the station (jsondata/spc_outlook.json, written by
// scripts/spc_outlook_update.py), the highest level over the next five days and the first day it comes. The card's
// chin links the Storm Outlook popout's flood layer. Hidden when the outlook is missing or over six hours old.
$ff = null; $ff_days = 0;
$ffj = @json_decode(@file_get_contents('jsondata/spc_outlook.json'), true);
if (is_array($ffj) && !empty($ffj['fetched']) && time() - strtotime($ffj['fetched']) < 6 * 3600) {
    $ff_rank = array('MRGL' => 1, 'SLGT' => 2, 'MDT' => 3, 'HIGH' => 4);
    foreach ($ffj['days'] ?? array() as $d) {
        if (!isset($d['point']['ero'])) continue;
        $ff_days++;
        $e = $d['point']['ero'];
        if ($e && isset($ff_rank[$e]) && (!$ff || $ff_rank[$e] > $ff_rank[$ff['cat']] || ($ff_rank[$e] == $ff_rank[$ff['cat']] && $d['day'] < $ff['day'])))
            $ff = array('cat' => $e, 'day' => (int)$d['day'], 'valid' => $d['valid'] ?? null);
    }
}
?>
<div class="updatedtime"><span><?php if(file_exists($livedata)&&time()- filemtime($livedata)>300)echo $offline. '<offline> Offline </offline>';else echo $online." ".$weather["time"];?></span></div>  

<div class="mod-rainfall">
  <div class="mod-rainfall-grid">

    <!-- LEFT COLUMN -->
    <div class="mod-rainfall-col-left">
<?php if ($ff_days):
    // the level, then the day it comes when that is not today ("Slight Sat"); "None" over the five days
    $ff_names = array('MRGL' => w34t('FfMRGL', 'Marginal'), 'SLGT' => w34t('FfSLGT', 'Slight'), 'MDT' => w34t('FfMDT', 'Moderate'), 'HIGH' => w34t('FfHIGH', 'High')); ?>
      <div class="rainratemodulehome mod-rainfall-ffpill<?php echo $ff ? ' mod-rainfall-ffpill--' . $ff['cat'] : ''; ?>" title="<?php echo htmlspecialchars(w34t('FfTip', 'WPC Excessive Rainfall Outlook: the chance rain exceeds flash-flood guidance within 25 miles')); ?>">
        <rainratetextheading><?php echo w34t('FfPill', 'Flood'); ?></rainratetextheading>
        <raiblue><?php echo $ff ? $ff_names[$ff['cat']] : w34t('FfNoneShort', 'None'); ?></raiblue>
        <?php if ($ff && $ff['day'] > 1) echo '<smallrainunit2>' . w34_date('D', strtotime($ff['valid'])) . '</smallrainunit2>'; ?>
      </div>
<?php endif; ?>
      <!-- Graphic Container -->
      <div class="mod-rainfall-graphic-container">
        <div class="weather34i-rairate-bar">
          <div id="raincontainer">
            <div id="weather34rainbeaker">
              <div id="weather34rainwater" style="height:<?php $rain_mm = ($weather["rain_units"] == 'in' ? $weather["rain_today"] * 25.4 : $weather["rain_today"]); echo number_format($rain_mm > 0 ? ($rain_mm * 2.5 + 1) : 0, 1); ?>px;"></div>
            </div>
          </div>
        </div>
        
        <!-- Big Value Overlay -->
        <div class="mod-rainfall-big-value">
          <div class="raintoday1">
            <?php 
              if ($weather["rain_units"] =='in') {
                echo number_format((float)$weather["rain_today"],2)."<smallrainunita> ".$weather["rain_units"]."</smallrainunita>";
              } else if ($weather["rain_units"] =='mm' && $weather["rain_today"]<10) {
                echo number_format((float)$weather["rain_today"],2)."<smallrainunita>".$weather["rain_units"]."</smallrainunita>";
              } else if ($weather["rain_units"] =='mm') {
                echo number_format((float)$weather["rain_today"],1)."<smallrainunita>".$weather["rain_units"]."</smallrainunita>";
              }
            ?>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="mod-rainfall-col-right">
      
      <!-- Top Pill (Converter) -->
      <div class="mod-rainfall-top-pill">
        <?php
          if ($weather["rain_units"] =='in') {
            echo "<div class='rainconvertercircle'>".number_format($weather["rain_today"]*25.400013716,1)."<smallrainunit>mm</smallrainunit></div>";
          } else if ($weather["rain_units"] =='mm') {
            echo "<div class='rainconvertercircle'>".number_format($weather["rain_today"]*0.0393701,2)."<smallrainunit>in</smallrainunit></div>";
          }
        ?>
      </div>

      <!-- The 2x2 grid -->
      <div class="mod-rainfall-2x2">
        <!-- Year: rain and ET (evapotranspiration) in one pill -->
        <div class="mod-rainfall-block">
          <valuetextheading1><?php echo date('Y');?></valuetextheading1>
          <div class="mod-rainfall-etbox">
          <div class="rainmodulehome">
            <raiblue><?php
              if($weather["rain_year"]>=1000) { echo round((float)$weather["rain_year"],0); }
              else { echo $weather["rain_year"]; }
            ?></raiblue>
            <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
          </div>
          <div class="mod-rainfall-et"><span><?php echo w34t('EtShort', 'ET'); ?></span><?php echo ($weather["et_year"] >= 1000) ? round((float)$weather["et_year"], 0) : $weather["et_year"]; ?><smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2></div>
          </div>
        </div>
        
        <!-- Month: rain and ET in one pill -->
        <div class="mod-rainfall-block">
          <valuetextheading1><?php echo w34_date('F');?></valuetextheading1>
          <div class="mod-rainfall-etbox">
          <div class="rainmodulehome">
            <raiblue><?php echo $weather["rain_month"];?></raiblue>
            <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
          </div>
          <div class="mod-rainfall-et"><span><?php echo w34t('EtShort', 'ET'); ?></span><?php echo $weather["et_month"]; ?><smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2></div>
          </div>
        </div>

        <!-- Last Hour -->
        <div class="mod-rainfall-block">
          <valuetextheading1><?php echo w34t('LastHour', 'Last Hour');?></valuetextheading1>
          <div class="rainmodulehome">
            <raiblue><?php echo $weather["rain_lasthour"];?></raiblue>
            <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
          </div>
        </div>

        <!-- Last 24hr -->
        <div class="mod-rainfall-block">
          <valuetextheading1><?php echo w34t('Last24hr', 'Last 24hr');?></valuetextheading1>
          <div class="rainmodulehome">
            <raiblue><?php echo $weather["rain_24hrs"];?></raiblue>
            <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
          </div>
        </div>
      </div>

      <!-- Bottom Pills (ET today, Rate) -->
      <div class="mod-rainfall-bot-pill">
        <div class="rainratemodulehome mod-rainfall-etpill">
          <rainratetextheading>&nbsp;<?php echo w34t('EtShort', 'ET'); ?>&nbsp;</rainratetextheading>
          <raiblue><?php echo number_format((float)$weather["et_today"], $weather["rain_units"] == 'mm' ? 1 : 2); ?></raiblue>
          <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
        </div>
        <div class="rainratemodulehome">
          <rainratetextheading>&nbsp;<?php echo w34t('Rate', 'Rate');?>&nbsp;</rainratetextheading>
          <raiblue><?php 
            if ($weather["rain_rate"]>100) { echo number_format((float)$weather["rain_rate"],1); } 
            else { echo number_format((float)$weather["rain_rate"],2); }
          ?></raiblue>
          <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
        </div>
      </div>

    </div>
  </div>
</div>
