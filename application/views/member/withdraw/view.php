<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Details - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>

    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>

        <div class="dashboard-content">
            <h1>💸 Withdrawal Request Details</h1>

            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 900px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <?php if (empty($request)): ?>
                    <p>Withdrawal request not found.</p>
                <?php else: ?>
                    <div style="display:flex; justify-content:space-between; gap:20px; align-items:flex-start;">
                        <div style="flex:1; min-width:300px;">
                            <p><strong>Request ID:</strong> #<?php echo htmlspecialchars($request['id']); ?></p>
                            <div style="background: #f0f9ff; padding: 15px; border-radius: 6px; margin: 15px 0; border-left: 4px solid #0284c7;">
                                <p style="margin: 0 0 8px 0;"><strong>Amount:</strong> <span style="font-size: 1.1em; color: #0369a1;">RWF <?php echo number_format($request['amount'], 2); ?></span></p>
                                <p style="margin: 0 0 8px 0; color: #6b7280;"><strong>Fee:</strong> RWF <?php echo number_format($request['fee'], 2); ?></p>
                                <p style="margin: 0; padding-top: 8px; border-top: 1px solid #bae6fd;"><strong>Total:</strong> <span style="font-size: 1.15em; color: #0c4a6e; font-weight: 600;">RWF <?php echo number_format($request['total_amount'], 2); ?></span></p>
                            </div>
                            <p><strong>Method:</strong> <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($request['withdrawal_method']))); ?></p>
                            <p><strong>Status:</strong> <span class="status <?php echo strtolower($request['status']); ?>"><?php echo htmlspecialchars(ucfirst($request['status'])); ?></span></p>
                            <p><strong>Requested At:</strong> <?php echo htmlspecialchars($request['requested_at']); ?></p>
                            <?php if (!empty($request['reviewed_at'])): ?>
                                <p><strong>Reviewed At:</strong> <?php echo htmlspecialchars($request['reviewed_at']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($request['completed_at'])): ?>
                                <p><strong>Completed At:</strong> <?php echo htmlspecialchars($request['completed_at']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($request['rejection_reason'])): ?>
                                <div style="margin-top: 12px; padding: 12px; background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 4px;">
                                    <p style="margin: 0; font-weight: 600; color: #991b1b;">❌ Rejection Reason:</p>
                                    <p style="margin: 8px 0 0 0; color: #7f1d1d;"><?php echo htmlspecialchars($request['rejection_reason']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="width:320px;">
                            <h3 style="margin-top: 0; color: #1f2937; border-bottom: 2px solid #667eea; padding-bottom: 8px;">💳 Payment Details</h3>
                            <?php if ($request['withdrawal_method'] === 'bank_transfer'): ?>
                                <div style="background: #fefce8; padding: 12px; border-radius: 6px; border: 1px solid #fde047;">
                                    <p style="margin: 0; font-weight: 600; color: #854d0e;">🏦 Bank Transfer</p>
                                    <p style="margin: 8px 0 0 0; color: #713f12;"><strong>Account:</strong> <?php echo htmlspecialchars($request['bank_account'] ?? 'N/A'); ?></p>
                                </div>
                            <?php elseif ($request['withdrawal_method'] === 'momo'): ?>
                                <div style="background: #fef3c7; padding: 12px; border-radius: 6px; border: 1px solid #fbbf24;">
                                    <p style="margin: 0; font-weight: 600; color: #78350f;">📱 Mobile Money</p>
                                    <p style="margin: 8px 0 0 0; color: #78350f;"><strong>Number:</strong> <?php echo htmlspecialchars($request['momo_number'] ?? 'N/A'); ?></p>
                                </div>
                            <?php else: ?>
                                <div style="background: #f0fdf4; padding: 12px; border-radius: 6px; border: 1px solid #86efac;">
                                    <p style="margin: 0; font-weight: 600; color: #166534;">💵 Cash Pickup</p>
                                    <p style="margin: 8px 0 0 0; color: #15803d;">Visit any branch to collect your funds</p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($request['blockchain_hash'])): ?>
                                <div style="margin-top: 20px; padding: 12px; background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px;">
                                    <p style="margin: 0 0 8px 0; font-weight: 600; color: #374151;">Blockchain Hash:</p>
                                    <div style="position: relative;">
                                        <code id="blockchainHash" style="display: block; font-family: 'Courier New', monospace; font-size: 0.85em; word-break: break-all; color: #1f2937; background: white; padding: 10px; border-radius: 4px; border: 1px solid #d1d5db;"><?php echo htmlspecialchars($request['blockchain_hash']); ?></code>
                                        <button onclick="copyBlockchainHash()" style="position: absolute; top: 8px; right: 8px; padding: 4px 10px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8em; transition: background 0.2s;" onmouseover="this.style.background='#5568d3'" onmouseout="this.style.background='#667eea'">📋 Copy</button>
                                    </div>
                                    <p style="margin: 8px 0 0 0; font-size: 0.85em; color: #6b7280;">Click to copy transaction hash</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="margin-top:20px;">
                        <a href="<?php echo BASE_URL; ?>member/withdraw" class="btn btn-secondary">Back to withdrawals</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<script>
function copyBlockchainHash() {
    const hashElement = document.getElementById('blockchainHash');
    const hash = hashElement.textContent;
    
    // Copy to clipboard
    navigator.clipboard.writeText(hash).then(() => {
        // Visual feedback
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '✓ Copied!';
        btn.style.background = '#10b981';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '#667eea';
        }, 2000);
    }).catch(err => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = hash;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        
        try {
            document.execCommand('copy');
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '✓ Copied!';
            btn.style.background = '#10b981';
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '#667eea';
            }, 2000);
        } catch (err) {
            alert('Failed to copy hash');
        }
        
        document.body.removeChild(textArea);
    });
}
</script>
</body>
</html>
