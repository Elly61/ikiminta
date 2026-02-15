<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .dashboard-content {
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

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }

        .stat-card:nth-child(2) {
            border-left-color: #10b981;
        }

        .stat-card:nth-child(3) {
            border-left-color: #f59e0b;
        }

        .stat-card:nth-child(4) {
            border-left-color: #8b5cf6;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #666;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .stat-card p.stat-value {
            color: #667eea;
        }

        .stat-card:nth-child(2) p.stat-value {
            color: #10b981;
        }

        .stat-card:nth-child(3) p.stat-value {
            color: #f59e0b;
        }

        .stat-card:nth-child(4) p.stat-value {
            color: #8b5cf6;
        }

        .dashboard-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .stat-box h3 {
            color: #666;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .stat-box p {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            margin: 0;
        }

        .recent-transactions {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .recent-transactions h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .table th {
            padding: 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .table td {
            padding: 16px;
            font-size: 14px;
            color: #333;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status.approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status.rejected {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .status.cancelled {
            background: #fee2e2;
            color: #7f1d1d;
        }

        @media (max-width: 768px) {
            .dashboard-content {
                padding: 15px;
            }

            .dashboard-stats,
            .dashboard-row {
                grid-template-columns: 1fr;
            }

            .dashboard-header h1 {
                font-size: 20px;
            }

            .balance-amount {
                font-size: 26px !important;
            }

            .total-balance-card {
                padding: 20px !important;
            }

            .report-nav {
                gap: 8px !important;
            }

            .report-btn {
                padding: 8px 14px !important;
                font-size: 13px !important;
                flex: 1;
                text-align: center;
                min-width: 0;
            }

            .table {
                font-size: 12px;
                min-width: 500px;
            }

            .table th,
            .table td {
                padding: 10px;
            }

            .stat-value {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .dashboard-header h1 {
                font-size: 18px;
            }

            .balance-amount {
                font-size: 22px !important;
            }

            .stat-card, .stat-box {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <div class="dashboard-header">
                <h1>👋 Welcome back, <?php echo htmlspecialchars($user['first_name']); ?></h1>
                <p>Here's your financial overview</p>
            </div>

            <!-- Total Balance Card - Prominent Display -->
            <div class="total-balance-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 25px; margin-bottom: 25px; color: white; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">💰 Total Balance</h3>
                <p class="balance-amount" style="margin: 0; font-size: 36px; font-weight: 700;">RWF <?php echo number_format($total_balance, 2); ?></p>
            </div>

            <!-- Report Navigation -->
            <div class="report-nav" style="margin-bottom: 25px; display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="<?php echo BASE_URL; ?>member/reports/weekly" class="report-btn" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block;">📊 Weekly</a>
                <a href="<?php echo BASE_URL; ?>member/reports/monthly" class="report-btn" style="padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block;">📈 Monthly</a>
                <a href="<?php echo BASE_URL; ?>member/reports/yearly" class="report-btn" style="padding: 10px 20px; background: #8b5cf6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; display: inline-block;">📅 Yearly</a>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3> Total Credited</h3>
                    <p class="stat-value">RWF <?php echo number_format($total_credited, 2); ?></p>
                </div>
                <div class="stat-card">
                    <h3>📤 Total Debited</h3>
                    <p class="stat-value">RWF <?php echo number_format($total_debited, 2); ?></p>
                </div>
                <div class="stat-card">
                    <h3>💳 Total Deposited</h3>
                    <p class="stat-value">RWF <?php echo number_format($total_deposited, 2); ?></p>
                </div>
            </div>

            <div class="dashboard-row">
                <div class="stat-box">
                    <h3>🔄 Total Transferred</h3>
                    <p>RWF <?php echo number_format($total_transferred, 2); ?></p>
                </div>
                <div class="stat-box">
                    <h3>📋 Total Loans</h3>
                    <p>RWF <?php echo number_format($total_loans, 2); ?></p>
                </div>
                <div class="stat-box">
                    <h3>✅ Loan Paid</h3>
                    <p>RWF <?php echo number_format($total_loan_paid, 2); ?></p>
                </div>
            </div>

            <div class="recent-transactions">
                <h2>📊 Recent Transactions</h2>
                <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_transactions)): ?>
                            <?php foreach ($recent_transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($transaction['created_at'])); ?></td>
                                <td><?php echo str_replace('_', ' ', ucfirst($transaction['transaction_type'])); ?></td>
                                <td>RWF <?php echo number_format($transaction['amount'], 2); ?></td>
                                <td>RWF <?php echo number_format($transaction['balance_after'], 2); ?></td>
                                <td><span class="status <?php echo strtolower(str_replace(' ', '_', $transaction['status'])); ?>"><?php echo $transaction['status']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #999; padding: 30px;">No transactions yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Company Branding Footer -->
<footer class="company-footer" style="text-align: center; padding: 15px; background: #1e293b; color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 20px;">
    <p style="margin: 0;"><?php echo COMPANY_POWERED_BY; ?> | CEO: <?php echo COMPANY_CEO; ?> | Founder: <?php echo COMPANY_FOUNDER; ?></p>
    <p style="margin: 5px 0 0 0; font-size: 11px;">&copy; <?php echo COMPANY_YEAR; ?> <?php echo COMPANY_NAME; ?>. All rights reserved. | Contact: <?php echo COMPANY_EMAIL; ?></p>
</footer>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
</body>
</html>
