<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Proof of Payment - IKIMINA</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/dashboard.css">
    <style>
        .upload-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .upload-area {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #5568d3;
            background: #f0f2ff;
        }
        .upload-area.dragover {
            border-color: #10b981;
            background: #f0fdf4;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1f2937;
        }
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #0284c7;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.95em;
            color: #0c4a6e;
        }
        .file-preview {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            display: none;
        }
        .file-preview.show {
            display: block;
        }
        .preview-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover:not(:disabled) {
            background: #5568d3;
        }
        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background: #d1d5db;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error-message {
            padding: 12px;
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 6px;
            margin-top: 16px;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .success-message {
            padding: 12px;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 6px;
            margin-top: 16px;
            display: none;
        }
        .success-message.show {
            display: block;
        }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <?php include VIEW_PATH . 'member/layouts/sidebar.php'; ?>
    
    <div class="main-content">
        <?php include VIEW_PATH . 'member/layouts/header.php'; ?>
        
        <div class="dashboard-content">
            <div class="upload-container">
                <h1>📤 Upload Proof of Payment</h1>
                <p style="color: #6b7280; margin-bottom: 20px;">Deposit: <strong><?php echo htmlspecialchars($deposit['transaction_reference']); ?></strong></p>

                <div class="info-box">
                    <strong>ℹ️ Accepted Formats:</strong> PNG, JPG, JPEG, PDF (Max 5MB)<br>
                    <strong>Tip:</strong> Upload a clear image of your payment receipt or proof document.
                </div>

                <form id="proofUploadForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="proofInput">Select Proof File:</label>
                        <div class="upload-area" id="uploadArea">
                            <div style="font-size: 2em; margin-bottom: 10px;">📎</div>
                            <p style="margin: 10px 0;"><strong>Click to browse</strong> or <strong>drag and drop</strong> your proof file</p>
                            <p style="color: #9ca3af; font-size: 0.9em;">PNG, JPG, JPEG or PDF • Up to 5MB</p>
                            <input type="file" id="proofInput" name="proof" accept="image/png,image/jpeg,application/pdf" style="display: none;">
                        </div>
                    </div>

                    <div class="file-preview" id="filePreview">
                        <h3 style="margin-top: 0;">Preview:</h3>
                        <div id="previewContent"></div>
                        <p style="color: #6b7280; font-size: 0.9em; margin-bottom: 0;">File: <span id="fileName"></span></p>
                    </div>

                    <div class="error-message" id="errorMessage"></div>
                    <div class="success-message" id="successMessage"></div>

                    <div class="btn-group">
                        <a href="<?php echo BASE_URL; ?>member/deposits/view/<?php echo htmlspecialchars($deposit['id'] ?? ''); ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Upload Proof</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const uploadArea = document.getElementById('uploadArea');
const proofInput = document.getElementById('proofInput');
const filePreview = document.getElementById('filePreview');
const previewContent = document.getElementById('previewContent');
const fileName = document.getElementById('fileName');
const form = document.getElementById('proofUploadForm');
const submitBtn = document.getElementById('submitBtn');
const errorMessage = document.getElementById('errorMessage');
const successMessage = document.getElementById('successMessage');

// Click to select file
uploadArea.addEventListener('click', () => proofInput.click());

// Drag and drop
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    if (e.dataTransfer.files.length > 0) {
        proofInput.files = e.dataTransfer.files;
        handleFileSelect(e.dataTransfer.files[0]);
    }
});

// File input change
proofInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

function handleFileSelect(file) {
    errorMessage.classList.remove('show');
    
    // Validate file size
    if (file.size > 5 * 1024 * 1024) {
        showError('File size exceeds 5MB limit');
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/png', 'image/jpeg', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        showError('Invalid file type. Please upload PNG, JPG, JPEG, or PDF');
        return;
    }
    
    // Show preview
    fileName.textContent = file.name;
    previewContent.innerHTML = '';
    
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewContent.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
            filePreview.classList.add('show');
        };
        reader.readAsDataURL(file);
    } else {
        previewContent.innerHTML = '<p>📄 PDF file selected</p>';
        filePreview.classList.add('show');
    }
}

function showError(message) {
    errorMessage.textContent = message;
    errorMessage.classList.add('show');
}

function showSuccess(message) {
    successMessage.textContent = message;
    successMessage.classList.add('show');
}

// Form submission
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if (!proofInput.files.length) {
        showError('Please select a file');
        return;
    }
    
    const formData = new FormData();
    formData.append('proof', proofInput.files[0]);
    
    submitBtn.disabled = true;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading-spinner"></span>Uploading...';
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>member/deposits/uploadProof/<?php echo htmlspecialchars($deposit['id'] ?? ''); ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.redirect && !data.success) {
            window.location.href = data.redirect;
            return;
        }

        if (data.success) {
            showSuccess('✓ Proof uploaded successfully! Redirecting...');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            showError(data.message || 'Failed to upload proof');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        showError('Upload error: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>
<?php include VIEW_PATH . 'member/layouts/footer.php'; ?>
</body>
</html>
