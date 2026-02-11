<?php
/**
 * Member Authentication Controller
 */

require_once dirname(__FILE__) . '/BaseController.php';
require_once dirname(__FILE__) . '/../model/UserModel.php';

class MemberAuthController extends BaseController {
    private $userModel;

    public function __construct() {
        // Skip session check for login/register
        $this->viewPath = VIEW_PATH;
        $this->modelPath = MODEL_PATH;
        $this->userModel = new UserModel();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleRegister();
        }
        $this->loadView('member/auth/register');
    }

    private function handleRegister() {
        $username = htmlspecialchars($_POST['username'] ?? '');
        $email = htmlspecialchars($_POST['email'] ?? '');
        $firstName = htmlspecialchars($_POST['first_name'] ?? '');
        $lastName = htmlspecialchars($_POST['last_name'] ?? '');
        $legalId = htmlspecialchars($_POST['legal_id'] ?? '');
        $phoneNumber = htmlspecialchars($_POST['phone_number'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        $errors = [];

        if (empty($username)) $errors[] = 'Username is required';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
        if (strlen($legalId) !== 16) $errors[] = 'Legal ID must be exactly 16 characters';
        if (empty($phoneNumber)) $errors[] = 'Phone number is required';
        if (strlen($password) < PASSWORD_MIN_LENGTH) $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';

        if (!empty($errors)) {
            return $this->response('error', implode(', ', $errors));
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'legal_id' => $legalId,
            'phone_number' => $phoneNumber,
            'password' => $password
        ];

        $userId = $this->userModel->register($data);

        if ($userId) {
            return $this->response('success', 'Registration successful. Please login.', ['redirect' => BASE_URL . 'member/auth/login']);
        } else {
            return $this->response('error', 'Registration failed. Email or Legal ID may already exist.');
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleLogin();
        }
        $this->loadView('member/auth/login');
    }

    private function handleLogin() {
        $email = htmlspecialchars($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->response('error', 'Email and password are required');
        }

        $user = $this->userModel->login($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            
            return $this->response('success', 'Login successful', ['redirect' => BASE_URL . 'member/dashboard']);
        } else {
            return $this->response('error', 'Invalid email or password');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect(BASE_URL . 'member/auth/login');
    }
}
