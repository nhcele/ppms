<?php
// app/controllers/interviews.php - Recruiter interviews scheduling

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);
require_once __DIR__ . '/../lib/validators.php';
require_once __DIR__ . '/../lib/mailer.php';

function interviews_list_action(): string {
    $pdo = db();
    $sql = "SELECT i.*, c.first_name, c.last_name, e.name AS employer
            FROM interviews i
            JOIN candidates c ON c.id=i.candidate_id
            JOIN employers e ON e.id=i.employer_id
            ORDER BY i.scheduled_at DESC LIMIT 200";
    $rows = $pdo->query($sql)->fetchAll();
    return view('interviews_list', ['rows' => $rows]);
}

function list_action(): string { return interviews_list_action(); }

function create_action(): string {
    $pdo = db();
    $errors = [];
    $data = [
        'candidate_id' => (int)($_GET['candidate_id'] ?? 0),
        'employer_id' => 0,
        'job_post_id' => '',
        'scheduled_at' => '',
        'duration_mins' => 30,
        'location' => '',
        'meeting_link' => '',
        'notes' => '',
        'notify' => '1',
    ];

    if (is_post()) {
        csrf_verify();
        $data['candidate_id'] = (int)($_POST['candidate_id'] ?? 0);
        $data['employer_id'] = (int)($_POST['employer_id'] ?? 0);
        $data['job_post_id'] = ($_POST['job_post_id'] ?? '') !== '' ? (int)$_POST['job_post_id'] : null;
        $data['scheduled_at'] = trim($_POST['scheduled_at'] ?? '');
        $data['duration_mins'] = (int)($_POST['duration_mins'] ?? 30);
        $data['location'] = trim($_POST['location'] ?? '');
        $data['meeting_link'] = trim($_POST['meeting_link'] ?? '');
        $data['notes'] = trim($_POST['notes'] ?? '');
        $data['notify'] = ($_POST['notify'] ?? '0') === '1' ? '1' : '0';

        if ($data['candidate_id'] <= 0) $errors['candidate_id'] = 'Candidate is required';
        if ($data['employer_id'] <= 0) $errors['employer_id'] = 'Employer is required';
        if (!$data['scheduled_at'] || !v_date($data['scheduled_at'])) $errors['scheduled_at'] = 'Valid date/time required';
        if ($data['location'] === '' && $data['meeting_link'] === '') $errors['location'] = 'Location or meeting link required';

        if (!$errors) {
            // Basic overlap warning: not enforced here
            $ins = $pdo->prepare("INSERT INTO interviews (candidate_id, employer_id, job_post_id, scheduled_at, duration_mins, location, meeting_link, status, created_by, notes)
                                  VALUES (:cid,:eid,:jid,:at,:dur,:loc,:link,'proposed',:uid,:notes)");
            $ins->execute([
                ':cid' => $data['candidate_id'],
                ':eid' => $data['employer_id'],
                ':jid' => $data['job_post_id'],
                ':at' => $data['scheduled_at'],
                ':dur' => $data['duration_mins'],
                ':loc' => $data['location'] ?: null,
                ':link' => $data['meeting_link'] ?: null,
                ':uid' => current_user()['id'],
                ':notes' => $data['notes'] ?: null,
            ]);
            $id = (int)$pdo->lastInsertId();

            if ($data['notify'] === '1') {
                // Send simple invite to candidate email if available
                $c = $pdo->prepare('SELECT c.email, CONCAT(c.first_name, " ", c.last_name) AS name FROM candidates c WHERE c.id=:id');
                $c->execute([':id' => $data['candidate_id']]);
                if ($cand = $c->fetch()) {
                    $subject = 'Interview Invitation';
                    $viewUrl = base_url('index.php?page=self&action=dashboard');
                    $html = '<p>Hello ' . htmlspecialchars($cand['name']) . ',</p>' .
                            '<p>You have been invited to an interview on ' . htmlspecialchars($data['scheduled_at']) . '.</p>' .
                            '<p>Please log into your portal for details: <a href="'.$viewUrl.'">Portal</a></p>';
                    send_mail($cand['email'] ?? '', $cand['name'] ?? '', $subject, $html, strip_tags($html));
                }
            }

            redirect(base_url('index.php?page=interviews&action=view&id=' . $id));
        }
    }

    $cands = $pdo->query('SELECT id, first_name, last_name FROM candidates ORDER BY created_at DESC LIMIT 200')->fetchAll();
    $emps = $pdo->query('SELECT id, name FROM employers WHERE is_active=1 ORDER BY name')->fetchAll();
    $jobs = $pdo->query('SELECT id, title FROM job_posts WHERE status=\'open\' ORDER BY created_at DESC LIMIT 200')->fetchAll();
    return view('interviews_form', ['data' => $data, 'errors' => $errors, 'cands' => $cands, 'emps' => $emps, 'jobs' => $jobs, 'mode' => 'create']);
}

function edit_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    $row = $pdo->prepare('SELECT * FROM interviews WHERE id=:id');
    $row->execute([':id' => $id]);
    $iv = $row->fetch();
    if (!$iv) { http_response_code(404); return 'Interview not found'; }

    $errors = [];
    $data = $iv;
    if (is_post()) {
        csrf_verify();
        $data['employer_id'] = (int)($_POST['employer_id'] ?? $iv['employer_id']);
        $data['job_post_id'] = ($_POST['job_post_id'] ?? '') !== '' ? (int)$_POST['job_post_id'] : null;
        $data['scheduled_at'] = trim($_POST['scheduled_at'] ?? '');
        $data['duration_mins'] = (int)($_POST['duration_mins'] ?? 30);
        $data['location'] = trim($_POST['location'] ?? '');
        $data['meeting_link'] = trim($_POST['meeting_link'] ?? '');
        $data['notes'] = trim($_POST['notes'] ?? '');

        if ($data['employer_id'] <= 0) $errors['employer_id'] = 'Employer is required';
        if (!$data['scheduled_at'] || !v_date($data['scheduled_at'])) $errors['scheduled_at'] = 'Valid date/time required';
        if ($data['location'] === '' && $data['meeting_link'] === '') $errors['location'] = 'Location or meeting link required';

        if (!$errors) {
            $upd = $pdo->prepare('UPDATE interviews SET employer_id=:eid, job_post_id=:jid, scheduled_at=:at, duration_mins=:dur, location=:loc, meeting_link=:link, updated_by=:uid, notes=:notes WHERE id=:id');
            $upd->execute([
                ':eid' => $data['employer_id'],
                ':jid' => $data['job_post_id'],
                ':at' => $data['scheduled_at'],
                ':dur' => $data['duration_mins'],
                ':loc' => $data['location'] ?: null,
                ':link' => $data['meeting_link'] ?: null,
                ':uid' => current_user()['id'],
                ':notes' => $data['notes'] ?: null,
                ':id' => $id,
            ]);
            redirect(base_url('index.php?page=interviews&action=view&id=' . $id));
        }
    }

    $emps = $pdo->query('SELECT id, name FROM employers WHERE is_active=1 ORDER BY name')->fetchAll();
    $jobs = $pdo->query('SELECT id, title FROM job_posts WHERE status=\'open\' ORDER BY created_at DESC LIMIT 200')->fetchAll();
    return view('interviews_form', ['data' => $data, 'errors' => $errors, 'cands' => [], 'emps' => $emps, 'jobs' => $jobs, 'mode' => 'edit']);
}

function view_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT i.*, c.first_name, c.last_name, c.email AS cand_email, e.name AS employer
                            FROM interviews i JOIN candidates c ON c.id=i.candidate_id JOIN employers e ON e.id=i.employer_id WHERE i.id=:id");
    $stmt->execute([':id' => $id]);
    $iv = $stmt->fetch();
    if (!$iv) { http_response_code(404); return 'Not found'; }
    return view('interviews_view', ['iv' => $iv]);
}

function cancel_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('UPDATE interviews SET status=\'cancelled\', updated_by=:uid WHERE id=:id');
    $stmt->execute([':uid' => current_user()['id'], ':id' => $id]);
    redirect(base_url('index.php?page=interviews&action=view&id=' . $id));
}
