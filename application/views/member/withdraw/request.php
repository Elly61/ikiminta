<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Withdrawal - IKIMINTA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .withdraw-request-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            max-width: 900px;
            margin: 0 auto;
        }

        .withdraw-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .withdraw-header h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 600;
        }

        .withdraw-header p {
            margin: 0;
            opacity: 0.95;
            font-size: 15px;
        }

        .withdraw-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 40px;
        }

        .withdraw-form {
            display: flex;
            flex-direction: column;
        }

        .form-section {
            margin-bottom: 28px;
        }

        .form-section label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-section input,
        .form-section select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-section input:focus,
        .form-section select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background-color: #f8f9ff;
        }

        .form-hint {
            font-size: 12px;
            color: #888;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .conditional-fields {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin-bottom: 20px;
            display: none;
        }

        .conditional-fields.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }
            to {
                opacity: 1;
                max-height: 200px;
            }
        }

        .method-icon {
            font-size: 24px;
            margin-right: 8px;
        }

        .withdraw-summary {
            background: #f8f9fa;
            padding: 28px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .summary-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e0e0e0;
        }

        .summary-item:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            color: #666;
            font-size: 13px;
            font-weight: 500;
        }

        .summary-value {
            color: #333;
            font-size: 14px;
            font-weight: 600;
        }

        .summary-total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
            text-align: center;
        }

        .summary-total-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-total-value {
            font-size: 28px;
            font-weight: 700;
            margin-top: 6px;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 14px 16px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 13px;
            color: #333;
            line-height: 1.6;
        }

        .info-box strong {
            color: #667eea;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .btn-submit {
            flex: 1;
            padding: 14px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-cancel {
            flex: 1;
            padding: 14px 24px;
            background: #f0f0f0;
            color: #333;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-cancel:hover {
            background: #e8e8e8;
            border-color: #d0d0d0;
        }

        @media (max-width: 768px) {
            .withdraw-content {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 25px;
            }

            .withdraw-header {
                padding: 30px 20px;
            }

            .withdraw-header h2 {
                font-size: 22px;
            }

            .withdraw-summary {
                position: static;
            }
        }

        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-submit:disabled .loading-spinner {
            display: inline-block;
            margin-right: 8px;
        }

        .fee-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <div class="withdraw-request-container">
                <div class="withdraw-header">
                    <h2>Withdraw Funds</h2>
                    <p>Request a withdrawal from your account</p>
                </div>

                <div class="withdraw-content">
                    <div class="withdraw-form">
                        <form id="withdrawForm" method="POST" action="<?php echo BASE_URL; ?>member/withdraw/request">
                            <div class="form-section">
                                <label for="amount">
                                    💰 Withdrawal Amount
                                </label>
                                <input 
                                    type="number" 
                                    id="amount" 
                                    name="amount" 
                                    required 
                                    placeholder="Enter amount in RWF" 
                                    step="0.01" 
                                    min="1000"
                                    value="0"
                                    data-type="amount"
                                >
                                <div class="form-hint">📌 Minimum: RWF 1,000</div>
                            </div>

                            <div class="form-section">
                                <label for="withdrawal_method">
                                    🏦 Withdrawal Method
                                </label>
                                <select id="withdrawal_method" name="withdrawal_method" required onchange="updateMethodFields()">
                                    <option value="">Select a method</option>
                                    <option value="bank_transfer">🏦 Bank Transfer</option>
                                    <option value="momo">📱 Mobile Money (MOMO)</option>
                                    <option value="cash">💵 Cash</option>
                                </select>
                                <div class="form-hint">📌 Choose how you want to receive your funds</div>
                            </div>

                            <div id="bank-fields" class="conditional-fields">
                                <div class="form-section" style="margin-bottom: 0;">
                                    <label for="bank_account">
                                        🏦 Bank Account Number
                                    </label>
                                    <input 
                                        type="text" 
                                        id="bank_account" 
                                        name="bank_account" 
                                        placeholder="Enter your bank account number"
                                    >
                                    <div class="form-hint">📌 Your RWF bank account</div>
                                </div>
                            </div>

                            <div id="momo-fields" class="conditional-fields">
                                <div class="form-section" style="margin-bottom: 0;">
                                    <label for="momo_number">
                                        📱 MOMO Phone Number
                                    </label>
                                    <input 
                                        type="tel" 
                                        id="momo_number" 
                                        name="momo_number" 
                                        placeholder="+250 700 000 000"
                                    >
                                    <div class="form-hint">📌 Your MOMO account phone number</div>
                                </div>
                            </div>

                            <div class="fee-warning">
                                <strong>⚠️ Important:</strong> A withdrawal fee of 2.5% will be deducted from your withdrawal amount.
                            </div>

                            <div class="info-box">
                                <strong>ⓘ Processing Time:</strong><br>
                                Your withdrawal request will be reviewed within 1-2 business days. 
                                Once approved, funds will be transferred to your selected method.
                            </div>

                            <div class="button-group">
                                <button type="submit" class="btn-submit" id="submitBtn">
                                    <span class="loading-spinner"></span>
                                    Submit Request
                                </button>
                                <a href="<?php echo BASE_URL; ?>member/withdraw" class="btn-cancel">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div class="withdraw-summary">
                        <div class="summary-title">
                            📊 Withdrawal Summary
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Requested Amount</span>
                            <span class="summary-value" id="summaryAmount">RWF 0.00</span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Withdrawal Fee (2.5%)</span>
                            <span class="summary-value" id="summaryFee">RWF 0.00</span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Method</span>
                            <span class="summary-value" id="summaryMethod">—</span>
                        </div>

                        <div class="summary-total">
                            <div class="summary-total-label">You Will Receive</div>
                            <div class="summary-total-value" id="summaryNet">RWF 0.00</div>
                        </div>

                        <div class="info-box" style="margin-top: 20px; border-left-color: #667eea;">
                            <strong>💡 Tip:</strong> Larger withdrawals may take slightly longer to process 
                            due to additional verification requirements.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<script>
    const amountInput = document.getElementById('amount');
    const methodSelect = document.getElementById('withdrawal_method');
    const withdrawForm = document.getElementById('withdrawForm');
    const submitBtn = document.getElementById('submitBtn');

    // Withdrawal fee percentage
    const WITHDRAWAL_FEE_PERCENT = 2.5;

    function formatCurrency(value) {
        return 'RWF ' + Math.round(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function updateMethodFields() {
        const method = document.getElementById('withdrawal_method').value;
        
        // Hide all method fields
        document.getElementById('bank-fields').classList.remove('active');
        document.getElementById('momo-fields').classList.remove('active');

        // Show selected method
        if (method === 'bank_transfer') {
            document.getElementById('bank-fields').classList.add('active');
            document.getElementById('bank_account').required = true;
            document.getElementById('momo_number').required = false;
        } else if (method === 'momo') {
            document.getElementById('momo-fields').classList.add('active');
            document.getElementById('momo_number').required = true;
            document.getElementById('bank_account').required = false;
        } else {
            document.getElementById('bank_account').required = false;
            document.getElementById('momo_number').required = false;
        }

        updateWithdrawalSummary();
    }

    function updateWithdrawalSummary() {
        const amount = parseFloat(amountInput.value) || 0;
        const method = methodSelect.value;

        // Update amount
        document.getElementById('summaryAmount').textContent = formatCurrency(amount);

        // Calculate fee and net amount
        const fee = amount * (WITHDRAWAL_FEE_PERCENT / 100);
        const netAmount = amount - fee;

        document.getElementById('summaryFee').textContent = formatCurrency(fee);
        document.getElementById('summaryNet').textContent = formatCurrency(netAmount);

        // Update method display
        let methodDisplay = '—';
        if (method === 'bank_transfer') {
            methodDisplay = '🏦 Bank Transfer';
        } else if (method === 'momo') {
            methodDisplay = '📱 Mobile Money';
        } else if (method === 'cash') {
            methodDisplay = '💵 Cash';
        }
        document.getElementById('summaryMethod').textContent = methodDisplay;
    }

    // Event listeners
    amountInput.addEventListener('input', updateWithdrawalSummary);
    methodSelect.addEventListener('change', updateMethodFields);

    // Form submission
    withdrawForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat(amountInput.value);
        const method = methodSelect.value;

        // Validation
        if (amount < 1000) {
            alert('Withdrawal amount must be at least RWF 1,000');
            amountInput.focus();
            return;
        }

        if (!method) {
            alert('Please select a withdrawal method');
            methodSelect.focus();
            return;
        }

        if (method === 'bank_transfer' && !document.getElementById('bank_account').value.trim()) {
            alert('Please enter your bank account number');
            document.getElementById('bank_account').focus();
            return;
        }

        if (method === 'momo' && !document.getElementById('momo_number').value.trim()) {
            alert('Please enter your MOMO phone number');
            document.getElementById('momo_number').focus();
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';

        const url = withdrawForm.action;
        const fd = new FormData(withdrawForm);

        fetch(url, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        }).then(async (res) => {
            let data = null;
            try {
                data = await res.json();
            } catch (err) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                alert('Unexpected server response. Please try again.');
                return;
            }

            const msg = (data && data.message) ? data.message : 'Operation completed.';

            if (data && data.status === 'success') {
                if (typeof showNotification === 'function') {
                    showNotification(msg, 'success');
                } else {
                    alert(msg);
                }

                if (data.data && data.data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.data.redirect;
                    }, 1200);
                } else {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            } else {
                if (typeof showNotification === 'function') {
                    showNotification(msg, 'error');
                } else {
                    alert(msg);
                }
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }).catch((err) => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            const msg = 'Network error. Please try again.';
            if (typeof showNotification === 'function') {
                showNotification(msg, 'error');
            } else {
                alert(msg);
            }
        });
    });
</script>
<?php include VIEW_PATH . 'member/layouts/footer.php'; ?>
</body>
</html>
