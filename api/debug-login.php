<?php
/**
 * Temporary debug script - DELETE AFTER USE
 */
require_once dirname(__FILE__) . '/../application/config/config.php';
require_once dirname(__FILE__) . '/../application/config/Database.php';

header('Content-Type: application/json');

$db = Database::getInstance();
$email = $_GET['email'] ?? 'test@gmial.com';

$user = $db->selectOne('SELECT id, email, username, status, user_type, password FROM users WHERE email = ?', [$email]);

if (!$user) {
    echo json_encode(['error' => 'User not found', 'email' => $email]);
    exit;
}

$passwordToTest = '12345678';
$hashValid = password_verify($passwordToTest, $user['password']);

echo json_encode([
    'user_found' => true,
    'id' => $user['id'],
    'email' => $user['email'],
    'username' => $user['username'],
    'status' => $user['status'],
    'user_type' => $user['user_type'],
    'password_hash_length' => strlen($user['password']),
    'password_hash_prefix' => substr($user['password'], 0, 7),
    'password_verify_result' => $hashValid,
    'test_password' => $passwordToTest
]);
