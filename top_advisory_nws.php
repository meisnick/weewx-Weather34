<?php
// top_advisory_nws.php — NWS active alerts for Washington County WI
// Replaces top_advisory_rw.php (EU MeteoAlarm / dead WU API)
include('w34CombinedData.php');
include('settings1.php');
error_reporting(0);

$_raw    = @file_get_contents("jsondata/nws_alerts.txt");
$_data   = @json_decode($_raw, true);
$_alerts = $_data['alerts'] ?? [];
$_count  = $_data['count']  ?? 0;
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
<div class="wulargeforecasthome"><div class="wulargediv">
<div class="eqcirclehomeregional"><div class="eqtexthomeregional">
<?php if ($_count === 0): ?>
<spanelightning>
<alertadvisory><?php echo $newalertgreen; ?></alertadvisory>
<alertvalue>No Active <lightgreen>Advisories</lightgreen></alertvalue>
</spanelightning>
<?php else: $a = $_alerts[0];
    $col  = $_sevcolour[$a['severity']] ?? '#aaaaaa';
    $evt  = htmlspecialchars($a['event']);
    $sev  = htmlspecialchars($a['severity']);
    $head = htmlspecialchars($a['headline'] ?: $evt);
    // Shorten headline for the small box
    if (strlen($head) > 60) { $head = substr($head, 0, 57) . '…'; }
    $more = $_count > 1 ? ' <orange>(+'.(($_count-1)).' more)</orange>' : '';
?>
<spanelightning>
<alertadvisory><?php echo $newalert; ?></alertadvisory>
<alertvalue style="color:<?php echo $col; ?>">
<?php echo $evt; ?> <br>
<span style="font-size:0.75em;color:#ccc;"><?php echo $head; ?><?php echo $more; ?></span>
</alertvalue>
</spanelightning>
<?php endif; ?>
</div></div></div></div>
