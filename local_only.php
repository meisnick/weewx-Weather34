<?php
/**
 * local_only.php — LAN-only gate for admin, setup and developer endpoints.
 *
 * include_once this at the top of any script that changes config or files, or exposes them.
 * Requests from outside the local network (including through the Cloudflare tunnel, where the
 * visitor's address arrives in CF-Connecting-IP) get a 403 and the script stops.
 */
if (!function_exists('is_local_ip')) {
    // Function to check if an IP is local/private
    function is_local_ip($ip) {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }
        // Check local IPv6 private/link-local ranges (fe80::, fc00::, fd00::)
        if (strpos($ip, ':') !== false) {
            $ip_lower = strtolower($ip);
            if (strpos($ip_lower, 'fe80:') === 0 || strpos($ip_lower, 'fc00:') === 0 || strpos($ip_lower, 'fd00:') === 0) {
                return true;
            }
            return false;
        }
        // Check standard IPv4 private subnets (192.168.x.x, 10.x.x.x, 172.16.x.x - 172.31.x.x)
        $ip_num = ip2long($ip);
        if ($ip_num !== false) {
            $private_ranges = [
                ['10.0.0.0', '10.255.255.255'],
                ['172.16.0.0', '172.31.255.255'],
                ['192.168.0.0', '192.168.255.255'],
            ];
            foreach ($private_ranges as $range) {
                if ($ip_num >= ip2long($range[0]) && $ip_num <= ip2long($range[1])) {
                    return true;
                }
            }
        }
        return false;
    }
}

if (!function_exists('w34_client_ip')) {
    function w34_client_ip() {
        // Behind the Cloudflare tunnel every request reaches Apache from 127.0.0.1; the real visitor is in CF-Connecting-IP
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
            return end($ips);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}

// a page that only needs is_local_ip()/w34_client_ip() sets this before including: no gate
if (!empty($w34_local_only_functions)) {
    return;
}

$client_ip = w34_client_ip();
if (!is_local_ip($client_ip)) {
    http_response_code(403);
    die(isset($w34_local_only_message) ? $w34_local_only_message : 'Access Denied: only allowed from the local network.');
}
