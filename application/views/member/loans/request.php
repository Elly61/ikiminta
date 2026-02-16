<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Loan - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .loan-request-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            max-width: 900px;
            margin: 0 auto;
        }

        .loan-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .loan-header h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 600;
        }

        .loan-header p {
            margin: 0;
            opacity: 0.95;
            font-size: 15px;
        }

        .loan-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 40px;
        }

        .loan-form {
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
        .form-section select,
        .form-section textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-section input:focus,
        .form-section select:focus,
        .form-section textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background-color: #f8f9ff;
        }

        .form-section textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-hint {
            font-size: 12px;
            color: #888;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .loan-summary {
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
            .loan-content {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 25px;
            }

            .loan-header {
                padding: 30px 20px;
            }

            .loan-header h2 {
                font-size: 22px;
            }

            .loan-summary {
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
            <div class="loan-request-container">
                <div class="loan-header">
                    <h2>Request a Loan</h2>
                    <p>Fill in the details below to submit your loan application</p>
                </div>

                <div class="loan-content">
                    <div class="loan-form">
                        <form id="loanForm" method="POST" action="<?php echo BASE_URL; ?>member/loans/request" enctype="multipart/form-data">
                            <div class="form-section">
                                <label for="amount">
                                    💰 Loan Amount
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
                                >
                                <div class="form-hint">📌 Minimum: RWF 1,000</div>
                            </div>

                            <div class="form-section">
                                <label for="duration_months">
                                    ⏱️ Repayment Period
                                </label>
                                <select id="duration_months" name="duration_months" required>
                                    <option value="">Select duration</option>
                                    <option value="3">3 months</option>
                                    <option value="6">6 months</option>
                                    <option value="12">12 months</option>
                                    <option value="24">24 months</option>
                                    <option value="36">36 months</option>
                                </select>
                                <div class="form-hint">📌 Choose your preferred repayment period</div>
                            </div>

                            <div class="form-section">
                                <label for="purpose">
                                    📝 Purpose of Loan
                                </label>
                                <textarea 
                                    id="purpose" 
                                    name="purpose" 
                                    required 
                                    placeholder="Describe what you'll use this loan for (e.g., business expansion, education, emergency, etc.)"
                                ></textarea>
                                <div class="form-hint">📌 Help us understand your loan purpose</div>
                            </div>

                            <div class="form-section">
                                <label for="proof">
                                    📄 Proof of Payment/Document (Optional)
                                </label>
                                <input type="file" id="proof" name="proof" accept=".pdf,.jpg,.jpeg,.png" />
                                <div class="form-hint">📌 Upload supporting documents. Max 5MB. Allowed: PDF, JPG, PNG</div>
                            </div>

                            <div class="info-box">
                                <strong>ⓘ How It Works:</strong><br>
                                Your loan request will be reviewed by our team within 1-2 business days. 
                                We'll notify you of the decision via email and SMS. If approved, funds will be 
                                deposited directly to your account.
                            </div>

                            <div class="button-group">
                                <button type="submit" class="btn-submit" id="submitBtn">
                                    <span class="loading-spinner"></span>
                                    Submit Request
                                </button>
                                <a href="<?php echo BASE_URL; ?>member/loans" class="btn-cancel">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <div class="loan-summary">
                        <div class="summary-title">
                            📊 Loan Summary
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Loan Amount</span>
                            <span class="summary-value" id="summaryAmount">RWF 0.00</span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Duration</span>
                            <span class="summary-value" id="summaryDuration">—</span>
                        </div>

                        <div class="summary-item">
                            <span class="summary-label">Estimated Monthly Payment</span>
                            <span class="summary-value" id="summaryMonthly">RWF 0.00</span>
                        </div>

                        <div class="summary-total">
                            <div class="summary-total-label">Total Repayment (Estimated)</div>
                            <div class="summary-total-value" id="summaryTotal">RWF 0.00</div>
                        </div>

                        <div class="info-box" style="margin-top: 20px; border-left-color: #667eea;">
                            <strong>💡 Note:</strong> Estimates are based on <?php echo htmlspecialchars($default_interest_rate ?? '1.5'); ?>% interest rate on the principal. 
                            Final terms will be confirmed upon approval.
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
    const durationSelect = document.getElementById('duration_months');
    const loanForm = document.getElementById('loanForm');
    const submitBtn = document.getElementById('submitBtn');

    // Loan interest rate from admin settings (flat rate on principal)
    const defaultInterestRate = <?php echo json_encode(floatval($default_interest_rate ?? 1.5)); ?>;
    const INTEREST_RATE = (defaultInterestRate / 100); // Convert percentage to decimal

    function calculateLoanSummary() {
        const amount = parseFloat(amountInput.value) || 0;
        const months = parseInt(durationSelect.value) || 0;

        // Update summary amount
        document.getElementById('summaryAmount').textContent = formatCurrency(amount);

        if (months > 0 && amount > 0) {
            // Simple/flat interest: Total = Principal + (Principal × Rate%)
            const interest = amount * INTEREST_RATE;
            const totalRepayment = amount + interest;
            const monthlyPayment = totalRepayment / months;

            document.getElementById('summaryDuration').textContent = months + ' months';
            document.getElementById('summaryMonthly').textContent = formatCurrency(monthlyPayment);
            document.getElementById('summaryTotal').textContent = formatCurrency(totalRepayment);
        } else {
            document.getElementById('summaryDuration').textContent = '—';
            document.getElementById('summaryMonthly').textContent = 'RWF 0.00';
            document.getElementById('summaryTotal').textContent = 'RWF 0.00';
        }
    }

    function formatCurrency(value) {
        return 'RWF ' + Math.round(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Event listeners for real-time calculation
    amountInput.addEventListener('input', calculateLoanSummary);
    durationSelect.addEventListener('change', calculateLoanSummary);

    // Form submission
    loanForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat(amountInput.value);
        const months = parseInt(durationSelect.value);
        const purpose = document.getElementById('purpose').value.trim();

        // Validation
        if (amount < 1000) {
            alert('Loan amount must be at least RWF 1,000');
            amountInput.focus();
            return;
        }

        if (!months) {
            alert('Please select a duration');
            durationSelect.focus();
            return;
        }

        if (!purpose) {
            alert('Please describe the purpose of the loan');
            document.getElementById('purpose').focus();
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';

        // Submit the form
        this.submit();
    });
</script>
</body>
</html>
