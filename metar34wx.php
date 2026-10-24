<?php
// metar34wx.php — shared METAR helpers for the dashboard and the METAR popup.
//   metar34_load()          the report node from jsondata/me.txt, or null when missing or older than METAR34_MAX_AGE
//   metar34_age()           seconds since the report was observed (null when unknown)
//   metar34_sky($node, $isnight)  [icon file, description] from the present-weather tokens and cloud layers
// Present weather is decoded by token (intensity - / + / VC, descriptors SH TS FZ BC MI..., phenomena RA SN DZ...):
// no prefix is MODERATE, - light, + heavy, VC "nearby". Cloud cover uses the most-covered layer, not the lowest.

if (!defined('METAR34_MAX_AGE')) define('METAR34_MAX_AGE', 7200); // older than 2 h is not "current conditions"

function metar34_raw_node() {
    static $node = false;
    if ($node === false) {
        $d = @json_decode(@file_get_contents(__DIR__ . '/jsondata/me.txt'));
        $node = $d->{'data'}[0] ?? null;
    }
    return $node;
}

function metar34_age($node = null) {
    $node = $node ?? metar34_raw_node();
    $t = $node ? strtotime($node->{'observed'} ?? '') : false;
    return $t ? time() - $t : null;
}

function metar34_load() {
    $node = metar34_raw_node();
    if (!$node || empty($node->{'raw_text'})) return null;
    $age = metar34_age($node);
    if ($age === null || $age > METAR34_MAX_AGE) return null;
    return $node;
}

// '-SHRA' -> ['int' => '-', 'desc' => ['SH'], 'ph' => ['RA']]
function metar34_token($tok) {
    $tok = strtoupper(trim($tok));
    $int = '';
    if (substr($tok, 0, 2) === 'VC') { $int = 'VC'; $tok = substr($tok, 2); }
    elseif ($tok !== '' && ($tok[0] === '-' || $tok[0] === '+')) { $int = $tok[0]; $tok = substr($tok, 1); }
    $desc = array();
    while (preg_match('/^(MI|PR|BC|DR|BL|SH|TS|FZ)/', $tok, $m)) { $desc[] = $m[1]; $tok = substr($tok, 2); }
    $ph = str_split($tok, 2);
    if ($tok === '' || $ph === array('')) $ph = array();
    // old me.txt files carry the bare code 'SNINCR' (increasing snow)
    if ($tok === 'SNINCR') $ph = array('SN');
    return array('int' => $int, 'desc' => $desc, 'ph' => $ph);
}

function metar34_cloud_code($node) {
    $rank = array('SKC' => 0, 'CLR' => 0, 'NSC' => 0, 'NCD' => 0, 'CAVOK' => 0, 'FEW' => 1, 'SCT' => 2, 'BKN' => 3,
                  'OVC' => 4, 'OVX' => 4);
    $best = null;
    foreach (($node->{'clouds'} ?? array()) as $layer) {
        $c = strtoupper($layer->{'code'} ?? '');
        if (isset($rank[$c]) && ($best === null || $rank[$c] > $rank[$best])) $best = $c;
    }
    if ($best === null && strpos(strtoupper($node->{'raw_text'} ?? ''), ' VV') !== false) $best = 'OVX';
    return $best;
}

function metar34_sky($node, $isnight) {
    $dn = $isnight ? 'n' : 'd';
    if (!$node) return array('offline.svg', "Data Offline");
    $toks = array();
    foreach (($node->{'conditions'} ?? array()) as $c) $toks[] = metar34_token($c->{'code'} ?? '');
    // the first on-station token leads (METAR lists the most significant first); "nearby" only if nothing on station
    $lead = null; $near = null;
    foreach ($toks as $t) {
        if ($t['int'] === 'VC') { if (!$near) $near = $t; }
        elseif (!$lead && ($t['ph'] || $t['desc'])) $lead = $t;
    }
    if ($lead) {
        $i = $lead['int']; $d = $lead['desc']; $p = $lead['ph'];
        $has = function ($x) use ($p) { return in_array($x, $p); };
        if ($has('FC')) return array('11.svg', "Tornado /\nWaterspout"); // the old nsvrtsa(t).svg icons never existed
        if (in_array('TS', $d)) {
            if ($has('GR') || $has('GS')) return array('11.svg', "Thunderstorms\nWith Hail");
            return $i === '+' ? array('11.svg', "Heavy\nThunderstorms") : array('22.svg', "Thunderstorm\nConditions");
        }
        if (in_array('FZ', $d) && $has('RA')) return array('12.svg', "Freezing Rain");
        if (in_array('FZ', $d) && $has('DZ')) return array('12.svg', "Freezing Drizzle");
        if ($has('GR') || $has('GS')) return array('12.svg', "Hail");
        if ($has('PL') || $has('IP')) return array('12.svg', "Ice Pellets");
        if ($has('SN') && $has('RA')) return array('12.svg', "Sleet Showers");
        if ($has('SN')) {
            $icon = $i === '-' ? '49.svg' : '13.svg';
            if (in_array('SH', $d)) return array($icon, $i === '-' ? "Light Snow\nShowers" : ($i === '+' ? "Heavy Snow\nShowers" : "Snow Showers"));
            return array($icon, $i === '-' ? "Light Snow" : ($i === '+' ? "Heavy Snow" : "Snow"));
        }
        if ($has('SG')) return array('49.svg', "Snow Grains");
        if ($has('RA') || $has('UP')) {
            if (in_array('SH', $d)) {
                if ($i === '+') return array('41' . $dn . '.svg', "Heavy Rain\nShowers");
                return array('40' . $dn . '.svg', $i === '-' ? "Light Rain\nShowers" : "Rain Showers");
            }
            if ($i === '+') return array('05' . $dn . '.svg', "Heavy Rain");
            return $i === '-' ? array('40' . $dn . '.svg', "Light Rain") : array('46.svg', "Rain");
        }
        if ($has('DZ')) return array('40' . $dn . '.svg', $i === '+' ? "Heavy Drizzle" : "Drizzle");
        if (in_array('SH', $d)) return array('40' . $dn . '.svg', "Rain Showers");
        if ($has('SQ')) return array('10w.svg', "Rain Squall\nShowers");
        if ($has('FG')) return array('15.svg', "Foggy\nConditions");
        if ($has('BR')) return array('15.svg', "Misty\nConditions");
        if ($has('HZ')) return array($isnight ? 'hazyn.svg' : 'hazyd.svg', "Hazy\nConditions");
        if ($has('FU')) return array($isnight ? 'hazyn.svg' : 'hazyd.svg', "Smoke");
        if (array_intersect($p, array('DU', 'SA', 'VA', 'PO', 'SS', 'DS', 'PY', 'IC')))
            return array($isnight ? 'hazyn.svg' : 'hazyd.svg', "Reduced\nVisibility");
    }
    // clouds (with a "nearby" note taking the description when there is one)
    $cc = metar34_cloud_code($node);
    switch ($cc) {
        case 'SKC': case 'CLR': case 'NSC': case 'NCD': case 'CAVOK':
            $sky = array('01' . $dn . '.svg', $isnight ? "Clear\nConditions" : "Sunny\nConditions"); break;
        case 'FEW': $sky = array('02' . $dn . '.svg', $isnight ? "Mostly Clear" : "Mostly Sunny"); break;
        case 'SCT': $sky = array('03' . $dn . '.svg', "Partly Cloudy\nConditions"); break;
        case 'BKN': $sky = array('03' . $dn . '.svg', "Mostly Cloudy\nConditions"); break;
        case 'OVC': case 'OVX': $sky = array('04.svg', "Overcast\nConditions"); break;
        default: $sky = array('offline.svg', "Data Offline");
    }
    if ($near && in_array('TS', $near['desc'])) $sky[1] = "Thunderstorms\nNearby";
    elseif ($near && in_array('SH', $near['desc'])) $sky[1] = "Showers\nNearby";
    return $sky;
}
