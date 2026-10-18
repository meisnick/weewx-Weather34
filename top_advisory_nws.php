<?php include_once('w34_i18n.php'); ?>
<?php
// top_advisory_nws.php — NWS active alerts for Washington County WI
// Replaces top_advisory_rw.php (EU MeteoAlarm / dead WU API)
include_once('shared_core.php');
include('shared_icons.php');
include('settings1.php');
error_reporting(0);

$_raw    = @file_get_contents("jsondata/nws_alerts.txt");
$_data   = @json_decode($_raw, true);
$_alerts = $_data['alerts'] ?? [];
// Filter expired alerts
$_now = new DateTime('now', new DateTimeZone('UTC'));
$_alerts = array_values(array_filter($_alerts, function($a) use ($_now) {
    if (empty($a['expires'])) return true;
    try { $exp = new DateTime($a['expires']); return $exp > $_now; }
    catch (Exception $e) { return true; }
}));
$_count  = count($_alerts);
$_age    = $_data['fetched'] ?? '';

// Severity CSS modifier map
$_sevclass = [
    'Extreme'  => 'mod-adv-sev-extreme',
    'Severe'   => 'mod-adv-sev-severe',
    'Moderate' => 'mod-adv-sev-moderate',
    'Minor'    => 'mod-adv-sev-minor',
    'Unknown'  => 'mod-adv-sev-unknown',
];
?>
<div class="mod-advisory">
<div class="wulargeforecasthome"><div class="wulargediv">
<div class="eqcirclehomeregional"><div class="eqtexthomeregional">
<?php if ($_count === 0): ?>
<spanelightning>
<alertadvisory><a alt="Alerts" title="<?php echo htmlspecialchars(w34t('NwsWeatherAlerts', 'NWS Weather Alerts')); ?>" href="pop_nwsalerts.php" data-lity><?php echo $newalertgreen; ?></a></alertadvisory>
<alertvalue><?php echo w34t('NoActive', 'No Active');?> <lightgreen><?php echo w34t('Advisories', 'Advisories');?></lightgreen></alertvalue>
</spanelightning>
<?php else: $a = $_alerts[0];
    $sev_cls = $_sevclass[$a['severity'] ?? ''] ?? 'mod-adv-sev-unknown';
    $evt     = htmlspecialchars($a['event']);
    
    // Dynamic font-size modifier based on length to prevent truncation cutoff
    $evt_cls = 'mod-adv-evt';
    $evt_len = strlen($evt);
    if ($evt_len > 22) {
        $evt_cls .= ' mod-adv-evt--sm';
    } elseif ($evt_len > 16) {
        $evt_cls .= ' mod-adv-evt--md';
    }
    
    // Construct the second line: "Until [Day] [Time] ([NWS Station])"
    $expires_str = '';
    if (!empty($a['expires'])) {
        $exp_ts = strtotime($a['expires']);
        if ($exp_ts) {
            $expires_str = 'Until ' . w34_date('D g:i A', $exp_ts);
        }
    }
    
    $sender = $a['sender'] ?? '';
    if ($sender) {
        $sender = str_replace('NWS ', '', $sender);
        // Shorten "Milwaukee/Sullivan WI" to "Milwaukee"
        $parts = explode('/', $sender);
        $sender_short = trim($parts[0]);
        if ($sender_short) {
            $expires_str .= ' (' . htmlspecialchars($sender_short) . ')';
        }
    }
    
    if (empty($expires_str)) {
        $expires_str = htmlspecialchars($a['headline'] ?: $evt);
        if (strlen($expires_str) > 60) { $expires_str = substr($expires_str, 0, 57) . '…'; }
    }
    
    $more = $_count > 1 ? ' <orange>(+'.(($_count-1)).' more)</orange>' : '';
?>
<spanelightning class="mod-adv-active <?php echo $sev_cls; ?>">
<alertadvisory><a alt="Alerts" title="<?php echo htmlspecialchars(w34t('NwsWeatherAlerts', 'NWS Weather Alerts')); ?>" href="pop_nwsalerts.php" data-lity><?php echo str_replace('#3b9cad', 'var(--adv-col)', $newalert); ?></a></alertadvisory>
<alertvalue class="mod-adv-val">
<span class="<?php echo $evt_cls; ?>"><?php echo $evt; ?><?php echo $more; ?></span>
<span class="mod-adv-exp"><?php echo $expires_str; ?></span>
</alertvalue>
</spanelightning>
<?php endif; ?>
</div></div></div></div>
</div><!-- /mod-advisory -->

