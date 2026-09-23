<?php
// app/controllers/questionnaire.php

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);
require_once __DIR__ . '/../lib/validators.php';

function questionnaire_code_generate(PDO $pdo): string {
    $year = date('Y');
    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM questionnaire_requests WHERE request_code LIKE :y");
    $stmt->execute([':y' => 'QR-' . $year . '-%']);
    $n = (int)$stmt->fetch()['cnt'] + 1;
    return sprintf('QR-%s-%04d', $year, $n);
}

/**
 * Master definition of all configurable questionnaire requirements.
 * Each item has a type (field or document), section, display label,
 * default required flag, and display order.
 */
function get_questionnaire_requirement_definitions(): array {
    return [
        // Personal information
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'full_name', 'label' => 'Full Name', 'is_required' => 1, 'display_order' => 1],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'date_of_birth', 'label' => 'Date of Birth', 'is_required' => 1, 'display_order' => 2],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'place_of_birth', 'label' => 'Place of Birth', 'is_required' => 1, 'display_order' => 3],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'address', 'label' => 'Address', 'is_required' => 1, 'display_order' => 4],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'mobile_number', 'label' => 'Mobile Number', 'is_required' => 1, 'display_order' => 5],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'email', 'label' => 'Email Address', 'is_required' => 1, 'display_order' => 6],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'passport_no', 'label' => 'Passport Number', 'is_required' => 1, 'display_order' => 7],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'passport_validity', 'label' => 'Passport Validity', 'is_required' => 1, 'display_order' => 8],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'mothers_name', 'label' => "Mother's Name", 'is_required' => 1, 'display_order' => 9],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'fathers_name', 'label' => "Father's Name", 'is_required' => 1, 'display_order' => 10],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'gender', 'label' => 'Gender', 'is_required' => 1, 'display_order' => 11],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'height', 'label' => 'Height', 'is_required' => 1, 'display_order' => 12],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'weight', 'label' => 'Weight', 'is_required' => 1, 'display_order' => 13],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'marital_status', 'label' => 'Marital Status', 'is_required' => 1, 'display_order' => 14],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'religion', 'label' => 'Religion', 'is_required' => 0, 'display_order' => 15],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'drivers_license', 'label' => "Driver's Licence", 'is_required' => 0, 'display_order' => 16],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'date_of_marriage', 'label' => 'Date of Marriage', 'is_required' => 0, 'display_order' => 17],
        ['requirement_type' => 'personal_info', 'item_type' => 'field', 'field_name' => 'spouse_name', 'label' => 'Name of Spouse', 'is_required' => 0, 'display_order' => 18],

        // Education
        ['requirement_type' => 'education_info', 'item_type' => 'field', 'field_name' => 'degree_course', 'label' => 'Degree/Course', 'is_required' => 1, 'display_order' => 1],
        ['requirement_type' => 'education_info', 'item_type' => 'field', 'field_name' => 'university_institute', 'label' => 'University / Institute', 'is_required' => 1, 'display_order' => 2],
        ['requirement_type' => 'education_info', 'item_type' => 'field', 'field_name' => 'year_of_graduation', 'label' => 'Year of Graduation', 'is_required' => 1, 'display_order' => 3],

        // Positions
        ['requirement_type' => 'positions', 'item_type' => 'field', 'field_name' => 'position_1', 'label' => 'Position 1', 'is_required' => 0, 'display_order' => 1],
        ['requirement_type' => 'positions', 'item_type' => 'field', 'field_name' => 'position_2', 'label' => 'Position 2', 'is_required' => 0, 'display_order' => 2],
        ['requirement_type' => 'positions', 'item_type' => 'field', 'field_name' => 'position_3', 'label' => 'Position 3', 'is_required' => 0, 'display_order' => 3],
        ['requirement_type' => 'positions', 'item_type' => 'field', 'field_name' => 'position_4', 'label' => 'Position 4', 'is_required' => 0, 'display_order' => 4],

        // Employment
        ['requirement_type' => 'employment_info', 'item_type' => 'field', 'field_name' => 'work_history', 'label' => 'Work History', 'is_required' => 1, 'display_order' => 1],
        ['requirement_type' => 'employment_info', 'item_type' => 'field', 'field_name' => 'personal_statement', 'label' => 'Personal Statement', 'is_required' => 0, 'display_order' => 2],

        // Additional info
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'computer_skills', 'label' => 'Computer Skills', 'is_required' => 0, 'display_order' => 1],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'video_link', 'label' => 'Video Link', 'is_required' => 0, 'display_order' => 2],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'languages', 'label' => 'Languages Known', 'is_required' => 1, 'display_order' => 3],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'health_condition', 'label' => 'Physical Health Condition', 'is_required' => 1, 'display_order' => 4],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'relatives_europe', 'label' => 'Relatives or Friends in Europe', 'is_required' => 0, 'display_order' => 5],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'previous_europe_work', 'label' => 'Previous Work Experience in Europe', 'is_required' => 0, 'display_order' => 6],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'daily_prayers', 'label' => 'Daily Prayers', 'is_required' => 0, 'display_order' => 7],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'work_men_women', 'label' => 'Ability to Work with Men and Women', 'is_required' => 0, 'display_order' => 8],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'work_pork', 'label' => 'Ability to Work with Pork/Meat Products', 'is_required' => 0, 'display_order' => 9],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'wear_uniform', 'label' => 'Readiness to Wear Work Uniform', 'is_required' => 0, 'display_order' => 10],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'willingness_work_conditions', 'label' => 'Willingness to Work Conditions', 'is_required' => 0, 'display_order' => 11],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'lithuania_address', 'label' => 'Residence Address in Lithuania', 'is_required' => 0, 'display_order' => 12],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'former_citizenship', 'label' => 'Former Citizenship', 'is_required' => 0, 'display_order' => 13],
        ['requirement_type' => 'additional_info', 'item_type' => 'field', 'field_name' => 'visas_last_5_years', 'label' => 'Visas/Residence Permits (Last 5 Years)', 'is_required' => 0, 'display_order' => 14],

        // References
        ['requirement_type' => 'references', 'item_type' => 'field', 'field_name' => 'reference_1', 'label' => 'Reference 1', 'is_required' => 0, 'display_order' => 1],
        ['requirement_type' => 'references', 'item_type' => 'field', 'field_name' => 'reference_2', 'label' => 'Reference 2', 'is_required' => 0, 'display_order' => 2],

        // Travel history
        ['requirement_type' => 'travel_history', 'item_type' => 'field', 'field_name' => 'countries', 'label' => 'Countries Visited (Last 5 Years)', 'is_required' => 0, 'display_order' => 1],

        // Declarations
        ['requirement_type' => 'declarations', 'item_type' => 'field', 'field_name' => 'visa_denied', 'label' => 'Visa Denied/Banned from Foreign Country', 'is_required' => 1, 'display_order' => 1],
        ['requirement_type' => 'declarations', 'item_type' => 'field', 'field_name' => 'visa_denied_details', 'label' => 'Visa Denial Details', 'is_required' => 0, 'display_order' => 2],
        ['requirement_type' => 'declarations', 'item_type' => 'field', 'field_name' => 'criminal_conviction', 'label' => 'Criminal Conviction', 'is_required' => 1, 'display_order' => 3],
        ['requirement_type' => 'declarations', 'item_type' => 'field', 'field_name' => 'criminal_details', 'label' => 'Criminal Conviction Details', 'is_required' => 0, 'display_order' => 4],

        // Health
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'chronic_disease', 'label' => 'Chronic Disease', 'is_required' => 1, 'display_order' => 1],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'chronic_disease_details', 'label' => 'Chronic Disease Details', 'is_required' => 0, 'display_order' => 2],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'infectious_disease', 'label' => 'Infectious Disease', 'is_required' => 1, 'display_order' => 3],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'infectious_disease_details', 'label' => 'Infectious Disease Details', 'is_required' => 0, 'display_order' => 4],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'surgery', 'label' => 'Surgery', 'is_required' => 1, 'display_order' => 5],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'surgery_details', 'label' => 'Surgery Details', 'is_required' => 0, 'display_order' => 6],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'medication', 'label' => 'Medication', 'is_required' => 1, 'display_order' => 7],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'medication_details', 'label' => 'Medication Details', 'is_required' => 0, 'display_order' => 8],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'physical_condition', 'label' => 'Physical Condition', 'is_required' => 1, 'display_order' => 9],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'physical_condition_details', 'label' => 'Physical Condition Details', 'is_required' => 0, 'display_order' => 10],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'substance_use', 'label' => 'Substance Use', 'is_required' => 1, 'display_order' => 11],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'substance_use_details', 'label' => 'Substance Use Details', 'is_required' => 0, 'display_order' => 12],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'treatment', 'label' => 'Treatment', 'is_required' => 1, 'display_order' => 13],
        ['requirement_type' => 'health', 'item_type' => 'field', 'field_name' => 'treatment_details', 'label' => 'Treatment Details', 'is_required' => 0, 'display_order' => 14],

        // Declaration
        ['requirement_type' => 'declaration', 'item_type' => 'field', 'field_name' => 'declaration_name', 'label' => 'Declaration Name', 'is_required' => 1, 'display_order' => 1],
        ['requirement_type' => 'declaration', 'item_type' => 'field', 'field_name' => 'declaration_date', 'label' => 'Declaration Date', 'is_required' => 1, 'display_order' => 2],
        ['requirement_type' => 'declaration', 'item_type' => 'field', 'field_name' => 'final_declaration', 'label' => 'Final Declaration Acceptance', 'is_required' => 1, 'display_order' => 3],

        // Documents
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'passport', 'label' => 'Passport', 'is_required' => 1, 'display_order' => 1],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'cv', 'label' => 'CV / Resume', 'is_required' => 1, 'display_order' => 2],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'passport_photograph', 'label' => 'Passport Photograph', 'is_required' => 1, 'display_order' => 3],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'academic_certificates', 'label' => 'Academic Certificates', 'is_required' => 0, 'display_order' => 4],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'professional_certificates', 'label' => 'Professional Certificates', 'is_required' => 0, 'display_order' => 5],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'drivers_license', 'label' => "Driver's Licence", 'is_required' => 0, 'display_order' => 6],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'reference_letter', 'label' => 'Reference Letter', 'is_required' => 0, 'display_order' => 7],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'police_clearance', 'label' => 'Police Clearance', 'is_required' => 0, 'display_order' => 8],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'medical_certificate', 'label' => 'Medical Certificate', 'is_required' => 0, 'display_order' => 9],
        ['requirement_type' => 'documents', 'item_type' => 'document', 'document_type' => 'other_document', 'label' => 'Other Document', 'is_required' => 0, 'display_order' => 10],
    ];
}

/**
 * Build a lookup array keyed by field_name or document_type for easy checks.
 */
function build_requirement_lookup(array $requirements): array {
    $lookup = [];
    foreach ($requirements as $req) {
        $key = $req['item_type'] === 'document' ? ('doc:' . $req['document_type']) : ('field:' . $req['field_name']);
        $lookup[$key] = $req;
    }
    return $lookup;
}

function generate_secure_token(): string {
    return bin2hex(random_bytes(32));
}

function validate_questionnaire_request(array $data): array {
    $errors = [];
    
    if (empty($data['position'])) {
        $errors['position'] = 'Position is required';
    }
    
    if (empty($data['recruitment_destination'])) {
        $errors['recruitment_destination'] = 'Recruitment destination is required';
    }
    
    if (empty($data['expiry_hours']) || $data['expiry_hours'] < 1 || $data['expiry_hours'] > 168) {
        $errors['expiry_hours'] = 'Expiry time must be between 1 and 168 hours';
    }
    
    return $errors;
}

function create_action(): string {
    $pdo = db();
    $errors = [];
    $data = [
        'candidate_id' => $_GET['candidate_id'] ?? '',
        'position' => '',
        'recruitment_destination' => '',
        'expiry_hours' => 1,
        'instructions' => '',
        'selected_requirements' => []
    ];

    if (is_post()) {
        csrf_verify();
        
        $data = [
            'candidate_id' => ($_POST['candidate_id'] ?? '') !== '' ? (int)$_POST['candidate_id'] : null,
            'position' => trim($_POST['position'] ?? ''),
            'recruitment_destination' => trim($_POST['recruitment_destination'] ?? ''),
            'expiry_hours' => (int)($_POST['expiry_hours'] ?? 1),
            'instructions' => trim($_POST['instructions'] ?? ''),
            'selected_requirements' => $_POST['requirements'] ?? []
        ];

        $errors = validate_questionnaire_request($data);
        
        if (!$errors) {
            try {
                $pdo->beginTransaction();
                
                $code = questionnaire_code_generate($pdo);
                $token = generate_secure_token();
                $expiryTime = date('Y-m-d H:i:s', strtotime("+" . $data['expiry_hours'] . " hours"));
                
                // Insert questionnaire request
                $sql = "INSERT INTO questionnaire_requests (
                    request_code, candidate_id, secure_token, position, recruitment_destination,
                    status, expiry_time, instructions, created_by
                ) VALUES (
                    :code, :candidate_id, :token, :position, :destination,
                    'link_created', :expiry, :instructions, :uid
                )";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':code' => $code,
                    ':candidate_id' => $data['candidate_id'],
                    ':token' => $token,
                    ':position' => $data['position'],
                    ':destination' => $data['recruitment_destination'],
                    ':expiry' => $expiryTime,
                    ':instructions' => $data['instructions'] ?: null,
                    ':uid' => current_user()['id']
                ]);
                
                $requestId = (int)$pdo->lastInsertId();
                
                // Insert requirements based on master definitions and admin selections
                $definitions = get_questionnaire_requirement_definitions();
                $postedRequirements = $_POST['requirements'] ?? [];
                $reqStmt = $pdo->prepare("INSERT INTO questionnaire_requirements (
                    questionnaire_request_id, requirement_type, document_type, field_name, is_required, display_order
                ) VALUES (:req_id, :req_type, :doc_type, :field_name, :is_required, :display_order)");
                
                foreach ($definitions as $def) {
                    $key = $def['item_type'] === 'document' ? ('doc:' . $def['document_type']) : ('field:' . $def['field_name']);
                    $posted = $postedRequirements[$key] ?? [];
                    $isSelected = !empty($posted['selected']);
                    
                    if (!$isSelected) {
                        continue;
                    }
                    
                    $isRequired = !empty($posted['required']) ? 1 : 0;
                    
                    $reqStmt->execute([
                        ':req_id' => $requestId,
                        ':req_type' => $def['requirement_type'],
                        ':doc_type' => $def['item_type'] === 'document' ? $def['document_type'] : null,
                        ':field_name' => $def['item_type'] === 'field' ? $def['field_name'] : null,
                        ':is_required' => $isRequired,
                        ':display_order' => $def['display_order']
                    ]);
                }
                
                // Log audit
                $auditStmt = $pdo->prepare("INSERT INTO questionnaire_audit_log (
                    questionnaire_request_id, action_type, action_details, user_id, ip_address, user_agent
                ) VALUES (:req_id, 'created', :details, :uid, :ip, :ua)");
                $auditStmt->execute([
                    ':req_id' => $requestId,
                    ':details' => 'Questionnaire request created',
                    ':uid' => current_user()['id'],
                    ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                    ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
                ]);
                
                $pdo->commit();
                
                redirect(base_url('index.php?page=questionnaire&action=view&id=' . $requestId));
                
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log('Questionnaire creation error: ' . $e->getMessage());
                $errors['general'] = 'Failed to create questionnaire request';
            }
        }
    }

    $candidates = $pdo->query('SELECT id, CONCAT(first_name, " ", last_name) as name, candidate_code FROM candidates ORDER BY first_name, last_name')->fetchAll();
    $requirementDefinitions = get_questionnaire_requirement_definitions();
    
    return view('questionnaire_form', [
        'data' => $data, 
        'errors' => $errors, 
        'candidates' => $candidates,
        'mode' => 'create',
        'requirementDefinitions' => $requirementDefinitions
    ]);
}

function view_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    $stmt = $pdo->prepare("SELECT qr.*, 
                          CONCAT(c.first_name, ' ', c.last_name) AS candidate_name,
                          c.email AS candidate_email,
                          u.username AS created_by_user
                          FROM questionnaire_requests qr
                          LEFT JOIN candidates c ON qr.candidate_id = c.id
                          LEFT JOIN users u ON qr.created_by = u.id
                          WHERE qr.id = :id");
    $stmt->execute([':id' => $id]);
    $request = $stmt->fetch();
    
    if (!$request) { http_response_code(404); return 'Questionnaire request not found'; }
    
    // Get requirements
    $reqStmt = $pdo->prepare("SELECT * FROM questionnaire_requirements WHERE questionnaire_request_id = :id ORDER BY display_order");
    $reqStmt->execute([':id' => $id]);
    $requirements = $reqStmt->fetchAll();
    
    // Get documents
    $docStmt = $pdo->prepare("SELECT * FROM questionnaire_documents WHERE questionnaire_request_id = :id AND is_active = 1 ORDER BY uploaded_at DESC");
    $docStmt->execute([':id' => $id]);
    $documents = $docStmt->fetchAll();
    
    // Get responses
    $respStmt = $pdo->prepare("SELECT * FROM questionnaire_responses WHERE questionnaire_request_id = :id ORDER BY section, field_name");
    $respStmt->execute([':id' => $id]);
    $responses = $respStmt->fetchAll();
    
    // Get audit log
    $auditStmt = $pdo->prepare("SELECT * FROM questionnaire_audit_log WHERE questionnaire_request_id = :id ORDER BY created_at DESC LIMIT 20");
    $auditStmt->execute([':id' => $id]);
    $auditLog = $auditStmt->fetchAll();
    
    // Generate questionnaire link
    $questionnaireLink = base_url('index.php?page=public-questionnaire&action=start&token=' . $request['secure_token']);
    
    return view('questionnaire_view', [
        'request' => $request,
        'requirements' => $requirements,
        'documents' => $documents,
        'responses' => $responses,
        'audit_log' => $auditLog,
        'questionnaire_link' => $questionnaireLink
    ]);
}

function list_action(): string {
    $pdo = db();
    $status = trim($_GET['status'] ?? '');
    $destination = trim($_GET['destination'] ?? '');
    
    // Pagination
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min(100, max(10, (int)($_GET['per'] ?? 20)));
    $offset = ($page - 1) * $perPage;
    
    // Build WHERE clause
    $where = [];
    $params = [];
    
    if ($status !== '') {
        $where[] = 'qr.status = ?';
        $params[] = $status;
    }
    
    if ($destination !== '') {
        $where[] = 'qr.recruitment_destination = ?';
        $params[] = $destination;
    }
    
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    
    // Count total
    $countSql = "SELECT COUNT(*) AS cnt FROM questionnaire_requests qr $whereSql";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetch()['cnt'];
    
    // Get data with missing required item counts
    $sql = "SELECT qr.*, 
                   CONCAT(c.first_name, ' ', c.last_name) AS candidate_name,
                   u.username AS created_by_user,
                   COALESCE(doc_counts.document_count, 0) AS document_count,
                   COALESCE(req_counts.missing_required_documents, 0) AS missing_required_documents,
                   COALESCE(req_counts.missing_required_fields, 0) AS missing_required_fields
            FROM questionnaire_requests qr
            LEFT JOIN candidates c ON qr.candidate_id = c.id
            LEFT JOIN users u ON qr.created_by = u.id
            LEFT JOIN (
                SELECT questionnaire_request_id, COUNT(*) AS document_count
                FROM questionnaire_documents
                WHERE is_active = 1
                GROUP BY questionnaire_request_id
            ) doc_counts ON qr.id = doc_counts.questionnaire_request_id
            LEFT JOIN (
                SELECT qreq.questionnaire_request_id,
                       SUM(CASE WHEN qreq.requirement_type = 'documents' AND qreq.is_required = 1 AND qreq.document_type IS NOT NULL AND qd.id IS NULL THEN 1 ELSE 0 END) AS missing_required_documents,
                       SUM(CASE WHEN qreq.requirement_type != 'documents' AND qreq.is_required = 1 AND qreq.field_name IS NOT NULL AND qr_resp.id IS NULL THEN 1 ELSE 0 END) AS missing_required_fields
                FROM questionnaire_requirements qreq
                LEFT JOIN questionnaire_documents qd ON qreq.questionnaire_request_id = qd.questionnaire_request_id AND qreq.document_type = qd.document_type AND qd.is_active = 1
                LEFT JOIN questionnaire_responses qr_resp ON qreq.questionnaire_request_id = qr_resp.questionnaire_request_id AND qreq.field_name = qr_resp.field_name AND qr_resp.field_value IS NOT NULL AND qr_resp.field_value != ''
                GROUP BY qreq.questionnaire_request_id
            ) req_counts ON qr.id = req_counts.questionnaire_request_id
            $whereSql
            ORDER BY qr.created_at DESC
            LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    
    $pages = (int)ceil($total / $perPage);
    
    return view('questionnaire_list', [
        'rows' => $rows,
        'status' => $status,
        'destination' => $destination,
        'page' => $page,
        'pages' => $pages,
        'per' => $perPage,
        'total' => $total,
    ]);
}

function send_link_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    if (is_post()) {
        csrf_verify();
        
        $stmt = $pdo->prepare("SELECT * FROM questionnaire_requests WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $request = $stmt->fetch();
        
        if (!$request) { http_response_code(404); return 'Questionnaire request not found'; }
        
        // Update status
        $pdo->prepare("UPDATE questionnaire_requests SET status = 'link_sent' WHERE id = :id")
            ->execute([':id' => $id]);
        
        // Log audit
        $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, user_id, ip_address, user_agent) VALUES (:req_id, 'sent', 'Link sent to candidate', :uid, :ip, :ua)")
            ->execute([
                ':req_id' => $id,
                ':uid' => current_user()['id'],
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        
        // Here you would implement email sending logic
        // For now, we'll just redirect back to view
        
        redirect(base_url('index.php?page=questionnaire&action=view&id=' . $id));
    }
    
    return view('questionnaire_send_link', ['id' => $id]);
}

function regenerate_link_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    if (is_post()) {
        csrf_verify();
        
        $stmt = $pdo->prepare("SELECT * FROM questionnaire_requests WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $request = $stmt->fetch();
        
        if (!$request) { http_response_code(404); return 'Questionnaire request not found'; }
        
        // Generate new token and extend expiry
        $newToken = generate_secure_token();
        $newExpiry = date('Y-m-d H:i:s', strtotime("+1 hour"));
        
        $pdo->prepare("UPDATE questionnaire_requests SET secure_token = :token, expiry_time = :expiry, status = 'link_created' WHERE id = :id")
            ->execute([
                ':token' => $newToken,
                ':expiry' => $newExpiry,
                ':id' => $id
            ]);
        
        // Log audit
        $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, user_id, ip_address, user_agent) VALUES (:req_id, 'link_regenerated', 'Link regenerated with new token', :uid, :ip, :ua)")
            ->execute([
                ':req_id' => $id,
                ':uid' => current_user()['id'],
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        
        redirect(base_url('index.php?page=questionnaire&action=view&id=' . $id));
    }
    
    return view('questionnaire_regenerate_link', ['id' => $id]);
}

function request_correction_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    if (is_post()) {
        csrf_verify();
        
        $correctionDetails = trim($_POST['correction_details'] ?? '');
        
        if (empty($correctionDetails)) {
            return view('questionnaire_request_correction', [
                'id' => $id,
                'error' => 'Correction details are required'
            ]);
        }
        
        // Update status
        $pdo->prepare("UPDATE questionnaire_requests SET status = 'correction_required' WHERE id = :id")
            ->execute([':id' => $id]);
        
        // Log audit
        $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, user_id, ip_address, user_agent) VALUES (:req_id, 'correction_requested', :details, :uid, :ip, :ua)")
            ->execute([
                ':req_id' => $id,
                ':details' => $correctionDetails,
                ':uid' => current_user()['id'],
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        
        redirect(base_url('index.php?page=questionnaire&action=view&id=' . $id));
    }
    
    return view('questionnaire_request_correction', ['id' => $id]);
}

function approve_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    csrf_verify();
    
    $pdo->prepare("UPDATE questionnaire_requests SET status = 'approved' WHERE id = :id")
        ->execute([':id' => $id]);
    
    $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, user_id, ip_address, user_agent) VALUES (:req_id, 'approved', 'Questionnaire approved by admin', :uid, :ip, :ua)")
        ->execute([
            ':req_id' => $id,
            ':uid' => current_user()['id'],
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    
    redirect(base_url('index.php?page=questionnaire&action=view&id=' . $id));
}

function revoke_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    csrf_verify();
    
    $pdo->prepare("UPDATE questionnaire_requests SET status = 'revoked' WHERE id = :id")
        ->execute([':id' => $id]);
    
    $pdo->prepare("INSERT INTO questionnaire_audit_log (questionnaire_request_id, action_type, action_details, user_id, ip_address, user_agent) VALUES (:req_id, 'revoked', 'Questionnaire link revoked by admin', :uid, :ip, :ua)")
        ->execute([
            ':req_id' => $id,
            ':uid' => current_user()['id'],
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    
    redirect(base_url('index.php?page=questionnaire&action=view&id=' . $id));
}

function dashboard_action(): string {
    $pdo = db();
    
    // Get summary statistics
    $summarySql = "SELECT 
                   COUNT(*) as total,
                   SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted,
                   SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                   SUM(CASE WHEN expiry_time < NOW() AND status NOT IN ('submitted', 'completed', 'revoked') THEN 1 ELSE 0 END) as expired
                   FROM questionnaire_requests";
    $summary = $pdo->query($summarySql)->fetch();
    
    // Get recent activity
    $recentSql = "SELECT qr.request_code, qr.status, qr.created_at, 
                  CONCAT(c.first_name, ' ', c.last_name) as candidate_name,
                  qal.action_type
                  FROM questionnaire_requests qr
                  LEFT JOIN candidates c ON qr.candidate_id = c.id
                  LEFT JOIN questionnaire_audit_log qal ON qr.id = qal.questionnaire_request_id
                  ORDER BY qal.created_at DESC
                  LIMIT 10";
    $recentActivity = $pdo->query($recentSql)->fetchAll();
    
    // Get pending actions
    $pendingSql = "SELECT qr.id, qr.request_code, qr.status, qr.expiry_time,
                   CONCAT(c.first_name, ' ', c.last_name) as candidate_name
                   FROM questionnaire_requests qr
                   LEFT JOIN candidates c ON qr.candidate_id = c.id
                   WHERE qr.status IN ('submitted', 'link_created', 'opened', 'in_progress')
                   OR (qr.expiry_time < NOW() AND qr.status NOT IN ('submitted', 'completed', 'revoked'))
                   ORDER BY qr.created_at DESC
                   LIMIT 10";
    $pendingActions = $pdo->query($pendingSql)->fetchAll();
    
    return view('questionnaire_dashboard', [
        'summary' => $summary,
        'recent_activity' => $recentActivity,
        'pending_actions' => $pendingActions
    ]);
}

function index_action(): string {
    return list_action();
}