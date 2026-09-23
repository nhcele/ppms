<?php
// app/lib/csrf.php - CSRF token utilities

declare(strict_types=1);

function generate_csrf_token(): string {
    // Reuse existing token within lifetime; regenerate if missing/expired
    if (empty($_SESSION[CSRF_TOKEN_KEY]) || empty($_SESSION[CSRF_TOKEN_KEY.'_expire']) || $_SESSION[CSRF_TOKEN_KEY.'_expire'] < time()) {
        $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        $_SESSION[CSRF_TOKEN_KEY.'_expire'] = time() + CSRF_TOKEN_LIFETIME;
    }
    return $_SESSION[CSRF_TOKEN_KEY];
}

function verify_csrf_token(string $token): bool {
    if (empty($_SESSION[CSRF_TOKEN_KEY]) || 
        empty($_SESSION[CSRF_TOKEN_KEY.'_expire']) ||
        $_SESSION[CSRF_TOKEN_KEY.'_expire'] < time()) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
}

function csrf_field(): string {
    $token = generate_csrf_token();
    return '<input type="hidden" name="'.CSRF_TOKEN_KEY.'" value="'.htmlspecialchars($token, ENT_QUOTES, 'UTF-8').'">';
}

function csrf_verify(): void {
    $token = $_POST[CSRF_TOKEN_KEY] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(419);
        exit('CSRF token mismatch');
    }
}
