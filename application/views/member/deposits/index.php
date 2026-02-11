<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposits - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <h1>My Deposits</h1>
            
            <div style="margin-bottom: 20px;">
                <a href="<?php echo BASE_URL; ?>member/deposits/create" class="btn btn-primary">+ New Deposit</a>
            </div>

            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Proof of Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($deposits)): ?>
                            <?php foreach ($deposits as $deposit): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($deposit['created_at'])); ?></td>
                                <td>RWF <?php echo number_format($deposit['amount'], 2); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $deposit['payment_method'])); ?></td>
                                <td><?php echo substr($deposit['transaction_reference'], 0, 15); ?>...</td>
                                <td>
                                    <?php
                                        $proofPath = $deposit['proof_payment'] ?? ($deposit['proof'] ?? ($deposit['proof_path'] ?? ($deposit['proof_of_payment'] ?? null)));
                                    ?>
                                    <?php if (!empty($proofPath)): ?>
                                        <a href="<?php echo BASE_URL . $proofPath; ?>" target="_blank">View proof</a>
                                    <?php else: ?>
                                        <span class="muted">No proof</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status <?php echo strtolower($deposit['status']); ?>"><?php echo ucfirst($deposit['status']); ?></span></td>
                                <td><a href="<?php echo BASE_URL; ?>member/deposits/view/<?php echo $deposit['id']; ?>">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">No deposits yet. <a href="<?php echo BASE_URL; ?>member/deposits/create">Create one</a></td>
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
