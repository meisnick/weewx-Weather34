<?php
include('w34CombinedData.php');
include('settings1.php');
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NWS Weather Alerts</title>
  <style>
  /* shared */
  body { margin: 8px; font-family: Verdana, Arial, sans-serif; font-size: 12px; }
  .alert-container  { padding: 15px; border-radius: 5px; margin-bottom: 10px; }
  .alert-extreme    { background-color: #d9534f; color: #fff; }
  .alert-severe     { background-color: #e8822a; color: #fff; }
  .alert-moderate   { background-color: #e8c22a; color: #000; }
  .alert-minor      { background-color: #5bc0de; color: #000; }
  .alert-unknown    { background-color: #aaaaaa; color: #000; }
  .alert-title      { font-size: 16px; font-weight: bold; margin-bottom: 10px;
                       border-bottom: 1px solid rgba(128,128,128,.3); padding-bottom: 5px; }
  .alert-desc       { font-family: monospace; white-space: pre-wrap; margin-top: 10px; font-size: 11px; }
  .alert-meta       { font-size: 11px; margin-top: 5px; opacity: 0.8; }
  </style>
  <?php if ($theme === 'dark'): ?>
  <style>
  body { background-color: rgba(33,34,39,.9); color: #fff; }
  </style>
  <?php else: ?>
  <style>
  body { background-color: #f5f5f5; color: #000; }
  .alert-container { box-shadow: 1px 1px 4px rgba(0,0,0,.2); }
  </style>
  <?php endif; ?>
</head>
<body>
<div style="font-size:18px;margin-bottom:15px;text-align:center;">
  NWS Weather Alerts<?php if (!empty($stationName)) echo ' for ' . htmlspecialchars($stationName); ?>
</div>
<?php if (count($_alerts) === 0): ?>
  <div style="text-align:center;padding:20px;">No Active Weather Alerts</div>
<?php else: ?>
  <?php foreach ($_alerts as $a):
    $sevClass = match($a['severity']) {
        'Extreme'  => 'alert-extreme',
        'Severe'   => 'alert-severe',
        'Moderate' => 'alert-moderate',
        'Minor'    => 'alert-minor',
        default    => 'alert-unknown',
    };
    $truncated = strlen($a['description']) >= 400;
  ?>
  <div class="alert-container <?php echo $sevClass; ?>">
    <div class="alert-title"><?php echo htmlspecialchars($a['event']); ?></div>
    <div class="alert-meta"><strong>Headline:</strong> <?php echo htmlspecialchars($a['headline']); ?></div>
    <?php if (!empty($a['effective'])): ?>
    <div class="alert-meta"><strong>Effective:</strong> <?php echo htmlspecialchars(date('M j, Y, g:i A T', strtotime($a['effective']))); ?></div>
    <?php endif; ?>
    <?php if (!empty($a['expires'])): ?>
    <div class="alert-meta"><strong>Expires:</strong> <?php echo htmlspecialchars(date('M j, Y, g:i A T', strtotime($a['expires']))); ?></div>
    <?php endif; ?>
    <div class="alert-desc"><?php echo htmlspecialchars($a['description']); ?><?php if ($truncated): ?> <em>[description truncated]</em><?php endif; ?></div>
    <div class="alert-meta" style="margin-top:10px;"><em>Issued by <?php echo htmlspecialchars($a['sender']); ?></em></div>
  </div>
  <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
