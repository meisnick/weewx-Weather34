<?php
/* Helpers for the rebuilt top-bar YEAR cards:
 *   top_rainfallfyearmonth.php · top_windgustyear.php · top_temperatureyear.php
 * Emits clean, valid flexbox markup (see css/modules/topyears.css). Replaces the
 * legacy absolute-positioned, invalid-HTML templates. Colour thresholds are a
 * faithful port of the old per-unit <topred1>/<toporange1>/... decision chains. */

function yt_wind_class($v, $u) {
    // [red, orange, yellow, green] cut-offs per wind unit; below green => blue
    $th = ['km/h' => [60, 40, 30, 10], 'mph' => [40, 24, 18, 6],
           'm/s'  => [16.6, 11, 8.3, 2.7], 'kn' => [32.4, 21.6, 16.2, 5.4]];
    if (!isset($th[$u])) { $u = 'mph'; }
    list($r, $o, $y, $g) = $th[$u];
    if ($v > $r) return 'yt-red';
    if ($v > $o) return 'yt-orange';
    if ($v > $y) return 'yt-yellow1';
    if ($v > $g) return 'yt-green';
    return 'yt-blue';
}

function yt_temp_class($v, $u) {
    if ($u === 'C') {
        if ($v > 30)  return 'yt-red';
        if ($v >= 24) return 'yt-orange';
        if ($v > 18)  return 'yt-yellow1';
        if ($v > 12)  return 'yt-yellow2';
        if ($v >= 10) return 'yt-green';
        return 'yt-blue';
    }
    if ($v > 86)    return 'yt-red';
    if ($v >= 75)   return 'yt-orange';
    if ($v >= 64)   return 'yt-yellow1';
    if ($v > 53.6)  return 'yt-yellow2';
    if ($v >= 42.8) return 'yt-green';
    return 'yt-blue';
}

/* One card: fixed-width pill = a coloured body (cap + value+unit) stacked on a grey
 * "chin" footer holding the sub-label. Body/chin are separate blocks so the colour
 * can't peek behind the chin; equal width left and right. */
function yt_side($cls, $cap, $val, $unit, $sub) {
    return '<div class="yt-pill ' . $cls . '">'
         .   '<div class="yt-body">'
         .     '<span class="yt-cap">' . $cap . '</span>'
         .     '<span class="yt-val">' . $val . '<span class="yt-unit">' . $unit . '</span></span>'
         .   '</div>'
         .   '<div class="yt-chin">' . $sub . '</div>'
         . '</div>';
}
