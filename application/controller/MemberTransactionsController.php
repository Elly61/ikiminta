<?php
/**
 * Member Transactions Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/TransactionModel.php';

class MemberTransactionsController extends BaseController {
    private $transactionModel;

    public function __construct() {
        parent::__construct();
        $this->transactionModel = $this->loadModel('TransactionModel');
    }

    public function index() {
        $user = $this->getCurrentUser();
        $page = intval($_GET['page'] ?? 1);
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        
        $transactions = $this->transactionModel->getUserTransactions($user['id'], ITEMS_PER_PAGE, $offset);
        
        $this->loadView('member/transactions/index', [
            'transactions' => $transactions,
            'user' => $user,
            'page' => $page
        ]);
    }

    public function view($transactionId) {
        $user = $this->getCurrentUser();
        $transaction = $this->transactionModel->getTransactionById($transactionId);

        if (!$transaction || $transaction['user_id'] != $user['id']) {
            $this->redirect(BASE_URL . 'member/transactions');
        }

        $this->loadView('member/transactions/view', ['transaction' => $transaction]);
    }

    public function filter() {
        $user = $this->getCurrentUser();
        $type = htmlspecialchars($_GET['type'] ?? '');
        $startDate = htmlspecialchars($_GET['start_date'] ?? '');
        $endDate = htmlspecialchars($_GET['end_date'] ?? '');

        $transactions = [];
        
        if (!empty($type)) {
            $transactions = $this->transactionModel->getTransactionsByType($user['id'], $type);
        } elseif (!empty($startDate) && !empty($endDate)) {
            $transactions = $this->transactionModel->getTransactionsByDateRange($user['id'], $startDate, $endDate);
        }

        $this->loadView('member/transactions/index', [
            'transactions' => $transactions,
            'user' => $user,
            'filter_applied' => true
        ]);
    }

    public function report() {
        $user = $this->getCurrentUser();
        $period = htmlspecialchars($_GET['period'] ?? 'daily');
        $date = htmlspecialchars($_GET['date'] ?? date('Y-m-d'));

        // Determine range
        if ($period === 'daily') {
            $start = $date;
            $end = $date;
            $label = date('F j, Y', strtotime($date));
        } elseif ($period === 'weekly') {
            // week starting Monday
            $ts = strtotime($date);
            $monday = date('Y-m-d', strtotime('monday this week', $ts));
            $start = $monday;
            $end = date('Y-m-d', strtotime($start . ' +6 days'));
            $label = date('M j', strtotime($start)) . ' - ' . date('M j, Y', strtotime($end));
        } else { // monthly
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            $start = date(sprintf('%04d-%02d-01', $year, $month));
            $end = date('Y-m-t', strtotime($start));
            $label = date('F Y', strtotime($start));
        }

        $transactions = $this->transactionModel->getTransactionsByDateRange($user['id'], $start, $end);
        $summary = $this->transactionModel->getSummaryTotals($user['id'], $start, $end);

        // Build explanations for each transaction and prepare chart data
        $chartData = [];
        $chartLabels = [];
        $chartMap = []; // date => total

        foreach ($transactions as &$t) {
            $t['explanation'] = $this->explainTransaction($t);

            $dateKey = date('Y-m-d', strtotime($t['created_at']));
            // determine sign: credit types positive, debit types negative
            $creditTypes = ['deposit','transfer_receive','loan_disbursement','saving_maturity'];
            $sign = in_array($t['transaction_type'], $creditTypes) ? 1 : -1;
            $amt = $sign * floatval($t['amount']);
            if (!isset($chartMap[$dateKey])) $chartMap[$dateKey] = 0;
            $chartMap[$dateKey] += $amt;
        }

        // Build ordered labels and data for chart across range
        $periodStart = new DateTime($start);
        $periodEnd = new DateTime($end);
        $interval = new DateInterval('P1D');
        $periodObj = new DatePeriod($periodStart, $interval, $periodEnd->modify('+1 day'));
        foreach ($periodObj as $d) {
            $k = $d->format('Y-m-d');
            $chartLabels[] = $d->format('M j');
            $chartData[] = isset($chartMap[$k]) ? round($chartMap[$k],2) : 0;
        }

        $this->loadView('member/transactions/report', [
            'transactions' => $transactions,
            'summary' => $summary,
            'period' => $period,
            'label' => $label,
            'start' => $start,
            'end' => $end,
            'user' => $user,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData
        ]);
    }

    private function explainTransaction($t) {
        // Provide human-friendly explanations by transaction_type, include links when possible
        $ref = $t['reference_id'] ?? null;
        switch ($t['transaction_type']) {
            case 'deposit':
                $link = $ref ? '<a href="' . BASE_URL . 'member/deposits/view/' . intval($ref) . '">View deposit</a>' : '';
                return 'Deposit: funds added to your account. ' . ($link ? "($link)" : '');
            case 'withdrawal':
                return 'Withdrawal: funds removed from your account to your selected method.';
            case 'transfer_send':
                $link = $ref ? '<a href="' . BASE_URL . 'member/transfer/view/' . intval($ref) . '">View transfer</a>' : '';
                return 'Transfer Sent: you sent funds to another user. ' . ($link ? "($link)" : '');
            case 'transfer_receive':
                $link = $ref ? '<a href="' . BASE_URL . 'member/transfer/view/' . intval($ref) . '">View transfer</a>' : '';
                return 'Transfer Received: you received funds from another user. ' . ($link ? "($link)" : '');
            case 'loan_disbursement':
                $link = $ref ? '<a href="' . BASE_URL . 'member/loans/view/' . intval($ref) . '">View loan</a>' : '';
                return 'Loan disbursement: loan amount credited to your account. ' . ($link ? "($link)" : '');
            case 'loan_payment':
                $link = $ref ? '<a href="' . BASE_URL . 'member/loans/view/' . intval($ref) . '">View loan</a>' : '';
                return 'Loan payment: repayment towards your loan. ' . ($link ? "($link)" : '');
            case 'saving_creation':
                return 'Savings created: amount locked into a savings product.';
            case 'saving_maturity':
                return 'Savings matured: saved amount + interest credited.';
            default:
                return 'Transaction: ' . htmlspecialchars($t['description'] ?? 'No details available');
        }
    }

    public function exportCsv() {
        $user = $this->getCurrentUser();
        $period = htmlspecialchars($_GET['period'] ?? 'daily');
        $date = htmlspecialchars($_GET['date'] ?? date('Y-m-d'));

        // reuse report range logic
        if ($period === 'daily') {
            $start = $date; $end = $date;
            $label = date('Y-m-d', strtotime($date));
        } elseif ($period === 'weekly') {
            $ts = strtotime($date);
            $monday = date('Y-m-d', strtotime('monday this week', $ts));
            $start = $monday; $end = date('Y-m-d', strtotime($start . ' +6 days'));
            $label = $start . '_to_' . $end;
        } else {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            $start = date(sprintf('%04d-%02d-01', $year, $month));
            $end = date('Y-m-t', strtotime($start));
            $label = date('Y-m', strtotime($start));
        }

        $transactions = $this->transactionModel->getTransactionsByDateRange($user['id'], $start, $end);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="transactions_' . $user['id'] . '_' . $label . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Date','Type','Amount','Fee','Balance Before','Balance After','Explanation']);
        foreach ($transactions as $t) {
            $ex = strip_tags($this->explainTransaction($t));
            fputcsv($out, [$t['created_at'], $t['transaction_type'], $t['amount'], $t['fee'] ?? '', $t['balance_before'] ?? '', $t['balance_after'] ?? '', $ex]);
        }
        fclose($out);
        exit();
    }

    public function exportPdf() {
        $user = $this->getCurrentUser();
        $period = htmlspecialchars($_GET['period'] ?? 'daily');
        $date = htmlspecialchars($_GET['date'] ?? date('Y-m-d'));

        // reuse report range logic
        if ($period === 'daily') {
            $start = $date; $end = $date;
            $label = date('F j, Y', strtotime($date));
        } elseif ($period === 'weekly') {
            $ts = strtotime($date);
            $monday = date('Y-m-d', strtotime('monday this week', $ts));
            $start = $monday; $end = date('Y-m-d', strtotime($start . ' +6 days'));
            $label = date('M j', strtotime($start)) . ' - ' . date('M j, Y', strtotime($end));
        } else {
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            $start = date(sprintf('%04d-%02d-01', $year, $month));
            $end = date('Y-m-t', strtotime($start));
            $label = date('F Y', strtotime($start));
        }

        $transactions = $this->transactionModel->getTransactionsByDateRange($user['id'], $start, $end);
        $summary = $this->transactionModel->getSummaryTotals($user['id'], $start, $end);

        foreach ($transactions as &$t) {
            $t['explanation'] = strip_tags($this->explainTransaction($t));
        }

        $this->loadView('member/transactions/report_print', [
            'transactions' => $transactions,
            'summary' => $summary,
            'label' => $label,
            'start' => $start,
            'end' => $end,
            'user' => $user
        ]);
    }
}
