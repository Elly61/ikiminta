<?php
/**
 * Admin Settings Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class AdminSettingsController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        if (!$this->isAdmin()) {
            $this->redirect(BASE_URL . 'admin/auth/login');
        }
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update();
        }

        $settings = $this->userModel->getSettings();
        $this->loadView('admin/settings/index', ['settings' => $settings]);
    }

    public function update() {
        $this->requireAjaxAuth();
        $transferFee = floatval($_POST['transfer_fee'] ?? 0);
        $withdrawFee = floatval($_POST['withdraw_fee'] ?? 0);
        $loanInterestRate = floatval($_POST['loan_interest_rate'] ?? 1.5);
        $savingsInterestRate = floatval($_POST['savings_interest_rate'] ?? 1.5);
        $signupBonus = floatval($_POST['signup_bonus'] ?? 0);

        $this->userModel->updateSettings([
            'transfer_fee' => $transferFee,
            'withdraw_fee' => $withdrawFee,
            'loan_interest_rate' => $loanInterestRate,
            'savings_interest_rate' => $savingsInterestRate,
            'signup_bonus' => $signupBonus,
            'updated_by' => $this->getCurrentUser()['id']
        ]);

        return $this->response('success', 'Settings updated successfully');
    }
}