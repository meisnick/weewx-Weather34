<?php
// sun34.php — sunrise/sunset as "HH:MM" for a given zenith, replacing date_sunrise()/date_sunset() (deprecated in
// PHP 8.1, removed in 9). date_sun_info() has no zenith argument, and settings.php tunes $rise_zenith/$set_zenith,
// so this is the same algorithm PHP uses (timelib astro.c, after Paul Schlyter's sunriset.c), giving identical times.

if (!function_exists('w34_sun_hm')) {
    // $ts: any moment of the (local) day; $zenith in degrees; $utc_offset in hours; $set: false = rise, true = set.
    // Returns "HH:MM", or false when the sun does not cross that altitude that day (as date_sunrise did).
    function w34_sun_hm($ts, $lat, $lon, $zenith, $utc_offset, $set = false)
    {
        $sind = function ($x) { return sin(deg2rad($x)); };
        $cosd = function ($x) { return cos(deg2rad($x)); };
        $rev = function ($x) { return $x - 360.0 * floor($x / 360.0); };
        $rev180 = function ($x) { return $x - 360.0 * floor($x / 360.0 + 0.5); };

        // 00:00 UTC on the local calendar date, then d = days since J2000 at local noon mean solar time
        $utc0 = gmmktime(0, 0, 0, (int)date('n', $ts), (int)date('j', $ts), (int)date('Y', $ts));
        $d = ($utc0 - 946728000) / 86400 + 2 - $lon / 360.0;

        $sidtime = $rev($rev(180.0 + 356.0470 + 282.9404 + (0.9856002585 + 4.70935E-5) * $d) + 180.0 + $lon);

        // Sun's position
        $M = $rev(356.0470 + 0.9856002585 * $d);
        $w = 282.9404 + 4.70935E-5 * $d;
        $e = 0.016709 - 1.151E-9 * $d;
        $E = $M + $e * rad2deg(1) * $sind($M) * (1.0 + $e * $cosd($M));
        $x = $cosd($E) - $e;
        $y = sqrt(1.0 - $e * $e) * $sind($E);
        $r = sqrt($x * $x + $y * $y);
        $slon = rad2deg(atan2($y, $x)) + $w;
        if ($slon >= 360.0) $slon -= 360.0;
        $x = $r * $cosd($slon);
        $y = $r * $sind($slon);
        $obl = 23.4393 - 3.563E-7 * $d;
        $z = $y * $sind($obl);
        $y = $y * $cosd($obl);
        $ra = rad2deg(atan2($y, $x));
        $dec = rad2deg(atan2($z, sqrt($x * $x + $y * $y)));

        $tsouth = 12.0 - $rev180($sidtime - $ra) / 15.0;
        $altit = (90 - $zenith) - 0.2666 / $r;   // upper limb
        $cost = ($sind($altit) - $sind($lat) * $sind($dec)) / ($cosd($lat) * $cosd($dec));
        if ($cost >= 1.0 || $cost <= -1.0) return false;
        $t = rad2deg(acos($cost)) / 15.0;

        $n = ($set ? $tsouth + $t : $tsouth - $t) + $utc_offset;
        if ($n > 24 || $n < 0) $n -= floor($n / 24) * 24;
        return sprintf('%02d:%02d', (int)$n, (int)(60 * ($n - (int)$n)));
    }
}
