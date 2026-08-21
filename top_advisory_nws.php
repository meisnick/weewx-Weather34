<?php
// top_advisory_nws.php — NWS active alerts for Washington County WI
// Replaces top_advisory_rw.php (EU MeteoAlarm / dead WU API)
include('shared_core.php');
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

// Colour map by severity
$_sevcolour = [
    'Extreme'  => '#d9534f',
    'Severe'   => '#e8822a',
    'Moderate' => '#e8c22a',
    'Minor'    => '#5bc0de',
    'Unknown'  => '#aaaaaa',
];
?>
<div class="mod-advisory">
<div class="wulargeforecasthome"><div class="wulargediv">
<div class="eqcirclehomeregional"><div class="eqtexthomeregional">
<?php if ($_count === 0): ?>
<spanelightning>
<alertadvisory><a alt="Alerts" title="Alerts" href="pop_nwsalerts.php" data-lity><?php echo $newalertgreen; ?></a></alertadvisory>
<alertvalue>No Active <lightgreen>Advisories</lightgreen></alertvalue>
</spanelightning>
<?php else: $a = $_alerts[0];
    $col  = $_sevcolour[$a['severity']] ?? '#aaaaaa';
    $evt  = htmlspecialchars($a['event']);
    
    // Dynamic font-size scaling based on length to prevent truncation cutoff
    $font_style = '';
    $evt_len = strlen($evt);
    if ($evt_len > 22) {
        $font_style = 'font-size:0.82em;line-height:1.2;';
    } elseif ($evt_len > 16) {
        $font_style = 'font-size:0.9em;';
    }
    
    // Construct the second line: "Until [Day] [Time] ([NWS Station])"
    $expires_str = '';
    if (!empty($a['expires'])) {
        $exp_ts = strtotime($a['expires']);
        if ($exp_ts) {
            $expires_str = 'Until ' . date('D g:i A', $exp_ts);
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
<spanelightning>
<alertadvisory><a alt="Alerts" title="Alerts" href="pop_nwsalerts.php" data-lity><?php echo str_replace('#3b9cad', $col, $newalert); ?></a></alertadvisory>
<alertvalue style="color:<?php echo $col; ?>;display:block;overflow:hidden;">
<span style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;<?php echo $font_style; ?>"><?php echo $evt; ?><?php echo $more; ?></span>
<span style="font-size:0.75em;color:#ccc;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo $expires_str; ?></span>
</alertvalue>
</spanelightning>
<?php endif; ?>
</div></div></div></div>
</div><!-- /mod-advisory -->
