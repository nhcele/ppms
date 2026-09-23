<?php
// app/controllers/education.php - Manage candidate education records

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);

function add_action(): string {
    $candidate_id = (int)($_GET['candidate_id'] ?? 0);
    if ($candidate_id <= 0) { http_response_code(400); return 'Invalid candidate'; }
    
    // Verify candidate exists
    $stmt = db()->prepare('SELECT id, first_name, last_name FROM candidates WHERE id=:id');
    $stmt->execute([':id' => $candidate_id]);
    $cand = $stmt->fetch();
    if (!$cand) { http_response_code(404); return 'Candidate not found'; }

    $errors = [];
    $data = [
        'institution_name' => '',
        'degree' => '',
        'field_of_study' => '',
        'start_date' => '',
        'end_date' => '',
        'is_current' => 0,
        'description' => ''
    ];

    if (is_post()) {
        csrf_verify();
        $data = [
            'institution_name' => trim($_POST['institution_name'] ?? ''),
            'degree' => trim($_POST['degree'] ?? ''),
            'field_of_study' => trim($_POST['field_of_study'] ?? ''),
            'start_date' => trim($_POST['start_date'] ?? ''),
            'end_date' => trim($_POST['end_date'] ?? ''),
            'is_current' => isset($_POST['is_current']) ? 1 : 0,
            'description' => trim($_POST['description'] ?? '')
        ];

        // Validate data
        if (empty($data['institution_name'])) {
            $errors['institution_name'] = 'Institution name is required';
        }
        if (empty($data['degree'])) {
            $errors['degree'] = 'Degree is required';
        }

        if (!$errors) {
            $sql = "INSERT INTO candidate_education (
                candidate_id, institution_name, degree, field_of_study, 
                start_date, end_date, is_current, description
            ) VALUES (
                :cid, :institution, :degree, :field, :start, :end, :current, :desc
            )";

            $stmt = db()->prepare($sql);
            $stmt->execute([
                ':cid' => $candidate_id,
                ':institution' => $data['institution_name'],
                ':degree' => $data['degree'],
                ':field' => $data['field_of_study'] ?: null,
                ':start' => $data['start_date'] ?: null,
                ':end' => $data['is_current'] ? null : ($data['end_date'] ?: null),
                ':current' => $data['is_current'],
                ':desc' => $data['description'] ?: null
            ]);

            // Update candidate progress
            $update = db()->prepare('UPDATE candidates SET updated_by=:uid WHERE id=:id');
            $update->execute([':uid' => current_user()['id'], ':id' => $candidate_id]);
            
            redirect(base_url('index.php?page=candidates&action=view&id=' . $candidate_id));
        }
    }

    return view('education_form', [
        'cand' => $cand,
        'data' => $data,
        'errors' => $errors,
        'mode' => 'add'
    ]);
}

// Additional actions (edit, delete, list) would be implemented similarly
// ...
