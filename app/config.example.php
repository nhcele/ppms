<?php
// app/config.example.php - Copy this file to config.php and fill in your local values

declare(strict_types=1);

// Base configuration
const APP_ENV = 'dev'; // dev|prod

// Dynamic base URL construction
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = dirname($scriptName);
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}
define('BASE_URL', $protocol . $host . $basePath);

if (APP_ENV === 'dev') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'ppms_db');
define('DB_USER', 'root');
define('DB_PASS', 'YOUR_DB_PASSWORD');
define('DB_PORT', '3306');

// Session Configuration
const SESSION_NAME = 'PPMSSESSID';
const SESSION_LIFETIME = 86400;
const SESSION_SECURE = false; // Set to true in production with HTTPS
const SESSION_HTTPONLY = true;
const SESSION_SAMESITE = 'Strict';

// CSRF Protection
const CSRF_TOKEN_KEY = 'csrf_token';
const CSRF_TOKEN_LIFETIME = 3600;

// Session configuration must be set before session starts
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');

// Include session initialization
require_once __DIR__ . '/lib/session.php';

// Password Hashing
const PASSWORD_ALGO = PASSWORD_BCRYPT;
const PASSWORD_COST = 12;
const PASSWORD_VERIFY_TIMEOUT = 1;

// Initialize secure session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'secure' => SESSION_SECURE,
        'httponly' => SESSION_HTTPONLY,
        'samesite' => SESSION_SAMESITE
    ]);
    session_name(SESSION_NAME);
    session_start();
}

// Uploads
const UPLOAD_DIR = __DIR__ . '/../uploads';
const MAX_UPLOAD_BYTES = 104857600; // 100MB

// Mail
const MAIL_FROM_NAME = 'PPMS';
const MAIL_FROM_EMAIL = 'no-reply@example.com';
const SMTP_HOST = '';
const SMTP_PORT = 587;
const SMTP_USERNAME = '';
const SMTP_PASSWORD = '';
const SMTP_ENCRYPTION = 'tls';

// Timezone
date_default_timezone_set('UTC');
