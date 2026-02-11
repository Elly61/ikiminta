<?php
/**
 * Member Withdraw Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/WithdrawalModel.php';

class MemberWithdrawController extends BaseController {
    private $withdrawalModel;

    public function __construct() {
        parent::__construct();
        $this->withdrawalModel = $this->loadModel('WithdrawalModel');
    }

    public function index() {
        $user = $this->getCurrentUser();
        $requests = $this->withdrawalModel->getUserWithdrawRequests($user['id']);
        
        $this->loadView('member/withdraw/index', [
            'requests' => $requests,
            'user' => $user
        ]);
    }

    public function request() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->loadView('member/withdraw/request');
        } else {
            return $this->handleRequest();
        }
    }

    public function view($requestId = 0) {
        $user = $this->getCurrentUser();

        if (empty($requestId)) {
            $this->redirect(BASE_URL . 'member/withdraw');
        }

        $request = $this->withdrawalModel->getWithdrawRequestById($requestId);

        if (!$request || $request['user_id'] != $user['id']) {
            $this->redirect(BASE_URL . 'member/withdraw');
        }

        $this->loadView('member/withdraw/view', [
            'request' => $request,
            'user' => $user
        ]);
    }

    private function handleRequest() {
        $user = $this->getCurrentUser();
        $amount = floatval($_POST['amount'] ?? 0);
        $method = htmlspecialchars($_POST['withdrawal_method'] ?? '');

        if ($amount <= 0) {
            return $this->response('error', 'Invalid amount');
        }

        if (!in_array($method, ['bank_transfer', 'momo', 'cash'])) {
            return $this->response('error', 'Invalid withdrawal method');
        }

        $details = [];
        if ($method === 'bank_transfer') {
            $details['bank_account'] = htmlspecialchars($_POST['bank_account'] ?? '');
        } elseif ($method === 'momo') {
            $details['momo_number'] = htmlspecialchars($_POST['momo_number'] ?? '');
        }

        $requestId = $this->withdrawalModel->createWithdrawRequest($user['id'], $amount, $method, $details);

        if ($requestId) {
            return $this->response('success', 'Withdrawal request submitted. Awaiting approval.', [
                'request_id' => $requestId,
                'redirect' => BASE_URL . 'member/withdraw'
            ]);
        } else {
            return $this->response('error', 'Withdrawal failed. Check your balance.');
        }
    }
}
