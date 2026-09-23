<?php
// app/controllers/roles.php - Admin role management for job roles

declare(strict_types=1);

require_login();
require_role(['admin']);

function list_action(): string {
    $pdo = db();
    $rows = $pdo->query('SELECT id, name, is_active, created_at, updated_at FROM job_roles ORDER BY name')->fetchAll();
    return view('roles/list', ['rows' => $rows]);
}

function create_action(): string {
    $pdo = db();
    $errors = [];
    $data = ['name' => '', 'is_active' => 1];
    if (is_post()) {
        csrf_verify();
        $data['name'] = trim($_POST['name'] ?? '');
        $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        if ($data['name'] === '') { $errors['name'] = 'Role name is required'; }
        if (!$errors) {
            $stmt = $pdo->prepare('INSERT INTO job_roles (name, is_active) VALUES (:name, :active)');
            $stmt->execute([':name' => $data['name'], ':active' => $data['is_active']]);
            redirect(base_url('index.php?page=roles&action=list'));
        }
    }
    return view('roles/form', ['data' => $data, 'errors' => $errors, 'mode' => 'create']);
}

function edit_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM job_roles WHERE id=:id');
    $stmt->execute([':id' => $id]);
    $role = $stmt->fetch();
    if (!$role) { http_response_code(404); return 'Role not found'; }

    $errors = [];
    $data = ['name' => $role['name'], 'is_active' => (int)$role['is_active']];
    if (is_post()) {
        csrf_verify();
        $data['name'] = trim($_POST['name'] ?? '');
        $data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        if ($data['name'] === '') { $errors['name'] = 'Role name is required'; }
        if (!$errors) {
            $upd = $pdo->prepare('UPDATE job_roles SET name=:name, is_active=:active WHERE id=:id');
            $upd->execute([':name' => $data['name'], ':active' => $data['is_active'], ':id' => $id]);
            redirect(base_url('index.php?page=roles&action=list'));
        }
    }
    return view('roles/form', ['data' => $data, 'errors' => $errors, 'mode' => 'edit', 'id' => $id]);
}

function delete_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid id'; }
    // If role is referenced in candidate_roles, block deletion with a message
    $check = $pdo->prepare('SELECT COUNT(*) AS cnt FROM candidate_roles WHERE role_id=:id');
    $check->execute([':id' => $id]);
    if ((int)$check->fetch()['cnt'] > 0) {
        set_flash_message('Cannot delete a role that is assigned to candidates. Disable it instead.', 'danger');
        redirect(base_url('index.php?page=roles&action=list'));
    }
    $pdo->prepare('DELETE FROM job_roles WHERE id=:id')->execute([':id' => $id]);
    redirect(base_url('index.php?page=roles&action=list'));
}
