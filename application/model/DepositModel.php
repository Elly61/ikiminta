<?php
/**
 * Deposit Model
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class DepositModel {
    private $db;
    private $tableColumns;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->tableColumns = null;
    }

    private function getTableColumns($table) {
        if (!empty($this->tableColumns)) {
            return $this->tableColumns;
        }

        $driver = $this->db->getDriver();
        if ($driver === 'pgsql') {
            $columns = $this->db->select(
                "SELECT column_name FROM information_schema.columns WHERE table_name = ?",
                [$table]
            );
            if (!$columns) {
                $this->tableColumns = [];
                return $this->tableColumns;
            }
            $this->tableColumns = array_map(function ($col) {
                return $col['column_name'] ?? '';
            }, $columns);
        } else {
            $columns = $this->db->select("SHOW COLUMNS FROM $table");
            if (!$columns) {
                $this->tableColumns = [];
                return $this->tableColumns;
            }
            $this->tableColumns = array_map(function ($col) {
                return $col['Field'] ?? '';
            }, $columns);
        }

        $this->tableColumns = array_values(array_filter($this->tableColumns));
        return $this->tableColumns;
    }

    public function createDeposit($userId, $amount, $paymentMethod, $description = '', $proofPath = null) {
        $transactionRef = 'DEP-' . time() . '-' . rand(1000, 9999);
        
        $data = [
            'user_id' => $userId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionRef,
            'status' => 'pending',
            'description' => $description,
            'proof_payment' => $proofPath,
            'proof' => $proofPath
        ];

        $columns = $this->getTableColumns('deposits');
        if (!empty($columns)) {
            // Map proof to alternative column names if needed
            if (!in_array('proof_payment', $columns, true) && !in_array('proof', $columns, true) && !empty($proofPath)) {
                if (in_array('proof_path', $columns, true)) {
                    $data['proof_path'] = $proofPath;
                    unset($data['proof_payment'], $data['proof']);
                } elseif (in_array('proof_of_payment', $columns, true)) {
                    $data['proof_of_payment'] = $proofPath;
                    unset($data['proof_payment'], $data['proof']);
                }
            }
            $data = array_intersect_key($data, array_flip($columns));
        }

        if (empty($data)) {
            return false;
        }

        return $this->db->insert('deposits', $data);
    }

    public function getDepositById($depositId) {
        return $this->db->selectOne('SELECT * FROM deposits WHERE id = ?', [$depositId]);
    }

    public function getUserDeposits($userId) {
        return $this->db->select(
            'SELECT * FROM deposits WHERE user_id = ? ORDER BY created_at DESC',
            [$userId]
        );
    }

    public function approveDeposit($depositId, $adminId = null) {
        try {
            $this->db->beginTransaction();

            $deposit = $this->getDepositById($depositId);
            
            // Update deposit status
            $this->db->update('deposits', [
                'status' => 'completed',
                'completed_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$depositId]);

            // Get current user balance
            $user = $this->db->selectOne('SELECT balance FROM users WHERE id = ?', [$deposit['user_id']]);
            $newBalance = $user['balance'] + $deposit['amount'];

            // Update user balance
            $this->db->update('users', ['balance' => $newBalance], 'id = ?', [$deposit['user_id']]);

            // Record transaction
            $this->db->insert('transactions', [
                'user_id' => $deposit['user_id'],
                'transaction_type' => 'deposit',
                'amount' => $deposit['amount'],
                'balance_before' => $user['balance'],
                'balance_after' => $newBalance,
                'reference_id' => $depositId,
                'description' => $deposit['description'] ?: 'Deposit via ' . $deposit['payment_method'],
                'status' => 'completed'
            ]);

            // Log audit entry
            $oldValues = json_encode($deposit);
            $newValues = json_encode(['status' => 'completed', 'completed_at' => date('Y-m-d H:i:s')]);
            $this->db->insert('audit_logs', [
                'user_id' => $adminId,
                'action' => 'approve_deposit',
                'table_name' => 'deposits',
                'record_id' => $depositId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function rejectDeposit($depositId, $reason = '', $adminId = null) {
        $deposit = $this->getDepositById($depositId);
        $res = $this->db->update('deposits', [
            'status' => 'failed',
            'completed_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$depositId]);

        if ($res) {
            $newValues = json_encode(['status' => 'failed', 'completed_at' => date('Y-m-d H:i:s'), 'reason' => $reason]);
            $oldValues = json_encode($deposit);
            $this->db->insert('audit_logs', [
                'user_id' => $adminId,
                'action' => 'reject_deposit',
                'table_name' => 'deposits',
                'record_id' => $depositId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        }

        return $res;
    }

    public function getAuditLogsForDeposit($depositId) {
        return $this->db->select(
            'SELECT al.*, u.username FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id WHERE al.table_name = ? AND al.record_id = ? ORDER BY al.created_at DESC',
            ['deposits', $depositId]
        );
    }

    public function logAudit($userId, $action, $recordId, $oldValues = null, $newValues = null) {
        return $this->db->insert('audit_logs', [
            'user_id' => $userId,
            'action' => $action,
            'table_name' => 'deposits',
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    public function getPendingDeposits() {
        return $this->db->select(
            "SELECT d.*, u.email, u.username FROM deposits d JOIN users u ON d.user_id = u.id WHERE d.status = 'pending' ORDER BY d.created_at DESC"
        );
    }

    public function getTotalDeposited($userId) {
        $result = $this->db->selectOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM deposits WHERE user_id = ? AND status = 'completed'",
            [$userId]
        );
        return $result['total'] ?? 0;
    }

    public function updateDepositProof($depositId, $proofPath) {
        return $this->db->update('deposits', [
            'proof' => $proofPath,
            'proof_payment' => $proofPath
        ], 'id = ?', [$depositId]);
    }
}
