<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin-dashboard.css">
    <style>
        .admin-dashboard-content {
            padding: 30px;
        }

        .dashboard-header {
            margin-bottom: 40px;
        }

        .dashboard-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .dashboard-header p {
            color: #888;
            font-size: 15px;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .admin-stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            border-top: 4px solid #667eea;
        }

        .admin-stat-card.alert {
            border-top-color: #f59e0b;
        }

        .admin-stat-card.warning {
            border-top-color: #ef4444;
        }

        .admin-stat-card.info {
            border-top-color: #3b82f6;
        }

        .admin-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .admin-stat-card h3 {
            color: #666;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .stat-count {
            font-size: 36px;
            font-weight: 700;
            color: #333;
            margin: 0 0 16px 0;
        }

        .admin-stat-card a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .admin-stat-card a:hover {
            color: #764ba2;
        }

        .admin-quick-actions {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 30px;
        }

        .admin-quick-actions h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            border: 1px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #e8e8e8;
        }

        .btn-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .btn-danger:hover {
            background: #fca5a5;
        }

        .admin-transactions-report {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 20px;
        }

        .report-header h2 {
            font-size: 22px;
            color: #333;
            margin: 0;
            font-weight: 600;
        }

        .period-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .period-selector label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .period-selector select {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .period-selector select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: #f8f9fa;
        }

        .summary-card-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .summary-card-value {
            font-size: 26px;
            font-weight: 700;
            color: #667eea;
        }

        .summary-card:nth-child(2) .summary-card-value {
            color: #ef4444;
        }

        .summary-card:nth-child(3) .summary-card-value {
            color: #10b981;
        }

        .report-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .chart-container {
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: white;
            min-height: 320px;
        }

        .recent-tx-container {
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: white;
            max-height: 400px;
            overflow-y: auto;
        }

        .recent-tx-container strong {
            display: block;
            margin-bottom: 15px;
            font-size: 14px;
            text-transform: uppercase;
            color: #666;
            letter-spacing: 0.5px;
        }

        .tx-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .tx-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .tx-item:last-child {
            border-bottom: none;
        }

        .tx-info {
            color: #666;
            flex: 1;
        }

        .tx-amount {
            font-weight: 600;
            color: #333;
            text-align: right;
            min-width: 100px;
        }

        .tx-amount.credit {
            color: #10b981;
        }

        .tx-amount.debit {
            color: #ef4444;
        }

        @media (max-width: 768px) {
            .admin-dashboard-content {
                padding: 20px;
            }

            .admin-stats {
                grid-template-columns: 1fr;
            }

            .report-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .report-content {
                grid-template-columns: 1fr;
            }

            .dashboard-header h1 {
                font-size: 24px;
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
            <div class="dashboard-header">
                <h1>👋 Admin Dashboard</h1>
                <p>Welcome back! Here's your system overview</p>
            </div>
            
            <div class="admin-stats">
                <div class="admin-stat-card alert">
                    <h3>⏳ Pending Deposits</h3>
                    <p class="stat-count"><?php echo count($pending_deposits); ?></p>
                    <a href="<?php echo BASE_URL; ?>admin/deposits/index">Review Deposits →</a>
                </div>
                <div class="admin-stat-card warning">
                    <h3>📋 Pending Loans</h3>
                    <p class="stat-count"><?php echo count($pending_loans); ?></p>
                    <a href="<?php echo BASE_URL; ?>admin/loans/index">Review Loans →</a>
                </div>
                <div class="admin-stat-card info">
                    <h3>💸 Pending Withdrawals</h3>
                    <p class="stat-count"><?php echo count($pending_withdrawals); ?></p>
                    <a href="<?php echo BASE_URL; ?>admin/withdrawals/index">Review Withdrawals →</a>
                </div>
            </div>

            <div class="admin-quick-actions">
                <h2>⚡ Quick Actions</h2>
                <div class="action-buttons">
                    <a href="<?php echo BASE_URL; ?>admin/users/index" class="btn btn-primary">👥 Manage Users</a>
                    <a href="<?php echo BASE_URL; ?>admin/settings/index" class="btn btn-primary">⚙️ Settings</a>
                    <a href="<?php echo BASE_URL; ?>admin/auth/logout" class="btn btn-danger">🚪 Logout</a>
                </div>
            </div>

            <div class="admin-transactions-report">
                <div class="report-header">
                    <h2>📊 Transactions Report</h2>
                    <div class="period-selector">
                        <label for="periodSelect">Period:</label>
                        <select id="periodSelect" onchange="window.location.href='<?php echo BASE_URL; ?>admin?period=' + this.value">
                            <option value="7" <?php echo (intval($periodDays ?? 7) === 7) ? 'selected' : ''; ?>>Last 7 days</option>
                            <option value="30" <?php echo (intval($periodDays ?? 7) === 30) ? 'selected' : ''; ?>>Last 30 days</option>
                            <option value="90" <?php echo (intval($periodDays ?? 7) === 90) ? 'selected' : ''; ?>>Last 90 days</option>
                        </select>
                    </div>
                </div>

                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="summary-card-label">📥 Total Credits</div>
                        <div class="summary-card-value">RWF <?php echo number_format($transactions_summary['credit'] ?? 0, 2); ?></div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">📤 Total Debits</div>
                        <div class="summary-card-value">RWF <?php echo number_format($transactions_summary['debit'] ?? 0, 2); ?></div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-label">🔄 Transactions</div>
                        <div class="summary-card-value"><?php echo intval($transactions_summary['count'] ?? 0); ?></div>
                    </div>
                </div>

                <div class="report-content">
                    <div class="chart-container">
                        <canvas id="adminTxChart" height="120"></canvas>
                    </div>
                    <div class="recent-tx-container">
                        <strong>📋 Recent Transactions</strong>
                        <ul class="tx-list">
                            <?php if (!empty($recent_transactions)): ?>
                                <?php foreach ($recent_transactions as $rt): ?>
                                    <li class="tx-item">
                                        <span class="tx-info">
                                            <?php echo htmlspecialchars(date('M j H:i', strtotime($rt['created_at']))); ?><br>
                                            <small><?php echo htmlspecialchars($rt['username'] ?? $rt['email']); ?></small>
                                        </span>
                                        <span class="tx-amount <?php echo in_array($rt['transaction_type'], ['deposit','transfer_receive','loan_disbursement','saving_maturity']) ? 'credit' : 'debit'; ?>">
                                            <?php echo in_array($rt['transaction_type'], ['deposit','transfer_receive','loan_disbursement','saving_maturity']) ? '+' : '−'; ?> RWF <?php echo number_format(abs($rt['amount']), 2); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="tx-item" style="text-align: center; justify-content: center;">
                                    <span class="tx-info">No recent transactions</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
</body>
</html>