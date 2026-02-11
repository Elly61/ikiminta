<?php
/**
 * Admin Users Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class AdminUsersController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        if (!$this->isAdmin()) {
            $this->redirect(BASE_URL . 'admin/auth/login');
        }
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index() {
        $page = intval($_GET['page'] ?? 1);
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        
        $users = $this->userModel->getAllUsers(ITEMS_PER_PAGE, $offset);
        
        $this->loadView('admin/users/index', [
            'users' => $users,
            'page' => $page
        ]);
    }

    public function view($userId) {
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            $this->redirect(BASE_URL . 'admin/users');
        }

        // Get user transactions
        $transactionModel = $this->loadModel('TransactionModel');
        $transactions = $transactionModel ? $transactionModel->getUserTransactions($userId, 10, 0) : [];

        // Get user deposits
        $depositModel = $this->loadModel('DepositModel');
        $deposits = $depositModel ? $depositModel->getUserDeposits($userId) : [];

        // Get user withdrawals
        $withdrawalModel = $this->loadModel('WithdrawalModel');
        $withdrawals = $withdrawalModel ? $withdrawalModel->getUserWithdrawals($userId) : [];

        // Get user loans
        $loanModel = $this->loadModel('LoanModel');
        $loans = $loanModel ? $loanModel->getUserLoans($userId) : [];

        $this->loadView('admin/users/view', [
            'user' => $user,
            'transactions' => $transactions,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
            'loans' => $loans
        ]);
    }

    public function suspend($userId) {
        $this->requireAjaxAuth();
        if ($this->userModel->suspendUser($userId)) {
            return $this->response('success', 'User suspended successfully');
        }
        return $this->response('error', 'Failed to suspend user');
    }

    public function activate($userId) {
        $this->requireAjaxAuth();
        if ($this->userModel->activateUser($userId)) {
            return $this->response('success', 'User activated successfully');
        }
        return $this->response('error', 'Failed to activate user');
    }
}
