<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Requests - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <h1>Loans</h1>
            
            <div style="margin-bottom: 20px;">
                <a href="<?php echo BASE_URL; ?>member/loans/request" class="btn btn-primary">+ Request Loan</a>
            </div>

            <h2>Active Loans</h2>
            <div class="recent-transactions" style="margin-bottom: 40px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Interest Rate</th>
                            <th>Monthly Payment</th>
                            <th>Total Paid</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($loans)): ?>
                            <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td>RWF <?php echo number_format($loan['principal_amount'], 2); ?></td>
                                <td><?php echo $loan['interest_rate']; ?>%</td>
                                <td>RWF <?php echo number_format($loan['monthly_payment'], 2); ?></td>
                                <td>RWF <?php echo number_format($loan['total_paid'], 2); ?></td>
                                <td><?php echo $loan['duration_months']; ?> months</td>
                                <td><span class="status <?php echo strtolower($loan['status']); ?>"><?php echo ucfirst($loan['status']); ?></span></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>member/loans/viewLoan/<?php echo $loan['id']; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">
                                        <?php echo $loan['status'] === 'active' ? '💳 Pay' : '👁 View'; ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">No active loans</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h2>Loan Requests</h2>
            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Duration</th>
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
                                <td>RWF <?php echo number_format($request['amount'], 2); ?></td>
                                <td><?php echo $request['duration_months']; ?> months</td>
                                <td><span class="status <?php echo strtolower($request['status']); ?>"><?php echo ucfirst($request['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($request['requested_at'])); ?></td>
                                <td>
                                    <?php
                                        $proofPath = $request['proof_payment'] ?? ($request['proof'] ?? ($request['proof_path'] ?? ($request['proof_of_payment'] ?? null)));
                                    ?>
                                    <?php if (!empty($proofPath)): ?>
                                        <a href="<?php echo BASE_URL . $proofPath; ?>" target="_blank">View proof</a>
                                    <?php else: ?>
                                        <span class="muted">No proof</span>
                                    <?php endif; ?>
                                </td>
                                <td><a href="#">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">No loan requests. <a href="<?php echo BASE_URL; ?>member/loans/request">Create one</a></td>
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
