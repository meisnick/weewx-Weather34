<?php
// modules.example.php — default dashboard module layout
// Copy to modules.php to customise. modules.php is gitignored and never committed.
// Each entry: module filename, display title, refresh interval in seconds.

$topbar_modules = [
    ['module' => 'weather34clock.php',        'title' => 'Station Time',     'refresh' => 60],
    ['module' => 'top_rainfallfyearmonth.php', 'title' => 'Rainfall Totals', 'refresh' => 600],
    ['module' => 'top_lightning.php',          'title' => 'Lightning',       'refresh' => 600],
    ['module' => 'top_advisory_nws.php',       'title' => 'Weather Advisory','refresh' => 300],
];

$grid_modules = [
    ['module' => 'temperaturein.php',        'title' => '',                'refresh' => 60],
    ['module' => 'forecast3om.php',          'title' => '',                'refresh' => 900],
    ['module' => 'currentconditionsw34.php', 'title' => '',                'refresh' => 600],
    ['module' => 'windspeeddirection.php',   'title' => '',                'refresh' => 4],
    ['module' => 'barometer.php',            'title' => '',                'refresh' => 300],
    ['module' => 'sun3.php',                 'title' => '',                'refresh' => 3600],
    ['module' => 'rainfall.php',             'title' => '',                'refresh' => 60],
    ['module' => 'moonphase.php',            'title' => '',                'refresh' => 600],
    ['module' => 'lightning34.php',          'title' => 'Lightning',       'refresh' => 60],
];
