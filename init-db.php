<?php
/**
 * Database Initialization Script
 * Runs PostgreSQL schema on first deployment to Neon
 * 
 * This script:
 * 1. Checks if DATABASE_URL is set
 * 2. Connects to PostgreSQL
 * 3. Creates all tables from schema_postgres.sql
 * 4. Verifies tables were created
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "[DB INIT] Starting database initialization...\n";

// Check for DATABASE_URL
$databaseUrl = getenv('DATABASE_URL');
if (!$databaseUrl) {
    echo "[DB INIT] ✗ DATABASE_URL not set. Skipping initialization.\n";
    exit(0);
}

echo "[DB INIT] ✓ DATABASE_URL found\n";

// Parse DATABASE_URL
$parsed = parse_url($databaseUrl);
$host = $parsed['host'] ?? 'localhost';
$port = $parsed['port'] ?? 5432;
$user = $parsed['user'] ?? 'root';
$pass = $parsed['pass'] ?? '';
$dbname = ltrim($parsed['path'] ?? '/neondb', '/');

echo "[DB INIT] Connecting to: $user@$host:$port/$dbname\n";

// Connect to PostgreSQL
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
    echo "[DB INIT] ✓ Connected to PostgreSQL\n";
} catch (Exception $e) {
    echo "[DB INIT] ✗ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Read schema file
$schemaFile = __DIR__ . '/schema_postgres.sql';
if (!file_exists($schemaFile)) {
    echo "[DB INIT] ✗ Schema file not found: $schemaFile\n";
    exit(1);
}

echo "[DB INIT] ✓ Schema file found\n";

$schema = file_get_contents($schemaFile);

// Split by semicolons and execute each statement
$statements = array_filter(array_map('trim', explode(';', $schema)));
$count = 0;

foreach ($statements as $statement) {
    if (empty($statement)) {
        continue;
    }
    
    try {
        $pdo->exec($statement);
        $count++;
        echo "[DB INIT] ✓ Executed statement $count\n";
    } catch (Exception $e) {
        // Table might already exist - that's OK
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "[DB INIT] ✓ Table already exists (OK)\n";
            $count++;
        } else {
            echo "[DB INIT] ⚠ Error: " . $e->getMessage() . "\n";
        }
    }
}

// Verify tables exist
$tables = [
    'users', 'settings', 'deposits', 'loan_requests', 'loans',
    'loan_payments', 'transactions', 'savings', 'transfers',
    'withdrawals', 'reports', 'admin_settings', 'audit_logs'
];

echo "\n[DB INIT] Verifying tables...\n";
$verified = 0;
foreach ($tables as $table) {
    try {
        $result = $pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = '$table' AND table_schema = 'public'");
        if ($result->fetch()) {
            echo "[DB INIT] ✓ Table '$table' exists\n";
            $verified++;
        }
    } catch (Exception $e) {
        echo "[DB INIT] ✗ Could not verify table '$table'\n";
    }
}

echo "\n[DB INIT] ========================================\n";
echo "[DB INIT] Database initialization complete!\n";
echo "[DB INIT] ✓ Verified $verified/$count tables\n";
echo "[DB INIT] ========================================\n";
?>
