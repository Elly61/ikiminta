<?php
/**
 * Main Router - index.php
 * This is the entry point for all requests
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting
if (defined('APP_ENV') && APP_ENV === 'development') {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
}

// Include configuration
require_once dirname(__FILE__) . '/application/config/config.php';
require_once dirname(__FILE__) . '/application/config/Database.php';
require_once dirname(__FILE__) . '/application/controller/BaseController.php';

// Simple URL routing
// Determine request path robustly (support setups where PATH_INFO isn't populated)
    // Prefer PATH_INFO but fall back to REQUEST_URI and strip script name/index.php
    if (!empty($_SERVER['PATH_INFO'])) {
        $request_uri = $_SERVER['PATH_INFO'];
    } else {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
        // remove query string
        $request_uri = parse_url($request_uri, PHP_URL_PATH);
        // remove script name (e.g., /index.php) or script directory if present
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if ($script && strpos($request_uri, $script) === 0) {
            $request_uri = substr($request_uri, strlen($script));
        }
        // remove any remaining /index.php occurrences
        $request_uri = preg_replace('#/index\.php#', '', $request_uri);
    }
    $request_uri = trim($request_uri, '/');

// If empty, redirect to member login
if ($request_uri === '') {
    header('Location: ' . BASE_URL . 'member/auth/login');
    exit;
}

// Split path into segments and ignore any 'index.php' or 'index' segments
$raw_parts = explode('/', $request_uri);
$parts = array_values(array_filter($raw_parts, function($p) {
    $p = trim($p);
    if ($p === '') return false;
    $lower = strtolower($p);
    return $lower !== 'index.php' && $lower !== 'index';
}));

// Parse request
$section = $parts[0] ?? 'member';
$controller = $parts[1] ?? 'auth';
$action = $parts[2] ?? ($controller === 'auth' ? 'login' : 'index');
$params = array_slice($parts, 3);

// Sanitize input
$section = preg_replace('/[^a-zA-Z0-9]/', '', $section);
$controller = preg_replace('/[^a-zA-Z0-9]/', '', $controller);
$action = preg_replace('/[^a-zA-Z0-9]/', '', $action);

// Load appropriate controller
$controller_class = ucfirst($section) . ucfirst($controller) . 'Controller';
$controller_file = CONTROLLER_PATH . $controller_class . '.php';

try {
    if (file_exists($controller_file)) {
        require_once $controller_file;
        
        if (class_exists($controller_class)) {
            $controller_instance = new $controller_class();
            
            // Call action method
            if (method_exists($controller_instance, $action)) {
                if (!empty($params)) {
                    call_user_func_array([$controller_instance, $action], $params);
                } else {
                    $controller_instance->$action();
                }
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "Error 404: Action '$action' not found in $controller_class";
                error_log("Action not found: $controller_class::$action");
            }
        } else {
            header("HTTP/1.0 500 Internal Server Error");
            echo "Error 500: Controller class '$controller_class' not found";
            error_log("Controller class not found: $controller_class");
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Error 404: Controller file not found: $controller_file";
        error_log("Controller file not found: $controller_file");
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo "Error 500: " . $e->getMessage();
    error_log("Router Exception: " . $e->getMessage());
}
