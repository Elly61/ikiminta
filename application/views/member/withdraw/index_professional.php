<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Withdrawals - IKIMINTA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .content-header h1 {
            font-size: 28px;
            color: #333;
            font-weight: 600;
            margin: 0;
        }

        .btn-new {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-new:hover {
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

        .status.approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status.rejected {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .action-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .action-link:hover {
            color: #764ba2;
            text-decoration: underline;
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

        .empty-state p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                align-items: flex-start;
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
                <h1>💸 My Withdrawals</h1>
                <a href="<?php echo BASE_URL; ?>member/withdraw/request" class="btn-new">
                    ➕ Request Withdrawal
                </a>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Fee</th>
                            <th>Net Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($request['requested_at'])); ?></td>
                                <td><strong>RWF <?php echo number_format($request['amount'], 2); ?></strong></td>
                                <td>
                                    <?php 
                                        $method = str_replace('_', ' ', ucfirst($request['withdrawal_method']));
                                        $icon = '';
                                        if (strpos($method, 'bank') !== false) $icon = '🏦';
                                        elseif (strpos($method, 'momo') !== false) $icon = '📱';
                                        elseif (strpos($method, 'cash') !== false) $icon = '💵';
                                        echo $icon . ' ' . $method;
                                    ?>
                                </td>
                                <td>RWF <?php echo number_format($request['fee'], 2); ?></td>
                                <td>RWF <?php echo number_format($request['amount'] - $request['fee'], 2); ?></td>
                                <td><span class="status <?php echo strtolower($request['status']); ?>">
                                    <?php 
                                        $status = ucfirst($request['status']);
                                        echo ($status === 'Pending') ? '⏳ ' . $status : (($status === 'Approved') ? '✅ ' . $status : (($status === 'Rejected') ? '❌ ' . $status : (($status === 'Completed') ? '✅ ' . $status : $status)));
                                    ?>
                                </span></td>
                                <td>
                                    <a href="#" class="action-link">View →</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📭</div>
                                        <p>No withdrawal requests yet</p>
                                        <a href="<?php echo BASE_URL; ?>member/withdraw/request" class="btn-new">
                                            ➕ Create Withdrawal
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<?php include VIEW_PATH . 'member/layouts/footer.php'; ?>
</body>
</html>