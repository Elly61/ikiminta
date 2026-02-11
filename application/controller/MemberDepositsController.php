<?php
/**
 * Member Deposits Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/DepositModel.php';

class MemberDepositsController extends BaseController {
    private $depositModel;

    public function __construct() {
        parent::__construct();
        $this->depositModel = $this->loadModel('DepositModel');
    }

    public function index() {
        $user = $this->getCurrentUser();
        $deposits = $this->depositModel->getUserDeposits($user['id']);
        
        $this->loadView('member/deposits/index', [
            'deposits' => $deposits,
            'user' => $user
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->loadView('member/deposits/create');
        } else {
            return $this->handleCreate();
        }
    }

    private function handleCreate() {
        $user = $this->getCurrentUser();
        $amount = floatval($_POST['amount'] ?? 0);
        $paymentMethod = htmlspecialchars($_POST['payment_method'] ?? '');
        $description = htmlspecialchars($_POST['description'] ?? '');

        // Handle uploaded proof file (optional)
        $proofPath = null;
        if (!empty($_FILES['proof']) && $_FILES['proof']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['proof'];
            $allowedExts = ['png', 'jpg', 'jpeg', 'pdf'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if ($file['error'] !== UPLOAD_ERR_OK) {
                return $this->response('error', 'Error uploading proof file');
            }

            if ($file['size'] > $maxSize) {
                return $this->response('error', 'Proof file exceeds maximum size of 5MB');
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts)) {
                return $this->response('error', 'Invalid proof file type');
            }

            // Basic mime/type check
            $allowedMimes = [
                'image/png', 'image/jpeg', 'application/pdf'
            ];
            if (!in_array($mime, $allowedMimes)) {
                return $this->response('error', 'Invalid proof file MIME type');
            }

            $uploadDir = __DIR__ . '/../../public/uploads/deposits/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeName = 'proof_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $destination = $uploadDir . $safeName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                return $this->response('error', 'Failed to save proof file');
            }

            // Store relative path to public folder
            $proofPath = 'public/uploads/deposits/' . $safeName;
        }

        if ($amount <= 0) {
            return $this->response('error', 'Invalid amount');
        }

        if (!in_array($paymentMethod, ['credit_card', 'momo'])) {
            return $this->response('error', 'Invalid payment method');
        }

        $depositId = $this->depositModel->createDeposit($user['id'], $amount, $paymentMethod, $description, $proofPath);

        if ($depositId) {
            return $this->response('success', 'Deposit initiated. Awaiting approval.', [
                'deposit_id' => $depositId,
                'redirect' => BASE_URL . 'member/deposits'
            ]);
        } else {
            return $this->response('error', 'Failed to create deposit');
        }
    }

    public function view($depositId) {
        $user = $this->getCurrentUser();
        $deposit = $this->depositModel->getDepositById($depositId);

        if (!$deposit || $deposit['user_id'] != $user['id']) {
            $this->redirect(BASE_URL . 'member/deposits');
        }

        $auditLogs = $this->depositModel->getAuditLogsForDeposit($depositId);
        $this->loadView('member/deposits/view', ['deposit' => $deposit, 'audit_logs' => $auditLogs]);
    }

    public function perform() {
        $depositId = $_GET['id'] ?? 0;
        $deposit = $this->depositModel->getDepositById($depositId);

        if (!$deposit) {
            $this->redirect(BASE_URL . 'member/deposits');
        }

        $this->loadView('member/deposits/perform', ['deposit' => $deposit]);
    }

    public function uploadProof($depositId = 0) {
        $user = $this->getCurrentUser();

        // Validate deposit ID
        if (empty($depositId)) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $this->redirect(BASE_URL . 'member/dashboard');
                return;
            }

            return $this->response('error', 'Deposit ID not provided', [
                'redirect' => BASE_URL . 'member/dashboard'
            ]);
        }
        
        $deposit = $this->depositModel->getDepositById($depositId);

        // Verify ownership
        if (!$deposit || $deposit['user_id'] != $user['id']) {
            return $this->response('error', 'Unauthorized access');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->loadView('member/deposits/upload_proof', ['deposit' => $deposit]);
            return;
        }

        // Handle file upload
        if (empty($_FILES['proof']) || $_FILES['proof']['error'] === UPLOAD_ERR_NO_FILE) {
            return $this->response('error', 'Please select a file to upload');
        }

        $file = $_FILES['proof'];
        $allowedExts = ['png', 'jpg', 'jpeg', 'pdf'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->response('error', 'Error uploading proof file');
        }

        if ($file['size'] > $maxSize) {
            return $this->response('error', 'Proof file exceeds maximum size of 5MB');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            return $this->response('error', 'Invalid proof file type. Allowed: PNG, JPG, JPEG, PDF');
        }

        $allowedMimes = ['image/png', 'image/jpeg', 'application/pdf'];
        if (!in_array($mime, $allowedMimes)) {
            return $this->response('error', 'Invalid proof file MIME type');
        }

        $uploadDir = __DIR__ . '/../../public/uploads/deposits/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $safeName = 'proof_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $destination = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return $this->response('error', 'Failed to save proof file');
        }

        // Store relative path to public folder
        $proofPath = 'public/uploads/deposits/' . $safeName;

        // Update deposit with proof
        if ($this->depositModel->updateDepositProof($depositId, $proofPath)) {
            return $this->response('success', 'Proof of payment uploaded successfully!', [
                'redirect' => BASE_URL . 'member/deposits/view/' . $depositId
            ]);
        } else {
            return $this->response('error', 'Failed to save proof file to database');
        }
    }

    public function downloadProof($depositId = 0) {
        $user = $this->getCurrentUser();

        // Validate deposit ID
        if (empty($depositId)) {
            $this->redirect(BASE_URL . 'member/dashboard');
            return;
        }
        
        $deposit = $this->depositModel->getDepositById($depositId);

        // Verify ownership
        if (!$deposit || $deposit['user_id'] != $user['id']) {
            header('HTTP/1.0 403 Forbidden');
            exit('Access denied');
        }

        $proofPath = $deposit['proof'] ?? $deposit['proof_payment'] ?? null;
        
        if (empty($proofPath)) {
            header('HTTP/1.0 404 Not Found');
            exit('Proof file not found');
        }

        $filePath = BASE_PATH . $proofPath;
        
        if (!file_exists($filePath)) {
            header('HTTP/1.0 404 Not Found');
            exit('File not found on server');
        }

        $fileName = basename($filePath);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($filePath);
        exit;
    }
}
