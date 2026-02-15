<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>

        <div class="dashboard-content">
            <h1>Transaction Report</h1>

            <div style="background:white; padding:20px; border-radius:8px; max-width:980px;">
                <form method="GET" action="" style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
                    <label>Period:
                        <select name="period">
                            <option value="daily" <?php echo $period==='daily' ? 'selected' : ''; ?>>Daily</option>
                            <option value="weekly" <?php echo $period==='weekly' ? 'selected' : ''; ?>>Weekly</option>
                            <option value="monthly" <?php echo $period==='monthly' ? 'selected' : ''; ?>>Monthly</option>
                        </select>
                    </label>

                    <label>Date:
                        <input type="date" name="date" value="<?php echo htmlspecialchars($start); ?>">
                    </label>

                    <button class="btn btn-primary" type="submit">Generate</button>
                    <a class="btn btn-secondary" href="<?php echo BASE_URL; ?>member/transactions">Back</a>
                </form>

                <h2><?php echo htmlspecialchars($label); ?></h2>
                <div style="display:flex; gap:20px; margin-bottom:12px;">
                    <div style="background:#f1f5f9; padding:12px; border-radius:6px;">
                        <strong>Total Credits</strong>
                        <div>FRW <?php echo number_format($summary['credit'],2); ?></div>
                    </div>
                    <div style="background:#f1f5f9; padding:12px; border-radius:6px;">
                        <strong>Total Debits</strong>
                        <div>FRW <?php echo number_format($summary['debit'],2); ?></div>
                    </div>
                    <div style="background:#f1f5f9; padding:12px; border-radius:6px;">
                        <strong>Transactions</strong>
                        <div><?php echo intval($summary['count']); ?></div>
                    </div>
                    <div style="margin-left:auto; display:flex; gap:8px; align-items:center;">
                        <a class="btn btn-outline" href="<?php echo BASE_URL; ?>member/transactions/exportCsv?period=<?php echo urlencode($period); ?>&date=<?php echo urlencode($start); ?>">Download CSV</a>
                        <a class="btn btn-outline" target="_blank" href="<?php echo BASE_URL; ?>member/transactions/exportPdf?period=<?php echo urlencode($period); ?>&date=<?php echo urlencode($start); ?>">Export PDF</a>
                    </div>
                </div>

                <canvas id="txChart" style="max-width:980px; margin-bottom:12px;"></canvas>

                <table class="table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance Before</th>
                            <th>Balance After</th>
                            <th>Explanation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($t['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($t['transaction_type']); ?></td>
                                <td><?php echo number_format($t['amount'],2); ?></td>
                                <td><?php echo number_format($t['balance_before'] ?? 0,2); ?></td>
                                <td><?php echo number_format($t['balance_after'] ?? 0,2); ?></td>
                                <td><?php echo $t['explanation']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding:18px;">No transactions for this period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = <?php echo json_encode($chartLabels ?? []); ?>;
    const data = <?php echo json_encode($chartData ?? []); ?>;

    const ctx = document.getElementById('txChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Net (credits - debits)',
                data: data,
                backgroundColor: data.map(v => v >= 0 ? 'rgba(16,185,129,0.6)' : 'rgba(239,68,68,0.6)'),
                borderColor: data.map(v => v >= 0 ? 'rgba(16,185,129,1)' : 'rgba(239,68,68,1)'),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
</body>
</html>
