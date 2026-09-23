<?php
// app/controllers/compliance.php - Manage candidate compliance data

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);

function view_action(): string {
    $id = (int)($_GET['candidate_id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid candidate'; }

    $pdo = db();
    
    // Get candidate info
    $c = $pdo->prepare('SELECT id, first_name, last_name FROM candidates WHERE id=:id');
    $c->execute([':id' => $id]);
    $cand = $c->fetch();
    if (!$cand) { http_response_code(404); return 'Candidate not found'; }

    // Get compliance data
    $row = $pdo->prepare('SELECT * FROM compliance WHERE candidate_id=:id');
    $row->execute([':id' => $id]);
    $comp = $row->fetch();

    // Get active alerts for this candidate
    $alerts = $pdo->prepare("SELECT * FROM alerts WHERE candidate_id=:id AND is_dismissed=0 ORDER BY alert_date");
    $alerts->execute([':id' => $id]);
    $active_alerts = $alerts->fetchAll();

    return view('compliance_view', [
        'cand' => $cand, 
        'comp' => $comp,
        'alerts' => $active_alerts
    ]);
}

function save_action(): string {
    if (!is_post()) { http_response_code(405); return 'Method not allowed'; }
    csrf_verify();

    $id = (int)($_GET['candidate_id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid candidate'; }

    $pdo = db();
    $data = [
        'visa_expiry' => ($_POST['visa_expiry'] ?? '') ?: null,
        'visa_status' => ($_POST['visa_status'] ?? '') ?: null,
        'work_permit_expiry' => ($_POST['work_permit_expiry'] ?? '') ?: null,
        'work_permit_status' => ($_POST['work_permit_status'] ?? '') ?: null,
        'contract_expiry' => ($_POST['contract_expiry'] ?? '') ?: null,
    ];

    // Validate dates
    $errors = [];
    foreach (['visa_expiry', 'work_permit_expiry', 'contract_expiry'] as $field) {
        if ($data[$field] && !validate_date($data[$field])) {
            $errors[$field] = 'Invalid date format';
        }
    }

    if ($errors) {
        flash('errors', $errors);
        redirect(base_url('index.php?page=compliance&action=view&candidate_id=' . $id));
    }

    // Check if record exists
    $exists = $pdo->prepare('SELECT id FROM compliance WHERE candidate_id=:id');
    $exists->execute([':id' => $id]);

    if ($exists->fetch()) {
        $upd = $pdo->prepare('UPDATE compliance SET 
            visa_expiry=:ve, visa_status=:vs, 
            work_permit_expiry=:wpe, work_permit_status=:wps, 
            contract_expiry=:ce, last_checked_at=NOW() 
            WHERE candidate_id=:id');
        $upd->execute([
            ':ve' => $data['visa_expiry'], 
            ':vs' => $data['visa_status'], 
            ':wpe' => $data['work_permit_expiry'], 
            ':wps' => $data['work_permit_status'], 
            ':ce' => $data['contract_expiry'], 
            ':id' => $id
        ]);
    } else {
        $ins = $pdo->prepare('INSERT INTO compliance (
            candidate_id, visa_expiry, visa_status, 
            work_permit_expiry, work_permit_status, 
            contract_expiry, last_checked_at
        ) VALUES (
            :id,:ve,:vs,:wpe,:wps,:ce,NOW()
        )');
        $ins->execute([
            ':id' => $id, 
            ':ve' => $data['visa_expiry'], 
            ':vs' => $data['visa_status'], 
            ':wpe' => $data['work_permit_expiry'], 
            ':wps' => $data['work_permit_status'], 
            ':ce' => $data['contract_expiry']
        ]);
    }

    // Update alerts for this candidate
    update_compliance_alerts($id);

    // Update candidate progress
    $update = $pdo->prepare('UPDATE candidates SET updated_by=:uid WHERE id=:id');
    $update->execute([':uid' => current_user()['id'], ':id' => $id]);

    flash('success', 'Compliance data saved');
    redirect(base_url('index.php?page=compliance&action=view&candidate_id=' . $id));
}

function update_compliance_alerts(int $candidate_id): void {
    $pdo = db();
    
    // Get compliance data
    $stmt = $pdo->prepare('SELECT * FROM compliance WHERE candidate_id=:id');
    $stmt->execute([':id' => $candidate_id]);
    $comp = $stmt->fetch();
    
    if (!$comp) return;
    
    // Clear existing alerts
    $pdo->prepare('DELETE FROM alerts WHERE candidate_id=:id')->execute([':id' => $candidate_id]);
    
    // Check for upcoming expiries and create alerts
    $today = new DateTime();
    $alert_types = [
        'visa_expiry' => 'visa_expiry',
        'work_permit_expiry' => 'work_permit_expiry',
        'contract_expiry' => 'contract_expiry'
    ];
    
    foreach ($alert_types as $field => $alert_type) {
        if (!empty($comp[$field])) {
            $expiry = new DateTime($comp[$field]);
            $interval = $today->diff($expiry);
            $days_left = (int)$interval->format('%r%a');
            
            if ($days_left <= 60) { // Alert for expiries within 60 days
                $alert_date = clone $expiry;
                $alert_date->modify("-30 days"); // Alert 30 days before expiry
                
                $stmt = $pdo->prepare('INSERT INTO alerts (
                    candidate_id, alert_type, alert_date, due_in_days
                ) VALUES (
                    :cid, :type, :date, :days
                )');
                $stmt->execute([
                    ':cid' => $candidate_id,
                    ':type' => $alert_type,
                    ':date' => $alert_date->format('Y-m-d'),
                    ':days' => $days_left
                ]);
            }
        }
    }
}
