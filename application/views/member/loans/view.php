<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Details - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .loan-detail-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .loan-overview {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            margin-bottom: 24px;
        }

        .loan-overview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .loan-overview-header h2 {
            margin: 0;
            font-size: 22px;
        }

        .loan-status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .loan-status-badge.active {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }

        .loan-status-badge.completed {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .loan-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            padding: 30px;
        }

        .loan-stat {
            text-align: center;
        }

        .loan-stat-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .loan-stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }

        .loan-stat-value.highlight {
            color: #667eea;
        }

        .loan-stat-value.danger {
            color: #ef4444;
        }

        .loan-stat-value.success {
            color: #10b981;
        }

        /* Progress Bar */
        .progress-section {
            padding: 0 30px 30px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
            color: #666;
        }

        .progress-bar-bg {
            background: #e5e7eb;
            border-radius: 10px;
            height: 14px;
            overflow: hidden;
        }

        .progress-bar-fill {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
            min-width: 2px;
        }

        /* Payment Form */
        .payment-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            margin-bottom: 24px;
        }

        .payment-section h3 {
            margin: 0 0 20px;
            font-size: 18px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-form {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: end;
        }

        .payment-form .form-group {
            margin-bottom: 0;
        }

        .payment-form label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }

        .payment-form input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 15px;
        }

        .payment-form input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .quick-amounts {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .quick-amount-btn {
            padding: 6px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            background: #f8f9fa;
            cursor: pointer;
            font-size: 12px;
            color: #555;
            transition: all 0.2s;
        }

        .quick-amount-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .btn-pay {
            padding: 12px 28px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-pay:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Payment History */
        .payment-history {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
        }

        .payment-history h3 {
            margin: 0 0 20px;
            font-size: 18px;
            color: #333;
        }

        @media (max-width: 768px) {
            .loan-overview-header {
                padding: 20px;
            }
            .loan-stats {
                grid-template-columns: repeat(2, 1fr);
                padding: 20px;
                gap: 15px;
            }
            .loan-stat-value {
                font-size: 16px;
            }
            .payment-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <div class="loan-detail-container">

                <?php
                    $totalOwed = $total_owed;
                    $remaining = $remaining;
                    $progressPercent = $totalOwed > 0 ? min(100, ($loan['total_paid'] / $totalOwed) * 100) : 0;
                ?>

                <!-- Loan Overview Card -->
                <div class="loan-overview">
                    <div class="loan-overview-header">
                        <h2>💰 Loan #<?php echo $loan['id']; ?></h2>
                        <span class="loan-status-badge <?php echo strtolower($loan['status']); ?>">
                            <?php echo ucfirst($loan['status']); ?>
                        </span>
                    </div>

                    <div class="loan-stats">
                        <div class="loan-stat">
                            <div class="loan-stat-label">Principal Amount</div>
                            <div class="loan-stat-value">RWF <?php echo number_format($loan['principal_amount'], 2); ?></div>
                        </div>
                        <div class="loan-stat">
                            <div class="loan-stat-label">Interest Rate</div>
                            <div class="loan-stat-value highlight"><?php echo $loan['interest_rate']; ?>%</div>
                        </div>
                        <div class="loan-stat">
                            <div class="loan-stat-label">Monthly Payment</div>
                            <div class="loan-stat-value">RWF <?php echo number_format($loan['monthly_payment'], 2); ?></div>
                        </div>
                        <div class="loan-stat">
                            <div class="loan-stat-label">Duration</div>
                            <div class="loan-stat-value"><?php echo $loan['duration_months']; ?> months</div>
                        </div>
                        <div class="loan-stat">
                            <div class="loan-stat-label">Total to Repay</div>
                            <div class="loan-stat-value danger">RWF <?php echo number_format($totalOwed, 2); ?></div>
                        </div>
                        <div class="loan-stat">
                            <div class="loan-stat-label">Total Paid</div>
                            <div class="loan-stat-value success">RWF <?php echo number_format($loan['total_paid'], 2); ?></div>
                        </div>
                        <div class="loan-stat">
                            <div class="loan-stat-label">Remaining</div>
                            <div class="loan-stat-value danger">RWF <?php echo number_format(max(0, $remaining), 2); ?></div>
                        </div>
                        <div class="loan-stat">
                            <div class="loan-stat-label">Start → End</div>
                            <div class="loan-stat-value" style="font-size: 14px;">
                                <?php echo date('M d', strtotime($loan['start_date'])); ?> → <?php echo date('M d, Y', strtotime($loan['end_date'])); ?>
                            </div>
                        </div>
                    </div>

                    <div class="progress-section">
                        <div class="progress-header">
                            <span>Repayment Progress</span>
                            <span><?php echo number_format($progressPercent, 1); ?>%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: <?php echo $progressPercent; ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form (only for active loans) -->
                <?php if ($loan['status'] === 'active'): ?>
                <div class="payment-section">
                    <h3>💳 Make a Payment</h3>
                    <p style="color: #666; margin-bottom: 15px; font-size: 14px;">
                        Your balance: <strong>RWF <?php echo number_format($user['balance'], 2); ?></strong>
                    </p>
                    <form id="paymentForm" class="payment-form">
                        <div class="form-group">
                            <label for="payAmount">Payment Amount (RWF)</label>
                            <input type="number" id="payAmount" name="amount" 
                                   min="1" max="<?php echo $remaining; ?>" 
                                   step="0.01"
                                   placeholder="Enter payment amount"
                                   value="<?php echo number_format($loan['monthly_payment'], 2, '.', ''); ?>"
                                   required>
                            <div class="quick-amounts">
                                <button type="button" class="quick-amount-btn" onclick="setAmount(<?php echo $loan['monthly_payment']; ?>)">
                                    1 Month (<?php echo number_format($loan['monthly_payment'], 0); ?>)
                                </button>
                                <button type="button" class="quick-amount-btn" onclick="setAmount(<?php echo $loan['monthly_payment'] * 3; ?>)">
                                    3 Months
                                </button>
                                <button type="button" class="quick-amount-btn" onclick="setAmount(<?php echo $remaining; ?>)">
                                    Full (<?php echo number_format($remaining, 0); ?>)
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn-pay" id="payBtn">Pay Now</button>
                    </form>
                </div>
                <?php elseif ($loan['status'] === 'completed'): ?>
                <div class="payment-section" style="text-align: center;">
                    <h3 style="color: #10b981; justify-content: center;">✅ Loan Fully Paid!</h3>
                    <p style="color: #666;">Congratulations! You have fully paid off this loan.</p>
                </div>
                <?php endif; ?>

                <!-- Payment History -->
                <div class="payment-history">
                    <h3>📋 Payment History</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($payments)): ?>
                                <?php $i = 1; foreach ($payments as $p): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td>RWF <?php echo number_format($p['payment_amount'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($p['payment_date'])); ?></td>
                                    <td><code><?php echo htmlspecialchars($p['transaction_reference'] ?? '—'); ?></code></td>
                                    <td><span class="status completed"><?php echo ucfirst($p['status']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 20px; color: #999;">No payments yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 20px;">
                    <a href="<?php echo BASE_URL; ?>member/loans" class="btn btn-primary" style="padding: 10px 20px;">← Back to Loans</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<?php if ($loan['status'] === 'active'): ?>
<script>
    function setAmount(val) {
        document.getElementById('payAmount').value = Math.round(val * 100) / 100;
    }

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var amount = parseFloat(document.getElementById('payAmount').value);
        var balance = <?php echo $user['balance']; ?>;
        var remaining = <?php echo $remaining; ?>;

        if (amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }

        if (amount > balance) {
            alert('Insufficient balance! Your balance is RWF ' + balance.toLocaleString());
            return;
        }

        if (amount > remaining) {
            alert('Amount exceeds remaining loan balance of RWF ' + Math.round(remaining).toLocaleString());
            return;
        }

        if (!confirm('Confirm payment of RWF ' + amount.toLocaleString() + ' towards Loan #<?php echo $loan['id']; ?>?')) {
            return;
        }

        var btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.textContent = 'Processing...';

        fetch('<?php echo BASE_URL; ?>member/loans/pay/<?php echo $loan['id']; ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'amount=' + amount
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.textContent = 'Pay Now';
            }
        })
        .catch(function(err) {
            alert('Network error. Please try again.');
            btn.disabled = false;
            btn.textContent = 'Pay Now';
        });
    });
</script>
<?php endif; ?>
</body>
</html>
