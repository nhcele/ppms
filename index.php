<?php
// index.php - Front Controller & Simple Router

declare(strict_types=1);

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/lib/helpers.php';
require_once __DIR__ . '/app/lib/db.php';
require_once __DIR__ . '/app/lib/auth.php';
require_once __DIR__ . '/app/lib/csrf.php';
require_once __DIR__ . '/app/lib/rbac.php';

// Start session after config so ini settings in config.php are applied first
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Basic mapping of controllers
$controllerMap = [
    'auth' => __DIR__ . '/app/controllers/auth.php',
    'dashboard' => __DIR__ . '/app/controllers/dashboard.php',
    'users' => __DIR__ . '/app/controllers/users.php',
    'candidates' => __DIR__ . '/app/controllers/candidates.php',
    'documents' => __DIR__ . '/app/controllers/documents.php',
    'deployments' => __DIR__ . '/app/controllers/deployments.php',
    'compliance' => __DIR__ . '/app/controllers/compliance.php',
    'alerts' => __DIR__ . '/app/controllers/alerts.php',
    'reports' => __DIR__ . '/app/controllers/reports.php',
    'agencies' => __DIR__ . '/app/controllers/agencies.php',
    'employers' => __DIR__ . '/app/controllers/employers.php',
    'admin-seeds' => __DIR__ . '/app/controllers/admin_seeds.php',
    // Self-service portal
    'self-auth' => __DIR__ . '/app/controllers/self-auth.php',
    'self' => __DIR__ . '/app/controllers/self.php',
    'self-profile' => __DIR__ . '/app/controllers/self-profile.php',
    'self-docs' => __DIR__ . '/app/controllers/self-docs.php',
    // Interviews & calendar
    'interviews' => __DIR__ . '/app/controllers/interviews.php',
    'calendar' => __DIR__ . '/app/controllers/calendar.php',
    // Added mappings for Stage 2 forms
    'education' => __DIR__ . '/app/controllers/education.php',
    'work_experience' => __DIR__ . '/app/controllers/work_experience.php',
    'job_applications' => __DIR__ . '/app/controllers/job_applications.php',
    // Roles management (admin)
    'roles' => __DIR__ . '/app/controllers/roles.php',
    // Questionnaire system
    'questionnaire' => __DIR__ . '/app/controllers/questionnaire.php',
    'public-questionnaire' => __DIR__ . '/app/controllers/public_questionnaire.php',
    // Template management
    'templates' => __DIR__ . '/app/controllers/templates.php',
    // Questionnaire dashboard
    'questionnaire-dashboard' => __DIR__ . '/app/controllers/questionnaire.php',
];

if (!isset($controllerMap[$page])) {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

require_once $controllerMap[$page];

// Handle special case for questionnaire-dashboard
if ($page === 'questionnaire-dashboard') {
    $fn = 'dashboard_action';
} else {
    $normalizedAction = str_replace('-', '_', $action);
    $fn = $normalizedAction . '_action';
}
if (!function_exists($fn)) {
    http_response_code(404);
    echo 'Action not found';
    exit;
}

try {
    // Handle actions that require ID parameter
    if (in_array($action, ['edit', 'view', 'delete', 'reset-password', 'download-cv', 'generate-destination-cv', 'approve', 'revoke'])) {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo 'Missing ID parameter';
            exit;
        }
        echo $fn((int)$id);
    } else {
        echo $fn();
    }
} catch (Throwable $e) {
    error_log('[ERROR] ' . $e->getMessage());
    http_response_code(500);
    if (defined('APP_ENV') && APP_ENV === 'dev') {
        echo '<pre style="white-space:pre-wrap;">'.htmlspecialchars($e->getMessage().'\n'.$e->getTraceAsString(), ENT_QUOTES, 'UTF-8').'</pre>';
    } else {
        echo 'An error occurred. Please try again later.';
    }
}