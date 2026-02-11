<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report - Print</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <style>
        /* Print-friendly overrides */
        body { color: #111827; font-family: Arial, sans-serif; }
        .report-container { max-width: 900px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 6px; border: 1px solid #e6eef6; }
        th { background: #f8fafc; }
        @media print {
            a { color: #000; text-decoration: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<div class="report-container">
    <h1>Transaction Report</h1>
    <p><strong>Period:</strong> <?php echo htmlspecialchars($label); ?></p>
    <p><strong>User:</strong> <?php echo htmlspecialchars($user['username'] ?? $user['email']); ?></p>

    <div style="display:flex; gap:12px; margin-bottom:12px;">
        <div style="padding:8px; border:1px solid #eef2f7; border-radius:6px;">
            <strong>Total Credits</strong>
            <div>FRW <?php echo number_format($summary['credit'],2); ?></div>
        </div>
        <div style="padding:8px; border:1px solid #eef2f7; border-radius:6px;">
            <strong>Total Debits</strong>
            <div>FRW <?php echo number_format($summary['debit'],2); ?></div>
        </div>
        <div style="padding:8px; border:1px solid #eef2f7; border-radius:6px;">
            <strong>Transactions</strong>
            <div><?php echo intval($summary['count']); ?></div>
        </div>
    </div>

    <table>
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
                    <td><?php echo htmlspecialchars($t['explanation']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:18px;">No transactions for this period.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top:18px;" class="no-print">
        <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
        <a href="<?php echo BASE_URL; ?>member/transactions/report?period=<?= urlencode($_GET['period'] ?? 'daily') ?>&date=<?= urlencode($_GET['date'] ?? date('Y-m-d')) ?>" class="btn btn-secondary">Back</a>
    </div>
</div>

<script>
// Auto trigger print when opened as Export PDF
window.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        window.print();
    }, 300);
});
</script>
</body>
</html>
