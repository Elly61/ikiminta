<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Deposits - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin-dashboard.css">
</head>
<body>
<div class="admin-dashboard-wrapper">
    <?php include VIEW_PATH . 'admin/layouts/sidebar.php'; ?>
    
    <div class="admin-main-content">
        <?php include VIEW_PATH . 'admin/layouts/header.php'; ?>
        
        <div class="admin-dashboard-content">
            <h1>Pending Deposits</h1>
            
            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($deposits)): ?>
                            <?php foreach ($deposits as $deposit): ?>
                            <tr>
                                <td><?php echo $deposit['username']; ?></td>
                                <td><?php echo $deposit['email']; ?></td>
                                <td>RWF <?php echo number_format($deposit['amount'], 2); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $deposit['payment_method'])); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($deposit['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-success" data-action="approve" onclick="approveDeposit(<?php echo $deposit['id']; ?>)">Approve</button>
                                    <button class="btn btn-danger" data-action="reject" onclick="rejectDeposit(<?php echo $deposit['id']; ?>)">Reject</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">No pending deposits</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function approveDeposit(depositId) {
    if (confirm('Approve this deposit?')) {
        fetch('<?php echo BASE_URL; ?>admin/deposits/approve/' + depositId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Deposit approved');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function rejectDeposit(depositId) {
    const reason = prompt('Enter rejection reason:');
    if (reason !== null) {
        fetch('<?php echo BASE_URL; ?>admin/deposits/reject/' + depositId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Deposit rejected');
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
