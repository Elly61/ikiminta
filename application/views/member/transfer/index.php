<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Funds - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <h1>Transfer History</h1>
            
            <div style="margin-bottom: 20px;">
                <a href="<?php echo BASE_URL; ?>member/transfer/create" class="btn btn-primary">+ New Transfer</a>
            </div>

            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>From/To</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Proof of Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transfers)): ?>
                            <?php foreach ($transfers as $transfer): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($transfer['created_at'])); ?></td>
                                <td>
                                    <?php if ($transfer['sender_id'] == $user['id']): ?>
                                        To: <?php echo $transfer['receiver_username']; ?>
                                    <?php else: ?>
                                        From: <?php echo $transfer['sender_username']; ?>
                                    <?php endif; ?>
                                </td>
                                <td>RWF <?php echo number_format($transfer['amount'], 2); ?></td>
                                <td>RWF <?php echo number_format($transfer['fee'], 2); ?></td>
                                <td><span class="status <?php echo strtolower($transfer['status']); ?>"><?php echo ucfirst($transfer['status']); ?></span></td>
                                <td><?php echo substr($transfer['reference_number'], 0, 12); ?>...</td>
                                <td>
                                    <?php
                                        $proofPath = $transfer['proof_payment'] ?? ($transfer['proof'] ?? ($transfer['proof_path'] ?? ($transfer['proof_of_payment'] ?? null)));
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
                                <td colspan="7" style="text-align: center; padding: 20px;">No transfers yet. <a href="<?php echo BASE_URL; ?>member/transfer/create">Start transferring</a></td>
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
