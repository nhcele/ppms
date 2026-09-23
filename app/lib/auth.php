<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../config.php';

// app/lib/auth.php - Authentication helpers

function login(string $username, string $password): bool {
    // Input validation
    if (empty($username) || empty($password)) {
        error_log('Login attempt with empty credentials');
        return false;
    }

    // Get user
    $stmt = db()->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        error_log("User not found: {$username}");
        return false;
    }
    
    if (!$user['is_active']) {
        error_log("Inactive account: {$username}");
        return false;
    }
    
    // Password verification with timeout
    $start = microtime(true);
    $valid = password_verify($password, $user['password_hash']);
    $time = microtime(true) - $start;
    
    if ($time > PASSWORD_VERIFY_TIMEOUT) {
        error_log("Potential timing attack on user: {$username}");
    }
    
    if (!$valid) {
        error_log("Invalid password for: {$username}");
        return false;
    }

    // Check if hash needs rehash
    if (password_needs_rehash($user['password_hash'], PASSWORD_ALGO, ['cost' => PASSWORD_COST])) {
        $newHash = password_hash($password, PASSWORD_ALGO, ['cost' => PASSWORD_COST]);
        db()->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$newHash, $user['id']]);
    }
    
    // Session is already started by session.php
    
    $_SESSION['user'] = $user;
    session_regenerate_id(true);
    
    return true;
}

function logout(): void {
    session_unset();
    session_destroy();
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        redirect(base_url('index.php?page=auth&action=login'));
    }
}
