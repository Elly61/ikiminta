<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?> - IKIMINA Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin-dashboard.css">
    <style>
        .user-profile-header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 40px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 30px;
            align-items: center;
        }

        .user-profile-header .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #2563eb;
            font-weight: bold;
        }

        .user-profile-header .info h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }

        .user-profile-header .info p {
            margin: 5px 0;
            opacity: 0.9;
        }

        .user-profile-header .actions {
            display: flex;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .info-card h3 {
            color: #6b7280;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-card p {
            font-size: 18px;
            color: #111827;
            font-weight: 600;
            margin: 0;
            word-break: break-all;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .section h2 {
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .empty-state {
            text-align: center;
            color: #9ca3af;
            padding: 40px 20px;
        }

        .empty-state p {
            margin: 0;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.suspended {
            background: #fee2e2;
            color: #991b1b;
        }

        .transaction-item {
            display: grid;
            grid-template-columns: 1fr auto;
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            align-items: center;
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .transaction-info h4 {
            margin: 0 0 5px 0;
            color: #111827;
            font-weight: 600;
        }

        .transaction-info p {
            margin: 0;
            color: #6b7280;
            font-size: 12px;
        }

        .transaction-amount {
            font-weight: 600;
            font-size: 16px;
        }

        .transaction-amount.credit {
            color: #10b981;
        }

        .transaction-amount.debit {
            color: #ef4444;
        }

        @media (max-width: 768px) {
            .user-profile-header {
                grid-template-columns: 1fr;
            }

            .user-profile-header .avatar {
                justify-self: center;
            }

            .user-profile-header .actions {
                justify-self: center;
                flex-direction: column;
                width: 100%;
            }

            .user-profile-header .actions button,
            .user-profile-header .actions a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="admin-dashboard-wrapper">
    <?php include VIEW_PATH . 'admin/layouts/sidebar.php'; ?>

    <div class="admin-main-content">
        <?php include VIEW_PATH . 'admin/layouts/header.php'; ?>

        <div class="admin-dashboard-content">
            <!-- User Profile Header -->
            <div class="user-profile-header">
                <div class="avatar">
                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                </div>
                <div class="info">
                    <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                    <p><strong>@<?php echo htmlspecialchars($user['username']); ?></strong></p>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                    <p><?php echo htmlspecialchars($user['phone_number']); ?></p>
                </div>
                <div class="actions">
                    <a href="<?php echo BASE_URL; ?>admin/users" class="btn btn-secondary">← Back to Users</a>
                    <?php if ($user['status'] === 'active'): ?>
                        <button class="btn btn-danger" onclick="suspendUser(<?php echo $user['id']; ?>)">Suspend User</button>
                    <?php else: ?>
                        <button class="btn btn-success" onclick="activateUser(<?php echo $user['id']; ?>)">Activate User</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="info-grid">
                <div class="info-card">
                    <h3>Current Balance</h3>
                    <p>RWF <?php echo number_format($user['balance'], 2); ?></p>
                </div>
                <div class="info-card">
                    <h3>Account Status</h3>
                    <p><span class="status-badge <?php echo strtolower($user['status']); ?>"><?php echo ucfirst($user['status']); ?></span></p>
                </div>
                <div class="info-card">
                    <h3>Account Created</h3>
                    <p><?php echo date('M d, Y', strtotime($user['date_created'])); ?></p>
                </div>
                <div class="info-card">
                    <h3>Last Login</h3>
                    <p><?php echo $user['last_login'] ? date('M d, Y H:i A', strtotime($user['last_login'])) : 'Never'; ?></p>
                </div>
                <div class="info-card">
                    <h3>Legal ID</h3>
                    <p><?php echo htmlspecialchars($user['legal_id']); ?></p>
                </div>
                <div class="info-card">
                    <h3>User Type</h3>
                    <p><?php echo ucfirst($user['user_type']); ?></p>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="section">
                <h2>Recent Transactions (Last 10)</h2>
                <?php if (!empty($transactions)): ?>
                    <div>
                        <?php foreach ($transactions as $txn): ?>
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <h4><?php echo ucfirst(str_replace('_', ' ', $txn['transaction_type'])); ?></h4>
                                    <p><?php echo htmlspecialchars($txn['description'] ?? 'N/A'); ?> - <?php echo isset($txn['created_at']) ? date('M d, Y H:i A', strtotime($txn['created_at'])) : 'N/A'; ?></p>
                                </div>
                                <div class="transaction-amount <?php echo ($txn['transaction_type'] === 'deposit' || strpos($txn['transaction_type'], 'receive') !== false) ? 'credit' : 'debit'; ?>">
                                    <?php echo ($txn['transaction_type'] === 'deposit' || strpos($txn['transaction_type'], 'receive') !== false) ? '+' : '-'; ?>RWF <?php echo number_format(abs($txn['amount']), 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No transactions found</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Deposits -->
            <div class="section">
                <h2>Deposits</h2>
                <?php if (!empty($deposits)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deposits as $deposit): ?>
                                <tr>
                                    <td>RWF <?php echo number_format($deposit['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($deposit['payment_method'] ?? 'N/A'); ?></td>
                                    <td><span class="status-badge <?php echo strtolower($deposit['status']); ?>"><?php echo ucfirst($deposit['status']); ?></span></td>
                                    <td><?php echo isset($deposit['created_at']) ? date('M d, Y', strtotime($deposit['created_at'])) : 'N/A'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No deposits found</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Withdrawals -->
            <div class="section">
                <h2>Withdrawals</h2>
                <?php if (!empty($withdrawals)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($withdrawals as $withdrawal): ?>
                                <tr>
                                    <td>RWF <?php echo number_format($withdrawal['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($withdrawal['withdrawal_method'] ?? 'N/A'); ?></td>
                                    <td><span class="status-badge <?php echo strtolower($withdrawal['status']); ?>"><?php echo ucfirst($withdrawal['status']); ?></span></td>
                                    <td><?php echo isset($withdrawal['date_created']) ? date('M d, Y', strtotime($withdrawal['date_created'])) : 'N/A'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No withdrawals found</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Loans -->
            <div class="section">
                <h2>Loans</h2>
                <?php if (!empty($loans)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Principal Amount</th>
                                <th>Interest Rate</th>
                                <th>Status</th>
                                <th>Start Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td>RWF <?php echo isset($loan['principal_amount']) ? number_format($loan['principal_amount'], 2) : '0.00'; ?></td>
                                    <td><?php echo isset($loan['interest_rate']) ? $loan['interest_rate'] : '0'; ?>%</td>
                                    <td><span class="status-badge <?php echo strtolower($loan['status']); ?>"><?php echo ucfirst($loan['status']); ?></span></td>
                                    <td><?php echo isset($loan['start_date']) ? date('M d, Y', strtotime($loan['start_date'])) : 'N/A'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No loans found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function suspendUser(userId) {
    if (confirm('Are you sure you want to suspend this user?')) {
        fetch('<?php echo BASE_URL; ?>admin/users/suspend/' + userId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('User suspended');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

function activateUser(userId) {
    if (confirm('Are you sure you want to activate this user?')) {
        fetch('<?php echo BASE_URL; ?>admin/users/activate/' + userId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('User activated');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
</body>
</html>