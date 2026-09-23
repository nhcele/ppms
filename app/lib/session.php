<?php
// app/lib/session.php - Session management

declare(strict_types=1);

function init_session(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        // Set session name and parameters
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => SESSION_SECURE,
            'httponly' => SESSION_HTTPONLY,
            'samesite' => SESSION_SAMESITE
        ]);
        
        // Start the session
        session_start();
    }
}

// Initialize session when this file is included
init_session();
