<?php
/**
 * NOAA OVATION Grid Parser - Localized Aurora Probability Generator
 * Designed for weather34 dashboard integration.
 * Runs in background via cron (recommended every 5 minutes).
 */

// Include station settings for coordinates
$dir = __DIR__;
include($dir . '/settings1.php');

if (!isset($lat) || !isset($lon)) {
    die("Error: Station latitude or longitude not configured in settings1.php.\n");
}

$target_file = $dir . '/jsondata/aurora_prob.json';
$url = 'https://services.swpc.noaa.gov/json/ovation_aurora_latest.json';

// Fetch the 1.5MB NOAA grid safely
$opts = [
    'http' => [
        'method' => 'GET',
        'timeout' => 15,
        'header' => "User-Agent: weather34-aurora-parser/1.0\r\n"
    ]
];
$context = stream_context_create($opts);
$raw_data = @file_get_contents($url, false, $context);

if ($raw_data === false) {
    die("Error: Failed to fetch OVATION grid from NOAA.\n");
}

$parsed = json_decode($raw_data, true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($parsed['coordinates'])) {
    die("Error: Failed to decode NOAA JSON or missing coordinates array.\n");
}

// Convert coordinates to grid indices
// NOAA Grid: Longitude 0 to 359, Latitude -90 to 90 (1-degree resolution)
$lon_norm = ($lon < 0) ? (360 + $lon) : $lon;
$target_lon = (int)round($lon_norm) % 360;
$target_lat = (int)round($lat);

$probability = 0;
$found = false;

// Search for the closest point
foreach ($parsed['coordinates'] as $pt) {
    if (count($pt) >= 3 && $pt[0] == $target_lon && $pt[1] == $target_lat) {
        $probability = (int)$pt[2];
        $found = true;
        break;
    }
}

if (!$found) {
    echo "Warning: Coordinates [Lon: $target_lon, Lat: $target_lat] not found in NOAA grid. Defaulting to 0%.\n";
}

$output = [
    'probability'      => $probability,
    'lat'              => $lat,
    'lon'              => $lon,
    'grid_lon'         => $target_lon,
    'grid_lat'         => $target_lat,
    'observation_time' => $parsed['Observation Time'] ?? '',
    'forecast_time'    => $parsed['Forecast Time'] ?? '',
    'last_updated'     => time()
];

$res = file_put_contents($target_file, json_encode($output, JSON_PRETTY_PRINT));
if ($res === false) {
    die("Error: Failed to write output to $target_file.\n");
}

echo "Success: Localized aurora probability calculated as $probability% for coordinates [$lat, $lon]. Saved to $target_file.\n";
?>
