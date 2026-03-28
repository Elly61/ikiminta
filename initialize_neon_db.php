<?php
/**
 * Manual Database Initialization
 * Use this to manually create all tables in your Neon database
 */

// Your Neon connection string
$databaseUrl = "postgresql://neondb_owner:npg_0aw6dbXNesqE@ep-fragrant-salad-am3tdpt2-pooler.c-5.us-east-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require";

echo "=== MANUAL DATABASE INITIALIZATION ===\n\n";

// Parse the URL
$parsed = parse_url($databaseUrl);
$host = $parsed['host'] ?? 'localhost';
$port = $parsed['port'] ?? 5432;
$user = $parsed['user'] ?? 'root';
$pass = $parsed['pass'] ?? '';
$dbname = ltrim($parsed['path'] ?? '/neondb', '/');

echo "Connection Details:\n";
echo "  Host: $host\n";
echo "  Port: $port\n";
echo "  Database: $dbname\n";
echo "  User: $user\n\n";

try {
    // Connect to PostgreSQL
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✓ Connected to Neon database successfully!\n\n";
    
    // Read schema
    $schemaFile = __DIR__ . '/schema_postgres.sql';
    if (!file_exists($schemaFile)) {
        echo "✗ Schema file not found: $schemaFile\n";
        exit(1);
    }
    
    echo "Reading schema from: $schemaFile\n";
    $schema = file_get_contents($schemaFile);
    
    // Split and execute
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    $count = 0;
    $errors = [];
    
    echo "\nExecuting SQL statements...\n";
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $pdo->exec($statement);
            $count++;
            echo "✓ Statement $count executed\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "✓ Table already exists (OK)\n";
                $count++;
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
                $errors[] = $e->getMessage();
            }
        }
    }
    
    echo "\nExecuted $count statements\n";
    
    if (!empty($errors)) {
        echo "\nErrors encountered:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }
    
    // Verify tables
    echo "\n=== VERIFYING TABLES ===\n";
    $tables = [
        'users', 'settings', 'deposits', 'loan_requests', 'loans',
        'loan_payments', 'transactions', 'savings', 'transfers',
        'withdrawals', 'reports', 'admin_settings', 'audit_logs'
    ];
    
    $verified = 0;
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = '$table' AND table_schema = 'public'");
            if ($result->fetch()) {
                echo "✓ $table\n";
                $verified++;
            } else {
                echo "✗ $table (NOT FOUND)\n";
            }
        } catch (Exception $e) {
            echo "✗ $table (ERROR)\n";
        }
    }
    
    echo "\n✓ Verified $verified/" . count($tables) . " tables\n";
    
    // Insert default settings if empty
    echo "\n=== INSERTING DEFAULT SETTINGS ===\n";
    try {
        $result = $pdo->query("SELECT COUNT(*) as count FROM settings");
        $row = $result->fetch();
        
        if ($row['count'] == 0) {
            $pdo->exec("INSERT INTO settings (signup_bonus) VALUES (50.00)");
            echo "✓ Default settings inserted (signup bonus: 50.00)\n";
        } else {
            echo "✓ Settings already exist\n";
        }
    } catch (Exception $e) {
        echo "✗ Error with settings: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== INITIALIZATION COMPLETE ===\n";
    echo "Your Neon database is ready!\n";
    
} catch (Exception $e) {
    echo "✗ CONNECTION FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nMake sure:\n";
    echo "  1. Neon database is created\n";
    echo "  2. Connection string is correct\n";
    echo "  3. Network can reach Neon (usually port 5432)\n";
}

?>
