<?php
/**
 * Diagnostic Script - Check which database Render is using
 */

echo "=== DATABASE DIAGNOSTIC ===\n\n";

// Step 1: Check environment
$dbUrl = getenv('DATABASE_URL');
$dbHost = getenv('IK_DB_HOST');
$dbName = getenv('IK_DB_NAME');

echo "1. Environment Variables on Current System:\n";
echo "   DATABASE_URL: " . ($dbUrl ? substr($dbUrl, 0, 50) . "..." : "NOT SET") . "\n";
echo "   IK_DB_HOST: " . ($dbHost ?: "NOT SET") . "\n";
echo "   IK_DB_NAME: " . ($dbName ?: "NOT SET") . "\n\n";

// Step 2: Check Render logs to see what DB connection is being used
echo "2. What should happen:\n";
echo "   - DATABASE_URL should be set to Neon: ep-cool-sea-amwynevw-pooler.c-5.us-east-1.aws.neon.tech\n";
echo "   - NOT the old Render database (if it still exists)\n\n";

// Step 3: Diagnostic steps
echo "3. Diagnostic Steps to Fix:\n";
echo "   ✓ Step 1: Go to Render Dashboard\n";
echo "   ✓ Step 2: Find your ikiminta service\n";
echo "   ✓ Step 3: Delete any old PostgreSQL databases\n";
echo "   ✓ Step 4: Go to Environment settings\n";
echo "   ✓ Step 5: Set DATABASE_URL to:\n";
echo "      postgresql://neondb_owner:npg_kuyt7Y4IxSUo@ep-cool-sea-amwynevw-pooler.c-5.us-east-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require\n";
echo "   ✓ Step 6: Save - this triggers redeployment\n";
echo "   ✓ Step 7: Wait for redeployment to complete\n";
echo "   ✓ Step 8: Run test_live_service.php again\n\n";

echo "4. After you complete these steps, run:\n";
echo "   php test_live_service.php\n";
echo "   to verify registration and login work!\n";

?>
