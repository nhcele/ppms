<?php
// app/controllers/job_applications.php - Manage candidate job applications

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

    // Get active job posts
    $jobs = db()->query('SELECT id, title FROM job_posts WHERE status=\'open\' ORDER BY title')->fetchAll();

    $errors = [];
    $data = [
        'job_post_id' => '',
        'status' => 'applied',
        'notes' => ''
    ];

    if (is_post()) {
        csrf_verify();
        $data = [
            'job_post_id' => (int)($_POST['job_post_id'] ?? 0),
            'status' => trim($_POST['status'] ?? 'applied'),
            'notes' => trim($_POST['notes'] ?? '')
        ];

        // Validate data
        if ($data['job_post_id'] <= 0) {
            $errors['job_post_id'] = 'Job selection is required';
        }
        if (!in_array($data['status'], ['applied', 'shortlisted', 'interviewed', 'offered', 'hired', 'rejected'])) {
            $errors['status'] = 'Invalid status';
        }

        if (!$errors) {
            $sql = "INSERT INTO candidate_job_applications (
                candidate_id, job_post_id, status, notes, applied_by
            ) VALUES (
                :cid, :job_id, :status, :notes, :uid
            )";

            $stmt = db()->prepare($sql);
            $stmt->execute([
                ':cid' => $candidate_id,
                ':job_id' => $data['job_post_id'],
                ':status' => $data['status'],
                ':notes' => $data['notes'] ?: null,
                ':uid' => current_user()['id']
            ]);

            // Update candidate progress
            $update = db()->prepare('UPDATE candidates SET updated_by=:uid WHERE id=:id');
            $update->execute([':uid' => current_user()['id'], ':id' => $candidate_id]);
            
            redirect(base_url('index.php?page=candidates&action=view&id=' . $candidate_id));
        }
    }

    return view('job_application_form', [
        'cand' => $cand,
        'jobs' => $jobs,
        'data' => $data,
        'errors' => $errors,
        'mode' => 'add'
    ]);
}

// Additional actions (edit, delete, list) would be implemented similarly
// ...
