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

// Text color helper based on theme
$text_color = ($theme === 'dark') ? 'silver' : 'black';
?>
<link href="css/popup.<?php echo $theme; ?>.css?version=<?php echo filemtime('css/popup.' . $theme . '.css'); ?>" rel="stylesheet prefetch">
<style>
/* Custom premium enhancements for alerts popout */
.alert-card {
    margin: 15px 0;
    padding: 18px;
    border-radius: 6px;
    background: rgba(33, 34, 39, 0.4);
    border: 1px solid rgba(84, 85, 86, 0.2);
    border-left-width: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.theme-light .alert-card {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-left-width: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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
    border-bottom: 1px solid rgba(84, 85, 86, 0.3);
    padding-bottom: 8px;
    margin-bottom: 12px;
}
.alert-event-name {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.3px;
}
.alert-badge {
    padding: 3px 10px;
    font-size: 10px;
    font-weight: bold;
    border-radius: 12px;
    text-transform: uppercase;
}
.alert-badge.severity-extreme { background: #d9534f; color: #fff; }
.alert-badge.severity-severe  { background: #e8822a; color: #fff; }
.alert-badge.severity-moderate { background: #e8c22a; color: #000; }
.alert-badge.severity-minor    { background: #5bc0de; color: #000; }
.alert-badge.severity-unknown  { background: #aaaaaa; color: #000; }

.alert-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 8px;
    font-size: 11px;
    opacity: 0.85;
    margin-bottom: 12px;
}
.alert-meta-item strong {
    color: var(--orange, #e8822a);
}
.alert-description-container {
    background: rgba(10, 10, 12, 0.5);
    border: 1px solid rgba(84, 85, 86, 0.15);
    border-radius: 4px;
    padding: 12px;
    font-family: 'Courier New', Courier, monospace;
    font-size: 11px;
    line-height: 1.5;
    white-space: pre-wrap;
    max-height: 300px;
    overflow-y: auto;
    color: #e0e0e0;
}
.theme-light .alert-description-container {
    background: rgba(240, 240, 245, 0.8);
    border: 1px solid rgba(0, 0, 0, 0.08);
    color: #222;
}
.no-alerts-placeholder {
    text-align: center;
    padding: 40px;
    font-size: 14px;
    opacity: 0.8;
}
</style>

<body class="theme-<?php echo $theme; ?>">
<div class="weather34darkbrowser" style="color:<?php echo $text_color; ?>;" url="National Weather Service — Active Alert Advisories for <?php echo htmlspecialchars($stationlocation); ?>"></div>

<div style="padding: 10px 15px;">

<?php if (count($_alerts) === 0): ?>
  <div class="no-alerts-placeholder" style="color:<?php echo $text_color; ?>;">
    No Active NWS Weather Alerts at present.
  </div>
<?php else: ?>
  <?php foreach ($_alerts as $a):
    $sev = $a['severity'] ?? 'Unknown';
    $sev_class = 'severity-' . strtolower($sev);
  ?>
  <div class="alert-card <?php echo $sev_class; ?>">
    <div class="alert-header">
      <span class="alert-event-name" style="color:<?php echo $text_color; ?>;"><?php echo htmlspecialchars($a['event']); ?></span>
      <span class="alert-badge <?php echo $sev_class; ?>"><?php echo htmlspecialchars($sev); ?></span>
    </div>
    
    <div class="alert-meta-grid" style="color:<?php echo $text_color; ?>;">
      <div class="alert-meta-item"><strong>Headline:</strong> <?php echo htmlspecialchars($a['headline']); ?></div>
      <?php if (!empty($a['effective'])): ?>
      <div class="alert-meta-item"><strong>Effective:</strong> <?php echo htmlspecialchars(date('D, M j, Y, g:i A', strtotime($a['effective']))); ?></div>
      <?php endif; ?>
      <?php if (!empty($a['expires'])): ?>
      <div class="alert-meta-item"><strong>Expires:</strong> <?php echo htmlspecialchars(date('D, M j, Y, g:i A', strtotime($a['expires']))); ?></div>
      <?php endif; ?>
      <div class="alert-meta-item"><strong>Issuer:</strong> <?php echo htmlspecialchars($a['sender']); ?></div>
    </div>
    
    <div class="alert-description-container"><?php echo htmlspecialchars(trim($a['description'])); ?></div>
  </div>
  <?php endforeach; ?>
<?php endif; ?>

</div>
</body>
