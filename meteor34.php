<?php
// meteor34.php — one table of the major meteor showers (IMO calendar: activity window, peak, ZHR) for the moon module
// and the meteor popup. Dates are month/day; each shower's dates are placed around the previous, current and next
// year so windows that cross New Year (Quadrantids) work. Names match w34_meteor()'s translation list.

function meteor34_table() {
    //            name               active from  active to    peak         ZHR
    return array(
        array('Quadrantids',    array(12, 28), array(1, 12),  array(1, 3),   80),
        array('Lyrids',         array(4, 14),  array(4, 30),  array(4, 22),  18),
        array('ETA Aquarids',   array(4, 19),  array(5, 28),  array(5, 6),   50),
        array('Delta Aquarids', array(7, 12),  array(8, 23),  array(7, 30),  25),
        array('Perseids',       array(7, 17),  array(8, 24),  array(8, 12),  100),
        array('Draconids',      array(10, 6),  array(10, 10), array(10, 8),  10),
        array('Orionids',       array(10, 2),  array(11, 7),  array(10, 21), 20),
        array('South Taurids',  array(9, 10),  array(11, 20), array(10, 10), 5),
        array('North Taurids',  array(10, 20), array(12, 10), array(11, 12), 5),
        array('Leonids',        array(11, 6),  array(11, 30), array(11, 17), 15),
        array('Geminids',       array(12, 4),  array(12, 20), array(12, 14), 150),
        array('Ursids',         array(12, 17), array(12, 26), array(12, 22), 10),
    );
}

// every shower placed around last, this and next year: name, start, end (end of day), peak (midnight), zhr
function meteor34_occurrences($now) {
    $y = (int)date('Y', $now);
    $out = array();
    foreach (meteor34_table() as $s) {
        list($name, $from, $to, $peak, $zhr) = $s;
        $pk = $peak[0] * 100 + $peak[1];
        foreach (array($y - 1, $y, $y + 1) as $py) {
            $sy = ($from[0] * 100 + $from[1] > $pk) ? $py - 1 : $py;
            $ey = ($to[0] * 100 + $to[1] < $pk) ? $py + 1 : $py;
            $out[] = array('name' => $name, 'zhr' => $zhr,
                'start' => mktime(0, 0, 0, $from[0], $from[1], $sy),
                'end'   => mktime(23, 59, 59, $to[0], $to[1], $ey),
                'peak'  => mktime(0, 0, 0, $peak[0], $peak[1], $py));
        }
    }
    return $out;
}

// ['peak' => shower at its peak (peak day +/- 1) or null, 'approaching' => peak within 7 days or null,
//  'active' => strongest active shower or null, 'next' => the next peak after today]
function meteor34_status($now = null) {
    $now = $now ?? time();
    $st = array('peak' => null, 'approaching' => null, 'active' => null, 'next' => null);
    foreach (meteor34_occurrences($now) as $o) {
        if ($now >= $o['peak'] - 86400 && $now <= $o['peak'] + 2 * 86400 - 1) {
            if (!$st['peak'] || $o['zhr'] > $st['peak']['zhr']) $st['peak'] = $o;
        }
        if ($o['peak'] > $now + 86400 && $o['peak'] <= $now + 7 * 86400) {
            if (!$st['approaching'] || $o['peak'] < $st['approaching']['peak']) $st['approaching'] = $o;
        }
        if ($now >= $o['start'] && $now <= $o['end']) {
            if (!$st['active'] || $o['zhr'] > $st['active']['zhr']) $st['active'] = $o;
        }
        if ($o['peak'] > $now + 86400 && (!$st['next'] || $o['peak'] < $st['next']['peak'])) $st['next'] = $o;
    }
    return $st;
}

// short English text for the moon module, e.g. 'Orionids peak', 'Approaching Lyrids', 'Orionids'; null = none
function meteor34_now_text($st) {
    if ($st['peak']) return $st['peak']['name'] . ' peak';
    if ($st['approaching']) return 'Approaching ' . $st['approaching']['name'];
    if ($st['active']) return $st['active']['name'];
    return null;
}

// the popup's card: 'Meteor Shower<br> <orange1>Name</orange1>...' (translated by w34_meteor)
function meteor34_card($o, $peak_now) {
    $html = 'Meteor Shower<br> <orange1>' . $o['name'] . '</orange1><div class=date><br>';
    if ($peak_now) $html .= 'Peak Viewing Now<br>';
    $html .= 'Active ' . date('M jS', $o['start']) . '-' . date('M jS', $o['end']) . '<br>'
           . '<green>Estimated ZHR: </green><orange>' . $o['zhr'] . '</orange><br> Peaks <orange>'
           . date('M jS', $o['peak']) . '</orange></div>';
    return $html;
}

// 'Dec 28-Jan 12' style activity range for the guide
function meteor34_range($name) {
    foreach (meteor34_table() as $s) {
        if ($s[0] === $name) {
            return date('M d', mktime(0, 0, 0, $s[1][0], $s[1][1], 2001)) . '-' . date('M d', mktime(0, 0, 0, $s[2][0], $s[2][1], 2001));
        }
    }
    return '';
}
