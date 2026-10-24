<?php
// metar34sky.php — sets $sky_icon, $sky_desc, $metar34vismiles, $metar34viskm
// from jsondata/me.txt (aviationweather.gov format written by metar_update.py)
// Does NOT re-include settings or w34CombinedData — safe to include anywhere.
// $_d is null when there is no report or it is older than METAR34_MAX_AGE (2 h): callers show "not available".
include_once(__DIR__ . '/metar34wx.php');

$_d = metar34_load();

$metar34conditions = $_d->{'conditions'}[0]->{'code'} ?? '';
$metar34clouds     = $_d ? (metar34_cloud_code($_d) ?? '') : '';
$metar34windir     = $_d->{'wind'}->{'degrees'}       ?? 0;
$metar34vismiles   = number_format(($_d->{'visibility'}->{'meters'} ?? 0) * 0.000621371, 1);
$metar34viskm      = number_format(($_d->{'visibility'}->{'meters'} ?? 0) * 0.001, 1);

// Day/night — reuse $dayPartNatural already set by w34CombinedData
$_isnight = isset($dayPartNatural) && $dayPartNatural === 'night';

list($sky_icon, $sky_desc) = metar34_sky($_d, $_isnight);
