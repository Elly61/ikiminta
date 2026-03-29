<?php
/**
 * Create Test Data for Admin Dashboard
 * Visit: https://ikimina.onrender.com/create-test-data.php
 */

header('Content-Type: text/html; charset=utf-8');

// Load config
require_once 'application/config/config.php';
require_once 'application/config/Database.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Test Data - IKIMINTA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .section {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
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
        <h1>Create Test Data for Admin Dashboard</h1>

        <?php
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();

            echo '<div class="section">';
            echo '<h2>Step 1: Create Test Members</h2>';

            $testUsers = [];
            for ($i = 1; $i <= 5; $i++) {
                $timestamp = time() + $i;
                $username = 'testuser' . $i;
                $email = 'testuser' . $i . '@test.com';
                $legalId = str_pad($i, 16, '0', STR_PAD_LEFT);
                $password = password_hash('Test@12345', PASSWORD_BCRYPT);

                $insertQuery = "INSERT INTO users 
                    (username, email, password, first_name, last_name, legal_id, phone_number, user_type, status, balance) 
                    VALUES 
                    (:username, :email, :password, :first_name, :last_name, :legal_id, :phone_number, :user_type, :status, :balance)
                    ON CONFLICT (email) DO NOTHING
                    RETURNING id";

                $stmt = $pdo->prepare($insertQuery);
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $password,
                    ':first_name' => 'Test',
                    ':last_name' => 'User ' . $i,
                    ':legal_id' => $legalId,
                    ':phone_number' => '+123456789' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    ':user_type' => 'member',
                    ':status' => 'active',
                    ':balance' => 50 + ($i * 100)
                ]);

                $result = $stmt->fetch();
                if ($result && $result['id']) {
                    $testUsers[$i] = $result['id'];
                    echo '<div class="success">✓ Created user: ' . htmlspecialchars($email) . ' (ID: ' . $result['id'] . ')</div>';
                }
            }

            echo '</div>';

            // Create test transactions
            if (!empty($testUsers)) {
                echo '<div class="section">';
                echo '<h2>Step 2: Create Test Transactions</h2>';

                $transactionTypes = ['deposit', 'withdrawal', 'transfer_send', 'transfer_receive', 'loan_disbursement'];
                $transactionCount = 0;

                foreach ($testUsers as $userId) {
                    // Create 3 transactions per user
                    for ($j = 0; $j < 3; $j++) {
                        $type = $transactionTypes[$j % count($transactionTypes)];
                        $amount = 100 + ($j * 50);
                        $fee = ($type === 'transfer_send' || $type === 'withdrawal') ? 2.50 : 0;
                        $status = 'completed';
                        $createdAt = date('Y-m-d H:i:s', strtotime('-' . (5 - $j) . ' days'));

                        $insertTx = "INSERT INTO transactions 
                            (user_id, transaction_type, amount, fee, status, description, created_at) 
                            VALUES 
                            (:user_id, :type, :amount, :fee, :status, :description, :created_at)";

                        $stmtTx = $pdo->prepare($insertTx);
                        $stmtTx->execute([
                            ':user_id' => $userId,
                            ':type' => $type,
                            ':amount' => $amount,
                            ':fee' => $fee,
                            ':status' => $status,
                            ':description' => 'Test ' . $type,
                            ':created_at' => $createdAt
                        ]);

                        $transactionCount++;
                    }
                }

                echo '<div class="success">✓ Created ' . $transactionCount . ' test transactions</div>';
                echo '</div>';
            }

            // Display summary
            echo '<div class="section">';
            echo '<h2>Step 3: Summary</h2>';

            $usersCount = $pdo->query("SELECT COUNT(*) as cnt FROM users WHERE user_type = 'member'")->fetch();
            $txCount = $pdo->query("SELECT COUNT(*) as cnt FROM transactions")->fetch();
            $depositsCount = $pdo->query("SELECT COUNT(*) as cnt FROM transactions WHERE transaction_type = 'deposit' AND status = 'completed'")->fetch();
            $withdrawalsCount = $pdo->query("SELECT COUNT(*) as cnt FROM transactions WHERE transaction_type = 'withdrawal' AND status = 'completed'")->fetch();

            echo '<table>';
            echo '<tr><th>Metric</th><th>Count</th></tr>';
            echo '<tr><td>Total Members</td><td>' . $usersCount['cnt'] . '</td></tr>';
            echo '<tr><td>Total Transactions</td><td>' . $txCount['cnt'] . '</td></tr>';
            echo '<tr><td>Deposits</td><td>' . $depositsCount['cnt'] . '</td></tr>';
            echo '<tr><td>Withdrawals</td><td>' . $withdrawalsCount['cnt'] . '</td></tr>';
            echo '</table>';

            echo '<div class="success">';
            echo '✓ Test data created successfully!';
            echo '</div>';

            echo '</div>';

            echo '<div class="section">';
            echo '<h2>Next Steps</h2>';
            echo '<p>Now visit the admin dashboard to see the test data:</p>';
            echo '<a href="' . BASE_URL . 'admin/dashboard">Go to Admin Dashboard</a>';
            echo '</div>';

        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
