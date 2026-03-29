<?php
/**
 * Admin Password Management
 * Visit: https://ikimina.onrender.com/admin-setup.php
 */

// Set content type
header('Content-Type: text/html; charset=utf-8');

// Load config
require_once 'application/config/config.php';
require_once 'application/config/Database.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Setup - IKIMINTA</title>
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
        }
        .error {
            background: #fee2e2;
            border-left-color: #ef4444;
            color: #991b1b;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
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
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Account Management</h1>

        <?php
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();

            echo '<div class="section">';
            echo '<h2>Current Admin Accounts</h2>';

            // Check admin users
            $query = "SELECT id, username, email, status, created_at FROM users WHERE user_type = 'admin' ORDER BY created_at DESC";
            $stmt = $pdo->query($query);
            $admins = $stmt->fetchAll();

            if (empty($admins)) {
                echo '<div class="info">No admin accounts found in database. Creating default admin...</div>';

                // Create default admin
                $adminUsername = 'admin';
                $adminEmail = 'admin@ikiminta.local';
                $adminPassword = 'Admin@12345';
                $adminHashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

                $insertQuery = "INSERT INTO users 
                    (username, email, password, first_name, last_name, legal_id, phone_number, user_type, status, balance, created_at, updated_at) 
                    VALUES 
                    (:username, :email, :password, :first_name, :last_name, :legal_id, :phone_number, :user_type, :status, :balance, NOW(), NOW())
                    ON CONFLICT (email) DO NOTHING";

                $stmt = $pdo->prepare($insertQuery);
                $result = $stmt->execute([
                    ':username' => $adminUsername,
                    ':email' => $adminEmail,
                    ':password' => $adminHashedPassword,
                    ':first_name' => 'System',
                    ':last_name' => 'Administrator',
                    ':legal_id' => '1111111111111111',
                    ':phone_number' => '+1234567890',
                    ':user_type' => 'admin',
                    ':status' => 'active',
                    ':balance' => 0
                ]);

                echo '<div class="success">✓ Admin account created successfully!</div>';
                echo '<table>';
                echo '<tr><th>Username</th><th>Email</th><th>Password</th><th>Status</th></tr>';
                echo '<tr>';
                echo '<td><strong>' . htmlspecialchars($adminUsername) . '</strong></td>';
                echo '<td><strong>' . htmlspecialchars($adminEmail) . '</strong></td>';
                echo '<td><strong>' . htmlspecialchars($adminPassword) . '</strong></td>';
                echo '<td>active</td>';
                echo '</tr>';
                echo '</table>';

            } else {
                echo '<div class="info">' . count($admins) . ' admin account(s) found:</div>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th><th>Created</th></tr>';

                foreach ($admins as $admin) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($admin['id']) . '</td>';
                    echo '<td><strong>' . htmlspecialchars($admin['username']) . '</strong></td>';
                    echo '<td>' . htmlspecialchars($admin['email']) . '</td>';
                    echo '<td>' . htmlspecialchars($admin['status']) . '</td>';
                    echo '<td>' . htmlspecialchars($admin['created_at']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';

                // Reset password for first admin
                $firstAdmin = $admins[0];
                $adminUsername = $firstAdmin['username'];
                $adminPassword = 'Admin@12345';
                $adminHashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

                $updateQuery = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
                $stmt = $pdo->prepare($updateQuery);
                $stmt->execute([
                    ':password' => $adminHashedPassword,
                    ':id' => $firstAdmin['id']
                ]);

                echo '<div class="success">✓ Password reset for admin: <strong>' . htmlspecialchars($adminUsername) . '</strong></div>';
            }

            echo '</div>';

            // Show login info
            echo '<div class="section">';
            echo '<h2>Admin Login Credentials</h2>';
            echo '<div class="info"><strong>Login URL:</strong> ' . BASE_URL . 'admin/auth/login</div>';

            if (!empty($admins)) {
                $adminUsername = $admins[0]['username'];
            }

            echo '<table>';
            echo '<tr><th>Username</th><th>Password</th></tr>';
            echo '<tr>';
            echo '<td><code>' . htmlspecialchars($adminUsername) . '</code></td>';
            echo '<td><code>' . htmlspecialchars($adminPassword) . '</code></td>';
            echo '</tr>';
            echo '</table>';
            echo '</div>';

        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
