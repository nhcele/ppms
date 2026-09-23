<?php
// app/controllers/employers.php - employers management (mirrors agencies)

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);

function list_action(): string {
    $pdo = db();
    $rows = $pdo->query('SELECT id, name, contact_person, phone, email, is_active, created_at FROM employers ORDER BY name')->fetchAll();
    return view('employers_list', ['rows' => $rows]);
}

function create_action(): string {
    $pdo = db();
    $errors = [];
    $data = [
        'name'=>'','contact_person'=>'','phone'=>'','email'=>'','address'=>''
    ];
    if (is_post()) {
        csrf_verify();
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
        ];
        if ($data['name'] === '') { $errors['name'] = 'Name is required'; }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Invalid email'; }
        if ($data['phone'] !== '' && !preg_match('/^[+0-9()\-\s]{7,20}$/', $data['phone'])) { $errors['phone'] = 'Invalid phone number'; }
        if (!$errors) {
            $stmt = $pdo->prepare('INSERT INTO employers(
                name, contact_person, phone, email, address, is_active
            ) VALUES (
                :n, :cp, :ph, :em, :ad, 1
            )');
            $stmt->execute([
                ':n' => $data['name'],
                ':cp' => $data['contact_person'] ?: null,
                ':ph' => $data['phone'] ?: null,
                ':em' => $data['email'] ?: null,
                ':ad' => $data['address'] ?: null,
            ]);
            redirect(base_url('index.php?page=employers&action=list'));
        }
    }
    return view('employers_form', ['data' => $data, 'errors' => $errors, 'mode' => 'create']);
}

function edit_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    $row = $pdo->prepare('SELECT id, name, contact_person, phone, email, address, is_active, created_at, updated_at FROM employers WHERE id=:id');
    $row->execute([':id' => $id]);
    $employer = $row->fetch();
    if (!$employer) { http_response_code(404); return 'Employer not found'; }

    $errors = [];
    $is_active = (int)($employer['is_active'] ?? 1);
    $data = [
        'name' => $employer['name'] ?? '',
        'contact_person' => $employer['contact_person'] ?? '',
        'phone' => $employer['phone'] ?? '',
        'email' => $employer['email'] ?? '',
        'address' => $employer['address'] ?? '',
        'is_active' => $is_active,
    ];

    if (is_post()) {
        csrf_verify();
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
        ];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        if ($data['name'] === '') { $errors['name'] = 'Name is required'; }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Invalid email'; }
        if ($data['phone'] !== '' && !preg_match('/^[+0-9()\-\s]{7,20}$/', $data['phone'])) { $errors['phone'] = 'Invalid phone number'; }
        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE employers SET name=:n, contact_person=:cp, phone=:ph, email=:em, address=:ad, is_active=:a WHERE id=:id');
            $stmt->execute([
                ':n' => $data['name'],
                ':cp' => $data['contact_person'] ?: null,
                ':ph' => $data['phone'] ?: null,
                ':em' => $data['email'] ?: null,
                ':ad' => $data['address'] ?: null,
                ':a' => $is_active,
                ':id' => $id,
            ]);
            redirect(base_url('index.php?page=employers&action=list'));
        }
    }
    return view('employers_form', ['data' => $data, 'is_active' => $is_active, 'errors' => $errors, 'mode' => 'edit', 'id' => $id]);
}

function activate_action(): string {
    if (!is_post()) { http_response_code(405); return 'Method Not Allowed'; }
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    $stmt = db()->prepare('UPDATE employers SET is_active=1 WHERE id=:id');
    $stmt->execute([':id' => $id]);
    redirect(base_url('index.php?page=employers&action=list'));
}

function deactivate_action(): string {
    if (!is_post()) { http_response_code(405); return 'Method Not Allowed'; }
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    $stmt = db()->prepare('UPDATE employers SET is_active=0 WHERE id=:id');
    $stmt->execute([':id' => $id]);
    redirect(base_url('index.php?page=employers&action=list'));
}

function view_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }

    $row = $pdo->prepare('SELECT * FROM employers WHERE id=:id');
    $row->execute([':id' => $id]);
    $emp = $row->fetch();
    if (!$emp) { http_response_code(404); return 'Employer not found'; }

    // Optional: count deployments and interviews referencing this employer
    $depCnt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM deployments WHERE employer_id=:id');
    $depCnt->execute([':id' => $id]);
    $deployments_count = (int)($depCnt->fetch()['cnt'] ?? 0);

    $ivCnt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM interviews WHERE employer_id=:id');
    $ivCnt->execute([':id' => $id]);
    $interviews_count = (int)($ivCnt->fetch()['cnt'] ?? 0);

    return view('employers_view', [
        'emp' => $emp,
        'deployments_count' => $deployments_count,
        'interviews_count' => $interviews_count,
    ]);
}
