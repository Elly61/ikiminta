<?php
/**
 * Detailed Registration Test - Show all errors
 */

define('BASE_URL', 'https://e-kimina.onrender.com/');

echo "=== DETAILED REGISTRATION TEST ===\n\n";

// Generate unique test credentials
$timestamp = time();
$username = 'test' . $timestamp;
$email = 'test' . $timestamp . '@example.com';
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

echo "Test Data:\n";
foreach ($testData as $key => $value) {
    echo "  $key: $value\n";
}
echo "\n";

// Make the registration request
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

echo "Sending registration request to: " . BASE_URL . "member/auth/register\n\n";

$response = @file_get_contents(BASE_URL . 'member/auth/register', false, $context);

echo "Raw Response:\n";
echo $response . "\n\n";

$responseData = json_decode($response, true);

echo "Parsed Response:\n";
echo "  Status: " . ($responseData['status'] ?? 'N/A') . "\n";
echo "  Message: " . ($responseData['message'] ?? 'N/A') . "\n";
echo "  Data: " . json_encode($responseData['data'] ?? []) . "\n\n";

if ($responseData['status'] === 'success') {
    echo "✓ Registration succeeded - but user may not be saved to database\n";
    echo "  Check Render logs to see if database connection worked\n";
} else {
    echo "✗ Registration failed\n";
}

?>
