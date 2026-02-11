<?php
/**
 * Base Controller
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__FILE__) . '/../config/config.php';

abstract class BaseController {
    protected $viewPath;
    protected $modelPath;

    public function __construct() {
        $this->viewPath = VIEW_PATH;
        $this->modelPath = MODEL_PATH;
        // Prevent caching of protected pages so back-button can't show stale authenticated pages
        if (!$this->isAuthController()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Expires: 0');

            // Don't check session in auth controllers
            $this->checkSession();
        }
    }

    protected function isAuthController() {
        $className = get_class($this);
        return strpos($className, 'AuthController') !== false;
    }

    protected function checkSession() {
        if (empty($_SESSION['user_id'])) {
            if ($this->isAdminController()) {
                header('Location: ' . BASE_URL . 'admin/auth/login');
            } else {
                header('Location: ' . BASE_URL . 'member/auth/login');
            }
            exit();
        }
    }

    protected function isAdminController() {
        $className = get_class($this);
        return strpos($className, 'Admin') !== false;
    }

    protected function loadModel($modelName) {
        $modelFile = $this->modelPath . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $modelName();
        }
        return null;
    }

    protected function loadView($viewName, $data = []) {
        extract($data);
        $viewFile = $this->viewPath . $viewName . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        }
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit();
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

    /**
     * Require AJAX-authenticated session. If no valid session, return 401 JSON and exit.
     */
    protected function requireAjaxAuth() {
        if (empty($_SESSION['user_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
            exit();
        }
    }

    protected function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            $userModel = $this->loadModel('UserModel');
            return $userModel->getUserById($_SESSION['user_id']);
        }
        return null;
    }

    protected function isAdmin() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'super';
    }

    protected function isMember() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'member';
    }
}
?>
