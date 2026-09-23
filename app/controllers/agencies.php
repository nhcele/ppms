<?php
// app/controllers/agencies.php - minimal agencies management

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);

function list_action(): string {
    $pdo = db();
    $rows = $pdo->query('SELECT id, name, contact_person, phone, email, is_active, created_at FROM agencies ORDER BY name')->fetchAll();
    return view('agencies_list', ['rows' => $rows]);
}

function create_action(): string {
    $pdo = db();
    $errors = [];
    $data = [
        'name'=>'','contact_person'=>'','phone'=>'','email'=>'','address'=>'',
        'address_line1'=>'','address_line2'=>'','city'=>'','state'=>'','postal_code'=>'','country'=>''
    ];
    if (is_post()) {
        csrf_verify();
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'address_line1' => trim($_POST['address_line1'] ?? ''),
            'address_line2' => trim($_POST['address_line2'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'state' => trim($_POST['state'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
        ];
        if ($data['name'] === '') { $errors['name'] = 'Name is required'; }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Invalid email'; }
        if ($data['phone'] !== '' && !preg_match('/^[+0-9()\-\s]{7,20}$/', $data['phone'])) { $errors['phone'] = 'Invalid phone number'; }
        if ($data['postal_code'] !== '' && !preg_match('/^[A-Za-z0-9 \-]{3,12}$/', $data['postal_code'])) { $errors['postal_code'] = 'Invalid postal code'; }
        if (!$errors) {
            $stmt = $pdo->prepare('INSERT INTO agencies(
                name, contact_person, phone, email, address, address_line1, address_line2, city, state, postal_code, country, is_active
            ) VALUES (
                :n, :cp, :ph, :em, :ad, :l1, :l2, :city, :state, :pc, :country, 1
            )');
            $stmt->execute([
                ':n' => $data['name'],
                ':cp' => $data['contact_person'] ?: null,
                ':ph' => $data['phone'] ?: null,
                ':em' => $data['email'] ?: null,
                ':ad' => $data['address'] ?: null,
                ':l1' => $data['address_line1'] ?: null,
                ':l2' => $data['address_line2'] ?: null,
                ':city' => $data['city'] ?: null,
                ':state' => $data['state'] ?: null,
                ':pc' => $data['postal_code'] ?: null,
                ':country' => $data['country'] ?: null,
            ]);
            redirect(base_url('index.php?page=agencies&action=list'));
        }
    }
    return view('agencies_form', ['data' => $data, 'errors' => $errors, 'mode' => 'create']);
}

function edit_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    $row = $pdo->prepare('SELECT id, name, contact_person, phone, email, address, is_active FROM agencies WHERE id=:id');
    $row->execute([':id' => $id]);
    $agency = $row->fetch();
    if (!$agency) { http_response_code(404); return 'Agency not found'; }

    $errors = [];
    $is_active = (int)$agency['is_active'];
    $data = [
        'name' => $agency['name'],
        'contact_person' => $agency['contact_person'] ?? '',
        'phone' => $agency['phone'] ?? '',
        'email' => $agency['email'] ?? '',
        'address' => $agency['address'] ?? '',
        'address_line1' => $agency['address_line1'] ?? '',
        'address_line2' => $agency['address_line2'] ?? '',
        'city' => $agency['city'] ?? '',
        'state' => $agency['state'] ?? '',
        'postal_code' => $agency['postal_code'] ?? '',
        'country' => $agency['country'] ?? '',
    ];

    if (is_post()) {
        csrf_verify();
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'contact_person' => trim($_POST['contact_person'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'address_line1' => trim($_POST['address_line1'] ?? ''),
            'address_line2' => trim($_POST['address_line2'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'state' => trim($_POST['state'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
        ];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        if ($data['name'] === '') { $errors['name'] = 'Name is required'; }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Invalid email'; }
        if ($data['phone'] !== '' && !preg_match('/^[+0-9()\-\s]{7,20}$/', $data['phone'])) { $errors['phone'] = 'Invalid phone number'; }
        if ($data['postal_code'] !== '' && !preg_match('/^[A-Za-z0-9 \-]{3,12}$/', $data['postal_code'])) { $errors['postal_code'] = 'Invalid postal code'; }
        if (!$errors) {
            $stmt = $pdo->prepare('UPDATE agencies SET name=:n, contact_person=:cp, phone=:ph, email=:em, address=:ad, address_line1=:l1, address_line2=:l2, city=:city, state=:state, postal_code=:pc, country=:country, is_active=:a WHERE id=:id');
            $stmt->execute([
                ':n' => $data['name'],
                ':cp' => $data['contact_person'] ?: null,
                ':ph' => $data['phone'] ?: null,
                ':em' => $data['email'] ?: null,
                ':ad' => $data['address'] ?: null,
                ':l1' => $data['address_line1'] ?: null,
                ':l2' => $data['address_line2'] ?: null,
                ':city' => $data['city'] ?: null,
                ':state' => $data['state'] ?: null,
                ':pc' => $data['postal_code'] ?: null,
                ':country' => $data['country'] ?: null,
                ':a' => $is_active,
                ':id' => $id,
            ]);
            redirect(base_url('index.php?page=agencies&action=list'));
        }
    }
    return view('agencies_form', ['data' => $data, 'is_active' => $is_active, 'errors' => $errors, 'mode' => 'edit', 'id' => $id]);
}

function activate_action(): string {
    if (!is_post()) { http_response_code(405); return 'Method Not Allowed'; }
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    $stmt = db()->prepare('UPDATE agencies SET is_active=1 WHERE id=:id');
    $stmt->execute([':id' => $id]);
    redirect(base_url('index.php?page=agencies&action=list'));
}

function deactivate_action(): string {
    if (!is_post()) { http_response_code(405); return 'Method Not Allowed'; }
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    $stmt = db()->prepare('UPDATE agencies SET is_active=0 WHERE id=:id');
    $stmt->execute([':id' => $id]);
    redirect(base_url('index.php?page=agencies&action=list'));
}
