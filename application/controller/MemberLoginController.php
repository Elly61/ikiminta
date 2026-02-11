<?php
/**
 * Member Login Controller
 * Redirects to the proper auth controller
 */

require_once dirname(__FILE__) . '/BaseController.php';

class MemberLoginController extends BaseController {
    public function __construct() {
        // Skip session check for login
        $this->viewPath = VIEW_PATH;
        $this->modelPath = MODEL_PATH;
    }

    public function login() {
        // Redirect to the proper auth login
        $this->redirect(BASE_URL . 'member/auth/login');
    }

    // Default action
    public function index() {
        $this->login();
    }
}