<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Withdrawals - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin-dashboard.css">
</head>
<body>
<div class="admin-dashboard-wrapper">
    <?php include VIEW_PATH . 'admin/layouts/sidebar.php'; ?>
    
    <div class="admin-main-content">
        <?php include VIEW_PATH . 'admin/layouts/header.php'; ?>
        
        <div class="admin-dashboard-content">
            <h1>Pending Withdrawals</h1>
            
            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Fee</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo $request['username']; ?></td>
                                <td><?php echo $request['email']; ?></td>
                                <td>RWF <?php echo number_format($request['amount'], 2); ?></td>
                                <td><?php echo str_replace('_', ' ', ucfirst($request['withdrawal_method'])); ?></td>
                                <td>RWF <?php echo number_format($request['fee'], 2); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($request['requested_at'])); ?></td>
                                <td>
                                    <button class="btn btn-success" onclick="approveWithdraw(<?php echo $request['id']; ?>)">Approve</button>
                                    <button class="btn btn-danger" onclick="rejectWithdraw(<?php echo $request['id']; ?>)">Reject</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">No pending withdrawals</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function approveWithdraw(requestId) {
    if (confirm('Approve this withdrawal?')) {
        fetch('<?php echo BASE_URL; ?>admin/withdrawals/approve/' + requestId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Withdrawal approved (Blockchain Hash: ' + data.data.blockchain_hash + ')');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function rejectWithdraw(requestId) {
    const reason = prompt('Enter rejection reason:');
    if (reason !== null) {
        fetch('<?php echo BASE_URL; ?>admin/withdrawals/reject/' + requestId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Withdrawal rejected');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}
</script>
<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
</body>
</html>
