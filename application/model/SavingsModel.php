<?php
/**
 * Savings Model
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class SavingsModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createSavingsWithoutBalanceCheck($userId, $amount, $maturityMonths, $interestRate = 0.0, $proofPath = null) {
        $maturityDate = date('Y-m-d', strtotime("+$maturityMonths months"));

        try {
            $this->db->beginTransaction();

            // Create savings record without deducting balance
            $savingsData = [
                'user_id' => $userId,
                'amount' => $amount,
                'interest_rate' => $interestRate,
                'maturity_date' => $maturityDate,
                'status' => 'active'
            ];

            if (!empty($proofPath)) {
                $savingsData['proof_payment'] = $proofPath;
            }

            $savingsId = $this->db->insert('savings', $savingsData);

            // Record transaction (but don't deduct balance)
            $user = $this->db->selectOne('SELECT balance FROM users WHERE id = ?', [$userId]);
            $this->db->insert('transactions', [
                'user_id' => $userId,
                'transaction_type' => 'saving_creation',
                'amount' => $amount,
                'balance_before' => $user['balance'],
                'balance_after' => $user['balance'], // Balance unchanged
                'reference_id' => $savingsId,
                'description' => "Savings Account Created - Matures on $maturityDate (Balance not deducted)",
                'status' => 'completed'
            ]);

            $this->db->commit();
            return $savingsId;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getUserSavings($userId) {
        return $this->db->select(
            'SELECT * FROM savings WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }

    public function getSavingsById($savingsId) {
        return $this->db->selectOne('SELECT * FROM savings WHERE id = ?', [$savingsId]);
    }

    public function maturitySavings($savingsId) {
        try {
            $this->db->beginTransaction();

            $savings = $this->getSavingsById($savingsId);
            $user = $this->db->selectOne('SELECT balance FROM users WHERE id = ?', [$savings['user_id']]);

            // Calculate maturity amount
            $maturityAmount = $savings['amount'] + ($savings['amount'] * $savings['interest_rate'] / 100);

            // Add to balance
            $newBalance = $user['balance'] + $maturityAmount;
            $this->db->update('users', ['balance' => $newBalance], 'id = ?', [$savings['user_id']]);

            // Update savings status
            $this->db->update('savings', ['status' => 'completed'], 'id = ?', [$savingsId]);

            // Record transaction
            $this->db->insert('transactions', [
                'user_id' => $savings['user_id'],
                'transaction_type' => 'saving_maturity',
                'amount' => $maturityAmount,
                'balance_before' => $user['balance'],
                'balance_after' => $newBalance,
                'reference_id' => $savingsId,
                'description' => "Savings Matured - Principal: {$savings['amount']}, Interest: " . ($maturityAmount - $savings['amount']),
                'status' => 'completed'
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getTotalSavings($userId) {
        $result = $this->db->selectOne(
            'SELECT COALESCE(SUM(amount), 0) as total FROM savings WHERE user_id = ? AND status = "active"',
            [$userId]
        );
        return $result['total'] ?? 0;
    }

    public function getActiveSavings() {
        return $this->db->select(
            'SELECT * FROM savings WHERE status = "active" AND maturity_date <= CURDATE()'
        );
    }
}
