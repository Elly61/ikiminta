<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin-dashboard.css">
</head>
<body>
<div class="admin-dashboard-wrapper">
    <?php include VIEW_PATH . 'admin/layouts/sidebar.php'; ?>
    
    <div class="admin-main-content">
        <?php include VIEW_PATH . 'admin/layouts/header.php'; ?>
        
        <div class="admin-dashboard-content">
            <h1>Manage Users</h1>
            
            <div class="recent-transactions">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                                <td>RWF <?php echo number_format($user['balance'], 2); ?></td>
                                <td><span class="status <?php echo strtolower($user['status']); ?>"><?php echo ucfirst($user['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($user['date_created'])); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>admin/users/view/<?php echo $user['id']; ?>" class="btn btn-primary">View</a>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <button class="btn btn-danger" onclick="suspendUser(<?php echo $user['id']; ?>)">Suspend</button>
                                    <?php else: ?>
                                        <button class="btn btn-success" onclick="activateUser(<?php echo $user['id']; ?>)">Activate</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function suspendUser(userId) {
    if (confirm('Are you sure you want to suspend this user?')) {
        fetch('<?php echo BASE_URL; ?>admin/users/suspend/' + userId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('User suspended');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
}

function activateUser(userId) {
    if (confirm('Are you sure you want to activate this user?')) {
        fetch('<?php echo BASE_URL; ?>admin/users/activate/' + userId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('User activated');
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
