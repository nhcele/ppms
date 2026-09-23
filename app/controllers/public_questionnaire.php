<?php
// app/controllers/public_questionnaire.php

declare(strict_types=1);

// No login required for public questionnaire access
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/validators.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function notify_admin_of_submission(PDO $pdo, array $request): void {
    try {
        $creatorStmt = $pdo->prepare("SELECT email, username FROM users WHERE id = :id");
        $creatorStmt->execute([':id' => $request['created_by']]);
        $creator = $creatorStmt->fetch();
        
        if (!$creator || empty($creator['email'])) {
            return;
        }
        
        $candidateName = trim(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? ''));
        if (empty($candidateName)) {
            $candidateName = 'A candidate';
        }
        
        $subject = 'Questionnaire Submitted: ' . $request['request_code'];
        $message = "Hello " . $creator['username'] . ",\n\n";
        $message .= "A candidate has submitted a questionnaire.\n\n";
        $message .= "Request Code: " . $request['request_code'] . "\n";
        $message .= "Candidate: " . $candidateName . "\n";
        $message .= "Position: " . ($request['position'] ?? 'Not specified') . "\n";
        $message .= "Destination: " . ($request['recruitment_destination'] ?? 'Not specified') . "\n";
        $message .= "Submitted At: " . date('Y-m-d H:i:s') . "\n\n";
        $message .= "View the submission: http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/ppms/index.php?page=questionnaire&action=view&id=" . $request['id'] . "\n";
        
        $headers = 'From: noreply@ppms.local' . "\r\n" .
                   'Reply-To: noreply@ppms.local' . "\r\n";
        
        if (!mail($creator['email'], $subject, $message, $headers)) {
            error_log('Failed to send submission notification email to ' . $creator['email']);
        }
    } catch (Exception $e) {
        error_log('Notification error: ' . $e->getMessage());
    }
}

function is_candidate_verified(string $token): bool {
    return isset($_SESSION['questionnaire_verified']) && 
           is_array($_SESSION['questionnaire_verified']) && 
           in_array($token, $_SESSION['questionnaire_verified'], true);
}

function mark_candidate_verified(string $token): void {
    if (!isset($_SESSION['questionnaire_verified'])) {
        $_SESSION['questionnaire_verified'] = [];
    }
    if (!in_array($token, $_SESSION['questionnaire_verified'], true)) {
        $_SESSION['questionnaire_verified'][] = $token;
    }
}

function log_token_attempt(PDO $pdo, int $requestId, string $actionType, string $details): void {
    try {
        $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, ip_address, user_agent) VALUES (:req_id, :action, :details, :ip, :ua)")
            ->execute([
                ':req_id' => $requestId,
                ':action' => $actionType,
                ':details' => $details,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
    } catch (Exception $e) {
        error_log('Audit log error: ' . $e->getMessage());
    }
}

function validate_token(string $token): ?array {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT qr.*, 
                          c.first_name, c.last_name, c.email, c.phone,
                          qreq.requirement_type, qreq.document_type, qreq.field_name, qreq.is_required
                          FROM questionnaire_requests qr
                          LEFT JOIN candidates c ON qr.candidate_id = c.id
                          LEFT JOIN questionnaire_requirements qreq ON qr.id = qreq.questionnaire_request_id
                          WHERE qr.secure_token = :token");
    $stmt->execute([':token' => $token]);
    $results = $stmt->fetchAll();
    
    if (empty($results)) {
        return null;
    }
    
    $request = $results[0];
    
    // Check if expired
    if (strtotime($request['expiry_time']) < time()) {
        log_token_attempt($pdo, (int)$request['id'], 'expired', 'Access attempt on expired link');
        return null;
    }
    
    // Check if already submitted
    if ($request['status'] === 'submitted') {
        log_token_attempt($pdo, (int)$request['id'], 'submitted', 'Access attempt after submission');
        return null;
    }
    
    // Check if revoked
    if ($request['status'] === 'revoked') {
        log_token_attempt($pdo, (int)$request['id'], 'revoked', 'Access attempt on revoked link');
        return null;
    }
    
    // Group requirements
    $requirements = [];
    foreach ($results as $row) {
        if ($row['requirement_type']) {
            $requirements[] = [
                'requirement_type' => $row['requirement_type'],
                'document_type' => $row['document_type'],
                'field_name' => $row['field_name'],
                'is_required' => $row['is_required']
            ];
        }
    }
    
    $request['requirements'] = $requirements;
    
    return $request;
}

function start_action(): string {
    $token = trim($_GET['token'] ?? '');
    
    if (empty($token)) {
        http_response_code(400);
        return 'Invalid or missing token';
    }
    
    $request = validate_token($token);
    
    if (!$request) {
        http_response_code(404);
        return 'Invalid, expired, or already submitted questionnaire link';
    }
    
    // Candidate identity verification: phone + date of birth
    if (!empty($request['candidate_id']) && !is_candidate_verified($token)) {
        $pdo = db();
        $candidateStmt = $pdo->prepare("SELECT phone, dob FROM candidates WHERE id = :id");
        $candidateStmt->execute([':id' => $request['candidate_id']]);
        $candidate = $candidateStmt->fetch();
        
        if ($candidate && !empty($candidate['phone']) && !empty($candidate['dob'])) {
            return view('public_questionnaire_verify', ['token' => $token, 'error' => '']);
        }
    }
    
    // Log that the questionnaire was opened
    $pdo = db();
    $pdo->prepare("UPDATE questionnaire_requests SET status = 'opened' WHERE id = :id")
        ->execute([':id' => $request['id']]);
    
    $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, ip_address, user_agent) VALUES (:req_id, 'opened', 'Questionnaire opened by candidate', :ip, :ua)")
        ->execute([
            ':req_id' => $request['id'],
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    
    // Load existing responses if any
    $respStmt = $pdo->prepare("SELECT * FROM questionnaire_responses WHERE questionnaire_request_id = :id");
    $respStmt->execute([':id' => $request['id']]);
    $existingResponses = $respStmt->fetchAll();
    
    // Load existing documents
    $docStmt = $pdo->prepare("SELECT * FROM questionnaire_documents WHERE questionnaire_request_id = :id AND is_active = 1");
    $docStmt->execute([':id' => $request['id']]);
    $existingDocuments = $docStmt->fetchAll();
    
    // Convert responses to key-value array for easier access
    $responses = [];
    foreach ($existingResponses as $resp) {
        $responses[$resp['section']][$resp['field_name']] = $resp['field_value'];
    }
    
    // Build selected and required lookups
    $selectedFields = [];
    $requiredFields = [];
    $selectedDocuments = [];
    $requiredDocuments = [];
    foreach ($request['requirements'] as $req) {
        if ($req['requirement_type'] !== 'documents' && $req['field_name']) {
            $selectedFields[$req['field_name']] = true;
            if ($req['is_required']) {
                $requiredFields[$req['field_name']] = true;
            }
        } elseif ($req['requirement_type'] === 'documents' && $req['document_type']) {
            $selectedDocuments[$req['document_type']] = true;
            if ($req['is_required']) {
                $requiredDocuments[$req['document_type']] = true;
            }
        }
    }
    
    return view('public_questionnaire_start', [
        'request' => $request,
        'responses' => $responses,
        'documents' => $existingDocuments,
        'token' => $token,
        'selectedFields' => array_keys($selectedFields),
        'requiredFields' => array_keys($requiredFields),
        'selectedDocuments' => array_keys($selectedDocuments),
        'requiredDocuments' => array_keys($requiredDocuments)
    ]);
}

function verify_action(): string {
    $token = trim($_POST['token'] ?? $_GET['token'] ?? '');
    
    if (empty($token)) {
        http_response_code(400);
        return 'Invalid or missing token';
    }
    
    $request = validate_token($token);
    if (!$request || empty($request['candidate_id'])) {
        http_response_code(404);
        return 'Invalid questionnaire link';
    }
    
    if (!is_post()) {
        return view('public_questionnaire_verify', ['token' => $token, 'error' => '']);
    }
    
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    
    if (empty($phone) || empty($dob)) {
        return view('public_questionnaire_verify', [
            'token' => $token,
            'error' => 'Phone number and date of birth are required'
        ]);
    }
    
    $pdo = db();
    $candidateStmt = $pdo->prepare("SELECT phone, dob FROM candidates WHERE id = :id");
    $candidateStmt->execute([':id' => $request['candidate_id']]);
    $candidate = $candidateStmt->fetch();
    
    if (!$candidate) {
        http_response_code(404);
        return 'Candidate not found';
    }
    
    // Normalize phone comparison: compare digits only
    $enteredPhoneDigits = preg_replace('/\D/', '', $phone);
    $storedPhoneDigits = preg_replace('/\D/', '', $candidate['phone'] ?? '');
    
    $enteredDob = date('Y-m-d', strtotime($dob));
    $storedDob = date('Y-m-d', strtotime($candidate['dob'] ?? ''));
    
    if ($enteredPhoneDigits !== $storedPhoneDigits || $enteredDob !== $storedDob) {
        return view('public_questionnaire_verify', [
            'token' => $token,
            'error' => 'Phone number and date of birth do not match our records. Please try again.'
        ]);
    }
    
    mark_candidate_verified($token);
    
    // Log verification
    $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, ip_address, user_agent) VALUES (:req_id, 'opened', 'Candidate verified with phone + DOB', :ip, :ua)")
        ->execute([
            ':req_id' => $request['id'],
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    
    redirect(base_url('index.php?page=public-questionnaire&action=start&token=' . urlencode($token)));
}

function save_action(): string {
    // This is an AJAX endpoint for autosave
    header('Content-Type: application/json');
    
    $token = trim($_POST['token'] ?? '');
    $section = trim($_POST['section'] ?? '');
    $fieldName = trim($_POST['field_name'] ?? '');
    $fieldValue = trim($_POST['value'] ?? '');
    $dataType = trim($_POST['data_type'] ?? 'text');
    
    if (empty($token) || empty($section) || empty($fieldName)) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
        return '';
    }
    
    $request = validate_token($token);
    if (!$request) {
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        return '';
    }
    
    try {
        $pdo = db();
        
        // Update status to in_progress if not already
        $pdo->prepare("UPDATE questionnaire_requests SET status = 'in_progress' WHERE id = :id")
            ->execute([':id' => $request['id']]);
        
        // Upsert the response
        $upsertSql = "INSERT INTO questionnaire_responses (questionnaire_request_id, section, field_name, field_value, data_type) 
                      VALUES (:req_id, :section, :field_name, :value, :data_type)
                      ON DUPLICATE KEY UPDATE field_value = :value, data_type = :data_type, updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $pdo->prepare($upsertSql);
        $stmt->execute([
            ':req_id' => $request['id'],
            ':section' => $section,
            ':field_name' => $fieldName,
            ':value' => $fieldValue,
            ':data_type' => $dataType
        ]);
        
        // Log autosave
        $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, ip_address, user_agent) VALUES (:req_id, 'autosave', :details, :ip, :ua)")
            ->execute([
                ':req_id' => $request['id'],
                ':details' => "Autosave: $section.$fieldName",
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        
        echo json_encode(['success' => true, 'message' => 'Saved successfully']);
        
    } catch (Exception $e) {
        error_log('Autosave error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Failed to save']);
    }
    
    return '';
}

function upload_document_action(): string {
    // This is an AJAX endpoint for document upload
    header('Content-Type: application/json');
    
    $token = trim($_POST['token'] ?? '');
    $documentType = trim($_POST['document_type'] ?? '');
    
    if (empty($token) || empty($documentType) || !isset($_FILES['document'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
        return '';
    }
    
    $request = validate_token($token);
    if (!$request) {
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        return '';
    }
    
    try {
        $pdo = db();
        $file = $_FILES['document'];
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'File upload error']);
            return '';
        }
        
        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            echo json_encode(['success' => false, 'error' => 'File too large (max 10MB)']);
            return '';
        }
        
        // Check file type
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
            return '';
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid('doc_', true) . '.' . $extension;
        $uploadPath = UPLOAD_DIR . '/questionnaire/' . $newFilename;
        
        // Create directory if it doesn't exist
        if (!is_dir(UPLOAD_DIR . '/questionnaire')) {
            mkdir(UPLOAD_DIR . '/questionnaire', 0755, true);
        }
        
        // Move file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
            return '';
        }
        
        // Store relative path
        $relativePath = 'uploads/questionnaire/' . $newFilename;
        
        // Insert document record
        $sql = "INSERT INTO questionnaire_documents (questionnaire_request_id, document_type, file_path, original_name, file_size, mime_type) 
                VALUES (:req_id, :doc_type, :file_path, :original_name, :file_size, :mime_type)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':req_id' => $request['id'],
            ':doc_type' => $documentType,
            ':file_path' => $relativePath,
            ':original_name' => $file['name'],
            ':file_size' => $file['size'],
            ':mime_type' => $mimeType
        ]);
        
        // Update status to in_progress
        $pdo->prepare("UPDATE questionnaire_requests SET status = 'in_progress' WHERE id = :id")
            ->execute([':id' => $request['id']]);
        
        // Log document upload
        $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, ip_address, user_agent) VALUES (:req_id, 'document_upload', :details, :ip, :ua)")
            ->execute([
                ':req_id' => $request['id'],
                ':details' => "Document uploaded: $documentType",
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Document uploaded successfully',
            'document_id' => $pdo->lastInsertId()
        ]);
        
    } catch (Exception $e) {
        error_log('Document upload error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Failed to upload document']);
    }
    
    return '';
}

function submit_action(): string {
    $token = trim($_POST['token'] ?? '');
    
    if (empty($token)) {
        http_response_code(400);
        return 'Invalid or missing token';
    }
    
    $request = validate_token($token);
    if (!$request) {
        http_response_code(404);
        return 'Invalid or expired questionnaire link';
    }
    
    try {
        $pdo = db();
        $pdo->beginTransaction();
        
        // Check if all required fields and documents are completed
        $reqStmt = $pdo->prepare("SELECT * FROM questionnaire_requirements WHERE questionnaire_request_id = :id AND is_required = 1");
        $reqStmt->execute([':id' => $request['id']]);
        $requiredItems = $reqStmt->fetchAll();
        
        $missingItems = [];
        $invalidItems = [];
        
        // Build response lookup for validation
        $responseValues = [];
        $respStmt = $pdo->prepare("SELECT field_name, field_value FROM questionnaire_responses WHERE questionnaire_request_id = :id");
        $respStmt->execute([':id' => $request['id']]);
        foreach ($respStmt->fetchAll() as $r) {
            $responseValues[$r['field_name']] = $r['field_value'];
        }
        
        foreach ($requiredItems as $item) {
            if ($item['document_type']) {
                // Check if document is uploaded
                $docCheck = $pdo->prepare("SELECT COUNT(*) FROM questionnaire_documents WHERE questionnaire_request_id = :id AND document_type = :doc_type AND is_active = 1");
                $docCheck->execute([':id' => $request['id'], ':doc_type' => $item['document_type']]);
                if ($docCheck->fetchColumn() == 0) {
                    $missingItems[] = "Document: {$item['document_type']}";
                }
            } elseif ($item['field_name']) {
                $value = $responseValues[$item['field_name']] ?? '';
                if (empty(trim($value))) {
                    $missingItems[] = "Field: {$item['field_name']}";
                } else {
                    // Format validation for known fields
                    if ($item['field_name'] === 'email' && !v_email($value)) {
                        $invalidItems[] = "Email address is invalid";
                    }
                    if ($item['field_name'] === 'mobile_number' && !v_phone($value)) {
                        $invalidItems[] = "Mobile number is invalid";
                    }
                    if ($item['field_name'] === 'passport_no' && !v_passport($value)) {
                        $invalidItems[] = "Passport number is invalid";
                    }
                    if ($item['field_name'] === 'date_of_birth' && !v_date($value)) {
                        $invalidItems[] = "Date of birth is invalid";
                    }
                    if ($item['field_name'] === 'passport_validity' && !v_date($value)) {
                        $invalidItems[] = "Passport validity date is invalid";
                    }
                }
            }
        }
        
        // Validate employment period consistency if work history is provided
        if (!empty($responseValues['work_history'])) {
            $workHistory = json_decode($responseValues['work_history'], true);
            if (is_array($workHistory)) {
                foreach ($workHistory as $index => $work) {
                    if (!empty($work['start_date']) && !empty($work['end_date']) && !v_date_range($work['start_date'], $work['end_date'])) {
                        $invalidItems[] = "Work experience entry " . ($index + 1) . ": end date is before start date";
                    }
                }
            }
        }
        
        if (!empty($missingItems) || !empty($invalidItems)) {
            $pdo->rollBack();
            return view('public_questionnaire_error', [
                'error' => 'Please correct the issues below before submitting',
                'missing_items' => array_merge($missingItems, $invalidItems),
                'token' => $token
            ]);
        }
        
        // Update status to submitted
        $pdo->prepare("UPDATE questionnaire_requests SET status = 'submitted', submitted_at = NOW() WHERE id = :id")
            ->execute([':id' => $request['id']]);
        
        // Log submission
        $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, ip_address, user_agent) VALUES (:req_id, 'submitted', 'Questionnaire submitted by candidate', :ip, :ua)")
            ->execute([
                ':req_id' => $request['id'],
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        
        // Notify admin/recruiter who created the request
        notify_admin_of_submission($pdo, $request);
        
        $pdo->commit();
        
        return view('public_questionnaire_success', [
            'request' => $request
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('Questionnaire submission error: ' . $e->getMessage());
        http_response_code(500);
        return 'Error submitting questionnaire';
    }
}

function delete_document_action(): string {
    header('Content-Type: application/json');
    
    $token = trim($_POST['token'] ?? '');
    $documentId = (int)($_POST['document_id'] ?? 0);
    
    if (empty($token) || $documentId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
        return '';
    }
    
    $request = validate_token($token);
    if (!$request) {
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        return '';
    }
    
    try {
        $pdo = db();
        
        // Soft delete the document
        $pdo->prepare("UPDATE questionnaire_documents SET is_active = 0 WHERE id = :id AND questionnaire_request_id = :req_id")
            ->execute([':id' => $documentId, ':req_id' => $request['id']]);
        
        echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
        
    } catch (Exception $e) {
        error_log('Document deletion error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Failed to delete document']);
    }
    
    return '';
}