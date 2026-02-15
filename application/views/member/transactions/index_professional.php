<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .content-header {
            margin-bottom: 30px;
        }

        .content-header h1 {
            font-size: 28px;
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .filter-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 30px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-box select {
            padding: 10px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .filter-box select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-filter {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow-x: auto;
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
            font-size: 12px;
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

        .transaction-type {
            font-weight: 600;
            color: #667eea;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
            align-items: center;
        }

        .pagination a {
            padding: 10px 16px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .filter-box {
                flex-direction: column;
            }

            .filter-box select,
            .btn-filter {
                width: 100%;
            }

            .table-container {
                padding: 20px;
            }

            .table th,
            .table td {
                padding: 12px 8px;
                font-size: 12px;
            }

            .content-header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content" style="padding: 30px;">
            <div class="content-header">
                <h1>📊 Transaction History</h1>
            </div>

            <div class="filter-box">
                <form method="GET" action="<?php echo BASE_URL; ?>member/transactions/filter" style="display: flex; gap: 12px; flex-wrap: wrap; width: 100%;">
                    <select name="type">
                        <option value="">🔄 All Transactions</option>
                        <option value="deposit">💳 Deposits</option>
                        <option value="withdrawal">💸 Withdrawals</option>
                        <option value="transfer_send">📤 Transfers Sent</option>
                        <option value="transfer_receive">📥 Transfers Received</option>
                        <option value="loan_disbursement">📋 Loan Disbursements</option>
                    </select>
                    <button type="submit" class="btn-filter">🔍 Filter</button>
                </form>
            </div>

            <div class="table-container">
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
                                <td>
                                    <span class="transaction-type">
                                        <?php 
                                            $type = str_replace('_', ' ', ucfirst($transaction['transaction_type']));
                                            $icon = '';
                                            if (strpos($type, 'deposit') !== false) $icon = '💳';
                                            elseif (strpos($type, 'withdrawal') !== false) $icon = '💸';
                                            elseif (strpos($type, 'transfer') !== false && strpos($type, 'send') !== false) $icon = '📤';
                                            elseif (strpos($type, 'transfer') !== false && strpos($type, 'receive') !== false) $icon = '📥';
                                            elseif (strpos($type, 'loan') !== false) $icon = '📋';
                                            echo $icon . ' ' . $type;
                                        ?>
                                    </span>
                                </td>
                                <td><strong>RWF <?php echo number_format($transaction['amount'], 2); ?></strong></td>
                                <td>RWF <?php echo number_format($transaction['fee'], 2); ?></td>
                                <td>RWF <?php echo number_format($transaction['balance_after'], 2); ?></td>
                                <td><span class="status <?php echo strtolower($transaction['status']); ?>">
                                    <?php 
                                        $status = ucfirst($transaction['status']);
                                        echo ($status === 'Pending') ? '⏳ ' . $status : (($status === 'Completed') ? '✅ ' . $status : (($status === 'Approved') ? '✅ ' . $status : (($status === 'Rejected') ? '❌ ' . $status : $status)));
                                    ?>
                                </span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📭</div>
                                        <p>No transactions found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (isset($page)): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?php echo BASE_URL; ?>member/transactions?page=<?php echo $page - 1; ?>">← Previous</a>
                <?php endif; ?>
                <span style="color: #666;">Page <?php echo $page; ?></span>
                <a href="<?php echo BASE_URL; ?>member/transactions?page=<?php echo $page + 1; ?>">Next →</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<?php include VIEW_PATH . 'member/layouts/footer.php'; ?>
</body>
</html>