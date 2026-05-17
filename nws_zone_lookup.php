<?php
header('Content-Type: application/json');

$cfgPath = '/usr/local/bin/w34config.py';

function writeCfg($path, $zones) {
    if (!is_writable($path)) {
        return "w34config.py not writable — run: sudo chown www-data {$path}";
    }
    $cfg = file_get_contents($path);
    $cfg = preg_replace('/^ALERT_ZONES\s*=\s*.*/m', "ALERT_ZONES = \"{$zones}\"", $cfg);
    file_put_contents($path, $cfg);
    return null;
}

// Manual override path
if (isset($_GET['manual'])) {
    $zones = trim($_GET['manual']);
    if (!preg_match('/^[A-Z]{2,3}\d{3}(,[A-Z]{2,3}\d{3})*$/', $zones)) {
        echo json_encode(['error' => 'Invalid format — expected codes like WIZ060,WIC089']);
        exit;
    }
    $err = writeCfg($cfgPath, $zones);
    echo $err ? json_encode(['error' => $err]) : json_encode(['zones' => $zones]);
    exit;
}

// Auto-detect path
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : 0;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : 0;

if ($lat === 0.0 && $lon === 0.0) {
    echo json_encode(['error' => 'Enter your Lat/Lon in the Location section above first.']);
    exit;
}

$ctx = stream_context_create(['http' => [
    'timeout' => 10,
    'header'  => "User-Agent: weewx-weather34/zone-lookup\r\nAccept: application/geo+json\r\n",
]]);

$body = @file_get_contents("https://api.weather.gov/points/{$lat},{$lon}", false, $ctx);
if ($body === false) {
    echo json_encode(['error' => 'NWS API request failed — check your internet connection.']);
    exit;
}

$data = json_decode($body, true);
$fzUrl = $data['properties']['forecastZone'] ?? '';
$coUrl = $data['properties']['county'] ?? '';

if (!$fzUrl || !$coUrl) {
    echo json_encode(['error' => 'Location not covered by NWS — this service is US-only.']);
    exit;
}

$zones = basename($fzUrl) . ',' . basename($coUrl);
$err = writeCfg($cfgPath, $zones);
echo $err ? json_encode(['error' => $err]) : json_encode(['zones' => $zones]);
