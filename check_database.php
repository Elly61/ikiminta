<?php
/**
 * Debug Script - Check Database Connection
 */

echo "=== DATABASE CONNECTION DEBUG ===\n\n";

// Check if DATABASE_URL is set
$dbUrl = getenv('DATABASE_URL');
echo "1. DATABASE_URL environment variable: ";
if ($dbUrl) {
    echo "✓ SET\n";
    echo "   URL: " . substr($dbUrl, 0, 50) . "...\n";
    
    // Parse it
    $parsed = parse_url($dbUrl);
    echo "   Host: " . ($parsed['host'] ?? 'N/A') . "\n";
    echo "   User: " . ($parsed['user'] ?? 'N/A') . "\n";
    echo "   Database: " . ($parsed['path'] ?? 'N/A') . "\n";
} else {
    echo "✗ NOT SET\n";
}

echo "\n2. Individual DB environment variables:\n";
echo "   IK_DB_HOST: " . (getenv('IK_DB_HOST') ?: 'NOT SET') . "\n";
echo "   IK_DB_USER: " . (getenv('IK_DB_USER') ?: 'NOT SET') . "\n";
echo "   IK_DB_NAME: " . (getenv('IK_DB_NAME') ?: 'NOT SET') . "\n";
echo "   IK_DB_DRIVER: " . (getenv('IK_DB_DRIVER') ?: 'NOT SET') . "\n";

echo "\n3. Testing Database Connection...\n";

require_once __DIR__ . '/application/config/Database.php';

try {
    $db = Database::getInstance();
    echo "✓ Database connection successful!\n\n";
    
    // Check if users table exists
    echo "4. Checking users table...\n";
    $users = $db->select("SELECT COUNT(*) as count FROM users", []);
    echo "   Total users: " . $users[0]['count'] . "\n";
    
    // Show recent users
    echo "\n5. Recent users:\n";
    $recentUsers = $db->select(
        "SELECT id, username, email, status, date_created FROM users ORDER BY id DESC LIMIT 5",
        []
    );
    
    if (empty($recentUsers)) {
        echo "   No users found in database\n";
    } else {
        foreach ($recentUsers as $user) {
            echo "   - {$user['username']} ({$user['email']}) - Status: {$user['status']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Database connection failed!\n";
    echo "   Error: " . $e->getMessage() . "\n";
}

?>
