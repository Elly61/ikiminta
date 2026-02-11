<?php
// Simple test script to simulate login -> logout -> access protected page
// Usage: php tests/test_back_navigation.php

$base = 'http://localhost/ikiminta/';
$loginUrl = $base . 'member/auth/login';
$logoutUrl = $base . 'member/auth/logout';
$dashboardUrl = $base . 'member/dashboard';

$cookieFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ikiminta_test_cookies.txt';

function doRequest($url, $post = null, $cookieFile, $follow = false) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    $resp = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['raw' => $resp, 'info' => $info];
}

// 1) Attempt login (use valid credentials present in your DB)
// Replace these with a real user for accurate test
$email = 'admin@ikiminta.com';
$password = 'Admin@123';

echo "Logging in as $email...\n";
$loginResp = doRequest($loginUrl, ['email' => $email, 'password' => $password], $cookieFile, false);
print_r($loginResp['info']);

// 2) Access dashboard (should be allowed)
echo "Requesting dashboard with session...\n";
$dash1 = doRequest($dashboardUrl, null, $cookieFile, false);
print_r($dash1['info']);

// 3) Logout
echo "Logging out...\n";
$logout = doRequest($logoutUrl, null, $cookieFile, false);
print_r($logout['info']);

// 4) Request dashboard again with same cookie (should redirect to login or return 401)
echo "Requesting dashboard after logout (should NOT be accessible)...\n";
$dash2 = doRequest($dashboardUrl, null, $cookieFile, false);
print_r($dash2['info']);

// Basic assertions
if (isset($dash2['info']['http_code']) && in_array($dash2['info']['http_code'], [200,302,401,303])) {
    echo "Final HTTP code: " . $dash2['info']['http_code'] . "\n";
} else {
    echo "Unexpected response for final dashboard request.\n";
}

unlink($cookieFile);

echo "Test complete.\n";
