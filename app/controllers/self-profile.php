<?php
// app/controllers/self-profile.php - Candidate Stage 1 profile editor

declare(strict_types=1);

require_once __DIR__ . '/../lib/validators.php';
require_once __DIR__ . '/self.php';

function sp_calculate_stage1_progress(array $c): int {
    // Minimal logic: if key fields present, set to at least 30%
    $required = [
        'first_name','last_name','dob','passport_number','passport_validity','phone','nationality','gender'
    ];
    foreach ($required as $k) {
        if (empty($c[$k])) return max(0, (int)($c['progress_percent'] ?? 0));
    }
    return max(30, (int)($c['progress_percent'] ?? 0));
}

function edit_action(): string {
    $cand = self_require_candidate();
    $errors = [];
    $data = $cand;

    if (is_post()) {
        csrf_verify();
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'dob' => trim($_POST['dob'] ?? ''),
            'passport_number' => trim($_POST['passport_number'] ?? ''),
            'passport_validity' => trim($_POST['passport_validity'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'nationality' => trim($_POST['nationality'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
        ];
        // Validate
        if (!v_required($data['first_name'])) $errors['first_name'] = 'First name is required';
        if (!v_required($data['last_name'])) $errors['last_name'] = 'Last name is required';
        if (!v_email($data['email'])) $errors['email'] = 'Invalid email';
        if (!v_date($data['dob'])) $errors['dob'] = 'Invalid date';
        if (!v_date($data['passport_validity'])) $errors['passport_validity'] = 'Invalid date';

        if (!$errors) {
            $pdo = db();
            $progress = sp_calculate_stage1_progress($data + ['progress_percent' => $cand['progress_percent']]);
            $status = $progress >= 30 ? 'basic_profile_created' : ($cand['status'] ?? 'basic_profile_created');
            $sql = 'UPDATE candidates SET first_name=:fn,last_name=:ln,email=:email,dob=:dob,passport_number=:pn,passport_validity=:pv,phone=:phone,nationality=:nat,gender=:gender,progress_percent=:pp,status=:st WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':fn' => $data['first_name'],
                ':ln' => $data['last_name'],
                ':email' => $data['email'] ?: null,
                ':dob' => $data['dob'] ?: null,
                ':pn' => $data['passport_number'] ?: null,
                ':pv' => $data['passport_validity'] ?: null,
                ':phone' => $data['phone'] ?: null,
                ':nat' => $data['nationality'] ?: null,
                ':gender' => $data['gender'] ?: null,
                ':pp' => $progress,
                ':st' => $status,
                ':id' => $cand['id'],
            ]);
            flash('success', 'Profile saved');
            redirect(base_url('index.php?page=self&action=dashboard'));
        }
    }

    return view('self_profile_edit', ['errors' => $errors, 'data' => $data]);
}
