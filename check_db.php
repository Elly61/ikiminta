<?php
/**
 * Direct Database Check
 */

require_once '/home/elly/Downloads/ikiminta/application/config/Database.php';

echo "═══════════════════════════════════════════\n";
echo "  DATABASE DEBUG CHECK\n";
echo "═══════════════════════════════════════════\n\n";

try {
    $db = Database::getInstance();
    
    // Get the most recent users
    echo "Recent users in database:\n";
    echo "──────────────────────────\n";
    $users = $db->select("SELECT id, username, email, status, date_created FROM users ORDER BY id DESC LIMIT 5");
    
    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "  Username: {$user['username']}\n";
        echo "  Email: {$user['email']}\n";
        echo "  Status: {$user['status']}\n";
        echo "  Created: {$user['date_created']}\n\n";
    }
    
    // Test a password verification
    if (!empty($users)) {
        echo "Password verification test:\n";
        echo "──────────────────────────\n";
        $testUser = $users[0];
        $testPass = 'BusTest@123';
        
        $fullUser = $db->selectOne('SELECT * FROM users WHERE id = ?', [$testUser['id']]);
        
        if ($fullUser) {
            $verified = password_verify($testPass, $fullUser['password']);
            echo "User: {$fullUser['username']}\n";
            echo "Test password: $testPass\n";
            echo "Password hash: " . substr($fullUser['password'], 0, 20) . "...\n";
            echo "Verification result: " . ($verified ? 'PASS' : 'FAIL') . "\n";
            echo "Status: {$fullUser['status']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Make sure you're running this from the workspace with proper config loaded.\n";
}

?>
