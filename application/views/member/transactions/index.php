<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <h1>Transaction History</h1>
            
            <div style="margin-bottom: 20px;">
                <form method="GET" action="<?php echo BASE_URL; ?>member/transactions/filter" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <select name="type" style="padding: 8px; border: 1px solid #e5e7eb; border-radius: 5px;">
                        <option value="">All Transactions</option>
                        <option value="deposit">Deposits</option>
                        <option value="withdrawal">Withdrawals</option>
                        <option value="transfer_send">Transfers Sent</option>
                        <option value="transfer_receive">Transfers Received</option>
                        <option value="loan_disbursement">Loan Disbursements</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>

            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></td>
                                <td><?php echo str_replace('_', ' ', ucfirst($transaction['transaction_type'])); ?></td>
                                <td>RWF <?php echo number_format($transaction['amount'], 2); ?></td>
                                <td>RWF <?php echo number_format($transaction['fee'], 2); ?></td>
                                <td>RWF <?php echo number_format($transaction['balance_after'], 2); ?></td>
                                <td><span class="status <?php echo strtolower($transaction['status']); ?>"><?php echo ucfirst($transaction['status']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">No transactions found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($page)): ?>
            <div style="margin-top: 20px; text-align: center;">
                <?php if ($page > 1): ?>
                    <a href="<?php echo BASE_URL; ?>member/transactions?page=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>member/transactions?page=<?php echo $page + 1; ?>" class="btn btn-secondary">Next</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<?php include VIEW_PATH . 'member/layouts/footer.php'; ?>
</body>
</html>
