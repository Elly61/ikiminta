<?php
/**
 * API: Lookup User by Phone Number
 * Used for transfer receiver lookup in real-time
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration
require_once dirname(__FILE__) . '/../application/config/config.php';
require_once dirname(__FILE__) . '/../application/config/Database.php';
require_once dirname(__FILE__) . '/../application/model/UserModel.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized'
    ]);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get JSON body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['phone_number'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Phone number is required'
    ]);
    exit;
}

$phoneNumber = trim($input['phone_number']);

// Look up user
$userModel = new UserModel();
$user = $userModel->getUserByPhoneNumber($phoneNumber);

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not found'
    ]);
    exit;
}

// Ensure user is active member and not the current user
if ($user['user_type'] !== 'member' || $user['status'] !== 'active' || $user['id'] == $_SESSION['user_id']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not available for transfer'
    ]);
    exit;
}

// Return user info
echo json_encode([
    'status' => 'success',
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'phone_number' => $user['phone_number']
    ]
]);
?>