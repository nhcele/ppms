<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';

function login_action() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token
        $token = $_POST[CSRF_TOKEN_KEY] ?? '';
        if (!verify_csrf_token($token)) {
            $_SESSION['error'] = 'Invalid CSRF token. Please try again.';
            header('Location: ' . base_url('index.php?page=login'));
            exit;
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (login($username, $password)) {
            header('Location: ' . base_url('index.php?page=dashboard'));
            exit;
        } else {
            $_SESSION['error'] = 'Invalid username or password';
            header('Location: ' . base_url('index.php?page=login'));
            exit;
        }
    }
    
    // If not POST, redirect to login
    header('Location: ' . base_url('index.php?page=login'));
    exit;
}

function logout_action() {
    logout();
    header('Location: ' . base_url('index.php?page=login'));
    exit;
}

// Route the request
$action = $_GET['action'] ?? '';
switch ($action) {
    case 'login':
        login_action();
        break;
    case 'logout':
        logout_action();
        break;
    default:
        header('Location: ' . base_url('index.php?page=login'));
        exit;
}
