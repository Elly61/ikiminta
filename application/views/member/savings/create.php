<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Savings - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .savings-request-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            max-width: 900px;
            margin: 0 auto;
        }

        .savings-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .savings-header h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 600;
        }

        .savings-header p {
            margin: 0;
            opacity: 0.95;
            font-size: 15px;
        }

        .savings-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 40px;
        }

        .savings-form {
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

        .savings-summary {
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

        .summary-item:last-child {
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
            .savings-content {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 25px;
            }

            .savings-header {
                padding: 30px 20px;
            }

            .savings-header h2 {
                font-size: 22px;
            }

            .savings-summary {
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
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <div class="savings-request-container">
                <div class="savings-header">
                    <h2>Create Savings Account</h2>
                    <p>Lock your funds and earn interest at maturity</p>
                </div>

                <div class="savings-content">
                    <div class="savings-form">
                        <form id="savingsForm" method="POST" action="<?php echo BASE_URL; ?>member/savings/create" enctype="multipart/form-data">
                            <div class="form-section">
                                <label for="amount">
                                    💰 Savings Amount
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
                                <label for="maturity_months">
                                    ⏱️ Maturity Period
                                </label>
                                <select id="maturity_months" name="maturity_months" required>
                                    <option value="">Select duration</option>
                                    <option value="3">3 months</option>
                                    <option value="6">6 months</option>
                                    <option value="12">12 months</option>
                                    <option value="24">24 months</option>
                                    <option value="36">36 months</option>
                                    <option value="60">60 months (5 years)</option>
                                </select>
                                <div class="form-hint">📌 Funds are locked until maturity</div>
                            </div>

                            <div class="form-section">
                                <label for="interest_rate">
                                    📈 Interest Rate (% per year)
                                </label>
                                <input 
                                    type="number" 
                                    id="interest_rate" 
                                    name="interest_rate" 
                                    required 
                                    placeholder="0.00" 
                                    step="0.01" 
                                    min="0" 
                                    max="100"
                                    value="<?php echo htmlspecialchars($default_interest_rate ?? '1.5'); ?>"
                                    readonly
                                >
                                <div class="form-hint">📌 Annual interest rate applied at maturity (Set by admin)</div>
                            </div>

                            <div class="form-section">
                                <label for="proof">
                                    📄 Proof of Payment (Optional)
                                </label>
                                <input type="file" id="proof" name="proof" accept=".pdf,.jpg,.jpeg,.png" />
                                <div class="form-hint">📌 Upload a receipt or document. Max 5MB. Allowed: PDF, JPG, PNG</div>
                            </div>

                            <div class="info-box">
                                <strong>ⓘ Important:</strong><br>
                                Your savings will be locked until the maturity date. 
                                Interest is calculated and credited at maturity.
                            </div>

                            <div class="button-group">
                                <button type="submit" class="btn-submit" id="submitBtn">
                                    <span class="loading-spinner"></span>
                                    Create Savings
                                </button>
                                <a href="<?php echo BASE_URL; ?>member/savings" class="btn-cancel">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div class="savings-summary">
                        <div class="summary-title">
                            📊 Savings Summary
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Principal Amount</span>
                            <span class="summary-value" id="summaryAmount">RWF 0.00</span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Maturity Period</span>
                            <span class="summary-value" id="summaryDuration">—</span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Estimated Interest</span>
                            <span class="summary-value" id="summaryInterest">RWF 0.00</span>
                        </div>

                        <div class="summary-total">
                            <div class="summary-total-label">Total at Maturity (Estimated)</div>
                            <div class="summary-total-value" id="summaryTotal">RWF 0.00</div>
                        </div>

                        <div class="info-box" style="margin-top: 20px; border-left-color: #667eea;">
                            <strong>💡 Tip:</strong> Longer periods typically yield higher returns.
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
    const maturitySelect = document.getElementById('maturity_months');
    const rateInput = document.getElementById('interest_rate');
    const savingsForm = document.getElementById('savingsForm');
    const submitBtn = document.getElementById('submitBtn');

    function formatCurrency(value) {
        return 'RWF ' + Math.round(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function calculateSavingsSummary() {
        const amount = parseFloat(amountInput.value) || 0;
        const months = parseInt(maturitySelect.value) || 0;
        const rate = parseFloat(rateInput.value) || 0;

        document.getElementById('summaryAmount').textContent = formatCurrency(amount);

        if (months > 0 && amount > 0 && rate >= 0) {
            const years = months / 12;
            const interest = amount * (rate / 100) * years;
            const total = amount + interest;

            document.getElementById('summaryDuration').textContent = months + ' months';
            document.getElementById('summaryInterest').textContent = formatCurrency(interest);
            document.getElementById('summaryTotal').textContent = formatCurrency(total);
        } else {
            document.getElementById('summaryDuration').textContent = '—';
            document.getElementById('summaryInterest').textContent = 'RWF 0.00';
            document.getElementById('summaryTotal').textContent = 'RWF 0.00';
        }
    }

    amountInput.addEventListener('input', calculateSavingsSummary);
    maturitySelect.addEventListener('change', calculateSavingsSummary);
    rateInput.addEventListener('input', calculateSavingsSummary);

    savingsForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat(amountInput.value);
        const months = parseInt(maturitySelect.value);
        const rate = parseFloat(rateInput.value);

        if (amount < 1000) {
            alert('Savings amount must be at least RWF 1,000');
            amountInput.focus();
            return;
        }

        if (!months) {
            alert('Please select a maturity period');
            maturitySelect.focus();
            return;
        }

        if (isNaN(rate) || rate < 0) {
            alert('Please enter a valid interest rate');
            rateInput.focus();
            return;
        }

        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';

        const url = savingsForm.action;
        const fd = new FormData(savingsForm);

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
                    }, 1500);
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
</body>
</html>