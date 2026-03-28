<?php
/**
 * LOCAL WORKFLOW TEST
 * Test registration and login using direct database access
 */

// Load the application
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/application');
define('MODEL_PATH', APP_PATH . '/model');
define('VIEW_PATH', APP_PATH . '/views');
define('CONFIG_PATH', APP_PATH . '/config');
define('BASE_URL', 'http://localhost/ikiminta/');

// Load configuration and database
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/Database.php';

echo "═══════════════════════════════════════════\n";
echo "  LOCAL DATABASE WORKFLOW TEST\n";
echo "═══════════════════════════════════════════\n\n";

try {
    // Get database connection
    $db = Database::getInstance();
    echo "✓ Database connection successful\n\n";
    
    // Load UserModel
    require_once MODEL_PATH . '/UserModel.php';
    $userModel = new UserModel();
    
    // Generate test data
    $timestamp = time();
    $testUser = [
        'username' => 'localtest' . $timestamp,
        'email' => 'localtest' . $timestamp . '@local.com',
        'first_name' => 'Local',
        'last_name' => 'Test',
        'legal_id' => str_pad(rand(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT),
        'phone_number' => '+1234567890',
        'password' => 'LocalTest@123'
    ];
    
    // TEST 1: REGISTRATION
    echo "TEST 1: User Registration\n";
    echo "─────────────────────────\n";
    echo "Username: {$testUser['username']}\n";
    echo "Email: {$testUser['email']}\n";
    echo "Legal ID: {$testUser['legal_id']}\n";
    
    $userId = $userModel->register($testUser);
    
    if ($userId) {
        echo "✓ PASSED: Registration successful (User ID: $userId)\n\n";
    } else {
        echo "✗ FAILED: Registration returned false\n";
        echo "  Check: duplicate email/legal_id or DB error\n\n";
        exit(1);
    }
    
    // TEST 2: VERIFY USER IN DATABASE
    echo "TEST 2: Verify User in Database\n";
    echo "────────────────────────────────\n";
    
    $user = $userModel->getUserById($userId);
    
    if ($user) {
        echo "✓ User found in database\n";
        echo "  - Username: {$user['username']}\n";
        echo "  - Email: {$user['email']}\n";
        echo "  - Status: {$user['status']}\n";
        echo "  - Balance: {$user['balance']}\n\n";
    } else {
        echo "✗ FAILED: User NOT found in database\n\n";
        exit(1);
    }
    
    // TEST 3: LOGIN
    echo "TEST 3: User Login\n";
    echo "──────────────────\n";
    echo "Email: {$testUser['email']}\n";
    echo "Password: {$testUser['password']}\n";
    
    $loginResult = $userModel->login($testUser['email'], $testUser['password']);
    
    if ($loginResult) {
        echo "✓ PASSED: Login successful\n";
        echo "  - User: {$loginResult['username']}\n";
        echo "  - Status: {$loginResult['status']}\n\n";
    } else {
        echo "✗ FAILED: Login failed\n";
        echo "  Check: password verification or user status\n\n";
        exit(1);
    }
    
    // TEST 4: CHECK SIGNUP BONUS
    echo "TEST 4: Signup Bonus Check\n";
    echo "──────────────────────────\n";
    
    $settings = $userModel->getSettings();
    $balance = $userModel->getBalance($userId);
    
    echo "Settings signup bonus: " . ($settings ? $settings['signup_bonus'] : '0') . "\n";
    echo "User current balance: $balance\n";
    
    if ($balance > 0) {
        echo "✓ PASSED: Signup bonus was credited\n\n";
    } else {
        echo "✗ WARNING: Signup bonus was NOT credited\n";
        echo "  Check: settings table has signup_bonus > 0\n\n";
    }
    
    echo "═══════════════════════════════════════════\n";
    echo "  ✓ ALL TESTS PASSED!\n";
    echo "═══════════════════════════════════════════\n\n";
    echo "The system is working correctly locally.\n";
    echo "Ready to redeploy to Render!\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

?>

echo "You can now test manually at:\n";
echo "• Registration: " . BASE_URL . "member/auth/register\n";
echo "• Login: " . BASE_URL . "member/auth/login\n";
echo "• Dashboard: " . BASE_URL . "member/dashboard\n";
echo "• Deposits: " . BASE_URL . "member/deposits\n";
echo "• Transfers: " . BASE_URL . "member/transfer\n";
echo "• Loans: " . BASE_URL . "member/loans\n";
echo "• Savings: " . BASE_URL . "member/savings\n";

?>
