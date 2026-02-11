<?php
/**
 * Loan Model
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class LoanModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function requestLoan($userId, $amount, $durationMonths, $purpose, $proofPath = null) {
        $data = [
            'user_id' => $userId,
            'amount' => $amount,
            'duration_months' => $durationMonths,
            'purpose' => $purpose,
            'status' => 'pending'
        ];

        if (!empty($proofPath)) {
            $data['proof_payment'] = $proofPath;
        }

        return $this->db->insert('loan_requests', $data);
    }

    public function getLoanRequestById($requestId) {
        return $this->db->selectOne('SELECT * FROM loan_requests WHERE id = ?', [$requestId]);
    }

    public function getUserLoanRequests($userId) {
        return $this->db->select(
            'SELECT * FROM loan_requests WHERE user_id = ? ORDER BY requested_at DESC',
            [$userId]
        );
    }

    public function getPendingLoanRequests() {
        return $this->db->select(
            'SELECT lr.*, u.email, u.username, u.phone_number FROM loan_requests lr
             JOIN users u ON lr.user_id = u.id
             WHERE lr.status = "pending"
             ORDER BY lr.requested_at DESC'
        );
    }

    public function approveLoanRequest($requestId, $adminId, $interestRate = 0.0) {
        try {
            $this->db->beginTransaction();

            $request = $this->getLoanRequestById($requestId);

            // Calculate monthly payment
            $monthlyRate = $interestRate / 100 / 12;
            $monthlyPayment = ($request['amount'] * $monthlyRate) / (1 - pow(1 + $monthlyRate, -$request['duration_months']));

            // Create loan record
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime("+{$request['duration_months']} months"));

            $loanId = $this->db->insert('loans', [
                'user_id' => $request['user_id'],
                'loan_request_id' => $requestId,
                'principal_amount' => $request['amount'],
                'interest_rate' => $interestRate,
                'monthly_payment' => $monthlyPayment,
                'duration_months' => $request['duration_months'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active'
            ]);

            // Update loan request status
            $this->db->update('loan_requests', [
                'status' => 'approved',
                'reviewed_at' => date('Y-m-d H:i:s'),
                'reviewed_by' => $adminId
            ], 'id = ?', [$requestId]);

            // Disburse loan amount to user
            $user = $this->db->selectOne('SELECT balance FROM users WHERE id = ?', [$request['user_id']]);
            $newBalance = $user['balance'] + $request['amount'];
            $this->db->update('users', ['balance' => $newBalance], 'id = ?', [$request['user_id']]);

            // Record transaction
            $this->db->insert('transactions', [
                'user_id' => $request['user_id'],
                'transaction_type' => 'loan_disbursement',
                'amount' => $request['amount'],
                'balance_before' => $user['balance'],
                'balance_after' => $newBalance,
                'reference_id' => $loanId,
                'description' => 'Loan Disbursement',
                'status' => 'completed'
            ]);

            $this->db->commit();
            return $loanId;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function rejectLoanRequest($requestId, $reason, $adminId) {
        return $this->db->update('loan_requests', [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => $adminId
        ], 'id = ?', [$requestId]);
    }

    public function getUserLoans($userId) {
        return $this->db->select(
            'SELECT * FROM loans WHERE user_id = ? ORDER BY start_date DESC',
            [$userId]
        );
    }

    public function getLoanById($loanId) {
        return $this->db->selectOne('SELECT * FROM loans WHERE id = ?', [$loanId]);
    }

    public function makeLoanPayment($loanId, $paymentAmount, $proofPath = null) {
        try {
            $this->db->beginTransaction();

            $loan = $this->getLoanById($loanId);

            // Record payment
            $paymentData = [
                'loan_id' => $loanId,
                'payment_amount' => $paymentAmount,
                'payment_date' => date('Y-m-d'),
                'status' => 'completed',
                'transaction_reference' => 'LP-' . time()
            ];

            if (!empty($proofPath)) {
                $paymentData['proof_payment'] = $proofPath;
            }

            $this->db->insert('loan_payments', $paymentData);

            // Update total paid
            $newTotalPaid = $loan['total_paid'] + $paymentAmount;
            $this->db->update('loans', ['total_paid' => $newTotalPaid], 'id = ?', [$loanId]);

            // Record transaction
            $this->db->insert('transactions', [
                'user_id' => $loan['user_id'],
                'transaction_type' => 'loan_payment',
                'amount' => $paymentAmount,
                'reference_id' => $loanId,
                'description' => 'Loan Payment',
                'status' => 'completed'
            ]);

            // Check if loan is fully paid
            if ($newTotalPaid >= ($loan['principal_amount'] * (1 + $loan['interest_rate'] / 100))) {
                $this->db->update('loans', ['status' => 'completed'], 'id = ?', [$loanId]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getTotalLoanAmount($userId) {
        $result = $this->db->selectOne(
            'SELECT COALESCE(SUM(principal_amount), 0) as total FROM loans WHERE user_id = ? AND status IN ("active", "completed")',
            [$userId]
        );
        return $result['total'] ?? 0;
    }

    public function getTotalLoanPaid($userId) {
        $result = $this->db->selectOne(
            'SELECT COALESCE(SUM(total_paid), 0) as total FROM loans WHERE user_id = ?',
            [$userId]
        );
        return $result['total'] ?? 0;
    }
}
