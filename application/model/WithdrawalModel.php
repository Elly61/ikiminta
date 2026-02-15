<?php
/**
 * Withdrawal Model
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class WithdrawalModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createWithdrawRequest($userId, $amount, $withdrawalMethod, $details) {
        // Get settings for withdrawal fee
        $settings = $this->db->selectOne('SELECT withdraw_fee FROM settings LIMIT 1');
        $fee = ($amount * $settings['withdraw_fee']) / 100;
        $totalAmount = $amount + $fee;

        // Check user balance
        $user = $this->db->selectOne('SELECT balance FROM users WHERE id = ?', [$userId]);
        if ($user['balance'] < $totalAmount) {
            return false; // Insufficient balance
        }

        $data = [
            'user_id' => $userId,
            'amount' => $amount,
            'fee' => $fee,
            'total_amount' => $totalAmount,
            'withdrawal_method' => $withdrawalMethod,
            'status' => 'pending'
        ];

        // Add method-specific details
        if ($withdrawalMethod === 'bank_transfer') {
            $data['bank_account'] = $details['bank_account'] ?? null;
        } elseif ($withdrawalMethod === 'momo') {
            $data['momo_number'] = $details['momo_number'] ?? null;
        }

        return $this->db->insert('withdraw_requests', $data);
    }

    public function getWithdrawRequestById($requestId) {
        return $this->db->selectOne('SELECT * FROM withdraw_requests WHERE id = ?', [$requestId]);
    }

    public function getUserWithdrawRequests($userId) {
        return $this->db->select(
            'SELECT * FROM withdraw_requests WHERE user_id = ? ORDER BY requested_at DESC',
            [$userId]
        );
    }

    public function getPendingWithdrawRequests() {
        return $this->db->select(
            "SELECT wr.*, u.email, u.username FROM withdraw_requests wr
             JOIN users u ON wr.user_id = u.id
             WHERE wr.status = 'pending'
             ORDER BY wr.requested_at DESC"
        );
    }

    public function approveWithdrawRequest($requestId, $adminId, $blockchainHash = '') {
        try {
            $this->db->beginTransaction();

            $request = $this->getWithdrawRequestById($requestId);

            // Deduct from user balance
            $user = $this->db->selectOne('SELECT balance FROM users WHERE id = ?', [$request['user_id']]);
            $newBalance = $user['balance'] - $request['total_amount'];
            $this->db->update('users', ['balance' => $newBalance], 'id = ?', [$request['user_id']]);

            // Add to admin account (fee)
            $admin = $this->db->selectOne("SELECT id, balance FROM users WHERE user_type = 'super' LIMIT 1");
            if ($admin) {
                $newAdminBalance = $admin['balance'] + $request['fee'];
                $this->db->update('users', ['balance' => $newAdminBalance], 'id = ?', [$admin['id']]);
            }

            // Create withdrawal record
            $withdrawalId = $this->db->insert('withdrawals', [
                'user_id' => $request['user_id'],
                'withdraw_request_id' => $requestId,
                'amount' => $request['amount'],
                'fee' => $request['fee'],
                'total_amount' => $request['total_amount'],
                'withdrawal_method' => $request['withdrawal_method'],
                'reference_number' => 'WTH-' . time() . '-' . rand(1000, 9999),
                'blockchain_hash' => $blockchainHash,
                'status' => 'completed'
            ]);

            // Update withdrawal request status
            $this->db->update('withdraw_requests', [
                'status' => 'approved',
                'blockchain_hash' => $blockchainHash,
                'reviewed_at' => date('Y-m-d H:i:s'),
                'reviewed_by' => $adminId,
                'completed_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$requestId]);

            // Record transaction
            $this->db->insert('transactions', [
                'user_id' => $request['user_id'],
                'transaction_type' => 'withdrawal',
                'amount' => $request['amount'],
                'fee' => $request['fee'],
                'balance_before' => $user['balance'],
                'balance_after' => $newBalance,
                'reference_id' => $withdrawalId,
                'description' => 'Withdrawal via ' . str_replace('_', ' ', $request['withdrawal_method']),
                'status' => 'completed'
            ]);

            $this->db->commit();
            return $withdrawalId;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function rejectWithdrawRequest($requestId, $reason, $adminId) {
        return $this->db->update('withdraw_requests', [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => $adminId
        ], 'id = ?', [$requestId]);
    }

    public function getTotalWithdrawn($userId) {
        $result = $this->db->selectOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM withdrawals WHERE user_id = ? AND status = 'completed'",
            [$userId]
        );
        return $result['total'] ?? 0;
    }

    public function getUserWithdrawals($userId) {
        return $this->db->select(
            'SELECT * FROM withdrawals WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }
}
