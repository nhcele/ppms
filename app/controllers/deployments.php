<?php
// app/controllers/deployments.php - Manage deployments

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);

require_once __DIR__ . '/../lib/file_upload.php';
require_once __DIR__ . '/../lib/video_helper.php';

function list_action(): string {
    $pdo = db();
    // Sorting
    $sortable = ['start_date','end_date','status','employer','agency','salary_amount','created_at'];
    $sort = $_GET['sort'] ?? 'created_at';
    $dir = strtolower($_GET['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
    if (!in_array($sort, $sortable, true)) { $sort = 'created_at'; }
    $orderBy = match($sort) {
        'employer' => 'e.name',
        'agency' => 'a.name',
        default => 'd.' . ($sort === 'created_at' ? 'created_at' : $sort),
    };

    // Pagination
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min(100, max(10, (int)($_GET['per'] ?? 20)));
    $offset = ($page - 1) * $perPage;

    $count = $pdo->query("SELECT COUNT(*) AS cnt FROM deployments")->fetch();
    $total = (int)$count['cnt'];

    $sql = "SELECT d.*, c.first_name, c.last_name, e.name AS employer, a.name AS agency
            FROM deployments d
            JOIN candidates c ON c.id=d.candidate_id
            JOIN employers e ON e.id=d.employer_id
            LEFT JOIN agencies a ON a.id=d.agency_id
            ORDER BY $orderBy $dir
            LIMIT :lim OFFSET :off";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $pages = (int)ceil($total / $perPage);
    return view('deployments_list', [
        'rows' => $rows,
        'sort' => $sort,
        'dir' => strtolower($dir),
        'page' => $page,
        'pages' => $pages,
        'per' => $perPage,
        'total' => $total,
    ]);
}

function create_action(): string {
    $pdo = db();
    $errors = [];
    $data = [
        'candidate_id' => (int)($_GET['candidate_id'] ?? 0),
        'employer_id' => 0,
        'agency_id' => '',
        'location_country' => '',
        'location_city' => '',
        'location_site' => '',
        'start_date' => '',
        'end_date' => '',
        'salary_amount' => '',
        'salary_currency' => 'USD',
        'status' => 'planned',
    ];

    if (is_post()) {
        csrf_verify();
        $data['candidate_id'] = (int)($_POST['candidate_id'] ?? 0);
        $data['employer_id'] = (int)($_POST['employer_id'] ?? 0);
        $data['agency_id'] = ($_POST['agency_id'] ?? '') !== '' ? (int)$_POST['agency_id'] : null;
        $data['location_country'] = trim($_POST['location_country'] ?? '');
        $data['location_city'] = trim($_POST['location_city'] ?? '');
        $data['location_site'] = trim($_POST['location_site'] ?? '');
        $data['start_date'] = trim($_POST['start_date'] ?? '');
        $data['end_date'] = trim($_POST['end_date'] ?? '');
        $data['salary_amount'] = trim($_POST['salary_amount'] ?? '');
        $data['salary_currency'] = trim($_POST['salary_currency'] ?? 'USD');
        $data['status'] = 'planned';
        // Work Permit
        $data['work_permit_status'] = trim($_POST['work_permit_status'] ?? 'not_applied');
        $data['work_permit_expiry'] = trim($_POST['work_permit_expiry'] ?? '');
        $data['contract_expiry'] = trim($_POST['contract_expiry'] ?? '');

        // Handle file uploads
        $visa_reference_file = null;
        $signed_contract_file = null;
        
        if (isset($_FILES['visa_reference']) && $_FILES['visa_reference']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $uploadDir = 'uploads/deployments/' . $data['candidate_id'];
                $upload = handle_upload($_FILES['visa_reference'], $uploadDir);
                $visa_reference_file = $upload['path'];
                
                // Generate thumbnail for video files
                if (is_video_file($upload['original'])) {
                    $thumbnailPath = get_video_thumbnail_path($upload['path']);
                    generate_video_thumbnail($upload['path'], $thumbnailPath);
                }
            } catch (Exception $e) {
                $errors['visa_reference'] = 'Visa reference upload failed: ' . $e->getMessage();
            }
        }
        
        if (isset($_FILES['signed_contract']) && $_FILES['signed_contract']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $uploadDir = 'uploads/deployments/' . $data['candidate_id'];
                $upload = handle_upload($_FILES['signed_contract'], $uploadDir);
                $signed_contract_file = $upload['path'];
                
                // Generate thumbnail for video files
                if (is_video_file($upload['original'])) {
                    $thumbnailPath = get_video_thumbnail_path($upload['path']);
                    generate_video_thumbnail($upload['path'], $thumbnailPath);
                }
            } catch (Exception $e) {
                $errors['signed_contract'] = 'Signed contract upload failed: ' . $e->getMessage();
            }
        }

        if ($data['candidate_id'] <= 0) $errors['candidate_id'] = 'Candidate is required';
        if ($data['employer_id'] <= 0) $errors['employer_id'] = 'Employer is required';
        if (!$data['start_date']) $errors['start_date'] = 'Start date is required';

        if (!$errors) {
            $ins = $pdo->prepare("INSERT INTO deployments (candidate_id, employer_id, agency_id, location_country, location_city, location_site, start_date, end_date, salary_amount, salary_currency, status, visa_reference_file, signed_contract_file)
                                  VALUES (:cid,:eid,:aid,:country,:city,:site,:start,:end,:salary,:currency,'planned',:visa_ref,:contract)");
            $ins->execute([
                ':cid' => $data['candidate_id'],
                ':eid' => $data['employer_id'],
                ':aid' => $data['agency_id'],
                ':country' => $data['location_country'] ?: null,
                ':city' => $data['location_city'] ?: null,
                ':site' => $data['location_site'] ?: null,
                ':start' => $data['start_date'],
                ':end' => $data['end_date'] ?: null,
                ':salary' => $data['salary_amount'] !== '' ? $data['salary_amount'] : null,
                ':currency' => $data['salary_currency'] ?: null,
                ':visa_ref' => $visa_reference_file,
                ':contract' => $signed_contract_file,
            ]);
            $id = (int)$pdo->lastInsertId();

            // Save compliance for candidate (work permit and contract only)
            $check = $pdo->prepare('SELECT id FROM compliance WHERE candidate_id=:cid ORDER BY updated_at DESC, id DESC LIMIT 1');
            $check->execute([':cid' => (int)$data['candidate_id']]);
            if ($row = $check->fetch()) {
                $updC = $pdo->prepare('UPDATE compliance SET work_permit_expiry=:wexp, work_permit_status=:wstat, contract_expiry=:cexp, updated_at=CURRENT_TIMESTAMP WHERE id=:id');
                $updC->execute([
                    ':wexp' => $data['work_permit_expiry'] ?: null,
                    ':wstat' => $data['work_permit_status'] ?: 'not_applied',
                    ':cexp' => $data['contract_expiry'] ?: null,
                    ':id' => (int)$row['id'],
                ]);
            } else {
                $insC = $pdo->prepare('INSERT INTO compliance (candidate_id, work_permit_expiry, work_permit_status, contract_expiry) VALUES (:cid, :wexp, :wstat, :cexp)');
                $insC->execute([
                    ':cid' => (int)$data['candidate_id'],
                    ':wexp' => $data['work_permit_expiry'] ?: null,
                    ':wstat' => $data['work_permit_status'] ?: 'not_applied',
                    ':cexp' => $data['contract_expiry'] ?: null,
                ]);
            }

            redirect(base_url('index.php?page=deployments&action=view&id=' . $id));
        }
    }

    $cands = $pdo->query('SELECT id, first_name, last_name FROM candidates ORDER BY created_at DESC LIMIT 200')->fetchAll();
    $emps = $pdo->query('SELECT id, name FROM employers WHERE is_active=1 ORDER BY name')->fetchAll();
    $agencies = $pdo->query('SELECT id, name FROM agencies WHERE is_active=1 ORDER BY name')->fetchAll();
    
    // Prefill work permit from latest compliance if candidate preselected
    if (!empty($data['candidate_id'])) {
        $cs = $pdo->prepare('SELECT work_permit_expiry, work_permit_status, contract_expiry FROM compliance WHERE candidate_id=:cid ORDER BY updated_at DESC, id DESC LIMIT 1');
        $cs->execute([':cid' => (int)$data['candidate_id']]);
        if ($row = $cs->fetch()) {
            $data['work_permit_status'] = $data['work_permit_status'] ?? $row['work_permit_status'];
            $data['work_permit_expiry'] = $data['work_permit_expiry'] ?? $row['work_permit_expiry'];
            $data['contract_expiry'] = $data['contract_expiry'] ?? $row['contract_expiry'];
        }
    }

    return view('deployments_form', ['data' => $data, 'errors' => $errors, 'cands' => $cands, 'emps' => $emps, 'agencies' => $agencies, 'mode' => 'create']);
}

function edit_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    $row = $pdo->prepare('SELECT * FROM deployments WHERE id=:id');
    $row->execute([':id' => $id]);
    $dep = $row->fetch();
    if (!$dep) { http_response_code(404); return 'Deployment not found'; }

    $errors = [];
    $data = $dep;
    if (is_post()) {
        csrf_verify();
        $data['employer_id'] = (int)($_POST['employer_id'] ?? $dep['employer_id']);
        $data['agency_id'] = ($_POST['agency_id'] ?? '') !== '' ? (int)$_POST['agency_id'] : null;
        $data['location_country'] = trim($_POST['location_country'] ?? '');
        $data['location_city'] = trim($_POST['location_city'] ?? '');
        $data['location_site'] = trim($_POST['location_site'] ?? '');
        $data['start_date'] = trim($_POST['start_date'] ?? '');
        $data['end_date'] = trim($_POST['end_date'] ?? '');
        $data['salary_amount'] = trim($_POST['salary_amount'] ?? '');
        $data['salary_currency'] = trim($_POST['salary_currency'] ?? 'USD');
        $data['status'] = trim($_POST['status'] ?? $dep['status']);
        // Normalize UI status labels to DB enum
        $statusMap = [
            'Draft' => 'planned',
            'Pending' => 'planned',
            'Active' => 'active',
            'On Hold' => 'planned',
            'Completed' => 'completed',
            'Terminated' => 'completed',
            'Cancelled' => 'completed',
        ];
        if (isset($statusMap[$data['status']])) {
            $data['status'] = $statusMap[$data['status']];
        }
        if (!in_array($data['status'], ['planned','active','completed'], true)) {
            $data['status'] = $dep['status'];
        }
        // Work Permit
        $data['work_permit_status'] = trim($_POST['work_permit_status'] ?? 'not_applied');
        $data['work_permit_expiry'] = trim($_POST['work_permit_expiry'] ?? '');
        $data['contract_expiry'] = trim($_POST['contract_expiry'] ?? '');

        // Handle file uploads for edit
        $visa_reference_file = $dep['visa_reference_file']; // Keep existing file
        $signed_contract_file = $dep['signed_contract_file']; // Keep existing file
        
        if (isset($_FILES['visa_reference']) && $_FILES['visa_reference']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $uploadDir = 'uploads/deployments/' . $dep['candidate_id'];
                $upload = handle_upload($_FILES['visa_reference'], $uploadDir);
                $visa_reference_file = $upload['path'];
                
                // Generate thumbnail for video files
                if (is_video_file($upload['original'])) {
                    $thumbnailPath = get_video_thumbnail_path($upload['path']);
                    generate_video_thumbnail($upload['path'], $thumbnailPath);
                }
                
                // Delete old file if exists
                if ($dep['visa_reference_file'] && file_exists($dep['visa_reference_file'])) {
                    unlink($dep['visa_reference_file']);
                    // Also delete old thumbnail if it exists
                    $oldThumbnail = get_video_thumbnail_path($dep['visa_reference_file']);
                    if (file_exists($oldThumbnail)) {
                        unlink($oldThumbnail);
                    }
                }
            } catch (Exception $e) {
                $errors['visa_reference'] = 'Visa reference upload failed: ' . $e->getMessage();
            }
        }
        
        if (isset($_FILES['signed_contract']) && $_FILES['signed_contract']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $uploadDir = 'uploads/deployments/' . $dep['candidate_id'];
                $upload = handle_upload($_FILES['signed_contract'], $uploadDir);
                $signed_contract_file = $upload['path'];
                
                // Generate thumbnail for video files
                if (is_video_file($upload['original'])) {
                    $thumbnailPath = get_video_thumbnail_path($upload['path']);
                    generate_video_thumbnail($upload['path'], $thumbnailPath);
                }
                
                // Delete old file if exists
                if ($dep['signed_contract_file'] && file_exists($dep['signed_contract_file'])) {
                    unlink($dep['signed_contract_file']);
                    // Also delete old thumbnail if it exists
                    $oldThumbnail = get_video_thumbnail_path($dep['signed_contract_file']);
                    if (file_exists($oldThumbnail)) {
                        unlink($oldThumbnail);
                    }
                }
            } catch (Exception $e) {
                $errors['signed_contract'] = 'Signed contract upload failed: ' . $e->getMessage();
            }
        }

        if ($data['employer_id'] <= 0) $errors['employer_id'] = 'Employer is required';
        if (!$data['start_date']) $errors['start_date'] = 'Start date is required';

        if (!$errors) {
            $upd = $pdo->prepare('UPDATE deployments SET employer_id=:eid, agency_id=:aid, location_country=:country, location_city=:city, location_site=:site, start_date=:start, end_date=:end, salary_amount=:salary, salary_currency=:currency, status=:status, visa_reference_file=:visa_ref, signed_contract_file=:contract WHERE id=:id');
            $upd->execute([
                ':eid' => $data['employer_id'],
                ':aid' => $data['agency_id'],
                ':country' => $data['location_country'] ?: null,
                ':city' => $data['location_city'] ?: null,
                ':site' => $data['location_site'] ?: null,
                ':start' => $data['start_date'],
                ':end' => $data['end_date'] ?: null,
                ':salary' => $data['salary_amount'] !== '' ? $data['salary_amount'] : null,
                ':currency' => $data['salary_currency'] ?: null,
                ':status' => $data['status'] ?: 'planned',
                ':visa_ref' => $visa_reference_file,
                ':contract' => $signed_contract_file,
                ':id' => $id,
            ]);

            // Save compliance for candidate (work permit and contract only)
            $check = $pdo->prepare('SELECT id FROM compliance WHERE candidate_id=:cid ORDER BY updated_at DESC, id DESC LIMIT 1');
            $check->execute([':cid' => (int)$dep['candidate_id']]);
            if ($row = $check->fetch()) {
                $updC = $pdo->prepare('UPDATE compliance SET work_permit_expiry=:wexp, work_permit_status=:wstat, contract_expiry=:cexp, updated_at=CURRENT_TIMESTAMP WHERE id=:id');
                $updC->execute([
                    ':wexp' => $data['work_permit_expiry'] ?: null,
                    ':wstat' => $data['work_permit_status'] ?: 'not_applied',
                    ':cexp' => $data['contract_expiry'] ?: null,
                    ':id' => (int)$row['id'],
                ]);
            } else {
                $insC = $pdo->prepare('INSERT INTO compliance (candidate_id, work_permit_expiry, work_permit_status, contract_expiry) VALUES (:cid, :wexp, :wstat, :cexp)');
                $insC->execute([
                    ':cid' => (int)$dep['candidate_id'],
                    ':wexp' => $data['work_permit_expiry'] ?: null,
                    ':wstat' => $data['work_permit_status'] ?: 'not_applied',
                    ':cexp' => $data['contract_expiry'] ?: null,
                ]);
            }

            redirect(base_url('index.php?page=deployments&action=view&id=' . $id));
        }
    }

    $emps = $pdo->query('SELECT id, name FROM employers WHERE is_active=1 ORDER BY name')->fetchAll();
    $agencies = $pdo->query('SELECT id, name FROM agencies WHERE is_active=1 ORDER BY name')->fetchAll();
    
    // Prefill work permit from latest compliance for this candidate
    $cs = $pdo->prepare('SELECT work_permit_expiry, work_permit_status, contract_expiry FROM compliance WHERE candidate_id=:cid ORDER BY updated_at DESC, id DESC LIMIT 1');
    $cs->execute([':cid' => (int)$dep['candidate_id']]);
    if ($row = $cs->fetch()) {
        $data['work_permit_status'] = $data['work_permit_status'] ?? $row['work_permit_status'];
        $data['work_permit_expiry'] = $data['work_permit_expiry'] ?? $row['work_permit_expiry'];
        $data['contract_expiry'] = $data['contract_expiry'] ?? $row['contract_expiry'];
    }

    return view('deployments_form', ['data' => $data, 'errors' => $errors, 'cands' => [], 'emps' => $emps, 'agencies' => $agencies, 'mode' => 'edit']);
}

function view_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT d.*, c.first_name, c.last_name, e.name AS employer, a.name AS agency
                             FROM deployments d
                             JOIN candidates c ON c.id=d.candidate_id
                             JOIN employers e ON e.id=d.employer_id
                             LEFT JOIN agencies a ON a.id=d.agency_id
                             WHERE d.id=:id");
    $stmt->execute([':id' => $id]);
    $dep = $stmt->fetch();
    if (!$dep) { http_response_code(404); return 'Not found'; }

    // Pull latest compliance for this candidate
    $cs = $pdo->prepare('SELECT work_permit_expiry, work_permit_status, contract_expiry FROM compliance WHERE candidate_id=:cid ORDER BY updated_at DESC, id DESC LIMIT 1');
    $cs->execute([':cid' => (int)$dep['candidate_id']]);
    if ($row = $cs->fetch()) {
        $dep['work_permit_status'] = $row['work_permit_status'];
        $dep['work_permit_expiry'] = $row['work_permit_expiry'];
        $dep['contract_expiry'] = $row['contract_expiry'];
    }

    return view('deployments_view', ['dep' => $dep]);
}
