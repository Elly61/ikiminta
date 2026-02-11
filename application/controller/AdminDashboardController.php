<?php
/**
 * Admin Dashboard Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';
require_once dirname(__FILE__) . '/../model/TransactionModel.php';
require_once dirname(__FILE__) . '/../model/DepositModel.php';
require_once dirname(__FILE__) . '/../model/LoanModel.php';
require_once dirname(__FILE__) . '/../model/WithdrawalModel.php';

class AdminDashboardController extends BaseController {
    private $userModel;
    private $transactionModel;
    private $depositModel;
    private $loanModel;
    private $withdrawalModel;

    public function __construct() {
        parent::__construct();
        if (!$this->isAdmin()) {
            $this->redirect(BASE_URL . 'admin/auth/login');
        }
        $this->userModel = $this->loadModel('UserModel');
        $this->transactionModel = $this->loadModel('TransactionModel');
        $this->depositModel = $this->loadModel('DepositModel');
        $this->loanModel = $this->loadModel('LoanModel');
        $this->withdrawalModel = $this->loadModel('WithdrawalModel');
    }

    public function index() {
        $admin = $this->getCurrentUser();
        
        // Determine period (days) for transactions summary (allowed: 7,30,90)
        $allowed = [7,30,90];
        $days = intval($_GET['days'] ?? 7);
        if (!in_array($days, $allowed)) $days = 7;

        // Prepare basic dashboard counts
        $dashboardData = [
            'admin' => $admin,
            'pending_deposits' => $this->depositModel->getPendingDeposits(),
            'pending_loans' => $this->loanModel->getPendingLoanRequests(),
            'pending_withdrawals' => $this->withdrawalModel->getPendingWithdrawRequests(),
            'total_users' => $this->userModel->getTotalUsers()
        ];

        // Add transactions summary for the last 7 days to show on admin dashboard
        $end = date('Y-m-d');
        $start = date('Y-m-d', strtotime('-' . ($days - 1) . ' days')); // window of $days
        $platformSummary = $this->transactionModel->getPlatformSummaryTotals($start, $end);
        $recentTx = $this->transactionModel->getRecentTransactions(8);
        $dailyNet = $this->transactionModel->getPlatformDailyNet($start, $end);

        // build chart labels/data for the 7-day window
        $labels = [];
        $data = [];
        $periodStart = new DateTime($start);
        $periodEnd = new DateTime($end);
        $interval = new DateInterval('P1D');
        $periodObj = new DatePeriod($periodStart, $interval, $periodEnd->modify('+1 day'));
        foreach ($periodObj as $d) {
            $k = $d->format('Y-m-d');
            $labels[] = $d->format('M j');
            $data[] = isset($dailyNet[$k]) ? round($dailyNet[$k],2) : 0;
        }

        $dashboardData['transactions_summary'] = $platformSummary;
        $dashboardData['recent_transactions'] = $recentTx;
        $dashboardData['chartLabels'] = $labels;
        $dashboardData['chartData'] = $data;
        $dashboardData['periodDays'] = $days;
        $dashboardData['periodLabel'] = $days . ' days';

        $this->loadView('admin/dashboard/index', $dashboardData);
    }
}
