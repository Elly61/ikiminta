<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IKIMINA Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin-dashboard.css">
</head>
<body>
<div class="admin-dashboard-wrapper">
    <?php include VIEW_PATH . 'admin/layouts/sidebar.php'; ?>
    
    <div class="admin-main-content">
        <?php include VIEW_PATH . 'admin/layouts/header.php'; ?>
        
        <div class="admin-dashboard-content">
            <h1>Admin Dashboard</h1>
            
            <div class="admin-stats">
                <div class="admin-stat-card alert">
                    <h3>Pending Deposits</h3>
                    <p class="stat-count"><?php echo count($pending_deposits); ?></p>
                    <a href="<?php echo BASE_URL; ?>admin/deposits/index">View</a>
                </div>
                <div class="admin-stat-card warning">
                    <h3>Pending Loans</h3>
                    <p class="stat-count"><?php echo count($pending_loans); ?></p>
                    <a href="<?php echo BASE_URL; ?>admin/loans/index">View</a>
                </div>
                <div class="admin-stat-card info">
                    <h3>Pending Withdrawals</h3>
                    <p class="stat-count"><?php echo count($pending_withdrawals); ?></p>
                    <a href="<?php echo BASE_URL; ?>admin/withdrawals/index">View</a>
                </div>
            </div>

            <div class="admin-quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="<?php echo BASE_URL; ?>admin/users/index" class="btn btn-primary">Manage Users</a>
                    <a href="<?php echo BASE_URL; ?>admin/settings/index" class="btn btn-secondary">Settings</a>
                    <a href="<?php echo BASE_URL; ?>admin/auth/logout" class="btn btn-danger">Logout</a>
                </div>
            </div>

            <div class="admin-transactions-report" style="margin-top:18px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h2>Transactions (Last <?php echo intval($periodDays ?? 7); ?> days)</h2>
                    <div>
                        <label for="periodSelect" style="margin-right:8px; font-size:14px; color:#374151;">Period:</label>
                        <select id="periodSelect" style="padding:6px 8px; border-radius:6px; border:1px solid #e5e7eb;">
                            <option value="7" <?php echo (intval($periodDays ?? 7) === 7) ? 'selected' : ''; ?>>7 days</option>
                            <option value="30" <?php echo (intval($periodDays ?? 7) === 30) ? 'selected' : ''; ?>>30 days</option>
                            <option value="90" <?php echo (intval($periodDays ?? 7) === 90) ? 'selected' : ''; ?>>90 days</option>
                        </select>
                    </div>
                </div>
                <div style="display:flex; gap:12px; margin-bottom:12px; align-items:stretch;">
                    <div style="flex:1; padding:12px; border:1px solid #eef2f7; border-radius:6px; background:#fff;">
                        <strong>Total Credits</strong>
                        <div style="font-size:18px; margin-top:6px;">FRW <?php echo number_format($transactions_summary['credit'] ?? 0,2); ?></div>
                    </div>
                    <div style="flex:1; padding:12px; border:1px solid #eef2f7; border-radius:6px; background:#fff;">
                        <strong>Total Debits</strong>
                        <div style="font-size:18px; margin-top:6px;">FRW <?php echo number_format($transactions_summary['debit'] ?? 0,2); ?></div>
                    </div>
                    <div style="flex:1; padding:12px; border:1px solid #eef2f7; border-radius:6px; background:#fff;">
                        <strong>Transactions</strong>
                        <div style="font-size:18px; margin-top:6px;"><?php echo intval($transactions_summary['count'] ?? 0); ?></div>
                    </div>
                </div>

                <div style="display:flex; gap:16px; align-items:flex-start;">
                    <div style="flex:2; padding:12px; border:1px solid #eef2f7; border-radius:6px; background:#fff;">
                        <canvas id="adminTxChart" height="120"></canvas>
                    </div>
                    <div style="flex:1; padding:12px; border:1px solid #eef2f7; border-radius:6px; background:#fff; max-height:320px; overflow:auto;">
                        <strong>Recent Transactions</strong>
                        <table style="width:100%; margin-top:8px; font-size:13px;">
                            <thead>
                                <tr><th style="text-align:left;">When</th><th style="text-align:right;">Amount</th></tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_transactions)): ?>
                                    <?php foreach ($recent_transactions as $rt): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(date('M j H:i', strtotime($rt['created_at']))); ?> - <?php echo htmlspecialchars($rt['username'] ?? $rt['email']); ?></td>
                                            <td style="text-align:right;"><?php echo number_format($rt['amount'] * (in_array($rt['transaction_type'], ['deposit','transfer_receive','loan_disbursement','saving_maturity']) ? 1 : -1),2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" style="text-align:center; padding:8px;">No recent transactions</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN for admin chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function(){
        var labels = <?php echo json_encode($chartLabels ?? []); ?>;
        var data = <?php echo json_encode($chartData ?? []); ?>;
        if (document.getElementById('adminTxChart')) {
            var ctx = document.getElementById('adminTxChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Net (FRW)',
                        data: data,
                        backgroundColor: 'rgba(54,162,235,0.6)'
                    }]
                },
                options: {responsive:true, maintainAspectRatio:false}
            });
        }
    })();
</script>
<script>
    // handle period select change
    (function(){
        var sel = document.getElementById('periodSelect');
        if (!sel) return;
        sel.addEventListener('change', function(){
            var days = this.value;
            var url = new URL(window.location.href);
            url.searchParams.set('days', days);
            window.location = url.toString();
        });
    })();
</script>

<!-- Company Branding Footer -->
<footer class="company-footer" style="text-align: center; padding: 15px; background: #1e293b; color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 20px;">
    <p style="margin: 0;"><?php echo COMPANY_POWERED_BY; ?> | CEO: <?php echo COMPANY_CEO; ?> | Founder: <?php echo COMPANY_FOUNDER; ?></p>
    <p style="margin: 5px 0 0 0; font-size: 11px;">&copy; <?php echo COMPANY_YEAR; ?> <?php echo COMPANY_NAME; ?>. All rights reserved. | Contact: <?php echo COMPANY_EMAIL; ?></p>
</footer>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
</body>
</html>
