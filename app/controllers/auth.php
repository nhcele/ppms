<?php
// app/controllers/auth.php

declare(strict_types=1);

function login_action(): string {
    if (current_user()) { redirect(base_url('index.php?page=dashboard')); }

    $errors = [];
    if (is_post()) {
        // Verify CSRF token first
        $token = $_POST[CSRF_TOKEN_KEY] ?? '';
        if (empty($token) || !verify_csrf_token($token)) {
            // Regenerate CSRF token on failure
            unset($_SESSION[CSRF_TOKEN_KEY]);
            $_SESSION['error'] = 'Invalid or expired security token. Please try again.';
            redirect(base_url('index.php?page=auth&action=login'));
            exit;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if ($username === '' || $password === '') {
            $errors[] = 'Username and password are required';
        } else {
            if (login($username, $password)) {
                // Regenerate session ID on successful login
                session_regenerate_id(true);
                redirect(base_url('index.php?page=dashboard'));
            } else {
                $errors[] = 'Invalid username or password';
            }
        }
    }

    return view('auth_login', [
        'errors' => $errors,
    ]);
}

function logout_action(): string {
    logout();
    redirect(base_url('index.php?page=auth&action=login'));
}
