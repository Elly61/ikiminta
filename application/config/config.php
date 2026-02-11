<?php
/**
 * IKIMINTA Configuration File
 */

define('APP_NAME', 'IKIMINTA');
define('APP_VERSION', '1.0.0');

// Environment - can be set via environment variable
$appEnv = getenv('APP_ENV') ?: 'development';
define('APP_ENV', $appEnv);

// Company Attribution
define('COMPANY_NAME', 'The Data');
define('COMPANY_POWERED_BY', 'Powered by The Data');
define('COMPANY_CEO', 'Kiza God');
define('COMPANY_FOUNDER', 'Nsengiyumva Elie');
define('COMPANY_EMAIL', 'nsengaelly61@gmail.com');
define('COMPANY_YEAR', '2026');

// Base URL - Dynamic for production
if (APP_ENV === 'production' || isset($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = ($scriptPath === '/' || $scriptPath === '\\') ? '/' : $scriptPath . '/';
    define('BASE_URL', $protocol . $host . $basePath);
} else {
    define('BASE_URL', 'http://localhost/ikiminta/');
}

// Paths
define('APP_PATH', dirname(dirname(__FILE__)) . '/');
define('BASE_PATH', dirname(APP_PATH) . '/');
define('VIEW_PATH', APP_PATH . 'views/');
define('MODEL_PATH', APP_PATH . 'model/');
define('CONTROLLER_PATH', APP_PATH . 'controller/');
define('LIBRARY_PATH', APP_PATH . 'libraries/');
define('THIRD_PARTY_PATH', APP_PATH . 'third_party/');
define('PUBLIC_PATH', dirname(dirname(APP_PATH)) . '/public/');

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Pagination
define('ITEMS_PER_PAGE', 10);

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'nsengaelly61@gmail.com');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_FROM', 'noreply@ikiminta.com');
define('SMTP_FROM_NAME', 'IKIMINTA');

// MOMO Payment Configuration
define('MOMO_API_KEY', 'your_momo_api_key');
define('MOMO_API_SECRET', 'your_momo_api_secret');
define('MOMO_MERCHANT_ID', 'your_merchant_id');
define('MOMO_CURRENCY', 'XOF');
define('MOMO_SANDBOX', true); // true for testing, false for production

// Blockchain Configuration
define('BLOCKCHAIN_ENABLED', true);
define('BLOCKCHAIN_API_URL', 'http://localhost:8080/api/');
define('BLOCKCHAIN_NETWORK', 'ethereum'); // ethereum, bitcoin, etc

// Currency Converter
define('DEFAULT_CURRENCY', 'RWF');
define('SUPPORTED_CURRENCIES', ['RWF', 'EUR', 'GBP', 'XOF']);

// Logging
define('LOG_PATH', dirname(dirname(APP_PATH)) . '/logs/');
define('LOG_LEVEL', 'debug'); // debug, info, warning, error

// Error Reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

// Session Configuration - Moved to BaseController
// session_start();
// ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);

// Create necessary directories if they don't exist (suppress errors)
if (!is_dir(LOG_PATH)) {
    @mkdir(LOG_PATH, 0755, true);
}
if (!is_dir(PUBLIC_PATH . 'uploads/')) {
    @mkdir(PUBLIC_PATH . 'uploads/', 0755, true);
}
if (!is_dir(PUBLIC_PATH . 'uploads/profile/')) {
    @mkdir(PUBLIC_PATH . 'uploads/profile/', 0755, true);
}

// Timezone
date_default_timezone_set('UTC');
