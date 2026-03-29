<?php
/**
 * Auto Admin Login and Dashboard Check
 * Visit: https://ikimina.onrender.com/auto-admin-login.php
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

// Load config
require_once 'application/config/config.php';
require_once 'application/config/Database.php';

$loginSuccess = false;
$error = '';

try {
    // Connect to database
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Check admin account
    $query = "SELECT id, username, email, user_type, status, password FROM users WHERE email = 'admin@ikimina.com' LIMIT 1";
    $stmt = $pdo->query($query);
    $admin = $stmt->fetch();

    if (!$admin) {
        $error = 'Admin account not found';
    } elseif ($admin['status'] !== 'active') {
        $error = 'Admin account is not active';
    } elseif (!password_verify('Admin@123', $admin['password'])) {
        $error = 'Password verification failed';
    } else {
        // Login successful - set session
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['email'] = $admin['email'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['user_type'] = $admin['user_type'];
        $loginSuccess = true;
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - IKIMINTA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #2563eb;
            margin-bottom: 30px;
        }
        .success-message {
            background: #dcfce7;
            border: 2px solid #10b981;
            color: #166534;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .error-message {
            background: #fee2e2;
            border: 2px solid #ef4444;
            color: #991b1b;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 18px;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        td:first-child {
            font-weight: bold;
            background: #f3f4f6;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        a:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Login</h1>

        <?php if ($loginSuccess): ?>
            <div class="success-message">
                ✓ Login Successful!
            </div>

            <p>Admin session has been created. Here are your details:</p>

            <table>
                <tr>
                    <td>User ID</td>
                    <td><?php echo htmlspecialchars($_SESSION['user_id']); ?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?php echo htmlspecialchars($_SESSION['email']); ?></td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td><?php echo htmlspecialchars($_SESSION['username']); ?></td>
                </tr>
                <tr>
                    <td>User Type</td>
                    <td><?php echo htmlspecialchars($_SESSION['user_type']); ?></td>
                </tr>
            </table>

            <p>You will be redirected to the admin dashboard in 3 seconds...</p>

            <script>
                setTimeout(function() {
                    window.location.href = '<?php echo BASE_URL; ?>admin/dashboard';
                }, 3000);
            </script>

            <a href="<?php echo BASE_URL; ?>admin/dashboard">Go to Dashboard Now</a>

        <?php else: ?>
            <div class="error-message">
                ✗ Login Failed
            </div>

            <p><?php echo htmlspecialchars($error); ?></p>

            <p style="margin-top: 20px; color: #666;">
                Please ensure the admin account exists and credentials are correct.
            </p>

            <a href="<?php echo BASE_URL; ?>create-admin.php">Create Admin Account</a>

        <?php endif; ?>
    </div>
</body>
</html>
