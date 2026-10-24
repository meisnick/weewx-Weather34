<?php
// metar34cloudcover.php — cloud cover % from jsondata/me.txt METAR sky codes.
// Defines metar34_cloudcover(): int 0..100, or null if there is no report or it is older than METAR34_MAX_AGE.
include_once(__DIR__ . '/metar34wx.php');
function metar34_cloudcover() {
    $node = metar34_load();
    if (!$node) return null;
    $map = ['SKC'=>0,'CLR'=>0,'NSC'=>0,'NCD'=>0,'CAVOK'=>0,'FEW'=>19,'SCT'=>44,'BKN'=>75,'OVC'=>100,'OVX'=>100];
    $c = metar34_cloud_code($node);
    return $c === null ? 0 : $map[$c];
}
