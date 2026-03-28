<?php
/**
 * Test Neon Database Connection with the actual Neon URL
 */

echo "=== NEON DATABASE CONNECTION TEST ===\n\n";

// The Neon URL you provided
$neonUrl = "postgresql://neondb_owner:npg_kuyt7Y4IxSUo@ep-cool-sea-amwynevw-pooler.c-5.us-east-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require";

echo "Testing connection to: ep-cool-sea-amwynevw-pooler.c-5.us-east-1.aws.neon.tech\n\n";

// Parse the URL
$parsed = parse_url($neonUrl);
$host = $parsed['host'] ?? 'localhost';
$port = $parsed['port'] ?? 5432;
$user = $parsed['user'] ?? 'root';
$pass = $parsed['pass'] ?? '';
$dbname = ltrim($parsed['path'] ?? '/neondb', '/');

echo "Connection Details:\n";
echo "  Host: $host\n";
echo "  Port: $port\n";
echo "  User: $user\n";
echo "  Database: $dbname\n\n";

// Try to connect
try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✓ CONNECTED TO NEON SUCCESSFULLY!\n\n";
    
    // Check tables
    echo "Checking database tables...\n";
    $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll();
    
    if (empty($tables)) {
        echo "  ✗ No tables found - database needs initialization\n";
    } else {
        echo "  ✓ Found " . count($tables) . " tables:\n";
        foreach ($tables as $table) {
            echo "    - " . $table['table_name'] . "\n";
        }
    }
    
    // Check users
    echo "\nChecking users table...\n";
    $userCount = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "  Total users: " . $userCount['count'] . "\n";
    
} catch (Exception $e) {
    echo "✗ CONNECTION FAILED\n";
    echo "  Error: " . $e->getMessage() . "\n";
}

?>
