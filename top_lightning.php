<?php
  include('w34CombinedData.php');
  include('settings.php');
  include('common.php');
  //include('shared.php');
  date_default_timezone_set($TZ);
  header('Content-type: text/html; charset=utf-8');
  error_reporting(0);
?>
<body>
  <div class="mod-top-lightning">
  <div class="wfstrike">
    <?php
      //weather34 lightning
      echo "<wfstriketoday>".$lightning['strike_count_3hr']; ?>
    </wfstriketoday>
  </div>
  <div class="minwordl"><?php echo $lang['Strikes']; ?></div></div>
  <div class="mintimedatex"><value>&nbsp;<?php echo $lang['Last3Hrs']; ?><value></div>
  <div class='wflaststrike'>
  <?php
    //weather34 weather34 last detect
    if ($lightning['last_time']>=1) {
      echo "<spanfeelstitle>".$lang['LastStrike'].": <orange> ".date("j M Y", $lightning['last_time'])." </orange> ";}?><br />
  <?php
    if ($windunit == 'mph'){
      echo "<spanfeelstitle>".$lang['LastDistanceAt'].":<orange> ".number_format($lightning['light_last_distance']*0.621371,1). "  </orange>".$lang['Miles'];
    }else{
      echo "<spanfeelstitle>".$lang['LastDistanceAt'].":<orange> ".$lightning['light_last_distance']. "  </orange>".$lang['Km'];
    }
  ?><br />
  <?php
    //weather34 weather34 last detect
    echo "<spanfeelstitle>".$lang['AllTimeStrikeTotal'].": <orange> ".$lightning['strike_count']." </orange> ";?><br>
</div>
<div class="lightningicon">
<?php
  // display an icon when strike(s) are detected
  if ($lightning['strike_count_3hr'] > 0){
    echo '<img src="img/lightningalert.svg" width="20" height="20" align="right"/>';
  }?>
</div></div><!-- /mod-top-lightning -->
