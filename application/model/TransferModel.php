<?php
/**
 * Transfer Model
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class TransferModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createTransfer($senderId, $receiverId, $amount, $description = '', $proofPath = null) {
        // Get settings for transfer fee
        $settings = $this->db->selectOne('SELECT transfer_fee FROM settings LIMIT 1');
        $fee = ($amount * $settings['transfer_fee']) / 100;
        $totalAmount = $amount + $fee;

        // Check sender balance
        $sender = $this->db->selectOne('SELECT balance FROM users WHERE id = ?', [$senderId]);
        if ($sender['balance'] < $totalAmount) {
            return false; // Insufficient balance
        }

        $refNumber = 'TRF-' . time() . '-' . rand(1000, 9999);

        try {
            $this->db->beginTransaction();

            // Create transfer record
            $transferData = [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'amount' => $amount,
                'fee' => $fee,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'reference_number' => $refNumber,
                'description' => $description,
                'completed_at' => date('Y-m-d H:i:s')
            ];

            if (!empty($proofPath)) {
                $transferData['proof_payment'] = $proofPath;
            }

            $transferId = $this->db->insert('transfer_funds', $transferData);

            // Deduct from sender
            $newSenderBalance = $sender['balance'] - $totalAmount;
            $this->db->update('users', ['balance' => $newSenderBalance], 'id = ?', [$senderId]);

            // Add to receiver
            $receiver = $this->db->selectOne('SELECT balance FROM users WHERE id = ?', [$receiverId]);
            $newReceiverBalance = $receiver['balance'] + $amount;
            $this->db->update('users', ['balance' => $newReceiverBalance], 'id = ?', [$receiverId]);

            // Add to admin account (fee)
            $admin = $this->db->selectOne('SELECT id, balance FROM users WHERE user_type = "super" LIMIT 1');
            if ($admin) {
                $newAdminBalance = $admin['balance'] + $fee;
                $this->db->update('users', ['balance' => $newAdminBalance], 'id = ?', [$admin['id']]);
            }

            // Record transactions
            $this->db->insert('transactions', [
                'user_id' => $senderId,
                'transaction_type' => 'transfer_send',
                'amount' => $amount,
                'fee' => $fee,
                'balance_before' => $sender['balance'],
                'balance_after' => $newSenderBalance,
                'reference_id' => $transferId,
                'description' => $description ?: 'Transfer to ' . $receiverId,
                'status' => 'completed'
            ]);

            $this->db->insert('transactions', [
                'user_id' => $receiverId,
                'transaction_type' => 'transfer_receive',
                'amount' => $amount,
                'balance_before' => $receiver['balance'],
                'balance_after' => $newReceiverBalance,
                'reference_id' => $transferId,
                'description' => 'Transfer from ' . $senderId,
                'status' => 'completed'
            ]);

            $this->db->commit();
            return $transferId;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getTransferById($transferId) {
        return $this->db->selectOne('SELECT * FROM transfer_funds WHERE id = ?', [$transferId]);
    }

    public function getUserTransfers($userId) {
        return $this->db->select(
            'SELECT t.*, 
                    su.username as sender_username, su.email as sender_email,
                    ru.username as receiver_username, ru.email as receiver_email
             FROM transfer_funds t
             JOIN users su ON t.sender_id = su.id
             JOIN users ru ON t.receiver_id = ru.id
             WHERE t.sender_id = ? OR t.receiver_id = ?
             ORDER BY t.created_at DESC',
            [$userId, $userId]
        );
    }

    public function getTotalTransferred($userId) {
        $result = $this->db->selectOne(
            'SELECT COALESCE(SUM(amount), 0) as total FROM transfer_funds WHERE sender_id = ? AND status = "completed"',
            [$userId]
        );
        return $result['total'] ?? 0;
    }

    public function validateReceiver($receiverId, $senderId) {
        // Ensure receiver exists and is not the same as sender
        $receiver = $this->db->selectOne('SELECT id FROM users WHERE id = ? AND user_type = "member" AND status = "active"', [$receiverId]);
        return $receiver && $receiverId != $senderId;
    }
}
