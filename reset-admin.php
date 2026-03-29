<?php
/**
 * Reset Admin Password to Admin@123
 * Visit: https://ikimina.onrender.com/reset-admin.php
 */

header('Content-Type: text/html; charset=utf-8');

// Load config
require_once 'application/config/config.php';
require_once 'application/config/Database.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Admin Password - IKIMINTA</title>
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
        .credentials {
            background: #fef08a;
            border: 2px solid #eab308;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .credentials h3 {
            margin-top: 0;
            color: #854d0e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Password Reset</h1>

        <?php
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();

            echo '<div class="section">';
            echo '<h2>Finding Admin Account</h2>';

            // Find admin by email
            $query = "SELECT id, username, email, status FROM users WHERE email = 'admin@ikimina.com' AND user_type = 'admin'";
            $stmt = $pdo->query($query);
            $admin = $stmt->fetch();

            if (!$admin) {
                echo '<div class="info">Admin account with email admin@ikimina.com not found. Searching for any admin...</div>';
                
                // Find any admin
                $query = "SELECT id, username, email, status FROM users WHERE user_type = 'admin' ORDER BY created_at LIMIT 1";
                $stmt = $pdo->query($query);
                $admin = $stmt->fetch();

                if (!$admin) {
                    echo '<div class="error">✗ No admin account found in database!</div>';
                    echo '</div>';
                    die();
                }
            }

            echo '<div class="success">✓ Found admin account</div>';
            echo '<table>';
            echo '<tr><th>Field</th><th>Value</th></tr>';
            echo '<tr><td>ID</td><td>' . htmlspecialchars($admin['id']) . '</td></tr>';
            echo '<tr><td>Username</td><td>' . htmlspecialchars($admin['username']) . '</td></tr>';
            echo '<tr><td>Email</td><td>' . htmlspecialchars($admin['email']) . '</td></tr>';
            echo '<tr><td>Status</td><td>' . htmlspecialchars($admin['status']) . '</td></tr>';
            echo '</table>';

            echo '</div>';

            // Reset password
            echo '<div class="section">';
            echo '<h2>Resetting Password</h2>';

            $newPassword = 'Admin@123';
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $updateQuery = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
            $stmt = $pdo->prepare($updateQuery);
            $result = $stmt->execute([
                ':password' => $hashedPassword,
                ':id' => $admin['id']
            ]);

            if ($result) {
                echo '<div class="success">✓ Password reset successfully!</div>';

                echo '<div class="credentials">';
                echo '<h3>📋 Admin Login Credentials</h3>';
                echo '<table>';
                echo '<tr><th>Field</th><th>Value</th></tr>';
                echo '<tr><td><strong>Email</strong></td><td><code>admin@ikimina.com</code></td></tr>';
                echo '<tr><td><strong>Password</strong></td><td><code>Admin@123</code></td></tr>';
                echo '<tr><td><strong>Login URL</strong></td><td><code>' . BASE_URL . 'admin/auth/login</code></td></tr>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<div class="error">✗ Failed to reset password</div>';
            }

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
