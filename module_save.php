<?php
header('Content-Type: application/json');
error_reporting(0);

$topbar = json_decode($_POST['topbar'] ?? '[]', true);
$grid   = json_decode($_POST['grid']   ?? '[]', true);

if (!is_array($topbar) || !is_array($grid)) {
    echo json_encode(['error' => 'Invalid data received']);
    exit;
}

// Load existing config to preserve refresh intervals for known modules
$existing = [];
if (file_exists('modules.php')) {
    include('modules.php');
    foreach (array_merge($topbar_modules ?? [], $grid_modules ?? []) as $m) {
        $existing[$m['module']] = $m;
    }
}

// Default refresh intervals for modules not yet in config
$defaults = [
    'weather34clock.php'        => 60,
    'top_rainfallfyearmonth.php'=> 600,
    'top_lightning.php'         => 600,
    'top_advisory_nws.php'      => 300,
    'temperaturein.php'         => 60,
    'forecast3om.php'           => 900,
    'forecast3omlarge.php'      => 900,
    'currentconditionsw34.php'  => 600,
    'windspeeddirection.php'    => 4,
    'barometer.php'             => 300,
    'sun3.php'                  => 3600,
    'rainfall.php'              => 60,
    'moonphase.php'             => 600,
    'lightning34.php'           => 60,
    'indoortemperature.php'     => 600,
    'airqualitymodule.php'      => 300,
    'weather34uvsolar.php'      => 300,
    'solaruv.php'               => 300,
    'solaruvwu.php'             => 300,
    'webcamsmall.php'           => 60,
];

function buildArray($items, $existing, $defaults) {
    $out = [];
    foreach ($items as $item) {
        $mod     = preg_replace('/[^a-zA-Z0-9_.\-]/', '', $item['module'] ?? '');
        $title   = htmlspecialchars($item['title'] ?? '', ENT_QUOTES);
        $refresh = isset($existing[$mod]) ? (int)$existing[$mod]['refresh']
                 : ($defaults[$mod] ?? 60);
        if ($mod) {
            $out[] = ['module' => $mod, 'title' => $title, 'refresh' => $refresh];
        }
    }
    return $out;
}

$new_topbar = buildArray($topbar, $existing, $defaults);
$new_grid   = buildArray($grid,   $existing, $defaults);

$php  = "<?php\n";
$php .= "// modules.php — live module layout (gitignored, auto-created from modules.example.php)\n\n";
$php .= "\$topbar_modules = " . var_export($new_topbar, true) . ";\n\n";
$php .= "\$grid_modules = "   . var_export($new_grid,   true) . ";\n";

$path = __DIR__ . '/modules.php';
if (file_put_contents($path, $php) === false) {
    echo json_encode(['error' => 'Could not write modules.php — check file permissions']);
    exit;
}

echo json_encode(['success' => true, 'topbar' => count($new_topbar), 'grid' => count($new_grid)]);
