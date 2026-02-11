<?php
/**
 * Member Savings Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/SavingsModel.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class MemberSavingsController extends BaseController {
    private $savingsModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->savingsModel = $this->loadModel('SavingsModel');
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index() {
        $user = $this->getCurrentUser();
        $savings = $this->savingsModel->getUserSavings($user['id']);
        
        $this->loadView('member/savings/index', [
            'savings' => $savings,
            'user' => $user
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get default interest rate from settings
            $settings = $this->userModel->getSettings();
            $defaultInterestRate = $settings['savings_interest_rate'] ?? 1.5;
            
            $this->loadView('member/savings/create', [
                'default_interest_rate' => $defaultInterestRate
            ]);
        } else {
            return $this->handleCreate();
        }
    }

    private function handleCreate() {
        $user = $this->getCurrentUser();
        $amount = floatval($_POST['amount'] ?? 0);
        $months = intval($_POST['maturity_months'] ?? 0);
        $interestRate = floatval($_POST['interest_rate'] ?? 0);

        if ($amount <= 0 || $months <= 0) {
            return $this->response('error', 'Invalid amount or duration');
        }

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

            $allowedMimes = [
                'image/png', 'image/jpeg', 'application/pdf'
            ];
            if (!in_array($mime, $allowedMimes)) {
                return $this->response('error', 'Invalid proof file MIME type');
            }

            $uploadDir = __DIR__ . '/../../public/uploads/savings/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeName = 'proof_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $destination = $uploadDir . $safeName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                return $this->response('error', 'Failed to save proof file');
            }

            $proofPath = 'public/uploads/savings/' . $safeName;
        }

        // For savings, we don't check balance as it's a commitment to save
        // The balance will be deducted when the savings matures or as per business logic
        $savingsId = $this->savingsModel->createSavingsWithoutBalanceCheck($user['id'], $amount, $months, $interestRate, $proofPath);

        if ($savingsId) {
            return $this->response('success', 'Savings account created successfully', [
                'savings_id' => $savingsId,
                'redirect' => BASE_URL . 'member/savings'
            ]);
        } else {
            return $this->response('error', 'Failed to create savings due to a system error. Please try again later.');
        }
    }
}
