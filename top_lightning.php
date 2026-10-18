<?php include_once('w34_i18n.php'); ?>
<?php
include_once('w34CombinedData.php');
include_once('top_year_helpers.php');
include('settings.php');
include('common.php');
date_default_timezone_set($TZ);
header('Content-type: text/html; charset=utf-8');
error_reporting(0);

$strike_3hr = (int)($lightning['strike_count_3hr'] ?? 0);
$last_ts = (isset($lightning['last_time']) && $lightning['last_time'] >= 1) ? (int)$lightning['last_time'] : 0;
if ($last_ts < 1) {
    try {
        $ltdb = new SQLite3('/var/lib/weewx/weewx.sdb', SQLITE3_OPEN_READONLY);
        $row  = $ltdb->querySingle(
            'SELECT dateTime FROM archive WHERE lightning_strike_count > 0 ORDER BY dateTime DESC LIMIT 1',
            true
        );
        $ltdb->close();
        if ($row && isset($row['dateTime'])) {
            $last_ts = (int)$row['dateTime'];
        }
    } catch (Exception $e) { /* fail silently */ }
}
?>
<div class="mod-top-lightning">
  <?php echo yt_side('yt-orange', $lang['Strikes'], $strike_3hr, '', $lang['Last3Hrs']); ?>
  <div class="wflaststrike">
    <?php if ($last_ts >= 1): ?>
      <div class="wflaststrike-row"><spanfeelstitle><?php echo $lang['LastStrike']; ?>: <orange><?php echo w34_date('j M Y', $last_ts); ?></orange></spanfeelstitle></div>
    <?php endif; ?>
    <div class="wflaststrike-row"><spanfeelstitle><?php echo $lang['LastDistanceAt']; ?>: <orange><?php
      if ($windunit == 'mph') {
          echo number_format((float)$lightning['light_last_distance'] * 0.621371, 1);
      } else {
          echo $lightning['light_last_distance'];
      }
    ?></orange> <?php echo ($windunit == 'mph') ? $lang['Miles'] : $lang['Km']; ?></spanfeelstitle></div>
    <div class="wflaststrike-row"><spanfeelstitle><?php echo $lang['AllTimeStrikeTotal']; ?>: <orange><?php echo $lightning['strike_count']; ?></orange></spanfeelstitle></div>
  </div>
  <?php if ($strike_3hr > 0): ?>
    <div class="lightningicon"><img src="img/lightningalert.svg" width="20" height="20" alt="" /></div>
  <?php endif; ?>
</div><!-- /mod-top-lightning -->

