<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Loan Requests - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin-dashboard.css">
</head>
<body>
<div class="admin-dashboard-wrapper">
    <?php include VIEW_PATH . 'admin/layouts/sidebar.php'; ?>
    
    <div class="admin-main-content">
        <?php include VIEW_PATH . 'admin/layouts/header.php'; ?>
        
        <div class="admin-dashboard-content">
            <h1>Pending Loan Requests</h1>
            
            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Duration</th>
                            <th>Purpose</th>
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
                                <td><?php echo $request['duration_months']; ?> months</td>
                                <td><?php echo substr($request['purpose'], 0, 30); ?>...</td>
                                <td><?php echo date('M d, Y', strtotime($request['requested_at'])); ?></td>
                                <td>
                                    <button class="btn btn-success" onclick="approveLoan(<?php echo $request['id']; ?>)">Approve</button>
                                    <button class="btn btn-danger" onclick="rejectLoan(<?php echo $request['id']; ?>)">Reject</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">No pending loan requests</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
var defaultInterestRate = <?php echo $default_interest_rate ?? 1.50; ?>;

function approveLoan(requestId) {
    const rate = prompt('Enter interest rate (%):', defaultInterestRate);
    if (rate !== null) {
        fetch('<?php echo BASE_URL; ?>admin/loans/approve/' + requestId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ interest_rate: rate })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Loan approved with ' + rate + '% interest rate');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function rejectLoan(requestId) {
    const reason = prompt('Enter rejection reason:');
    if (reason !== null) {
        fetch('<?php echo BASE_URL; ?>admin/loans/reject/' + requestId, {
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
                alert('Loan request rejected');
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
