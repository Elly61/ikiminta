<?php
/**
 * Member Dashboard Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';
require_once dirname(__FILE__) . '/../model/TransactionModel.php';
require_once dirname(__FILE__) . '/../model/DepositModel.php';
require_once dirname(__FILE__) . '/../model/TransferModel.php';
require_once dirname(__FILE__) . '/../model/LoanModel.php';
require_once dirname(__FILE__) . '/../model/WithdrawalModel.php';

class MemberDashboardController extends BaseController {
    private $userModel;
    private $transactionModel;
    private $depositModel;
    private $transferModel;
    private $loanModel;
    private $withdrawalModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = $this->loadModel('UserModel');
        $this->transactionModel = $this->loadModel('TransactionModel');
        $this->depositModel = $this->loadModel('DepositModel');
        $this->transferModel = $this->loadModel('TransferModel');
        $this->loanModel = $this->loadModel('LoanModel');
        $this->withdrawalModel = $this->loadModel('WithdrawalModel');
    }

    public function index() {
        $user = $this->getCurrentUser();
        
        $dashboardData = [
            'user' => $user,
            'total_balance' => $user['balance'],
            'total_credited' => $this->transactionModel->getTotalCredit($user['id']),
            'total_debited' => $this->transactionModel->getTotalDebit($user['id']),
            'total_deposited' => $this->depositModel->getTotalDeposited($user['id']),
            'total_transferred' => $this->transferModel->getTotalTransferred($user['id']),
            'total_withdrawn' => $this->withdrawalModel->getTotalWithdrawn($user['id']),
            'total_loans' => $this->loanModel->getTotalLoanAmount($user['id']),
            'total_loan_paid' => $this->loanModel->getTotalLoanPaid($user['id']),
            'recent_transactions' => $this->transactionModel->getUserTransactions($user['id'], 10)
        ];

        $this->loadView('member/dashboard/index', $dashboardData);
    }

    public function profile() {
        $user = $this->getCurrentUser();
        $this->loadView('member/profile', ['user' => $user]);
    }

    public function updateProfile() {
        $this->requireAjaxAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response('error', 'Invalid request method');
        }

        $user = $this->getCurrentUser();
        $firstName = htmlspecialchars($_POST['first_name'] ?? '');
        $lastName = htmlspecialchars($_POST['last_name'] ?? '');
        $phoneNumber = htmlspecialchars($_POST['phone_number'] ?? '');

        $this->userModel->updateProfile($user['id'], [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $phoneNumber
        ]);

        return $this->response('success', 'Profile updated successfully');
    }
}
