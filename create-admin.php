<?php
/**
 * Create Admin Account
 * Visit: https://ikimina.onrender.com/create-admin.php
 */

header('Content-Type: text/html; charset=utf-8');

// Load config
require_once 'application/config/config.php';
require_once 'application/config/Database.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Admin Account - IKIMINTA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .section {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #2563eb;
        }
        .success {
            background: #dcfce7;
            border-left-color: #10b981;
            color: #166534;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 16px;
        }
        .error {
            background: #fee2e2;
            border-left-color: #ef4444;
            color: #991b1b;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-size: 16px;
        }
        .info {
            background: #dbeafe;
            border-left-color: #3b82f6;
            color: #1e40af;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        code {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
            display: block;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
        .credentials-box {
            background: #fef08a;
            border: 2px solid #eab308;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .credentials-box h3 {
            margin-top: 0;
            color: #854d0e;
        }
        .step {
            margin: 15px 0;
            padding: 15px;
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Admin Account</h1>

        <?php
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();

            echo '<div class="section">';
            echo '<h2>Step 1: Check Existing Admin Account</h2>';

            // Check if admin exists
            $query = "SELECT id, username, email, user_type, status FROM users WHERE email = 'admin@ikimina.com' LIMIT 1";
            $stmt = $pdo->query($query);
            $existingAdmin = $stmt->fetch();

            if ($existingAdmin) {
                echo '<div class="info">Found existing account with email admin@ikimina.com</div>';
                echo '<table>';
                echo '<tr><th>Field</th><th>Current Value</th></tr>';
                echo '<tr><td>ID</td><td>' . htmlspecialchars($existingAdmin['id']) . '</td></tr>';
                echo '<tr><td>Username</td><td>' . htmlspecialchars($existingAdmin['username']) . '</td></tr>';
                echo '<tr><td>Email</td><td>' . htmlspecialchars($existingAdmin['email']) . '</td></tr>';
                echo '<tr><td>User Type</td><td>' . htmlspecialchars($existingAdmin['user_type']) . '</td></tr>';
                echo '<tr><td>Status</td><td>' . htmlspecialchars($existingAdmin['status']) . '</td></tr>';
                echo '</table>';
                echo '<br>';
                
                // Update existing account
                echo '<h2>Step 2: Update Existing Admin Account</h2>';
                $adminId = $existingAdmin['id'];
            } else {
                echo '<div class="info">No admin account found. Will create new one.</div>';
                echo '<br>';
                
                // Create new admin account
                echo '<h2>Step 2: Create New Admin Account</h2>';
                
                $adminId = null;
            }

            echo '</div>';

            // Now create or update the admin account
            echo '<div class="section">';

            $email = 'admin@ikimina.com';
            $password = 'Admin@123';
            $username = 'admin';
            $firstName = 'System';
            $lastName = 'Administrator';
            $legalId = '1111111111111111';
            $phone = '+1234567890';
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            if ($adminId) {
                // Update existing
                $updateQuery = "UPDATE users 
                    SET password = :password, 
                        user_type = 'admin', 
                        status = 'active',
                        updated_at = NOW() 
                    WHERE id = :id";
                
                $stmt = $pdo->prepare($updateQuery);
                $result = $stmt->execute([
                    ':password' => $hashedPassword,
                    ':id' => $adminId
                ]);

                if ($result) {
                    echo '<div class="success">✓ Admin account updated successfully!</div>';
                } else {
                    echo '<div class="error">✗ Failed to update admin account</div>';
                    die();
                }
            } else {
                // Create new
                $insertQuery = "INSERT INTO users 
                    (username, email, password, first_name, last_name, legal_id, phone_number, user_type, status, balance, created_at, updated_at) 
                    VALUES 
                    (:username, :email, :password, :first_name, :last_name, :legal_id, :phone_number, :user_type, :status, :balance, NOW(), NOW())";
                
                $stmt = $pdo->prepare($insertQuery);
                $result = $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $hashedPassword,
                    ':first_name' => $firstName,
                    ':last_name' => $lastName,
                    ':legal_id' => $legalId,
                    ':phone_number' => $phone,
                    ':user_type' => 'admin',
                    ':status' => 'active',
                    ':balance' => 0
                ]);

                if ($result) {
                    $adminId = $pdo->lastInsertId();
                    echo '<div class="success">✓ Admin account created successfully!</div>';
                } else {
                    echo '<div class="error">✗ Failed to create admin account</div>';
                    die();
                }
            }

            // Display credentials
            echo '<div class="credentials-box">';
            echo '<h3>📋 Admin Login Credentials</h3>';
            echo '<div class="step">';
            echo '<strong>Login URL:</strong>';
            echo '<code>' . BASE_URL . 'admin/auth/login</code>';
            echo '</div>';
            
            echo '<table>';
            echo '<tr><th>Field</th><th>Value</th></tr>';
            echo '<tr><td><strong>Email</strong></td><td><code>' . htmlspecialchars($email) . '</code></td></tr>';
            echo '<tr><td><strong>Password</strong></td><td><code>' . htmlspecialchars($password) . '</code></td></tr>';
            echo '<tr><td><strong>Username</strong></td><td><code>' . htmlspecialchars($username) . '</code></td></tr>';
            echo '</table>';

            echo '<br><div class="step">';
            echo '<strong>✓ Account is now ACTIVE and ready to login!</strong>';
            echo '</div>';
            echo '</div>';

            echo '</div>';

            // Verify the account was created
            echo '<div class="section">';
            echo '<h2>Verification</h2>';

            $verifyQuery = "SELECT id, username, email, user_type, status, password FROM users WHERE email = 'admin@ikimina.com' LIMIT 1";
            $stmt = $pdo->query($verifyQuery);
            $verified = $stmt->fetch();

            if ($verified) {
                echo '<div class="success">✓ Account verified in database!</div>';
                echo '<table>';
                echo '<tr><th>Field</th><th>Value</th></tr>';
                echo '<tr><td>ID</td><td>' . htmlspecialchars($verified['id']) . '</td></tr>';
                echo '<tr><td>Username</td><td>' . htmlspecialchars($verified['username']) . '</td></tr>';
                echo '<tr><td>Email</td><td>' . htmlspecialchars($verified['email']) . '</td></tr>';
                echo '<tr><td>User Type</td><td>' . htmlspecialchars($verified['user_type']) . '</td></tr>';
                echo '<tr><td>Status</td><td>' . htmlspecialchars($verified['status']) . '</td></tr>';
                echo '<tr><td>Password Hash</td><td><code style="word-break: break-all;">' . substr(htmlspecialchars($verified['password']), 0, 50) . '...</code></td></tr>';
                echo '</table>';
                
                // Test password
                echo '<br><h3>Testing Password Hash</h3>';
                $testPassword = 'Admin@123';
                if (password_verify($testPassword, $verified['password'])) {
                    echo '<div class="success">✓ Password verification PASSED - password hash is correct!</div>';
                } else {
                    echo '<div class="error">✗ Password verification FAILED</div>';
                }
            } else {
                echo '<div class="error">✗ Failed to verify account in database</div>';
            }

            echo '</div>';

        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            if (method_exists($e, 'getTraceAsString')) {
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
