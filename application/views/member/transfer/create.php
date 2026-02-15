<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Funds - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .transfer-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }
        .transfer-header h1 { margin: 0; }
        .transfer-subtitle { color: #6b7280; margin-top: 6px; }
        .transfer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .card {
            background: white;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .summary-card {
            display: grid;
            gap: 12px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #374151;
        }
        .summary-row strong { font-size: 16px; color: #111827; }
        .pill {
            display: inline-block;
            background: #eef2ff;
            color: #3730a3;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 8px 0;
        }
        @media (max-width: 900px) {
            .transfer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <div class="transfer-header">
                <div>
                    <h1>Transfer Funds</h1>
                    <div class="transfer-subtitle">Send money quickly and securely to another member.</div>
                </div>
                <span class="pill">Fee: 2.5%</span>
            </div>
            
            <div class="transfer-grid">
                <div class="card">
                    <form id="transferForm" method="POST" action="<?php echo BASE_URL; ?>member/transfer/create" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="receiver_id">Receiver MOMO Number:</label>
                        <input type="text" id="receiver_id" name="receiver_id" required placeholder="Enter receiver MOMO number">
                        <div class="hint">MOMO number must match an active member account (not your own). <a href="<?php echo BASE_URL; ?>member/transfer/find">Find receiver</a></div>
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount (RWF):</label>
                        <input type="number" id="amount" name="amount" required placeholder="0.00" step="0.01" min="1" data-type="amount">
                        <div class="hint">Minimum transfer: RWF 1.00</div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description (Optional):</label>
                        <textarea id="description" name="description" placeholder="Enter transfer description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="proof">Proof of Payment (Optional):</label>
                        <input type="file" id="proof" name="proof" accept=".pdf,.jpg,.jpeg,.png" />
                        <div class="hint">Upload a receipt or screenshot. Max 5MB. Allowed: PDF, JPG, PNG</div>
                    </div>

                    <div class="form-group">
                        <p><strong>Note:</strong> The transfer fee is calculated automatically and added to the total debit.</p>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Transfer</button>
                    <a href="<?php echo BASE_URL; ?>member/transfer" class="btn btn-secondary" style="margin-left: 10px;">Back</a>
                </form>
                </div>

                <div class="card summary-card">
                    <h3 style="margin:0;">Transfer Summary</h3>
                    <div class="summary-row">
                        <span>Receiver</span>
                        <strong id="summaryReceiver">Not found</strong>
                    </div>
                    <div class="summary-row">
                        <span>Amount</span>
                        <strong id="summaryAmount">RWF 0.00</strong>
                    </div>
                    <div class="summary-row">
                        <span>Fee (2.5%)</span>
                        <strong id="summaryFee">RWF 0.00</strong>
                    </div>
                    <div class="divider"></div>
                    <div class="summary-row">
                        <span>Total Debit</span>
                        <strong id="summaryTotal">RWF 0.00</strong>
                    </div>
                    <div class="hint">Total debit includes the transfer fee.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('transferForm');
    const receiverInput = document.getElementById('receiver_id');
    const amountInput = document.getElementById('amount');
    const summaryReceiver = document.getElementById('summaryReceiver');
    const summaryAmount = document.getElementById('summaryAmount');
    const summaryFee = document.getElementById('summaryFee');
    const summaryTotal = document.getElementById('summaryTotal');
    if (!form) return;

    const formatCurrency = (value) => {
        const num = isNaN(value) ? 0 : value;
        return 'RWF ' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const lookupReceiver = () => {
        const momoNumber = (receiverInput.value || '').trim();
        if (!momoNumber) {
            if (summaryReceiver) summaryReceiver.textContent = 'Not found';
            if (summaryReceiver) summaryReceiver.style.color = '#9ca3af';
            return;
        }

        fetch('<?php echo BASE_URL; ?>member/transfer/lookup', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phone_number: momoNumber }),
            credentials: 'same-origin'
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.user) {
                const displayName = data.user.username ? '@' + data.user.username : 'Found';
                if (summaryReceiver) {
                    summaryReceiver.textContent = displayName;
                    summaryReceiver.style.color = '#10b981';
                }
            } else {
                if (summaryReceiver) {
                    summaryReceiver.textContent = 'Not found';
                    summaryReceiver.style.color = '#ef4444';
                }
            }
        })
        .catch(err => {
            if (summaryReceiver) {
                summaryReceiver.textContent = 'Not found';
                summaryReceiver.style.color = '#ef4444';
            }
        });
    };

    const updateSummary = () => {
        const amount = parseFloat(amountInput.value || '0') || 0;
        const fee = amount * 0.025;
        const total = amount + fee;
        if (summaryAmount) summaryAmount.textContent = formatCurrency(amount);
        if (summaryFee) summaryFee.textContent = formatCurrency(fee);
        if (summaryTotal) summaryTotal.textContent = formatCurrency(total);
    };

    if (receiverInput) {
        receiverInput.addEventListener('input', lookupReceiver);
    }

    if (amountInput) {
        amountInput.addEventListener('input', updateSummary);
        updateSummary();
    }


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
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
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
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
                }
            } else {
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
<?php include VIEW_PATH . 'member/layouts/footer.php'; ?>
</body>
</html>
