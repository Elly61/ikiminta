<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Loans - IKIMINA</title>
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

        .section-title {
            font-size: 20px;
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
            margin-top: 40px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title:first-of-type {
            margin-top: 0;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow-x: auto;
            margin-bottom: 30px;
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

        .status.active {
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
                <h1>📋 Loans</h1>
                <a href="<?php echo BASE_URL; ?>member/loans/request" class="btn-new">
                    ➕ Request Loan
                </a>
            </div>

            <h2 class="section-title">💰 Active Loans</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Interest Rate</th>
                            <th>Monthly Payment</th>
                            <th>Duration</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($loans)): ?>
                            <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><strong>RWF <?php echo number_format($loan['principal_amount'], 2); ?></strong></td>
                                <td><?php echo $loan['interest_rate']; ?>%</td>
                                <td>RWF <?php echo number_format($loan['monthly_payment'], 2); ?></td>
                                <td><?php echo $loan['duration_months']; ?> months</td>
                                <td><?php echo date('M d, Y', strtotime($loan['start_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($loan['end_date'])); ?></td>
                                <td><span class="status <?php echo strtolower($loan['status']); ?>"><?php echo ucfirst($loan['status']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📊</div>
                                        <p>No active loans</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h2 class="section-title">📝 Loan Requests</h2>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Duration</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th>Proof of Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><strong>RWF <?php echo number_format($request['amount'], 2); ?></strong></td>
                                <td><?php echo $request['duration_months']; ?> months</td>
                                <td><?php echo substr($request['purpose'] ?? 'N/A', 0, 30); ?>...</td>
                                <td><span class="status <?php echo strtolower($request['status']); ?>">
                                    <?php 
                                        $status = ucfirst($request['status']);
                                        echo ($status === 'Pending') ? '⏳ ' . $status : (($status === 'Approved') ? '✅ ' . $status : (($status === 'Rejected') ? '❌ ' . $status : $status));
                                    ?>
                                </span></td>
                                <td><?php echo date('M d, Y', strtotime($request['requested_at'])); ?></td>
                                <td>
                                    <?php
                                        $proofPath = $request['proof_payment'] ?? ($request['proof'] ?? ($request['proof_path'] ?? ($request['proof_of_payment'] ?? null)));
                                    ?>
                                    <?php if (!empty($proofPath)): ?>
                                        <a href="<?php echo BASE_URL . $proofPath; ?>" target="_blank" style="color: #667eea; text-decoration: none; font-weight: 600;">View proof →</a>
                                    <?php else: ?>
                                        <span style="color: #999;">No proof</span>
                                    <?php endif; ?>
                                </td>
                                <td><a href="#" style="color: #667eea; text-decoration: none; font-weight: 600;">View →</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📭</div>
                                        <p>No loan requests yet</p>
                                        <a href="<?php echo BASE_URL; ?>member/loans/request" class="btn-new">
                                            ➕ Create Request
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
</body>
</html>