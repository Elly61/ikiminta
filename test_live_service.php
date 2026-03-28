<?php
/**
 * Test Registration and Login on Live Render Service
 */

define('BASE_URL', 'https://ikimina.onrender.com/');

echo "=== TESTING LIVE RENDER SERVICE ===\n\n";

// Generate unique test credentials with timestamp
$timestamp = time();
$username = 'testuser' . $timestamp;
$email = 'testuser' . $timestamp . '@test.com';
$legalId = str_pad($timestamp, 16, '0', STR_PAD_LEFT);
$phoneNumber = '+1' . str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
$password = 'TestPass@12345';

$testData = [
    'username' => $username,
    'email' => $email,
    'first_name' => 'Test',
    'last_name' => 'User',
    'legal_id' => $legalId,
    'phone_number' => $phoneNumber,
    'password' => $password,
    'confirm_password' => $password
];

echo "Test Credentials:\n";
echo "  Username: $username\n";
echo "  Email: $email\n";
echo "  Password: $password\n\n";

// Step 1: Register
echo "STEP 1: Testing Registration...\n";

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($testData),
        'timeout' => 10
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$response = @file_get_contents(BASE_URL . 'member/auth/register', false, $context);
$responseData = json_decode($response, true);

if ($responseData && $responseData['status'] === 'success') {
    echo "✓ Registration successful!\n";
    echo "  Message: {$responseData['message']}\n\n";
} else {
    echo "✗ Registration failed\n";
    echo "  Response: " . substr($response, 0, 300) . "\n\n";
}

// Wait for database
echo "Waiting 3 seconds for database to sync...\n";
sleep(3);

// Step 2: Login
echo "\nSTEP 2: Testing Login...\n";

$loginContext = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'email' => $email,
            'password' => $password
        ]),
        'timeout' => 10
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$loginResponse = @file_get_contents(BASE_URL . 'member/auth/login', false, $loginContext);
$loginData = json_decode($loginResponse, true);

if ($loginData && $loginData['status'] === 'success') {
    echo "✓ Login successful!\n";
    echo "  Message: {$loginData['message']}\n";
    if ($loginData['data']['redirect'] ?? null) {
        echo "  Redirecting to: {$loginData['data']['redirect']}\n";
    }
    echo "\n✓ SYSTEM WORKING! Users are being saved to Neon database!\n";
} else {
    echo "✗ Login failed\n";
    echo "  Message: " . ($loginData['message'] ?? 'Unknown error') . "\n";
    echo "  This means the user was not found in Neon database\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Make sure DATABASE_URL is set on Render with your Neon connection string\n";
echo "2. Render should be using: ep-cool-sea-amwynevw-pooler.c-5.us-east-1.aws.neon.tech\n";
echo "3. If still not working, check Render logs for connection errors\n";

?>
