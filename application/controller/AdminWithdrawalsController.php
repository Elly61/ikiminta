<?php
/**
 * Admin Withdrawals Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/WithdrawalModel.php';

class AdminWithdrawalsController extends BaseController {
    private $withdrawalModel;

    public function __construct() {
        parent::__construct();
        if (!$this->isAdmin()) {
            $this->redirect(BASE_URL . 'admin/auth/login');
        }
        $this->withdrawalModel = $this->loadModel('WithdrawalModel');
    }

    public function index() {
        $requests = $this->withdrawalModel->getPendingWithdrawRequests();
        $this->loadView('admin/requests/withdrawals', ['requests' => $requests]);
    }

    public function approve($requestId) {
        $this->requireAjaxAuth();
        $admin = $this->getCurrentUser();
        $blockchainHash = $this->generateBlockchainHash($requestId);

        if ($this->withdrawalModel->approveWithdrawRequest($requestId, $admin['id'], $blockchainHash)) {
            return $this->response('success', 'Withdrawal approved successfully', ['blockchain_hash' => $blockchainHash]);
        }
        return $this->response('error', 'Failed to approve withdrawal');
    }

    public function reject($requestId) {
        $this->requireAjaxAuth();
        $admin = $this->getCurrentUser();
        $reason = htmlspecialchars($_POST['reason'] ?? '');

        if ($this->withdrawalModel->rejectWithdrawRequest($requestId, $reason, $admin['id'])) {
            return $this->response('success', 'Withdrawal request rejected');
        }
        return $this->response('error', 'Failed to reject withdrawal request');
    }

    private function generateBlockchainHash($requestId) {
        // Simple blockchain hash generation
        $data = $requestId . time() . rand(1000, 9999);
        return hash('sha256', $data);
    }
}
