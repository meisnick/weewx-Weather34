<?php
include_once('settings1.php');
include_once('shared.php');
error_reporting(0);

$_raw    = @file_get_contents('jsondata/nws_alerts.txt');
$_data   = @json_decode($_raw, true);
$_alerts = $_data['alerts'] ?? [];

// Filter expired alerts
$_now = new DateTime('now', new DateTimeZone('UTC'));
$_alerts = array_values(array_filter($_alerts, function($a) use ($_now) {
    if (empty($a['expires'])) return true;
    try {
        $exp = new DateTime($a['expires']);
        $exp->setTimezone(new DateTimeZone('UTC'));
        return $exp > $_now;
    } catch (Exception $e) { return true; }
}));

$is_dark   = ($theme !== 'light');
$bg        = $is_dark ? '#151819' : '#fff';
$bg_chrome = $is_dark ? '#1e2124' : '#f0f2f5';
$bg_card   = $is_dark ? '#252729' : '#e8eaef';
$text      = $is_dark ? '#ddd'    : '#222';
$text_dim  = $is_dark ? '#777'    : '#666';
$border    = $is_dark ? '#2e3033' : '#ccc';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NWS Weather Alerts</title>
<style>
html, body {
  height: 100%; overflow: hidden; margin: 0;
  background: <?php echo $bg; ?>; color: <?php echo $text; ?>;
  font-family: Arial, sans-serif; font-size: 13px;
}
body { display: flex; flex-direction: column; }

/* Header — always visible at top */
.pop-header {
  flex: 0 0 auto;
  display: flex; align-items: center; justify-content: space-between;
  padding: 6px 10px;
  background: <?php echo $bg_chrome; ?>;
  border-bottom: 1px solid <?php echo $border; ?>;
  font-family: weathertext2, Arial, sans-serif;
  font-size: 13px;
}
/* Last item must clear the lity close button in the top-right corner */
.pop-header .pop-last { margin-right: 50px; color: <?php echo $text_dim; ?>; font-size: 11px; }

/* Content area — fills remaining height, scrollable */
.pop-content {
  flex: 1; min-height: 0;
  position: relative; 
  overflow-y: auto;
  padding: 15px;
  box-sizing: border-box;
}

/* Premium Alert Cards */
.alert-card {
    margin-bottom: 15px;
    padding: 15px;
    border-radius: 4px;
    background: <?php echo $bg_card; ?>;
    border: 1px solid <?php echo $border; ?>;
    border-left-width: 5px;
}
.alert-card.severity-extreme { border-left-color: #d9534f; }
.alert-card.severity-severe  { border-left-color: #e8822a; }
.alert-card.severity-moderate { border-left-color: #e8c22a; }
.alert-card.severity-minor    { border-left-color: #5bc0de; }
.alert-card.severity-unknown  { border-left-color: #aaaaaa; }

.alert-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid <?php echo $border; ?>;
    padding-bottom: 6px;
    margin-bottom: 10px;
}
.alert-event-name {
    font-family: weathertext2, Arial, sans-serif;
    font-size: 14px;
    font-weight: bold;
}
.alert-badge {
    padding: 2px 8px;
    font-size: 9px;
    font-weight: bold;
    border-radius: 3px;
    text-transform: uppercase;
    font-family: Arial, sans-serif;
}
.alert-badge.severity-extreme { background: #d9534f; color: #fff; }
.alert-badge.severity-severe  { background: #e8822a; color: #fff; }
.alert-badge.severity-moderate { background: #e8c22a; color: #000; }
.alert-badge.severity-minor    { background: #5bc0de; color: #000; }
.alert-badge.severity-unknown  { background: #aaaaaa; color: #000; }

.alert-meta-row {
    font-size: 11px;
    margin-bottom: 6px;
    color: <?php echo $text_dim; ?>;
}
.alert-meta-row strong {
    color: <?php echo $is_dark ? 'silver' : '#444'; ?>;
}
.alert-description {
    background: <?php echo $is_dark ? '#1a1d1f' : '#f5f6f8'; ?>;
    border: 1px solid <?php echo $border; ?>;
    border-radius: 3px;
    padding: 10px;
    font-family: monospace;
    font-size: 11px;
    line-height: 1.4;
    white-space: pre-wrap;
    color: <?php echo $text; ?>;
    margin-top: 10px;
}
.no-alerts {
    text-align: center;
    padding: 30px;
    color: <?php echo $text_dim; ?>;
    font-size: 13px;
}
</style>
</head>
<body>

<div class="pop-header">
  <div>Active NWS Weather Alerts<?php if (!empty($stationName)) echo ' for ' . htmlspecialchars($stationName); ?></div>
  <div class="pop-last">Updated: <?php echo date($timeFormat); ?></div>
</div>

<div class="pop-content">
<?php if (count($_alerts) === 0): ?>
  <div class="no-alerts">No Active Weather Alerts at present.</div>
<?php else: ?>
  <?php foreach ($_alerts as $a):
    $sev = $a['severity'] ?? 'Unknown';
    $sev_class = 'severity-' . strtolower($sev);
  ?>
  <div class="alert-card <?php echo $sev_class; ?>">
    <div class="alert-header">
      <span class="alert-event-name"><?php echo htmlspecialchars($a['event']); ?></span>
      <span class="alert-badge <?php echo $sev_class; ?>"><?php echo htmlspecialchars($sev); ?></span>
    </div>
    
    <div class="alert-meta-row"><strong>Headline:</strong> <?php echo htmlspecialchars($a['headline']); ?></div>
    <?php if (!empty($a['effective'])): ?>
    <div class="alert-meta-row"><strong>Effective:</strong> <?php echo htmlspecialchars(date('D, M j, Y, g:i A', strtotime($a['effective']))); ?></div>
    <?php endif; ?>
    <?php if (!empty($a['expires'])): ?>
    <div class="alert-meta-row"><strong>Expires:</strong> <?php echo htmlspecialchars(date('D, M j, Y, g:i A', strtotime($a['expires']))); ?></div>
    <?php endif; ?>
    <div class="alert-meta-row" style="margin-bottom: 0;"><strong>Issued by:</strong> <?php echo htmlspecialchars($a['sender']); ?></div>
    
    <div class="alert-description"><?php echo htmlspecialchars(trim($a['description'])); ?></div>
  </div>
  <?php endforeach; ?>
<?php endif; ?>
</div>

</body>
</html>
