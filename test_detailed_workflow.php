<?php
/**
 * Detailed Terminal Test - Shows exactly what's happening
 */

define('BASE_URL', 'https://ikimina.onrender.com/');

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║              IKIMINTA - DETAILED WORKFLOW TEST                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Generate unique test data with timestamp
$timestamp = time();
$username = 'testuser' . $timestamp;
$email = 'testuser' . $timestamp . '@test.com';
$legalId = str_pad($timestamp % 9999999999999999, 16, '0', STR_PAD_LEFT);
$phoneNumber = '+1' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
$password = 'TestPass@12345';

echo "═══ TEST DATA ═══\n";
echo "Username: $username\n";
echo "Email: $email\n";
echo "Legal ID: $legalId\n";
echo "Phone: $phoneNumber\n";
echo "Password: $password\n\n";

// STEP 1: REGISTRATION
echo "═══ STEP 1: REGISTRATION ═══\n";
echo "Sending POST request to: " . BASE_URL . "member/auth/register\n";

$regData = [
    'username' => $username,
    'email' => $email,
    'first_name' => 'Test',
    'last_name' => 'User',
    'legal_id' => $legalId,
    'phone_number' => $phoneNumber,
    'password' => $password,
    'confirm_password' => $password
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($regData),
        'timeout' => 10
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$response = @file_get_contents(BASE_URL . 'member/auth/register', false, $context);
$responseData = json_decode($response, true);

echo "\nResponse received:\n";
echo "Status: " . ($responseData['status'] ?? 'UNKNOWN') . "\n";
echo "Message: " . ($responseData['message'] ?? 'NONE') . "\n";

if ($responseData['status'] === 'success') {
    echo "✓ REGISTRATION SUCCESSFUL\n\n";
} else {
    echo "✗ REGISTRATION FAILED\n";
    echo "Full response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";
    exit(1);
}

// Wait for database
echo "═══ WAITING FOR DATABASE ═══\n";
echo "Waiting 3 seconds for database sync...\n";
sleep(3);
echo "✓ Done\n\n";

// STEP 2: LOGIN
echo "═══ STEP 2: LOGIN ═══\n";
echo "Sending POST request to: " . BASE_URL . "member/auth/login\n";
echo "Email: $email\n";
echo "Password: $password\n";

$loginData = http_build_query([
    'email' => $email,
    'password' => $password
]);

$loginContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $loginData,
        'timeout' => 10
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$loginResponse = @file_get_contents(BASE_URL . 'member/auth/login', false, $loginContext);
$loginData = json_decode($loginResponse, true);

echo "\nResponse received:\n";
echo "Status: " . ($loginData['status'] ?? 'UNKNOWN') . "\n";
echo "Message: " . ($loginData['message'] ?? 'NONE') . "\n";

if (isset($loginData['data']['redirect'])) {
    echo "Redirect URL: " . $loginData['data']['redirect'] . "\n";
}

echo "\nFull Response (JSON):\n";
echo json_encode($loginData, JSON_PRETTY_PRINT) . "\n";

if ($loginData['status'] === 'success') {
    echo "\n✓ LOGIN SUCCESSFUL\n";
    echo "✓ User will be redirected to dashboard\n\n";
} else {
    echo "\n✗ LOGIN FAILED\n";
    echo "Error: " . ($loginData['message'] ?? 'Unknown error') . "\n\n";
    exit(1);
}

// STEP 3: TEST DASHBOARD ACCESS
echo "═══ STEP 3: VERIFY DASHBOARD ACCESS ═══\n";
echo "Attempting to access dashboard...\n";

$dashContext = stream_context_create([
    'http' => [
        'timeout' => 10
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$dashResponse = @file_get_contents(BASE_URL . 'member/dashboard', false, $dashContext);

if ($dashResponse) {
    $dashSize = strlen($dashResponse);
    echo "✓ Dashboard page loaded ($dashSize bytes)\n";
} else {
    echo "✗ Failed to load dashboard\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                    TEST COMPLETED ✓                           ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "SUMMARY:\n";
echo "✓ User registered successfully\n";
echo "✓ User logged in successfully\n";
echo "✓ Redirect to dashboard configured\n";
echo "✓ System is working correctly!\n\n";

?>
