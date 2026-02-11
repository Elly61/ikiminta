<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Deposit - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <h1>New Deposit</h1>
            
            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px;">
                <form id="depositForm" method="POST" action="<?php echo BASE_URL; ?>member/deposits/create" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="amount">Deposit Amount (RWF):</label>
                        <input type="number" id="amount" name="amount" required placeholder="0.00" step="0.01" min="1">
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Payment Method:</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="momo">Mobile Money (MOMO)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description (Optional):</label>
                        <textarea id="description" name="description" placeholder="Enter deposit description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="proof">Proof of Payment:</label>
                        <input type="file" id="proof" name="proof" accept="image/png, image/jpeg, image/jpg, application/pdf">
                        <small>Allowed types: png, jpg, jpeg, pdf. Max 5MB.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Initiate Deposit</button>
                    <a href="<?php echo BASE_URL; ?>member/deposits" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('depositForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.textContent : null;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
        }

        const url = form.action;
        const fd = new FormData(form);

        fetch(url, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        }).then(async (res) => {
            let data = null;
            try {
                data = await res.json();
            } catch (err) {
                // Non-JSON response
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
                alert('Unexpected server response. Please try again.');
                return;
            }

            if (data.status && data.status === 'success') {
                // On success, redirect if provided
                if (data.data && data.data.redirect) {
                    window.location.href = data.data.redirect;
                } else {
                    showNotification(data.message || 'Success', 'success');
                }
            } else {
                // Show error popup and stay on page
                const msg = (data && data.message) ? data.message : 'An error occurred';
                // Use existing showNotification if available, otherwise fallback to alert
                if (typeof showNotification === 'function') {
                    showNotification(msg, 'error');
                } else {
                    alert(msg);
                }
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
            }
        }).catch((err) => {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
            const msg = 'Network error. Please try again.';
            if (typeof showNotification === 'function') {
                showNotification(msg, 'error');
            } else {
                alert(msg);
            }
        });
    });
});
</script>
</body>
</html>
