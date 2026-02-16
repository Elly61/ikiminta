<?php
/**
 * Member Loans Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/LoanModel.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class MemberLoansController extends BaseController {
    private $loanModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->loanModel = $this->loadModel('LoanModel');
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index() {
        $user = $this->getCurrentUser();
        $requests = $this->loanModel->getUserLoanRequests($user['id']);
        $loans = $this->loanModel->getUserLoans($user['id']);
        
        $this->loadView('member/loans/index', [
            'requests' => $requests,
            'loans' => $loans,
            'user' => $user
        ]);
    }

    public function request() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get default loan interest rate from settings
            $settings = $this->userModel->getSettings();
            $defaultInterestRate = $settings['loan_interest_rate'] ?? 1.5;
            
            $this->loadView('member/loans/request', [
                'default_interest_rate' => $defaultInterestRate
            ]);
        } else {
            return $this->handleRequest();
        }
    }

    private function handleRequest() {
        $this->requireAjaxAuth();
        $user = $this->getCurrentUser();
        $amount = floatval($_POST['amount'] ?? 0);
        $duration = intval($_POST['duration_months'] ?? 0);
        $purpose = htmlspecialchars($_POST['purpose'] ?? '');

        if ($amount <= 0 || $duration <= 0) {
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

            $uploadDir = __DIR__ . '/../../public/uploads/loans/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeName = 'proof_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $destination = $uploadDir . $safeName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                return $this->response('error', 'Failed to save proof file');
            }

            $proofPath = 'public/uploads/loans/' . $safeName;
        }

        $requestId = $this->loanModel->requestLoan($user['id'], $amount, $duration, $purpose, $proofPath);

        if ($requestId) {
            return $this->response('success', 'Loan request submitted. Awaiting approval.', [
                'request_id' => $requestId,
                'redirect' => BASE_URL . 'member/loans'
            ]);
        } else {
            return $this->response('error', 'Failed to submit loan request');
        }
    }

    public function viewLoan($loanId) {
        $user = $this->getCurrentUser();
        $loan = $this->loanModel->getLoanById($loanId);

        if (!$loan || $loan['user_id'] != $user['id']) {
            $this->redirect(BASE_URL . 'member/loans');
        }

        $payments = $this->loanModel->getLoanPayments($loanId);
        $totalOwed = $loan['monthly_payment'] * $loan['duration_months'];
        $remaining = $totalOwed - $loan['total_paid'];

        $this->loadView('member/loans/view', [
            'loan' => $loan,
            'payments' => $payments,
            'user' => $user,
            'total_owed' => $totalOwed,
            'remaining' => $remaining
        ]);
    }

    public function pay($loanId) {
        $this->requireAjaxAuth();
        $user = $this->getCurrentUser();

        $loan = $this->loanModel->getLoanById($loanId);
        if (!$loan || $loan['user_id'] != $user['id'] || $loan['status'] !== 'active') {
            return $this->response('error', 'Invalid loan or loan is not active');
        }

        $amount = floatval($_POST['amount'] ?? 0);
        if ($amount <= 0) {
            return $this->response('error', 'Invalid payment amount');
        }

        // Check balance
        if ($user['balance'] < $amount) {
            return $this->response('error', 'Insufficient balance. Your balance is RWF ' . number_format($user['balance'], 2));
        }

        // Don't allow overpayment
        $totalOwed = $loan['monthly_payment'] * $loan['duration_months'];
        $remaining = $totalOwed - $loan['total_paid'];
        if ($amount > $remaining) {
            return $this->response('error', 'Payment exceeds remaining balance. You only owe RWF ' . number_format($remaining, 2));
        }

        $result = $this->loanModel->makeLoanPayment($loanId, $amount, $user['id']);

        if ($result === true) {
            return $this->response('success', 'Payment of RWF ' . number_format($amount, 2) . ' recorded successfully', [
                'redirect' => BASE_URL . 'member/loans/viewLoan/' . $loanId
            ]);
        } else {
            return $this->response('error', is_string($result) ? $result : 'Payment failed');
        }
    }
}
