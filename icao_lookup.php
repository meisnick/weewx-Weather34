<?php
header('Content-Type: application/json');

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : 0;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : 0;

if ($lat === 0.0 && $lon === 0.0) {
    echo json_encode(['error' => 'Enter your Lat/Lon in the Location section above first.']);
    exit;
}

$headers = "User-Agent: weewx-weather34/icao-lookup\r\nAccept: application/geo+json\r\n";
$ctx = stream_context_create(['http' => ['timeout' => 10, 'header' => $headers]]);

// Step 1: get observationStations URL from points API
$pts = @file_get_contents("https://api.weather.gov/points/{$lat},{$lon}", false, $ctx);
if ($pts === false) {
    echo json_encode(['error' => 'NWS API request failed — check internet connection.']);
    exit;
}
$ptsData = json_decode($pts, true);
$stationsUrl = $ptsData['properties']['observationStations'] ?? '';
if (!$stationsUrl) {
    echo json_encode(['error' => 'Location not covered by NWS — this service is US-only.']);
    exit;
}

// Step 2: get nearest station ICAO
$sts = @file_get_contents($stationsUrl, false, $ctx);
if ($sts === false) {
    echo json_encode(['error' => 'Failed to fetch nearby stations from NWS.']);
    exit;
}
$stsData = json_decode($sts, true);
$features = $stsData['features'] ?? [];
if (empty($features)) {
    echo json_encode(['error' => 'No observation stations found near these coordinates.']);
    exit;
}

$icao = $features[0]['properties']['stationIdentifier'] ?? '';
$name = $features[0]['properties']['name'] ?? '';
if (!$icao) {
    echo json_encode(['error' => 'Could not determine ICAO code from NWS station data.']);
    exit;
}

// Step 3: write ICAO to w34config.py
$cfgPath = '/usr/local/bin/w34config.py';
if (!is_writable($cfgPath)) {
    echo json_encode(['error' => "w34config.py not writable — run: sudo chown www-data {$cfgPath}"]);
    exit;
}

$cfg = file_get_contents($cfgPath);
$cfg = preg_replace('/^ICAO\s*=\s*.*/m', "ICAO = \"{$icao}\"", $cfg);
file_put_contents($cfgPath, $cfg);

echo json_encode(['icao' => $icao, 'name' => $name]);
