<?php
// aq_outlook.php: air-quality cause and outlook for the Air Quality card and the popout's Outlook & Cause tab.
// Reads jsondata/aq_outlook.json (scripts/aq_outlook_update.py, hourly): NOAA HMS satellite smoke over the station,
// the agency AQI forecast (AirNow), black carbon at a nearby monitor and the Open-Meteo (CAMS) model AQI.
include_once('w34_i18n.php');

// The outlook with stale parts dropped, or null when there is nothing usable.
function aqx_load($file = 'jsondata/aq_outlook.json') {
    if (!is_file($file)) return null;
    $ox = json_decode(@file_get_contents($file), true);
    if (!is_array($ox)) return null;
    $age = function ($part) { return isset($part['fetched']) ? time() - strtotime($part['fetched']) : PHP_INT_MAX; };
    // HMS analyses are per UTC day: keep today's or yesterday's
    if (isset($ox['smoke']) && (strtotime($ox['smoke']['date'] . ' UTC') < strtotime('yesterday UTC') || $age($ox['smoke']) > 86400)) unset($ox['smoke']);
    if (isset($ox['forecast'])) {
        // drop days already past; keep it only while it is less than a day old
        $today = date('Y-m-d');
        $ox['forecast']['days'] = array_values(array_filter($ox['forecast']['days'] ?? [], function ($d) use ($today) { return $d['date'] >= $today; }));
        if (!$ox['forecast']['days'] || $age($ox['forecast']) > 86400) unset($ox['forecast']);
    }
    if (isset($ox['monitor']) && (empty($ox['monitor']['time']) || time() - strtotime($ox['monitor']['time']) > 4 * 3600)) unset($ox['monitor']);
    if (isset($ox['model']) && $age($ox['model']) > 6 * 3600) unset($ox['model']);
    return (isset($ox['smoke']) || isset($ox['forecast'])) ? $ox : null;
}

// Current composite AQI and dominant pollutant (WAQI, jsondata/aq.txt), as on the card.
function aqx_now($file = 'jsondata/aq.txt') {
    $d = (is_file($file) && time() - filemtime($file) < 7200) ? (json_decode(@file_get_contents($file), true)['data'] ?? []) : [];
    return [(int)($d['aqi'] ?? 0), strtolower($d['dominentpol'] ?? '')];
}

function aqx_density($ox) {
    $d = $ox['smoke']['density'] ?? 'None';
    return in_array($d, ['Light', 'Medium', 'Heavy'], true) ? $d : '';
}

// Black carbon well above its usual level: combustion smoke at ground level.
function aqx_soot_high($ox) {
    return isset($ox['monitor']['bc_ratio']) && $ox['monitor']['bc_ratio'] >= 4;
}

// The card's one-line cause: [colour kind, title, detail].
function aqx_cause($aqi, $dom, $ox) {
    $den = aqx_density($ox);
    $thick = ($den === 'Medium' || $den === 'Heavy');
    $plume = ['Light' => w34t('AqxPlumeLight', 'light plume overhead'), 'Medium' => w34t('AqxPlumeMedium', 'medium plume overhead'),
              'Heavy' => w34t('AqxPlumeHeavy', 'heavy plume overhead')];
    $aloft = ['Light' => w34t('AqxAloftLight', 'Light smoke aloft'), 'Medium' => w34t('AqxAloftMedium', 'Medium smoke aloft'),
              'Heavy' => w34t('AqxAloftHeavy', 'Heavy smoke aloft')];
    $kind = ['Light' => 'light', 'Medium' => 'med', 'Heavy' => 'heavy'];
    // A medium/heavy satellite plume is wildfire smoke; soot alone may be wood burning, so it is just "smoke".
    if ($aqi > 50 && $dom === 'pm25' && $thick) return [$kind[$den], w34t('AqxWildfire', 'Wildfire smoke'), $plume[$den]];
    if ($aqi > 50 && $dom === 'pm25' && aqx_soot_high($ox)) return ['med', w34t('AqxSmoke', 'Smoke'), w34t('AqxSootGround', 'soot elevated at ground')];
    if ($aqi > 50 && $dom === 'o3') return ['o3', w34t('AqxOzone', 'Ozone'), w34t('AqxOzoneDetail', 'sun-made, peaks late afternoon')];
    if ($aqi > 50 && $dom === 'pm25') {
        $detail = $den ? w34t('AqxFineSmoke', 'with smoke aloft') : (isset($ox['smoke']) ? w34t('AqxHaze', 'regional haze, no smoke plume') : '');
        return ['light', w34t('AqxFine', 'Fine particles'), $detail];
    }
    if ($aqi > 50 && $dom) {
        $lbl = ['pm10' => 'PM10', 'no2' => 'NO₂', 'co' => 'CO', 'so2' => 'SO₂'][$dom] ?? strtoupper($dom);
        return ['light', $lbl, w34t('AqxDominant', 'dominant pollutant')];
    }
    if ($den) return [$kind[$den], $aloft[$den], w34t('AqxAloftDetail', 'satellite plume, air at ground good')];
    return ['none', w34t('AqxClean', 'Clean air'), isset($ox['smoke']) ? w34t('AqxNoPlume', 'no smoke plumes overhead') : ''];
}

// AirNow category name -> [level class suffix, short card label, long label]
function aqx_cat($cat) {
    $m = [
        'Good'                           => ['good',      w34t('AqxGoodShort', 'Good'), w34t('AqGood', 'Good')],
        'Moderate'                       => ['moderate',  w34t('AqxModShort', 'Mod'),   w34t('AqModerate', 'Moderate')],
        'Unhealthy for Sensitive Groups' => ['usg',       w34t('AqxUsgShort', 'USG'),   w34t('AqxUsgLong', 'Unhealthy (sensitive)')],
        'Unhealthy'                      => ['unhealthy', w34t('AqxUnhShort', 'Unh'),   w34t('AqUnhealthy', 'Unhealthy')],
        'Very Unhealthy'                 => ['vu',        w34t('AqxVunhShort', 'V.Unh'), w34t('AqVeryUnhealthy', 'V. Unhealthy')],
        'Hazardous'                      => ['hazard',    w34t('AqxHazShort', 'Haz'),   w34t('AqHazardous', 'Hazardous')],
    ];
    return $m[$cat] ?? ['none', '--', $cat];
}

function aqx_pol($pol) {
    return ['OZONE' => w34t('AqxOzone', 'Ozone'), 'PM2.5' => 'PM2.5', 'PM10' => 'PM10', 'NO2' => 'NO₂', 'CO' => 'CO', 'SO2' => 'SO₂'][$pol] ?? $pol;
}

// Day label for a forecast date: 'Today', or the short weekday.
function aqx_day($date) {
    return $date === date('Y-m-d') ? w34t('AqxToday', 'Today') : w34_date('D', strtotime($date . ' 12:00'));
}

// The popout's "why" paragraph (HTML).
function aqx_why($aqi, $dom, $ox) {
    $den = aqx_density($ox);
    $denlc = ['Light' => w34t('AqxDenLightLc', 'light'), 'Medium' => w34t('AqxDenMediumLc', 'medium'), 'Heavy' => w34t('AqxDenHeavyLc', 'heavy')];
    $s = [];
    if (!isset($ox['smoke'])) {
        // no analysis to go on
    } elseif (!$den) {
        $s[] = w34t('AqxWhyNoPlume', 'No satellite smoke plume is mapped over the station.');
    } elseif ($aqi > 50 && $dom === 'pm25') {
        $s[] = w34f('AqxWhyPlumeGround', 'Satellite analysts mapped a %s smoke plume over the station, and PM2.5 is the dominant pollutant at ground level.', '<b>' . $denlc[$den] . '</b>');
    } elseif ($aqi <= 50) {
        $s[] = w34f('AqxWhyPlumeAloft', 'Satellite analysts mapped a %s smoke plume over the station, but the air at ground level is good, so the smoke is passing overhead rather than mixing down.', '<b>' . $denlc[$den] . '</b>');
    } else {
        $s[] = w34f('AqxWhyPlumeOther', 'Satellite analysts mapped a %s smoke plume over the station.', '<b>' . $denlc[$den] . '</b>');
    }
    if (isset($ox['monitor']['bc'], $ox['monitor']['bc_ratio'], $ox['monitor']['bc_base'])) {
        $m = $ox['monitor'];
        $s[] = w34f('AqxWhySoot', 'Black carbon (soot) at the %s monitor is %s µg/m³, %s× its usual %s µg/m³.',
                    htmlspecialchars($m['name']), '<b>' . round($m['bc'], 2) . '</b>', round($m['bc_ratio'], 1), round($m['bc_base'], 2))
             . (aqx_soot_high($ox) ? ' ' . w34t('AqxWhySootHigh', 'That points to combustion smoke at ground level (wildfire or wood burning).') : '');
    }
    if ($aqi > 50 && $dom === 'o3') {
        $s[] = w34t('AqxWhyOzone', 'Ozone is the dominant pollutant: it forms in sunlight from vehicle and industrial emissions and peaks in the afternoon.');
    } elseif ($aqi > 50 && $dom === 'pm25' && !$den && !aqx_soot_high($ox)) {
        $s[] = w34t('AqxWhyFine', 'Fine particles (PM2.5) dominate without a smoke signal, which usually means regional haze or local emissions.');
    }
    if (isset($ox['forecast'])) {
        $d = $ox['forecast']['days'];
        $c0 = aqx_cat($d[0]['cat'])[2];
        $c1 = isset($d[1]) ? aqx_cat($d[1]['cat'])[2] : '';
        $day = function ($date) { return $date === date('Y-m-d') ? w34t('AqxTodayLc', 'today') : w34_date('l', strtotime($date . ' 12:00')); };
        $s[] = $c1 !== ''
            ? w34f('AqxWhyForecast2', 'Forecast for %s: %s %s, %s %s.', htmlspecialchars($ox['forecast']['area']),
                   '<b>' . $c0 . '</b>', $day($d[0]['date']), '<b>' . $c1 . '</b>', $day($d[1]['date']))
            : w34f('AqxWhyForecast1', 'Forecast for %s: %s %s.', htmlspecialchars($ox['forecast']['area']), '<b>' . $c0 . '</b>', $day($d[0]['date']));
    }
    return implode(' ', $s);
}
