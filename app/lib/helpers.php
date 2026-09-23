<?php
// app/lib/helpers.php

declare(strict_types=1);

// Check if user has required permission/role
function check_permission($required_roles = 'admin') {
    if (!has_role($required_roles)) {
        set_flash_message('You do not have permission to access this page', 'danger');
        redirect('index.php?page=dashboard');
    }
}

// Include auth and rbac functions for user management
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/rbac.php';

/**
 * Advance a candidate to the next logical recruitment stage.
 * Returns the new status or null if already at final stage.
 */
function advance_candidate_stage(PDO $pdo, int $candidateId): ?string {
    $stageMap = [
        'basic_profile_created' => 'profile_in_progress',
        'profile_in_progress' => 'ready_for_selection',
        'ready_for_selection' => 'shortlisted',
        'shortlisted' => 'selected',
        'selected' => 'deployed',
        'deployed' => 'completed',
        'completed' => null
    ];
    
    $stmt = $pdo->prepare("SELECT status FROM candidates WHERE id = :id");
    $stmt->execute([':id' => $candidateId]);
    $current = $stmt->fetchColumn();
    
    if (!$current || !isset($stageMap[$current]) || $stageMap[$current] === null) {
        return null;
    }
    
    $newStatus = $stageMap[$current];
    $stmt = $pdo->prepare("UPDATE candidates SET status = :status, updated_by = :uid, updated_at = NOW() WHERE id = :id");
    $stmt->execute([
        ':status' => $newStatus,
        ':uid' => current_user()['id'] ?? null,
        ':id' => $candidateId
    ]);
    
    return $newStatus;
}

// Set flash message
function set_flash_message($message, $type = 'info') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Get flash message
function get_flash_message() {
    return $_SESSION['flash']['message'] ?? '';
}

// Get flash message type
function get_flash_message_type() {
    return $_SESSION['flash']['type'] ?? 'info';
}

// Check if there's a flash message
function has_flash_message() {
    return !empty($_SESSION['flash']);
}

// Clear flash message after displaying
function clear_flash_message() {
    unset($_SESSION['flash']);
}

// Get database connection
function get_db_connection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            die('Database connection failed. Please try again later.');
        }
    }
    
    return $pdo;
}

function base_url(string $path = ''): string {
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $path ? $base . '/' . $path : $base;
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function view(string $viewName, array $vars = []): string {
    extract($vars, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/../views/' . $viewName . '.php';
    return ob_get_clean();
}

function is_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }

function old(string $key, $default = '') {
    return $_POST[$key] ?? $default;
}

function flash(string $key, ?string $value = null) {
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return;
    }
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}

function get_status_badge_class(string $status): string {
    $statusClasses = [
        'link_created' => 'secondary',
        'link_sent' => 'info',
        'opened' => 'primary',
        'in_progress' => 'warning',
        'incomplete' => 'danger',
        'submitted' => 'success',
        'under_review' => 'info',
        'approved' => 'success',
        'correction_required' => 'danger',
        'expired' => 'secondary',
        'revoked' => 'dark',
        'completed' => 'success'
    ];
    return $statusClasses[$status] ?? 'secondary';
}

function format_file_size(int $bytes): string {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
