<?php
/**
 * Transaction Model
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class TransactionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getUserTransactions($userId, $limit = 50, $offset = 0) {
        return $this->db->select(
            'SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [$userId, $limit, $offset]
        );
    }

    public function getTransactionById($transactionId) {
        return $this->db->selectOne('SELECT * FROM transactions WHERE id = ?', [$transactionId]);
    }

    public function getTotalCredit($userId) {
        $result = $this->db->selectOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
             WHERE user_id = ? AND transaction_type IN ('deposit', 'transfer_receive', 'loan_disbursement', 'saving_maturity')
             AND status = 'completed'",
            [$userId]
        );
        return $result['total'] ?? 0;
    }

    public function getTotalDebit($userId) {
        $result = $this->db->selectOne(
            "SELECT COALESCE(SUM(amount + fee), 0) as total FROM transactions 
             WHERE user_id = ? AND transaction_type IN ('withdrawal', 'transfer_send', 'saving_creation')
             AND status = 'completed'",
            [$userId]
        );
        return $result['total'] ?? 0;
    }

    public function getTransactionsByType($userId, $type) {
        return $this->db->select(
            'SELECT * FROM transactions WHERE user_id = ? AND transaction_type = ? ORDER BY created_at DESC',
            [$userId, $type]
        );
    }

    public function getTransactionsByDateRange($userId, $startDate, $endDate) {
        return $this->db->select(
            'SELECT * FROM transactions WHERE user_id = ? AND DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC',
            [$userId, $startDate, $endDate]
        );
    }

    /**
     * Get summary totals (credits and debits) for a user between two dates (inclusive).
     * Returns ['credit' => x, 'debit' => y, 'count' => n]
     */
    public function getSummaryTotals($userId, $startDate, $endDate) {
        $credit = $this->db->selectOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND DATE(created_at) BETWEEN ? AND ? AND transaction_type IN ('deposit', 'transfer_receive', 'loan_disbursement', 'saving_maturity') AND status = 'completed'",
            [$userId, $startDate, $endDate]
        );

        $debit = $this->db->selectOne(
            "SELECT COALESCE(SUM(amount + COALESCE(fee,0)), 0) as total FROM transactions WHERE user_id = ? AND DATE(created_at) BETWEEN ? AND ? AND transaction_type IN ('withdrawal', 'transfer_send', 'saving_creation') AND status = 'completed'",
            [$userId, $startDate, $endDate]
        );

        $count = $this->db->selectOne(
            'SELECT COUNT(*) as cnt FROM transactions WHERE user_id = ? AND DATE(created_at) BETWEEN ? AND ?',
            [$userId, $startDate, $endDate]
        );

        return [
            'credit' => $credit['total'] ?? 0,
            'debit' => $debit['total'] ?? 0,
            'count' => $count['cnt'] ?? 0
        ];
    }

    /**
     * Platform-wide summary totals between two dates (inclusive).
     * Returns ['credit' => x, 'debit' => y, 'count' => n]
     */
    public function getPlatformSummaryTotals($startDate, $endDate) {
        $credit = $this->db->selectOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE DATE(created_at) BETWEEN ? AND ? AND transaction_type IN ('deposit', 'transfer_receive', 'loan_disbursement', 'saving_maturity') AND status = 'completed'",
            [$startDate, $endDate]
        );

        $debit = $this->db->selectOne(
            "SELECT COALESCE(SUM(amount + COALESCE(fee,0)), 0) as total FROM transactions WHERE DATE(created_at) BETWEEN ? AND ? AND transaction_type IN ('withdrawal', 'transfer_send', 'saving_creation') AND status = 'completed'",
            [$startDate, $endDate]
        );

        $count = $this->db->selectOne(
            'SELECT COUNT(*) as cnt FROM transactions WHERE DATE(created_at) BETWEEN ? AND ?',
            [$startDate, $endDate]
        );

        return [
            'credit' => $credit['total'] ?? 0,
            'debit' => $debit['total'] ?? 0,
            'count' => $count['cnt'] ?? 0
        ];
    }

    /**
     * Get recent transactions across the platform with user info.
     */
    public function getRecentTransactions($limit = 10) {
        return $this->db->select(
            'SELECT t.*, u.username, u.email FROM transactions t LEFT JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT ?',
            [$limit]
        );
    }

    /**
     * Get daily net (credits - debits) for each date in range.
     * Returns array keyed by YYYY-MM-DD => net amount
     */
    public function getPlatformDailyNet($startDate, $endDate) {
        $rows = $this->db->select(
            "SELECT DATE(created_at) as d, 
                SUM(CASE 
                    WHEN transaction_type IN ('deposit', 'transfer_receive', 'loan_disbursement', 'saving_maturity') THEN amount
                    WHEN transaction_type IN ('withdrawal', 'transfer_send', 'saving_creation') THEN - (amount + COALESCE(fee,0))
                    ELSE 0 END) as net
             FROM transactions
             WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'
             GROUP BY DATE(created_at)
             ORDER BY DATE(created_at)
            ",
            [$startDate, $endDate]
        );

        $map = [];
        foreach ($rows as $r) {
            $map[$r['d']] = floatval($r['net']);
        }
        return $map;
    }
}
