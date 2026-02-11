<?php
/**
 * Admin Authentication Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class AdminAuthController extends BaseController {
    private $userModel;

    public function __construct() {
        // Skip session check for login
        $this->viewPath = VIEW_PATH;
        $this->modelPath = MODEL_PATH;
        $this->userModel = new UserModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLogin();
        }
        $this->loadView('admin/auth/login');
    }

    private function handleLogin() {
        $email = htmlspecialchars($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->response('error', 'Email and password are required');
        }

        $user = $this->userModel->login($email, $password);

        if ($user && $user['user_type'] === 'super') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            
            return $this->response('success', 'Login successful', ['redirect' => BASE_URL . 'admin/dashboard']);
        } else {
            return $this->response('error', 'Invalid admin credentials');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect(BASE_URL . 'admin/auth/login');
    }

    protected function response($status, $message, $data = null) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }
}
