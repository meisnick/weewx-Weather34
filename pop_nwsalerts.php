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

$is_dark   = ($theme !== 'light');
$bg        = $is_dark ? '#151819' : '#fff';
$bg_chrome = $is_dark ? '#1e2124' : '#f0f2f5';
$bg_card   = $is_dark ? '#252729' : '#e8eaef';
$text      = $is_dark ? '#ddd'    : '#222';
$text_dim  = $is_dark ? '#777'    : '#666';
$border    = $is_dark ? '#2e3033' : '#ccc';

// Curated premium desaturated color palette
$sev_colors = [
    'extreme'  => '#c84b4b', // Deep Crimson
    'severe'   => '#d0702c', // Warm Amber-Orange
    'moderate' => '#bfa128', // Golden Ochre (e.g. Beach Hazards)
    'minor'    => '#4a8b9f', // Muted Teal-Blue
    'unknown'  => '#76828a', // Slate-Gray
];

$_count = count($_alerts);
$_issued = $_data['fetched'] ?? '';
if ($_issued) {
    $_issued = date($timeFormat, strtotime($_issued));
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo htmlspecialchars($theme); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NWS Weather Alerts</title>
<style>
@font-face {
  font-family: weathertext2;
  src: url(css/fonts/verbatim-regular.woff) format("woff"),
       url(css/fonts/verbatim-regular.woff2) format("woff2"),
       url(css/fonts/verbatim-regular.ttf) format("truetype");
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
  height: 100%; overflow: hidden;
  font-family: Arial, sans-serif;
  font-size: 13px;
  background: <?php echo $bg; ?>;
  color: <?php echo $text; ?>;
  -webkit-font-smoothing: antialiased;
}
body { display: flex; flex-direction: column; }

/* ── Header strip ─────────────────────────────────────────────────────────── */
.pop-header {
  flex: 0 0 auto;
  background: <?php echo $bg_chrome; ?>;
  border-bottom: 1px solid <?php echo $border; ?>;
  padding: 5px 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.pop-title {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .8em;
  color: <?php echo $text; ?>;
  letter-spacing: 0.3px;
}
.pop-issued {
  font-size: .65em;
  color: <?php echo $text_dim; ?>;
  white-space: nowrap;
  margin-right: 50px; /* clear lity close button */
}

/* ── Content wrapper ─────────────────────────────────────────────────────── */
.pop-content {
  flex: 1;
  min-height: 0;
  position: relative;
  overflow: hidden;
}
.discussion-body {
  height: 100%;
  display: flex;
  flex-direction: column;
  padding: 8px 10px;
  overflow-y: auto;
  
  /* Hide scrollbar completely */
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.discussion-body::-webkit-scrollbar {
  display: none;
}

.fcst-card {
  background: <?php echo $bg_card; ?>;
  border-radius: 3px;
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  margin-bottom: 10px;
  flex: 0 0 auto;
}


.fcst-card-title {
  font-family: weathertext2, Arial, sans-serif;
  font-size: .75em;
  color: silver;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
  flex: 0 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.fcst-card-text {
  font-family: Arial, sans-serif;
  font-size: .85em;
  color: <?php echo $text; ?>;
  line-height: 1.5;
  white-space: pre-wrap;
  text-align: left;
  padding-right: 2px;
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

<!-- Header -->
<div class="pop-header">
  <span class="pop-title">Active NWS Weather Alerts</span>
  <span class="pop-issued">Fetched: <?php echo htmlspecialchars($_issued); ?></span>
</div>

<div class="pop-content">
  <div class="discussion-body">
  <?php if ($_count === 0): ?>
    <div class="no-alerts">No Active Weather Alerts at present.</div>
  <?php else: ?>
    <?php foreach ($_alerts as $a):
      $sev = $a['severity'] ?? 'Unknown';
      $sev_lower = strtolower($sev);
      $sev_class = 'severity-' . $sev_lower;
      $col = $sev_colors[$sev_lower] ?? $sev_colors['unknown'];
    ?>
    <div class="fcst-card <?php echo $sev_class; ?>">
      <div class="fcst-card-title">
        <span style="color: <?php echo $col; ?>; font-weight: bold;"><?php echo htmlspecialchars($a['event']); ?></span>
        <span style="font-size:0.75em;opacity:0.9;background-color:<?php echo $col; ?>;color:#fff;padding:2px 6px;border-radius:3px;font-family:Arial,sans-serif;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;"><?php echo htmlspecialchars($sev); ?></span>
      </div>
      
      <div style="font-size:0.7em;color:<?php echo $text_dim; ?>;margin-bottom:8px;line-height:1.4;border-bottom:1px solid <?php echo $border; ?>;padding-bottom:6px;">
        <strong>Headline:</strong> <span style="color: <?php echo $is_dark ? '#eee' : '#222'; ?>;"><?php echo htmlspecialchars($a['headline']); ?></span><br>
        <?php if (!empty($a['effective'])): ?><strong>Effective:</strong> <?php echo htmlspecialchars(date('D, M j, g:i A', strtotime($a['effective']))); ?> &nbsp;|&nbsp; <?php endif; ?>
        <?php if (!empty($a['expires'])): ?><strong>Expires:</strong> <span style="color: <?php echo $col; ?>; font-weight: bold;"><?php echo htmlspecialchars(date('D, M j, g:i A', strtotime($a['expires']))); ?></span><?php endif; ?>
      </div>
      
      <div class="fcst-card-text"><?php echo nl2br(htmlspecialchars(trim($a['description']))); ?></div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
  </div>
</div>

</body>
</html>
