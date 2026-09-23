<?php
// app/lib/rbac.php - Role-based access control helpers

declare(strict_types=1);

function has_role($roles): bool {
    $user = current_user();
    if (!$user) return false;
    $roles = is_array($roles) ? $roles : [$roles];
    return in_array($user['role'], $roles, true);
}

function require_role($roles): void {
    if (!has_role($roles)) {
        http_response_code(403);
        exit('Forbidden');
    }
}
