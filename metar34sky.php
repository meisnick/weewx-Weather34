<?php
// metar34sky.php — sets $sky_icon, $sky_desc, $metar34vismiles, $metar34viskm
// from jsondata/me.txt (CheckWX-compatible format written by metar_update.py)
// Does NOT re-include settings or w34CombinedData — safe to include anywhere.

$_metar_raw = @file_get_contents("jsondata/me.txt");
$_metar     = @json_decode($_metar_raw);
$_d         = $_metar->{'data'}[0] ?? null;

$metar34conditions = $_d->{'conditions'}[0]->{'code'} ?? '';
$metar34clouds     = $_d->{'clouds'}[0]->{'code'}     ?? '';
$metar34windir     = $_d->{'wind'}->{'degrees'}       ?? 0;
$metar34vismiles   = number_format(($_d->{'visibility'}->{'meters'} ?? 0) * 0.000621371, 1);
$metar34viskm      = number_format(($_d->{'visibility'}->{'meters'} ?? 0) * 0.001, 1);

// Day/night — reuse $dayPartNatural already set by w34CombinedData
$_isnight = isset($dayPartNatural) && $dayPartNatural === 'night';

// Condition-based icon (weather phenomena take priority over clouds)
if ($metar34conditions === '-SHRA' || $metar34conditions === 'SHRA') {
    $sky_icon = $_isnight ? '40n.svg' : '40d.svg';
    $sky_desc = "Light Rain\nShowers";
} elseif ($metar34conditions === '+SHRA') {
    $sky_icon = $_isnight ? '41n.svg' : '41d.svg';
    $sky_desc = "Heavy Rain\nShowers";
} elseif ($metar34conditions === '-RA') {
    $sky_icon = $_isnight ? '40n.svg' : '40d.svg';
    $sky_desc = "Light Rain\nShowers";
} elseif ($metar34conditions === '+RA') {
    $sky_icon = $_isnight ? '05n.svg' : '05d.svg';
    $sky_desc = "Moderate Rain\nShowers";
} elseif ($metar34conditions === 'RA') {
    $sky_icon = '46.svg';
    $sky_desc = "Light Rain\nShowers";
} elseif ($metar34conditions === 'SQ') {
    $sky_icon = '10w.svg';
    $sky_desc = "Rain Squall\nShowers";
} elseif ($metar34conditions === '-SN') {
    $sky_icon = '49.svg';
    $sky_desc = "Light Snow\nShowers";
} elseif ($metar34conditions === '+SN') {
    $sky_icon = '13.svg';
    $sky_desc = "Moderate Snow\nShowers";
} elseif ($metar34conditions === 'SN' || $metar34conditions === 'SG' || $metar34conditions === 'SNINCR') {
    $sky_icon = '13.svg';
    $sky_desc = "Snow Showers";
} elseif ($metar34conditions === 'IP' || $metar34conditions === 'PL') {
    $sky_icon = '12.svg';
    $sky_desc = "Sleet Showers";
} elseif ($metar34conditions === 'GR' || $metar34conditions === 'GS') {
    $sky_icon = '12.svg';
    $sky_desc = "Hail";
} elseif ($metar34conditions === 'HZ') {
    $sky_icon = $_isnight ? 'hazyn.svg' : 'hazyd.svg';
    $sky_desc = "Hazy\nConditions";
} elseif ($metar34conditions === 'FG' || $metar34conditions === 'BCFG' || $metar34conditions === 'NFG' || $metar34conditions === 'FZFG') {
    $sky_icon = '15.svg';
    $sky_desc = "Foggy\nConditions";
} elseif ($metar34conditions === 'BR' || $metar34conditions === 'NBR') {
    $sky_icon = '15.svg';
    $sky_desc = "Misty\nConditions";
} elseif ($metar34conditions === 'TS' || $metar34conditions === '-TS' || $metar34conditions === 'TSRA') {
    $sky_icon = '22.svg';
    $sky_desc = "Thunderstorm\nConditions";
} elseif ($metar34conditions === '+TS') {
    $sky_icon = '11.svg';
    $sky_desc = "Heavy\nThunderstorms";
} elseif ($metar34conditions === '+FC') {
    $sky_icon = $_isnight ? 'nsvrtsa.svg' : 'nsvrtsat.svg';
    $sky_desc = "Tornado /\nWaterspout";
} elseif (in_array($metar34conditions, ['DS','DU','PO','SA','SS','VA'])) {
    $sky_icon = $_isnight ? 'hazyn.svg' : 'hazyd.svg';
    $sky_desc = "Reduced\nVisibility";
// Cloud-based icon
} elseif ($metar34clouds === 'SKC' || $metar34clouds === 'CLR' || $metar34clouds === 'CAVOK') {
    $sky_icon = $_isnight ? '01n.svg' : '01d.svg';
    $sky_desc = $_isnight ? "Clear\nConditions" : "Sunny\nConditions";
} elseif ($metar34clouds === 'FEW') {
    $sky_icon = $_isnight ? '03n.svg' : '03d.svg';
    $sky_desc = "Partly Cloudy\nConditions";
} elseif ($metar34clouds === 'SCT') {
    $sky_icon = $_isnight ? '02n.svg' : '02d.svg';
    $sky_desc = "Mostly Scattered\nClouds";
} elseif ($metar34clouds === 'BKN') {
    $sky_icon = $_isnight ? '03n.svg' : '03d.svg';
    $sky_desc = "Mostly Cloudy\nConditions";
} elseif ($metar34clouds === 'OVC' || $metar34clouds === 'OVX') {
    $sky_icon = '04.svg';
    $sky_desc = "Overcast\nConditions";
} else {
    $sky_icon = 'offline.svg';
    $sky_desc = "Data Offline";
}
