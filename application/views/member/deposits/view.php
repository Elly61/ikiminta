<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Details - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>

        <div class="dashboard-content">
            <h1>Deposit Details</h1>

            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 900px;">
                <?php if (empty($deposit)): ?>
                    <p>Deposit not found.</p>
                <?php else: ?>
                    <div style="display:flex; justify-content:space-between; gap:20px; align-items:flex-start;">
                        <div style="flex:1; min-width:300px;">
                            <p><strong>Transaction #:</strong> <?php echo htmlspecialchars($deposit['transaction_reference']); ?></p>
                            <p><strong>Amount:</strong> FRW <?php echo number_format($deposit['amount'], 2); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($deposit['payment_method']))); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($deposit['status'])); ?></p>
                            <p><strong>Created At:</strong> <?php echo htmlspecialchars($deposit['created_at']); ?></p>
                            <?php if (!empty($deposit['completed_at'])): ?>
                                <p><strong>Completed At:</strong> <?php echo htmlspecialchars($deposit['completed_at']); ?></p>
                            <?php endif; ?>
                            <p><strong>Description:</strong></p>
                            <p style="white-space:pre-wrap; background:#fafafa; padding:12px; border-radius:6px;"><?php echo nl2br(htmlspecialchars($deposit['description'] ?? '')); ?></p>
                        </div>

                        <div style="width:320px;">
                            <h3>Proof of Payment</h3>
                            <?php
                                $proofPath = $deposit['proof']
                                    ?? ($deposit['proof_payment']
                                    ?? ($deposit['proof_path']
                                    ?? ($deposit['proof_of_payment'] ?? null)));
                            ?>
                            <?php if (!empty($proofPath)): ?>
                                <?php
                                      $ext = strtolower(pathinfo($proofPath, PATHINFO_EXTENSION)); ?>

                                <?php if (in_array($ext, ['png','jpg','jpeg'])): ?>
                                    <div style="border:1px solid #e5e7eb; padding:8px; border-radius:6px; text-align:center;">
                                        <img id="proofPreview" src="<?php echo BASE_URL . $proofPath; ?>" alt="Proof" style="max-width:100%; height:auto; border-radius:4px; cursor:pointer;">
                                        <p style="margin-top:8px;">
                                            <a href="<?php echo BASE_URL . $proofPath; ?>" target="_blank">Open full image</a>
                                            &nbsp;|&nbsp; <a href="<?php echo BASE_URL; ?>member/deposits/downloadProof/<?php echo $deposit['id']; ?>">Download</a>
                                        </p>
                                    </div>
                                <?php elseif ($ext === 'pdf'): ?>
                                    <div style="border:1px solid #e5e7eb; padding:16px; border-radius:6px;">
                                        <p>PDF proof available.</p>
                                        <p>
                                            <a href="<?php echo BASE_URL . $proofPath; ?>" target="_blank">Open PDF</a>
                                            &nbsp;|&nbsp; <a href="<?php echo BASE_URL; ?>member/deposits/downloadProof/<?php echo $deposit['id']; ?>">Download</a>
                                        </p>
                                    </div>
                                <?php else: ?>
                                    <p>Proof file available: <a href="<?php echo BASE_URL; ?>member/deposits/downloadProof/<?php echo $deposit['id']; ?>" target="_blank">Download</a></p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="muted">No proof uploaded.</p>
                                <form id="uploadProofForm" style="margin-top:12px;">
                                    <input type="file" id="proofFile" name="proof" accept="image/png,image/jpeg,application/pdf" required style="display:none;">
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('proofFile').click();" style="width:100%; padding:8px; cursor:pointer; margin-bottom:8px;">📤 Upload Proof</button>
                                    <a href="<?php echo BASE_URL; ?>member/deposits/uploadProof/<?php echo htmlspecialchars($deposit['id'] ?? ''); ?>" style="display:block; text-align:center; padding:8px; background:#10b981; color:white; border-radius:4px; text-decoration:none; font-size:0.9em;">Or use dedicated form</a>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="margin-top:20px; display:flex; gap:12px; align-items:center;">
                        <a href="<?php echo BASE_URL; ?>member/deposits" class="btn btn-secondary">Back to deposits</a>

                        <?php if (!empty($audit_logs)): ?>
                            <div style="margin-left:auto;">
                                <button class="btn btn-info" onclick="document.getElementById('historyPanel').scrollIntoView({behavior:'smooth'});">View Approval History</button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($audit_logs)): ?>
                <div id="historyPanel" style="background:white; padding:20px; border-radius:8px; margin-top:18px; max-width:900px;">
                    <h2>Approval / Audit History</h2>
                    <ul style="list-style:none; padding:0;">
                        <?php foreach ($audit_logs as $log): ?>
                            <li style="padding:10px 0; border-bottom:1px solid #eef2f7;">
                                <div style="display:flex; justify-content:space-between;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($log['action']); ?></strong>
                                        <div style="color:#6b7280; font-size:0.9em;">By: <?php echo htmlspecialchars($log['username'] ?? 'System'); ?></div>
                                    </div>
                                    <div style="color:#6b7280; font-size:0.9em;"><?php echo htmlspecialchars($log['created_at']); ?></div>
                                </div>
                                <?php if (!empty($log['new_values'])): ?>
                                    <?php $newValues = json_decode($log['new_values'], true); ?>
                                    <?php if (json_last_error() === JSON_ERROR_NONE && is_array($newValues)): ?>
                                        <div style="background:#f8fafc; padding:8px; border-radius:6px; margin-top:8px;">
                                            <?php if (!empty($newValues['status'])): ?>
                                                <div><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($newValues['status'])); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($newValues['completed_at'])): ?>
                                                <div><strong>Completed At:</strong> <?php echo htmlspecialchars($newValues['completed_at']); ?></div>
                                            <?php endif; ?>
                                            <?php if (array_key_exists('reason', $newValues) && $newValues['reason'] !== ''): ?>
                                                <div><strong>Reason:</strong> <?php echo htmlspecialchars($newValues['reason']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <pre style="background:#f8fafc; padding:8px; border-radius:6px; margin-top:8px; overflow:auto;"><?php echo htmlspecialchars($log['new_values']); ?></pre>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image modal -->
<div id="imageModal" style="display:none; position:fixed; z-index:10000; inset:0; background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
    <div style="max-width:90%; max-height:90%;">
        <img id="modalImg" src="" style="width:auto; max-width:100%; max-height:90vh; border-radius:6px; display:block; margin:0 auto;">
        <div style="text-align:center; margin-top:8px;"><button class="btn btn-secondary" id="closeModal">Close</button></div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>public/js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const preview = document.getElementById('proofPreview');
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImg');
    const closeBtn = document.getElementById('closeModal');
    const proofFile = document.getElementById('proofFile');

    if (preview) {
        preview.addEventListener('click', function() {
            modalImg.src = this.src;
            modal.style.display = 'flex';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
            modalImg.src = '';
        });
    }

    // Close modal on overlay click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            modalImg.src = '';
        }
    });

    // Handle proof file upload
    if (proofFile) {
        proofFile.addEventListener('change', function() {
            if (this.files.length > 0) {
                uploadProof(this.files[0]);
            }
        });
    }
});

function uploadProof(file) {
    const depositId = '<?php echo htmlspecialchars($deposit['id'] ?? ''); ?>';
    if (!depositId) return;

    const formData = new FormData();
    formData.append('proof', file);

    const btn = document.querySelector('button[onclick*="proofFile"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '⏳ Uploading...';

    fetch('<?php echo BASE_URL; ?>member/deposits/uploadProof/' + depositId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;

        if (data.redirect) {
            window.location.href = data.redirect;
            return;
        }

        if (data.success) {
            alert('✓ Proof uploaded successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to upload proof'));
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        alert('Upload error: ' + error.message);
    });
}
</script>
</body>
</html>
