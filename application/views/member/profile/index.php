<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>

    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>

        <div class="dashboard-content">
            <h1>My Profile</h1>

            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px;">
                <form id="profileForm" method="POST" action="<?php echo BASE_URL; ?>member/profile/update">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name:</label>
                            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="legal_id">Legal ID:</label>
                        <input type="text" id="legal_id" name="legal_id" value="<?php echo htmlspecialchars($user['legal_id']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">Phone Number:</label>
                        <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="<?php echo BASE_URL; ?>member/dashboard" class="btn btn-secondary" style="margin-left: 10px;">Back to Dashboard</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<script>
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating profile');
    });
});
</script>
</body>
</html>