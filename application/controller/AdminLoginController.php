<?php
/**
 * Admin Login Controller - Redirects to AdminAuthController
 * This controller exists to handle direct access to /admin/login
 * and redirect to the proper /admin/auth/login URL
 */

require_once dirname(__FILE__) . '/BaseController.php';

class AdminLoginController extends BaseController {
    public function index() {
        // Redirect to the proper admin auth login page
        $this->redirect(BASE_URL . 'admin/auth/login');
    }

    public function login() {
        // Also handle /admin/login/login just in case
        $this->redirect(BASE_URL . 'admin/auth/login');
    }
}