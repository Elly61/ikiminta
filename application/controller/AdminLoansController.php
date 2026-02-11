<?php
/**
 * Admin Loans Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/LoanModel.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class AdminLoansController extends BaseController {
    private $loanModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        if (!$this->isAdmin()) {
            $this->redirect(BASE_URL . 'admin/auth/login');
        }
        $this->loanModel = $this->loadModel('LoanModel');
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index() {
        $requests = $this->loanModel->getPendingLoanRequests();
        $settings = $this->userModel->getSettings();
        $this->loadView('admin/requests/loans', [
            'requests' => $requests,
            'default_interest_rate' => $settings['loan_interest_rate'] ?? 1.50
        ]);
    }

    public function approve($requestId) {
        $this->requireAjaxAuth();
        $admin = $this->getCurrentUser();

        // Support JSON body (fetch with application/json) or regular form POST
        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);
        $interestRate = null;
        if (isset($_POST['interest_rate'])) {
            $interestRate = floatval($_POST['interest_rate']);
        } elseif (is_array($input) && isset($input['interest_rate'])) {
            $interestRate = floatval($input['interest_rate']);
        } else {
            $interestRate = 0;
        }

        $loanId = $this->loanModel->approveLoanRequest($requestId, $admin['id'], $interestRate);
        if ($loanId) {
            return $this->response('success', 'Loan request approved', ['loan_id' => $loanId]);
        }
        return $this->response('error', 'Failed to approve loan request');
    }

    public function reject($requestId) {
        $this->requireAjaxAuth();
        $admin = $this->getCurrentUser();

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);
        if (isset($_POST['reason'])) {
            $reason = htmlspecialchars($_POST['reason']);
        } elseif (is_array($input) && isset($input['reason'])) {
            $reason = htmlspecialchars($input['reason']);
        } else {
            $reason = '';
        }

        if ($this->loanModel->rejectLoanRequest($requestId, $reason, $admin['id'])) {
            return $this->response('success', 'Loan request rejected');
        }
        return $this->response('error', 'Failed to reject loan request');
    }
}
