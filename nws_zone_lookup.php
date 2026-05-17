<?php
header('Content-Type: application/json');

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : 0;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : 0;

if ($lat === 0.0 && $lon === 0.0) {
    echo json_encode(['error' => 'Enter your Lat/Lon in the Location section above first.']);
    exit;
}

$url = "https://api.weather.gov/points/{$lat},{$lon}";
$ctx = stream_context_create(['http' => [
    'timeout' => 10,
    'header'  => "User-Agent: weewx-weather34/zone-lookup\r\nAccept: application/geo+json\r\n",
]]);

$body = @file_get_contents($url, false, $ctx);
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

$fzCode = basename($fzUrl);
$coCode = basename($coUrl);
$zones  = "{$fzCode},{$coCode}";

$cfgPath = '/usr/local/bin/w34config.py';
if (!is_writable($cfgPath)) {
    echo json_encode(['error' => "w34config.py not writable — run: sudo chown www-data {$cfgPath}"]);
    exit;
}

$cfg = file_get_contents($cfgPath);
$cfg = preg_replace('/^ALERT_ZONES\s*=\s*.*/m', "ALERT_ZONES = \"{$zones}\"", $cfg);
file_put_contents($cfgPath, $cfg);

echo json_encode(['zones' => $zones]);
