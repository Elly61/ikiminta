<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Settings - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/admin-dashboard.css">
</head>
<body>
<div class="admin-dashboard-wrapper">
    <?php include VIEW_PATH . 'admin/layouts/sidebar.php'; ?>
    
    <div class="admin-main-content">
        <?php include VIEW_PATH . 'admin/layouts/header.php'; ?>
        
        <div class="admin-dashboard-content">
            <h1>System Settings</h1>
            
            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px;">
                <form id="settingsForm" method="POST" action="<?php echo BASE_URL; ?>admin/settings/index">
                    <h3 style="margin-bottom: 20px; color: #333; border-bottom: 2px solid #3b82f6; padding-bottom: 10px;">💰 Fee Settings</h3>
                    
                    <div class="form-group">
                        <label for="transfer_fee">Transfer Fee (%):</label>
                        <input type="number" id="transfer_fee" name="transfer_fee" value="<?php echo $settings['transfer_fee']; ?>" step="0.01" min="0" max="100">
                        <small style="color: #666; display: block; margin-top: 5px;">Fee charged on each transfer between members</small>
                    </div>

                    <div class="form-group">
                        <label for="withdraw_fee">Withdrawal Fee (%):</label>
                        <input type="number" id="withdraw_fee" name="withdraw_fee" value="<?php echo $settings['withdraw_fee']; ?>" step="0.01" min="0" max="100">
                        <small style="color: #666; display: block; margin-top: 5px;">Fee charged on each withdrawal request</small>
                    </div>

                    <h3 style="margin: 30px 0 20px; color: #333; border-bottom: 2px solid #10b981; padding-bottom: 10px;">📈 Interest Rate Settings</h3>

                    <div class="form-group">
                        <label for="loan_interest_rate">Loan Interest Rate (%):</label>
                        <input type="number" id="loan_interest_rate" name="loan_interest_rate" value="<?php echo $settings['loan_interest_rate'] ?? 1.50; ?>" step="0.01" min="0" max="100">
                        <small style="color: #666; display: block; margin-top: 5px;">Monthly interest rate applied to approved loans</small>
                    </div>

                    <div class="form-group">
                        <label for="savings_interest_rate">Savings Interest Rate (%):</label>
                        <input type="number" id="savings_interest_rate" name="savings_interest_rate" value="<?php echo $settings['savings_interest_rate'] ?? 1.50; ?>" step="0.01" min="0" max="100">
                        <small style="color: #666; display: block; margin-top: 5px;">Daily interest rate applied to active savings accounts</small>
                    </div>

                    <h3 style="margin: 30px 0 20px; color: #333; border-bottom: 2px solid #8b5cf6; padding-bottom: 10px;">🎁 Bonus Settings</h3>

                    <div class="form-group">
                        <label for="signup_bonus">Signup Bonus (RWF):</label>
                        <input type="number" id="signup_bonus" name="signup_bonus" value="<?php echo $settings['signup_bonus']; ?>" step="0.01" min="0">
                        <small style="color: #666; display: block; margin-top: 5px;">Bonus amount credited to new members upon registration</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 20px; padding: 12px 30px;">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
</body>
</html>
