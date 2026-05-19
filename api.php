<?php
include('settings.php');
include('w34CombinedData.php');

$lightning = [];
@include('serverdata/lightningdata.php');

header('Content-Type: application/json');
header('Cache-Control: no-store');

echo json_encode([
    'weather'   => $weather,
    'lightning' => $lightning,
    'config'    => [
        'temp_units'  => $weather['temp_units']      ?? 'C',
        'wind_units'  => $windunit                   ?? 'mph',
        'rain_units'  => $weather['rain_units']      ?? 'mm',
        'baro_units'  => $weather['barometer_units'] ?? 'hPa',
        'theme'       => $theme                      ?? 'dark',
        'station'     => $stationlocation            ?? '',
        'lat'         => $lat                        ?? 0,
        'lon'         => $lon                        ?? 0,
        'chartsrc'    => $chartsource                ?? 'w34highcharts',
    ],
], JSON_UNESCAPED_UNICODE);
