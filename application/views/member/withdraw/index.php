<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawals - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <h1>Withdrawals</h1>
            
            <div style="margin-bottom: 20px;">
                <a href="<?php echo BASE_URL; ?>member/withdraw/request" class="btn btn-primary">+ Request Withdrawal</a>
            </div>

            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($request['requested_at'])); ?></td>
                                <td>FRW <?php echo number_format($request['amount'], 2); ?></td>
                                <td><?php echo str_replace('_', ' ', ucfirst($request['withdrawal_method'])); ?></td>
                                <td>FRW <?php echo number_format($request['fee'], 2); ?></td>
                                <td><span class="status <?php echo strtolower($request['status']); ?>"><?php echo ucfirst($request['status']); ?></span></td>
                                <td><a href="<?php echo BASE_URL; ?>member/withdraw/view/<?php echo $request['id']; ?>">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">No withdrawal requests. <a href="<?php echo BASE_URL; ?>member/withdraw/request">Create one</a></td>
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
