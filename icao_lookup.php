<?php
header('Content-Type: application/json');

$cfgPath = '/usr/local/bin/w34config.py';

function writeCfg($path, $icao) {
    if (!is_writable($path)) {
        return "w34config.py not writable — run: sudo chown www-data {$path}";
    }
    $cfg = file_get_contents($path);
    $cfg = preg_replace('/^ICAO\s*=\s*.*/m', "ICAO = \"{$icao}\"", $cfg);
    file_put_contents($path, $cfg);
    return null;
}

// Manual override path
if (isset($_GET['manual'])) {
    $icao = strtoupper(trim($_GET['manual']));
    if (!preg_match('/^[A-Z]{4}$/', $icao)) {
        echo json_encode(['error' => 'Invalid format — expected a 4-letter ICAO code like KETB']);
        exit;
    }
    $err = writeCfg($cfgPath, $icao);
    echo $err ? json_encode(['error' => $err]) : json_encode(['icao' => $icao, 'name' => '']);
    exit;
}

// Auto-detect path
$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : 0;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : 0;

if ($lat === 0.0 && $lon === 0.0) {
    echo json_encode(['error' => 'Enter your Lat/Lon in the Location section above first.']);
    exit;
}

$headers = "User-Agent: weewx-weather34/icao-lookup\r\nAccept: application/geo+json\r\n";
$ctx = stream_context_create(['http' => ['timeout' => 10, 'header' => $headers]]);

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

$err = writeCfg($cfgPath, $icao);
echo $err ? json_encode(['error' => $err]) : json_encode(['icao' => $icao, 'name' => $name]);
