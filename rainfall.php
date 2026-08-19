<?php  //weather34 rain module
include_once('w34CombinedData.php');
?>
<div class="updatedtime"><span><?php if(file_exists($livedata)&&time()- filemtime($livedata)>300)echo $offline. '<offline> Offline </offline>';else echo $online." ".$weather["time"];?></span></div>  

<div class="mod-rainfall">
  <div class="mod-rainfall-grid">
    
    <!-- LEFT COLUMN -->
    <div class="mod-rainfall-col-left">
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
                echo number_format($weather["rain_today"],2)."<smallrainunita> ".$weather["rain_units"]."</smallrainunita>";
              } else if ($weather["rain_units"] =='mm' && $weather["rain_today"]<10) {
                echo number_format($weather["rain_today"],2)."<smallrainunita>".$weather["rain_units"]."</smallrainunita>";
              } else if ($weather["rain_units"] =='mm') {
                echo number_format($weather["rain_today"],1)."<smallrainunita>".$weather["rain_units"]."</smallrainunita>";
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
        <!-- Year -->
        <div class="mod-rainfall-block">
          <valuetextheading1><?php echo date('Y');?></valuetextheading1>
          <div class="rainmodulehome">
            <raiblue><?php 
              if($weather["rain_year"]>=1000) { echo round($weather["rain_year"],0); }
              else { echo $weather["rain_year"]; }
            ?></raiblue>
            <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
          </div>
        </div>
        
        <!-- Month -->
        <div class="mod-rainfall-block">
          <valuetextheading1><?php echo date('F');?></valuetextheading1>
          <div class="rainmodulehome">
            <raiblue><?php echo $weather["rain_month"];?></raiblue>
            <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
          </div>
        </div>

        <!-- Last Hour -->
        <div class="mod-rainfall-block">
          <valuetextheading1>Last Hour</valuetextheading1>
          <div class="rainmodulehome">
            <raiblue><?php echo $weather["rain_lasthour"];?></raiblue>
            <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
          </div>
        </div>

        <!-- Last 24hr -->
        <div class="mod-rainfall-block">
          <valuetextheading1>Last 24hr</valuetextheading1>
          <div class="rainmodulehome">
            <raiblue><?php echo $weather["rain_24hrs"];?></raiblue>
            <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
          </div>
        </div>
      </div>

      <!-- Bottom Pill (Rate) -->
      <div class="mod-rainfall-bot-pill">
        <div class="rainratemodulehome">
          <rainratetextheading>&nbsp;Rate&nbsp;</rainratetextheading>
          <raiblue><?php 
            if ($weather["rain_rate"]>100) { echo number_format($weather["rain_rate"],1); } 
            else { echo number_format($weather["rain_rate"],2); }
          ?></raiblue>
          <smallrainunit2><?php echo $weather["rain_units"];?></smallrainunit2>
        </div>
      </div>

    </div>
  </div>
</div>
