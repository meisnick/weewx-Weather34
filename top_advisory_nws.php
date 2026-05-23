<?php
// top_advisory_nws.php — NWS active alerts for Washington County WI
// Replaces top_advisory_rw.php (EU MeteoAlarm / dead WU API)
include('w34CombinedData.php');
include('settings1.php');
error_reporting(0);

$_raw    = @file_get_contents("jsondata/nws_alerts.txt");
$_data   = @json_decode($_raw, true);
$_alerts = $_data['alerts'] ?? [];
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
    $head = htmlspecialchars($a['headline'] ?: $evt);
    if (strlen($head) > 60) { $head = substr($head, 0, 57) . '…'; }
    $more = $_count > 1 ? ' <orange>(+'.(($_count-1)).' more)</orange>' : '';
?>
<spanelightning>
<alertadvisory><a alt="Alerts" title="Alerts" href="pop_nwsalerts.php" data-lity><?php echo $newalert; ?></a></alertadvisory>
<alertvalue style="color:<?php echo $col; ?>;display:block;overflow:hidden;">
<span style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo $evt; ?><?php echo $more; ?></span>
<span style="font-size:0.75em;color:#ccc;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo $head; ?></span>
</alertvalue>
</spanelightning>
<?php endif; ?>
</div></div></div></div>
</div><!-- /mod-advisory -->
