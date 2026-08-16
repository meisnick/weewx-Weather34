<?php
// metar34cloudcover.php — cloud cover % from jsondata/me.txt METAR sky codes.
// Defines metar34_cloudcover(): int 0..100, or null if me.txt unreadable.
function metar34_cloudcover() {
    $raw = @file_get_contents(__DIR__ . '/jsondata/me.txt');
    if ($raw === false) return null;
    $d = @json_decode($raw, true);
    $node = $d['data'][0] ?? null;
    if (!$node) return null;
    $map = ['SKC'=>0,'CLR'=>0,'NSC'=>0,'NCD'=>0,'CAVOK'=>0,'FEW'=>19,'SCT'=>44,'BKN'=>75,'OVC'=>100,'OVX'=>100];
    $pct = 0;
    foreach (($node['clouds'] ?? []) as $layer) {
        $c = strtoupper($layer['code'] ?? '');
        if (isset($map[$c])) $pct = max($pct, $map[$c]);
    }
    if (empty($node['clouds']) && strpos(strtoupper($node['raw_text'] ?? ''), 'VV') !== false) $pct = 100;
    return $pct;
}
