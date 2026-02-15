<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Report - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/reports.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="report-content">
            <div class="report-header">
                <div>
                    <h1>📊 Weekly Report</h1>
                    <p><?php echo date('M d, Y', strtotime($report['start_date'])); ?> - <?php echo date('M d, Y', strtotime($report['end_date'])); ?></p>
                </div>
                <div class="report-navigation">
                    <a href="<?php echo BASE_URL; ?>member/reports/weekly" class="active">Weekly</a>
                    <a href="<?php echo BASE_URL; ?>member/reports/monthly">Monthly</a>
                    <a href="<?php echo BASE_URL; ?>member/reports/yearly">Yearly</a>
                    <a href="<?php echo BASE_URL; ?>member/reports/downloadWeeklyPDF" style="background: #10b981;" onmouseover="this.style.background='#059669';" onmouseout="this.style.background='#10b981';">📥 Download PDF</a>
                </div>
            </div>

            <!-- Current Balance Card -->
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 30px; margin-bottom: 30px; color: white; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);">
                <h3 style="margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9;">💰 Current Balance</h3>
                <p style="margin: 0; font-size: 42px; font-weight: 700;">RWF <?php echo number_format($report['user']['balance'] ?? 0, 2); ?></p>
            </div>

            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card deposits">
                    <h3>Total Deposits</h3>
                    <p class="value">RWF <?php echo number_format($report['summary']['total_deposits'], 2); ?></p>
                </div>
                <div class="summary-card withdrawals">
                    <h3>Total Withdrawals</h3>
                    <p class="value">RWF <?php echo number_format($report['summary']['total_withdrawals'], 2); ?></p>
                </div>
                <div class="summary-card transfers">
                    <h3>Total Transfers (Sent)</h3>
                    <p class="value">RWF <?php echo number_format($report['summary']['total_transfers_sent'], 2); ?></p>
                </div>
                <div class="summary-card transfers">
                    <h3>Total Transfers (Received)</h3>
                    <p class="value">RWF <?php echo number_format($report['summary']['total_transfers_received'], 2); ?></p>
                </div>
                <div class="summary-card net">
                    <h3>Net Change</h3>
                    <p class="value">RWF <?php echo number_format($report['summary']['net_change'], 2); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Transaction Count</h3>
                    <p class="value"><?php echo $report['summary']['transaction_count']; ?></p>
                </div>
            </div>

            <!-- Transaction Type Breakdown -->
            <?php if (!empty($typeBreakdown)): ?>
            <div class="report-section">
                <h2>Transaction Type Breakdown</h2>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Count</th>
                            <th>Total Amount</th>
                            <th>Average Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($typeBreakdown as $type): ?>
                        <tr>
                            <td><?php echo ucfirst(str_replace('_', ' ', $type['transaction_type'])); ?></td>
                            <td><?php echo $type['count']; ?></td>
                            <td>RWF <?php echo number_format($type['total_amount'], 2); ?></td>
                            <td>RWF <?php echo number_format($type['avg_amount'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Recent Transactions -->
            <?php if (!empty($report['transactions'])): ?>
            <div class="report-section">
                <h2>Recent Transactions</h2>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance After</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($report['transactions'], 0, 10) as $transaction): ?>
                        <tr>
                            <td><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $transaction['transaction_type'])); ?></td>
                            <td>RWF <?php echo number_format($transaction['amount'], 2); ?></td>
                            <td>RWF <?php echo number_format($transaction['balance_after'], 2); ?></td>
                            <td><span class="status <?php echo strtolower($transaction['status']); ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="report-section">
                <div class="no-data">
                    <p>No transactions found for this period</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Deposits Section -->
            <?php if (!empty($deposits)): ?>
            <div class="report-section">
                <h2>Deposits</h2>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deposits as $deposit): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($deposit['created_at'])); ?></td>
                            <td>RWF <?php echo number_format($deposit['amount'], 2); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $deposit['payment_method'])); ?></td>
                            <td><?php echo substr($deposit['transaction_reference'], 0, 15); ?>...</td>
                            <td><span class="status <?php echo strtolower($deposit['status']); ?>"><?php echo ucfirst($deposit['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Loans Section -->
            <?php if (!empty($loans)): ?>
            <div class="report-section">
                <h2>Loans</h2>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Principal Amount</th>
                            <th>Interest Rate</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($loan['created_at'])); ?></td>
                            <td>RWF <?php echo number_format($loan['principal_amount'], 2); ?></td>
                            <td><?php echo $loan['interest_rate']; ?>%</td>
                            <td><?php echo $loan['duration_months']; ?> months</td>
                            <td><span class="status <?php echo strtolower($loan['status']); ?>"><?php echo ucfirst($loan['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Savings Section -->
            <?php if (!empty($savings)): ?>
            <div class="report-section">
                <h2>Savings</h2>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Interest Rate</th>
                            <th>Maturity Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($savings as $saving): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($saving['created_at'])); ?></td>
                            <td>RWF <?php echo number_format($saving['amount'], 2); ?></td>
                            <td><?php echo $saving['interest_rate']; ?>%</td>
                            <td><?php echo date('M d, Y', strtotime($saving['maturity_date'])); ?></td>
                            <td><span class="status <?php echo strtolower($saving['status']); ?>"><?php echo ucfirst($saving['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
</body>
</html>
