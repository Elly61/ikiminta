<?php
/**
 * Report Model
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class ReportModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get weekly report for a user
     */
    public function getWeeklyReport($userId) {
        $startDate = date('Y-m-d', strtotime('7 days ago'));
        $endDate = date('Y-m-d');

        return $this->getReportData($userId, $startDate, $endDate, 'weekly');
    }

    /**
     * Get monthly report for a user
     */
    public function getMonthlyReport($userId) {
        $startDate = date('Y-m-d', strtotime('30 days ago'));
        $endDate = date('Y-m-d');

        return $this->getReportData($userId, $startDate, $endDate, 'monthly');
    }

    /**
     * Get yearly report for a user
     */
    public function getYearlyReport($userId) {
        $startDate = date('Y-m-d', strtotime('365 days ago'));
        $endDate = date('Y-m-d');

        return $this->getReportData($userId, $startDate, $endDate, 'yearly');
    }

    /**
     * Get report data for a specific period
     */
    private function getReportData($userId, $startDate, $endDate, $periodType) {
        // Get transactions for the period
        $transactions = $this->db->select(
            'SELECT * FROM transactions WHERE user_id = ? AND created_at BETWEEN ? AND ? ORDER BY created_at DESC',
            [$userId, $startDate, $endDate]
        );

        // Calculate summary statistics
        $summary = $this->calculateSummary($transactions);

        // Get user info
        $user = $this->db->selectOne('SELECT * FROM users WHERE id = ?', [$userId]);

        return [
            'user' => $user,
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'transactions' => $transactions,
            'summary' => $summary
        ];
    }

    /**
     * Calculate summary statistics from transactions
     */
    private function calculateSummary($transactions) {
        $summary = [
            'total_deposits' => 0,
            'total_withdrawals' => 0,
            'total_transfers_sent' => 0,
            'total_transfers_received' => 0,
            'total_loans' => 0,
            'total_loan_payments' => 0,
            'total_savings' => 0,
            'net_change' => 0,
            'transaction_count' => count($transactions),
            'average_transaction' => 0
        ];

        foreach ($transactions as $transaction) {
            $type = $transaction['transaction_type'];
            $amount = $transaction['amount'];

            switch ($type) {
                case 'deposit':
                case 'deposit_approved':
                    $summary['total_deposits'] += $amount;
                    break;
                case 'withdrawal':
                case 'withdrawal_approved':
                    $summary['total_withdrawals'] += $amount;
                    break;
                case 'transfer_sent':
                    $summary['total_transfers_sent'] += $amount;
                    break;
                case 'transfer_received':
                    $summary['total_transfers_received'] += $amount;
                    break;
                case 'loan_issued':
                    $summary['total_loans'] += $amount;
                    break;
                case 'loan_repayment':
                    $summary['total_loan_payments'] += $amount;
                    break;
                case 'saving_creation':
                    $summary['total_savings'] += $amount;
                    break;
            }
        }

        // Calculate net change
        $summary['net_change'] = $summary['total_deposits'] + $summary['total_transfers_received'] + $summary['total_loans']
                                - $summary['total_withdrawals'] - $summary['total_transfers_sent'] - $summary['total_savings'];

        // Calculate average transaction
        if ($summary['transaction_count'] > 0) {
            $totalAmount = array_sum(array_column($transactions, 'amount'));
            $summary['average_transaction'] = $totalAmount / $summary['transaction_count'];
        }

        return $summary;
    }

    /**
     * Get daily breakdown for a period (for charts)
     */
    public function getDailyBreakdown($userId, $startDate, $endDate) {
        $transactions = $this->db->select(
            'SELECT DATE(created_at) as date, transaction_type, SUM(amount) as total_amount, COUNT(*) as count 
             FROM transactions 
             WHERE user_id = ? AND created_at BETWEEN ? AND ? 
             GROUP BY DATE(created_at), transaction_type 
             ORDER BY DATE(created_at)',
            [$userId, $startDate, $endDate]
        );

        return $transactions;
    }

    /**
     * Get transaction type breakdown for a period
     */
    public function getTransactionTypeBreakdown($userId, $startDate, $endDate) {
        $transactions = $this->db->select(
            'SELECT transaction_type, SUM(amount) as total_amount, COUNT(*) as count, AVG(amount) as avg_amount 
             FROM transactions 
             WHERE user_id = ? AND created_at BETWEEN ? AND ? 
             GROUP BY transaction_type 
             ORDER BY total_amount DESC',
            [$userId, $startDate, $endDate]
        );

        return $transactions;
    }

    /**
     * Get deposit/loan/savings details for a period
     */
    public function getDepositBreakdown($userId, $startDate, $endDate) {
        $deposits = $this->db->select(
            'SELECT * FROM deposits WHERE user_id = ? AND created_at BETWEEN ? AND ? ORDER BY created_at DESC',
            [$userId, $startDate, $endDate]
        );
        return $deposits;
    }

    public function getLoanBreakdown($userId, $startDate, $endDate) {
        $loans = $this->db->select(
            'SELECT * FROM loans WHERE user_id = ? AND created_at BETWEEN ? AND ? ORDER BY created_at DESC',
            [$userId, $startDate, $endDate]
        );
        return $loans;
    }

    public function getSavingsBreakdown($userId, $startDate, $endDate) {
        $savings = $this->db->select(
            'SELECT * FROM savings WHERE user_id = ? AND created_at BETWEEN ? AND ? ORDER BY created_at DESC',
            [$userId, $startDate, $endDate]
        );
        return $savings;
    }
}
?>
