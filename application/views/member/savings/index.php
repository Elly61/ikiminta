<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Savings - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <h1>Savings</h1>
            
            <div style="margin-bottom: 20px;">
                <a href="<?php echo BASE_URL; ?>member/savings/create" class="btn btn-primary">+ Create Savings Account</a>
            </div>

            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Interest Rate</th>
                            <th>Maturity Date</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Proof of Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($savings)): ?>
                            <?php foreach ($savings as $saving): ?>
                            <tr>
                                <td>RWF <?php echo number_format($saving['amount'], 2); ?></td>
                                <td><?php echo $saving['interest_rate']; ?>%</td>
                                <td><?php echo date('M d, Y', strtotime($saving['maturity_date'])); ?></td>
                                <td><span class="status <?php echo strtolower($saving['status']); ?>"><?php echo ucfirst($saving['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($saving['created_at'])); ?></td>
                                <td>
                                    <?php
                                        $proofPath = $saving['proof_payment'] ?? ($saving['proof'] ?? ($saving['proof_path'] ?? ($saving['proof_of_payment'] ?? null)));
                                    ?>
                                    <?php if (!empty($proofPath)): ?>
                                        <a href="<?php echo BASE_URL . $proofPath; ?>" target="_blank">View proof</a>
                                    <?php else: ?>
                                        <span class="muted">No proof</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">No savings accounts. <a href="<?php echo BASE_URL; ?>member/savings/create">Create one</a></td>
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
