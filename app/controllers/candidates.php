<?php
// app/controllers/candidates.php

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);
require_once __DIR__ . '/../lib/validators.php';

function candidates_code_generate(PDO $pdo): string {
    $year = date('Y');
    // Count existing for the year to create sequence
    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM candidates WHERE candidate_code LIKE :y");
    $stmt->execute([':y' => 'CAND-' . $year . '-%']);
    $n = (int)$stmt->fetch()['cnt'] + 1;
    return sprintf('CAND-%s-%04d', $year, $n);
}

function validate_candidate(array $data, ?int $excludeId = null): array {
    $errors = [];
    $pdo = db();
    
    if (empty($data['first_name'])) {
        $errors['first_name'] = 'First name is required';
    } elseif (strlen($data['first_name']) > 100) {
        $errors['first_name'] = 'First name too long';
    }
    
    if (empty($data['last_name'])) {
        $errors['last_name'] = 'Last name is required';
    } elseif (strlen($data['last_name']) > 100) {
        $errors['last_name'] = 'Last name too long';
    }
    
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (!empty($data['phone']) && !v_phone($data['phone'])) {
        $errors['phone'] = 'Invalid phone number format';
    }
    
    // Duplicate candidate detection
    $dupParams = [];
    $dupConditions = [];
    if (!empty($data['email'])) {
        $dupConditions[] = 'email = :email';
        $dupParams[':email'] = $data['email'];
    }
    if (!empty($data['phone'])) {
        $dupConditions[] = 'phone = :phone';
        $dupParams[':phone'] = $data['phone'];
    }
    if (!empty($data['passport_number'])) {
        $dupConditions[] = 'passport_number = :passport';
        $dupParams[':passport'] = $data['passport_number'];
    }
    
    if (!empty($dupConditions)) {
        $sql = "SELECT id, candidate_code, first_name, last_name FROM candidates WHERE (" . implode(' OR ', $dupConditions) . ")";
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $dupParams[':exclude_id'] = $excludeId;
        }
        $sql .= " LIMIT 1";
        $dupStmt = $pdo->prepare($sql);
        $dupStmt->execute($dupParams);
        $duplicate = $dupStmt->fetch();
        
        if ($duplicate) {
            $errors['duplicate'] = 'Possible duplicate candidate detected: ' . $duplicate['first_name'] . ' ' . $duplicate['last_name'] . ' (' . $duplicate['candidate_code'] . '). Please verify before proceeding.';
        }
    }
    
    if (!empty($data['dob']) && !v_date($data['dob'])) {
        $errors['dob'] = 'Invalid date format (YYYY-MM-DD)';
    }
    
    if (!empty($data['passport_validity']) && !v_date($data['passport_validity'])) {
        $errors['passport_validity'] = 'Invalid date format (YYYY-MM-DD)';
    }
    
    // Validate passport number format (2 letters + 6 digits, e.g., FN123456)
    if (!empty($data['passport_number'])) {
        if (!preg_match('/^[A-Z]{2}\d{6}$/', strtoupper($data['passport_number']))) {
            $errors['passport_number'] = 'Passport number must be 2 letters followed by 6 digits (e.g., FN123456)';
        }
    }
    
    // Add validation for new fields
    if (!empty($data['height_cm']) && ($data['height_cm'] < 50 || $data['height_cm'] > 250)) {
        $errors['height_cm'] = 'Height must be between 50 and 250 cm';
    }
    
    if (!empty($data['weight_kg']) && ($data['weight_kg'] < 30 || $data['weight_kg'] > 200)) {
        $errors['weight_kg'] = 'Weight must be between 30 and 200 kg';
    }
    
    return $errors;
}

function create_action(): string {
    $pdo = db();
    $errors = [];
    $data = [
        'first_name' => '', 'last_name' => '', 'email' => '', 'dob' => '', 'passport_number' => '', 'passport_validity' => '',
        'phone' => '', 'nationality' => '', 'gender' => '', 'agency_id' => null,
        // New fields
        'place_of_birth' => '', 'mothers_name' => '', 'fathers_name' => '', 'marital_status' => '', 'num_children' => 0,
        'height_cm' => null, 'weight_kg' => null, 'is_smoker' => 0, 'has_drivers_license' => 0, 'drivers_license_number' => '',
        'religion' => '', 'address_line1' => '', 'address_line2' => '', 'address_city' => '', 'address_state' => '',
        'address_postal_code' => '', 'address_country' => '', 'emergency_contact_name' => '', 'emergency_contact_relation' => '',
        'emergency_contact_phone' => '', 'emergency_contact_address' => '', 'computer_skills_level' => '', 'computer_skills_notes' => '',
        'personal_statement' => ''
    ];

    if (is_post()) {
        csrf_verify();
        
        // Collect all form data
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
            'agency_id' => ($_POST['agency_id'] ?? '') !== '' ? (int)$_POST['agency_id'] : null,
            // New fields
            'place_of_birth' => trim($_POST['place_of_birth'] ?? ''),
            'mothers_name' => trim($_POST['mothers_name'] ?? ''),
            'fathers_name' => trim($_POST['fathers_name'] ?? ''),
            'marital_status' => trim($_POST['marital_status'] ?? ''),
            'num_children' => (int)($_POST['num_children'] ?? 0),
            'height_cm' => !empty($_POST['height_cm']) ? (int)$_POST['height_cm'] : null,
            'weight_kg' => !empty($_POST['weight_kg']) ? (float)$_POST['weight_kg'] : null,
            'is_smoker' => isset($_POST['is_smoker']) ? 1 : 0,
            'has_drivers_license' => isset($_POST['has_drivers_license']) ? 1 : 0,
            'drivers_license_number' => trim($_POST['drivers_license_number'] ?? ''),
            'religion' => trim($_POST['religion'] ?? ''),
            'address_line1' => trim($_POST['address_line1'] ?? ''),
            'address_line2' => trim($_POST['address_line2'] ?? ''),
            'address_city' => trim($_POST['address_city'] ?? ''),
            'address_state' => trim($_POST['address_state'] ?? ''),
            'address_postal_code' => trim($_POST['address_postal_code'] ?? ''),
            'address_country' => trim($_POST['address_country'] ?? ''),
            'emergency_contact_name' => trim($_POST['emergency_contact_name'] ?? ''),
            'emergency_contact_relation' => trim($_POST['emergency_contact_relation'] ?? ''),
            'emergency_contact_phone' => trim($_POST['emergency_contact_phone'] ?? ''),
            'emergency_contact_address' => trim($_POST['emergency_contact_address'] ?? ''),
            'computer_skills_level' => trim($_POST['computer_skills_level'] ?? ''),
            'computer_skills_notes' => trim($_POST['computer_skills_notes'] ?? ''),
            'personal_statement' => trim($_POST['personal_statement'] ?? '')
        ];

        // Process work experience
        $work_experience = [];
        if (isset($_POST['work_experience']) && is_array($_POST['work_experience'])) {
            foreach ($_POST['work_experience'] as $exp) {
                if (!empty($exp['company']) || !empty($exp['position'])) {
                    $work_experience[] = [
                        'company' => trim($exp['company'] ?? ''),
                        'position' => trim($exp['position'] ?? ''),
                        'start_date' => trim($exp['start_date'] ?? ''),
                        'end_date' => trim($exp['end_date'] ?? ''),
                        'description' => trim($exp['description'] ?? '')
                    ];
                }
            }
        }
        // Store work experience for later insertion into candidate_work_experience table
        $data['work_experience_data'] = $work_experience;

        // Process academic qualifications
        $academic_qualifications = [];
        if (isset($_POST['academic_qualifications']) && is_array($_POST['academic_qualifications'])) {
            foreach ($_POST['academic_qualifications'] as $qual) {
                if (!empty($qual['institution']) || !empty($qual['degree'])) {
                    $academic_qualifications[] = [
                        'institution' => trim($qual['institution'] ?? ''),
                        'degree' => trim($qual['degree'] ?? ''),
                        'field_of_study' => trim($qual['field_of_study'] ?? ''),
                        'graduation_year' => trim($qual['graduation_year'] ?? ''),
                        'grade' => trim($qual['grade'] ?? '')
                    ];
                }
            }
        }
        // Store academic qualifications for later insertion into candidate_education table
        $data['academic_qualifications_data'] = $academic_qualifications;

        $errors = validate_candidate($data);
        if (!$errors) {
            $code = candidates_code_generate($pdo);
            $sql = "INSERT INTO candidates (
                candidate_code, first_name, last_name, dob, place_of_birth, mothers_name, fathers_name,
                passport_number, passport_validity, phone, email, nationality, gender, marital_status,
                num_children, height_cm, weight_kg, is_smoker, has_drivers_license, drivers_license_number,
                religion, address_line1, address_line2, address_city, address_state, address_postal_code,
                address_country, emergency_contact_name, emergency_contact_relation, emergency_contact_phone,
                emergency_contact_address, computer_skills_level, computer_skills_notes, personal_statement,
                status, progress_percent, agency_id, created_by, updated_by
            ) VALUES (
                :code, :first_name, :last_name, :dob, :place_of_birth, :mothers_name, :fathers_name,
                :passport_number, :passport_validity, :phone, :email, :nationality, :gender, :marital_status,
                :num_children, :height_cm, :weight_kg, :is_smoker, :has_drivers_license, :drivers_license_number,
                :religion, :address_line1, :address_line2, :address_city, :address_state, :address_postal_code,
                :address_country, :emergency_contact_name, :emergency_contact_relation, :emergency_contact_phone,
                :emergency_contact_address, :computer_skills_level, :computer_skills_notes, :personal_statement,
                'basic_profile_created', 30, :agency_id, :uid1, :uid2
            )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':code' => $code,
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':dob' => $data['dob'] ?: null,
                ':place_of_birth' => $data['place_of_birth'] ?: null,
                ':mothers_name' => $data['mothers_name'] ?: null,
                ':fathers_name' => $data['fathers_name'] ?: null,
                ':passport_number' => $data['passport_number'] ?: null,
                ':passport_validity' => $data['passport_validity'] ?: null,
                ':phone' => $data['phone'] ?: null,
                ':email' => $data['email'] ?: null,
                ':nationality' => $data['nationality'] ?: null,
                ':gender' => $data['gender'] ?: null,
                ':marital_status' => $data['marital_status'] ?: null,
                ':num_children' => $data['num_children'],
                ':height_cm' => $data['height_cm'],
                ':weight_kg' => $data['weight_kg'],
                ':is_smoker' => $data['is_smoker'],
                ':has_drivers_license' => $data['has_drivers_license'],
                ':drivers_license_number' => $data['drivers_license_number'] ?: null,
                ':religion' => $data['religion'] ?: null,
                ':address_line1' => $data['address_line1'] ?: null,
                ':address_line2' => $data['address_line2'] ?: null,
                ':address_city' => $data['address_city'] ?: null,
                ':address_state' => $data['address_state'] ?: null,
                ':address_postal_code' => $data['address_postal_code'] ?: null,
                ':address_country' => $data['address_country'] ?: null,
                ':emergency_contact_name' => $data['emergency_contact_name'] ?: null,
                ':emergency_contact_relation' => $data['emergency_contact_relation'] ?: null,
                ':emergency_contact_phone' => $data['emergency_contact_phone'] ?: null,
                ':emergency_contact_address' => $data['emergency_contact_address'] ?: null,
                ':computer_skills_level' => $data['computer_skills_level'] ?: null,
                ':computer_skills_notes' => $data['computer_skills_notes'] ?: null,
                ':personal_statement' => $data['personal_statement'] ?: null,
                ':agency_id' => $data['agency_id'],
                ':uid1' => current_user()['id'],
                ':uid2' => current_user()['id'],
            ]);

            $id = (int)$pdo->lastInsertId();

            // Insert work experience into candidate_work_experience table
            if (!empty($data['work_experience_data'])) {
                $workExpStmt = $pdo->prepare('INSERT INTO candidate_work_experience (candidate_id, employer_name, position, start_date, end_date, description) VALUES (:cid, :employer, :position, :start_date, :end_date, :description)');
                foreach ($data['work_experience_data'] as $exp) {
                    $workExpStmt->execute([
                        ':cid' => $id,
                        ':employer' => $exp['company'],
                        ':position' => $exp['position'],
                        ':start_date' => $exp['start_date'] ?: null,
                        ':end_date' => $exp['end_date'] ?: null,
                        ':description' => $exp['description'] ?: null
                    ]);
                }
            }

            // Insert academic qualifications into candidate_education table
            if (!empty($data['academic_qualifications_data'])) {
                $eduStmt = $pdo->prepare('INSERT INTO candidate_education (candidate_id, institution_name, degree, field_of_study, end_date, description) VALUES (:cid, :institution, :degree, :field, :end_date, :description)');
                foreach ($data['academic_qualifications_data'] as $qual) {
                    $eduStmt->execute([
                        ':cid' => $id,
                        ':institution' => $qual['institution'],
                        ':degree' => $qual['degree'],
                        ':field' => $qual['field_of_study'] ?: null,
                        ':end_date' => $qual['graduation_year'] ? ($qual['graduation_year'] . '-12-31') : null,
                        ':description' => $qual['grade'] ?: null
                    ]);
                }
            }

            // Persist candidate roles
            $roleIds = array_map('intval', (array)($_POST['role_ids'] ?? []));
            $roleIds = array_values(array_unique(array_filter($roleIds)));
            if (!empty($roleIds)) {
                $ins = $pdo->prepare('INSERT INTO candidate_roles (candidate_id, role_id) VALUES (:cid, :rid)');
                foreach ($roleIds as $rid) { $ins->execute([':cid' => $id, ':rid' => $rid]); }
            }

            redirect(base_url('index.php?page=candidates&action=view&id=' . $id));
        }
    }

    $agencies = db()->query('SELECT id, name FROM agencies WHERE is_active=1 ORDER BY name')->fetchAll();
    $roles = $pdo->query('SELECT id, name FROM job_roles WHERE is_active=1 ORDER BY name')->fetchAll();
    return view('candidates_form', ['data' => $data, 'errors' => $errors, 'agencies' => $agencies, 'roles' => $roles, 'selected_roles' => [], 'mode' => 'create']);
}

function edit_action(): string {
    error_log('Starting edit_action for candidate ID: ' . ($_GET['id'] ?? 'null'));
    
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    
    // Initialize all fields with empty values
    $data = [
        'first_name' => '', 'last_name' => '', 'email' => '', 'dob' => '', 'passport_number' => '', 'passport_validity' => '',
        'phone' => '', 'nationality' => '', 'gender' => '', 'agency_id' => null,
        'place_of_birth' => '', 'mothers_name' => '', 'fathers_name' => '', 'marital_status' => '', 'num_children' => 0,
        'height_cm' => null, 'weight_kg' => null, 'is_smoker' => 0, 'has_drivers_license' => 0, 'drivers_license_number' => '',
        'religion' => '', 'address_line1' => '', 'address_line2' => '', 'address_city' => '', 'address_state' => '',
        'address_postal_code' => '', 'address_country' => '', 'emergency_contact_name' => '', 'emergency_contact_relation' => '',
        'emergency_contact_phone' => '', 'emergency_contact_address' => '', 'computer_skills_level' => '', 'computer_skills_notes' => '',
        'personal_statement' => '', 'candidate_code' => '', 'status' => '', 'progress_percent' => '', 'created_by' => '', 'updated_by' => '', 'created_at' => '', 'updated_at' => ''
    ];
    
    try {
        // Load candidate data
        $stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($candidate) {
            error_log('Retrieved candidate data: ' . print_r($candidate, true));
            
            // Merge loaded data with defaults
            foreach ($data as $field => $value) {
                if (array_key_exists($field, $candidate)) {
                    $data[$field] = $candidate[$field];
                }
            }
            
            // Load work experience from candidate_work_experience table
            $workExpStmt = $pdo->prepare('SELECT employer_name as company, position, start_date, end_date, description FROM candidate_work_experience WHERE candidate_id = :id ORDER BY start_date DESC');
            $workExpStmt->execute([':id' => $id]);
            $data['work_experience'] = $workExpStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Load academic qualifications from candidate_education table
            $eduStmt = $pdo->prepare('SELECT institution_name as institution, degree, field_of_study, YEAR(end_date) as graduation_year, description as grade FROM candidate_education WHERE candidate_id = :id ORDER BY end_date DESC');
            $eduStmt->execute([':id' => $id]);
            $data['academic_qualifications'] = $eduStmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log('Data being sent to view: ' . print_r($data, true));
        } else {
            error_log('No candidate found for ID: ' . $id);
            http_response_code(404);
            return 'Candidate not found';
        }
        
        $errors = [];
        
        if (is_post()) {
            csrf_verify();
            
            // Collect all form data
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
                'agency_id' => ($_POST['agency_id'] ?? '') !== '' ? (int)$_POST['agency_id'] : null,
                // New fields
                'place_of_birth' => trim($_POST['place_of_birth'] ?? ''),
                'mothers_name' => trim($_POST['mothers_name'] ?? ''),
                'fathers_name' => trim($_POST['fathers_name'] ?? ''),
                'marital_status' => trim($_POST['marital_status'] ?? ''),
                'num_children' => (int)($_POST['num_children'] ?? 0),
                'height_cm' => !empty($_POST['height_cm']) ? (int)$_POST['height_cm'] : null,
                'weight_kg' => !empty($_POST['weight_kg']) ? (float)$_POST['weight_kg'] : null,
                'is_smoker' => isset($_POST['is_smoker']) ? 1 : 0,
                'has_drivers_license' => isset($_POST['has_drivers_license']) ? 1 : 0,
                'drivers_license_number' => trim($_POST['drivers_license_number'] ?? ''),
                'religion' => trim($_POST['religion'] ?? ''),
                'address_line1' => trim($_POST['address_line1'] ?? ''),
                'address_line2' => trim($_POST['address_line2'] ?? ''),
                'address_city' => trim($_POST['address_city'] ?? ''),
                'address_state' => trim($_POST['address_state'] ?? ''),
                'address_postal_code' => trim($_POST['address_postal_code'] ?? ''),
                'address_country' => trim($_POST['address_country'] ?? ''),
                'emergency_contact_name' => trim($_POST['emergency_contact_name'] ?? ''),
                'emergency_contact_relation' => trim($_POST['emergency_contact_relation'] ?? ''),
                'emergency_contact_phone' => trim($_POST['emergency_contact_phone'] ?? ''),
                'emergency_contact_address' => trim($_POST['emergency_contact_address'] ?? ''),
                'computer_skills_level' => trim($_POST['computer_skills_level'] ?? ''),
                'computer_skills_notes' => trim($_POST['computer_skills_notes'] ?? ''),
                'personal_statement' => trim($_POST['personal_statement'] ?? '')
            ];

            // Process work experience
            $work_experience = [];
            if (isset($_POST['work_experience']) && is_array($_POST['work_experience'])) {
                foreach ($_POST['work_experience'] as $exp) {
                    if (!empty($exp['company']) || !empty($exp['position'])) {
                        $work_experience[] = [
                            'company' => trim($exp['company'] ?? ''),
                            'position' => trim($exp['position'] ?? ''),
                            'start_date' => trim($exp['start_date'] ?? ''),
                            'end_date' => trim($exp['end_date'] ?? ''),
                            'description' => trim($exp['description'] ?? '')
                        ];
                    }
                }
            }
            // Store work experience for later insertion into candidate_work_experience table
            $data['work_experience_data'] = $work_experience;

            // Process academic qualifications
            $academic_qualifications = [];
            if (isset($_POST['academic_qualifications']) && is_array($_POST['academic_qualifications'])) {
                foreach ($_POST['academic_qualifications'] as $qual) {
                    if (!empty($qual['institution']) || !empty($qual['degree'])) {
                        $academic_qualifications[] = [
                            'institution' => trim($qual['institution'] ?? ''),
                            'degree' => trim($qual['degree'] ?? ''),
                            'field_of_study' => trim($qual['field_of_study'] ?? ''),
                            'graduation_year' => trim($qual['graduation_year'] ?? ''),
                            'grade' => trim($qual['grade'] ?? '')
                        ];
                    }
                }
            }
            // Store academic qualifications for later insertion into candidate_education table
            $data['academic_qualifications_data'] = $academic_qualifications;

            $errors = validate_candidate($data, $id);
            if (!$errors) {
                $sql = "UPDATE candidates SET
                    first_name = :first_name,
                    last_name = :last_name,
                    dob = :dob,
                    place_of_birth = :place_of_birth,
                    mothers_name = :mothers_name,
                    fathers_name = :fathers_name,
                    passport_number = :passport_number,
                    passport_validity = :passport_validity,
                    phone = :phone,
                    email = :email,
                    nationality = :nationality,
                    gender = :gender,
                    marital_status = :marital_status,
                    num_children = :num_children,
                    height_cm = :height_cm,
                    weight_kg = :weight_kg,
                    is_smoker = :is_smoker,
                    has_drivers_license = :has_drivers_license,
                    drivers_license_number = :drivers_license_number,
                    religion = :religion,
                    address_line1 = :address_line1,
                    address_line2 = :address_line2,
                    address_city = :address_city,
                    address_state = :address_state,
                    address_postal_code = :address_postal_code,
                    address_country = :address_country,
                    emergency_contact_name = :emergency_contact_name,
                    emergency_contact_relation = :emergency_contact_relation,
                    emergency_contact_phone = :emergency_contact_phone,
                    emergency_contact_address = :emergency_contact_address,
                    computer_skills_level = :computer_skills_level,
                    computer_skills_notes = :computer_skills_notes,
                    personal_statement = :personal_statement,
                    agency_id = :agency_id,
                    updated_by = :uid
                WHERE id = :id";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':first_name' => $data['first_name'],
                    ':last_name' => $data['last_name'],
                    ':dob' => $data['dob'] ?: null,
                    ':place_of_birth' => $data['place_of_birth'] ?: null,
                    ':mothers_name' => $data['mothers_name'] ?: null,
                    ':fathers_name' => $data['fathers_name'] ?: null,
                    ':passport_number' => $data['passport_number'] ?: null,
                    ':passport_validity' => $data['passport_validity'] ?: null,
                    ':phone' => $data['phone'] ?: null,
                    ':email' => $data['email'] ?: null,
                    ':nationality' => $data['nationality'] ?: null,
                    ':gender' => $data['gender'] ?: null,
                    ':marital_status' => $data['marital_status'] ?: null,
                    ':num_children' => $data['num_children'],
                    ':height_cm' => $data['height_cm'],
                    ':weight_kg' => $data['weight_kg'],
                    ':is_smoker' => $data['is_smoker'],
                    ':has_drivers_license' => $data['has_drivers_license'],
                    ':drivers_license_number' => $data['drivers_license_number'] ?: null,
                    ':religion' => $data['religion'] ?: null,
                    ':address_line1' => $data['address_line1'] ?: null,
                    ':address_line2' => $data['address_line2'] ?: null,
                    ':address_city' => $data['address_city'] ?: null,
                    ':address_state' => $data['address_state'] ?: null,
                    ':address_postal_code' => $data['address_postal_code'] ?: null,
                    ':address_country' => $data['address_country'] ?: null,
                    ':emergency_contact_name' => $data['emergency_contact_name'] ?: null,
                    ':emergency_contact_relation' => $data['emergency_contact_relation'] ?: null,
                    ':emergency_contact_phone' => $data['emergency_contact_phone'] ?: null,
                    ':emergency_contact_address' => $data['emergency_contact_address'] ?: null,
                    ':computer_skills_level' => $data['computer_skills_level'] ?: null,
                    ':computer_skills_notes' => $data['computer_skills_notes'] ?: null,
                    ':personal_statement' => $data['personal_statement'] ?: null,
                    ':agency_id' => $data['agency_id'],
                    ':uid' => current_user()['id'],
                    ':id' => $id
                ]);

                // Update work experience
                $pdo->prepare('DELETE FROM candidate_work_experience WHERE candidate_id=:cid')->execute([':cid' => $id]);
                if (!empty($data['work_experience_data'])) {
                    $workExpStmt = $pdo->prepare('INSERT INTO candidate_work_experience (candidate_id, employer_name, position, start_date, end_date, description) VALUES (:cid, :employer, :position, :start_date, :end_date, :description)');
                    foreach ($data['work_experience_data'] as $exp) {
                        $workExpStmt->execute([
                            ':cid' => $id,
                            ':employer' => $exp['company'],
                            ':position' => $exp['position'],
                            ':start_date' => $exp['start_date'] ?: null,
                            ':end_date' => $exp['end_date'] ?: null,
                            ':description' => $exp['description'] ?: null
                        ]);
                    }
                }

                // Update academic qualifications
                $pdo->prepare('DELETE FROM candidate_education WHERE candidate_id=:cid')->execute([':cid' => $id]);
                if (!empty($data['academic_qualifications_data'])) {
                    $eduStmt = $pdo->prepare('INSERT INTO candidate_education (candidate_id, institution_name, degree, field_of_study, end_date, description) VALUES (:cid, :institution, :degree, :field, :end_date, :description)');
                    foreach ($data['academic_qualifications_data'] as $qual) {
                        $eduStmt->execute([
                            ':cid' => $id,
                            ':institution' => $qual['institution'],
                            ':degree' => $qual['degree'],
                            ':field' => $qual['field_of_study'] ?: null,
                            ':end_date' => $qual['graduation_year'] ? ($qual['graduation_year'] . '-12-31') : null,
                            ':description' => $qual['grade'] ?: null
                        ]);
                    }
                }

                // Update candidate roles
                $pdo->prepare('DELETE FROM candidate_roles WHERE candidate_id=:cid')->execute([':cid' => $id]);
                $roleIds = array_map('intval', (array)($_POST['role_ids'] ?? []));
                $roleIds = array_values(array_unique(array_filter($roleIds)));
                if (!empty($roleIds)) {
                    $ins = $pdo->prepare('INSERT INTO candidate_roles (candidate_id, role_id) VALUES (:cid, :rid)');
                    foreach ($roleIds as $rid) { $ins->execute([':cid' => $id, ':rid' => $rid]); }
                }

                redirect(base_url('index.php?page=candidates&action=view&id=' . $id));
            }
        }

        $agencies = db()->query('SELECT id, name FROM agencies WHERE is_active=1 ORDER BY name')->fetchAll();
        $roles = $pdo->query('SELECT id, name FROM job_roles WHERE is_active=1 ORDER BY name')->fetchAll();
        $selected = $pdo->prepare('SELECT role_id FROM candidate_roles WHERE candidate_id=:cid');
        $selected->execute([':cid' => $id]);
        $selected_roles = array_map('intval', array_column($selected->fetchAll(), 'role_id'));
        return view('candidates_form', ['data' => $data, 'errors' => $errors, 'agencies' => $agencies, 'roles' => $roles, 'selected_roles' => $selected_roles, 'mode' => 'edit']);
    } catch (PDOException $e) {
        error_log('Candidate edit error: ' . $e->getMessage());
        http_response_code(500);
        return 'Database error';
    }
}

function view_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    // Use the same logic as the candidates list to show consistent status
    $stmt = $pdo->prepare("SELECT c.*, a.name AS agency_name,
                          CASE WHEN EXISTS (SELECT 1 FROM deployments d WHERE d.candidate_id=c.id AND d.status IN ('active','completed'))
                               THEN 'deployed'
                               ELSE c.status
                          END AS status
                          FROM candidates c
                          LEFT JOIN agencies a ON a.id=c.agency_id
                          WHERE c.id=:id");
    $stmt->execute([':id' => $id]);
    $cand = $stmt->fetch();
    if (!$cand) { http_response_code(404); return 'Candidate not found'; }

    // Fetch candidate documents
    $docs = $pdo->prepare('SELECT * FROM candidate_documents WHERE candidate_id=:id AND is_active=1 ORDER BY uploaded_at DESC');
    $docs->execute([':id' => $id]);
    $documents = $docs->fetchAll();

    // Load work experience from candidate_work_experience table
    $workExpStmt = $pdo->prepare('SELECT employer_name as company, position, start_date, end_date, description FROM candidate_work_experience WHERE candidate_id = :id ORDER BY start_date DESC');
    $workExpStmt->execute([':id' => $id]);
    $work_experience = $workExpStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Load academic qualifications from candidate_education table
    $eduStmt = $pdo->prepare('SELECT institution_name as institution, degree, field_of_study, YEAR(end_date) as graduation_year, description as grade FROM candidate_education WHERE candidate_id = :id ORDER BY end_date DESC');
    $eduStmt->execute([':id' => $id]);
    $academic_qualifications = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch roles
    $rs = $pdo->prepare('SELECT r.name FROM candidate_roles cr JOIN job_roles r ON r.id=cr.role_id WHERE cr.candidate_id=:cid ORDER BY r.name');
    $rs->execute([':cid' => $id]);
    $roles = array_column($rs->fetchAll(), 'name');

    // Fetch generated document history
    $generatedDocsStmt = $pdo->prepare('SELECT gd.*, u.username AS generated_by_user FROM generated_documents gd LEFT JOIN users u ON gd.generated_by = u.id WHERE gd.candidate_id = :cid ORDER BY gd.generated_at DESC LIMIT 50');
    $generatedDocsStmt->execute([':cid' => $id]);
    $generatedDocuments = $generatedDocsStmt->fetchAll();

    return view('candidates_view', [
        'cand' => $cand,
        'documents' => $documents,
        'roles' => $roles,
        'work_experience' => $work_experience,
        'academic_qualifications' => $academic_qualifications,
        'generatedDocuments' => $generatedDocuments
    ]);
}

function download_cv_action(): void {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { 
        http_response_code(400); 
        echo 'Invalid ID'; 
        return; 
    }
    
    // Fetch candidate data
    $stmt = $pdo->prepare("SELECT c.*, a.name AS agency_name FROM candidates c LEFT JOIN agencies a ON a.id=c.agency_id WHERE c.id=:id");
    $stmt->execute([':id' => $id]);
    $cand = $stmt->fetch();
    if (!$cand) { 
        http_response_code(404); 
        echo 'Candidate not found'; 
        return; 
    }

    // Load work experience from candidate_work_experience table
    $workExpStmt = $pdo->prepare('SELECT employer_name as company, position, start_date, end_date, description FROM candidate_work_experience WHERE candidate_id = :id ORDER BY start_date DESC');
    $workExpStmt->execute([':id' => $id]);
    $workExperience = $workExpStmt->fetchAll(PDO::FETCH_ASSOC);

    // Load academic qualifications from candidate_education table
    $eduStmt = $pdo->prepare('SELECT institution_name as institution, degree, field_of_study, YEAR(end_date) as graduation_year, description as grade FROM candidate_education WHERE candidate_id = :id ORDER BY end_date DESC');
    $eduStmt->execute([':id' => $id]);
    $education = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch documents
    $docs = $pdo->prepare('SELECT * FROM candidate_documents WHERE candidate_id=:id AND is_active=1 ORDER BY uploaded_at DESC');
    $docs->execute([':id' => $id]);
    $documents = $docs->fetchAll();

    // Fetch roles
    $rs = $pdo->prepare('SELECT r.name FROM candidate_roles cr JOIN job_roles r ON r.id=cr.role_id WHERE cr.candidate_id=:cid ORDER BY r.name');
    $rs->execute([':cid' => $id]);
    $roles = array_column($rs->fetchAll(), 'name');

    try {
        require_once __DIR__ . '/../lib/pdf_generator.php';
        
        $generator = new CandidatePDFGenerator($cand, $workExperience, $education, $documents, $roles);
        $pdfContent = $generator->generate();
        
        // Set headers for PDF download
        $filename = 'CV_' . $cand['candidate_code'] . '_' . $cand['first_name'] . '_' . $cand['last_name'] . '.pdf';
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $pdfContent;
        exit;
        
    } catch (Exception $e) {
        error_log('PDF generation error: ' . $e->getMessage());
        http_response_code(500);
        echo 'Error generating PDF: ' . $e->getMessage();
    }
}

function generate_destination_cv_action(): void {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    $destination = trim($_GET['destination'] ?? '');
    
    if ($id <= 0 || $destination === '') { 
        http_response_code(400); 
        echo 'Invalid candidate or destination'; 
        return; 
    }
    
    // Map destination to template type
    $destinationMap = [
        'lithuania' => 'cv_lithuania',
        'turkey' => 'cv_turkey',
        'generic' => 'cv_generic',
    ];
    
    $templateType = $destinationMap[strtolower($destination)] ?? 'cv_generic';
    
    try {
        // Find active template for this destination
        $templateStmt = $pdo->prepare("SELECT * FROM document_templates WHERE template_type = :type AND is_active = 1 ORDER BY created_at DESC LIMIT 1");
        $templateStmt->execute([':type' => $templateType]);
        $template = $templateStmt->fetch();
        
        if (!$template) {
            http_response_code(404);
            echo 'No active template found for destination: ' . htmlspecialchars($destination);
            return;
        }
        
        // Find most recent questionnaire request for this candidate
        $qrStmt = $pdo->prepare("SELECT id FROM questionnaire_requests WHERE candidate_id = :cid ORDER BY created_at DESC LIMIT 1");
        $qrStmt->execute([':cid' => $id]);
        $questionnaireRequest = $qrStmt->fetch();
        $questionnaireRequestId = $questionnaireRequest ? (int)$questionnaireRequest['id'] : null;
        
        // Build document data
        require_once __DIR__ . '/../lib/document_generator.php';
        $documentData = DocumentGenerator::buildDataFromMappings($pdo, (int)$template['id'], $id, $questionnaireRequestId);
        
        // Generate document
        $generator = new DocumentGenerator($template, $documentData);
        $documentContent = $generator->generate();
        
        // Save generated document and version history
        $savedDoc = DocumentGenerator::saveGeneratedDocument(
            $pdo,
            $id,
            $questionnaireRequestId,
            (int)$template['id'],
            $destination,
            $documentContent,
            null,
            current_user()['id'] ?? null
        );
        
        // Advance candidate to next recruitment stage automatically
        $newStatus = advance_candidate_stage($pdo, $id);
        if ($newStatus) {
            error_log("Candidate {$id} advanced to status: {$newStatus}");
        }
        
        // Set headers for download
        $filename = $savedDoc['filename'];
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($documentContent));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $documentContent;
        exit;
        
    } catch (Exception $e) {
        error_log('Destination CV generation error: ' . $e->getMessage());
        http_response_code(500);
        echo 'Error generating CV: ' . $e->getMessage();
    }
}

function advance_stage_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    csrf_verify();
    
    $newStatus = advance_candidate_stage($pdo, $id);
    if ($newStatus) {
        set_flash_message('Candidate advanced to stage: ' . ucfirst(str_replace('_', ' ', $newStatus)), 'success');
    } else {
        set_flash_message('Candidate is already at the final stage.', 'info');
    }
    
    redirect(base_url('index.php?page=candidates&action=view&id=' . $id));
}

function list_action(): string {
    $pdo = db();
    $q = trim($_GET['q'] ?? '');
    $status = trim($_GET['status'] ?? '');
    // Sorting
    $sortable = ['candidate_code','first_name','email','status','agency','created_at'];
    $sort = $_GET['sort'] ?? 'created_at';
    $dir = strtolower($_GET['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
    if (!in_array($sort, $sortable, true)) { $sort = 'created_at'; }

    // Pagination
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min(100, max(10, (int)($_GET['per'] ?? 20)));
    $offset = ($page - 1) * $perPage;

    // Build dynamic WHERE to avoid repeated placeholders
    $where = [];
    $params = [];
    if ($q !== '') {
        $where[] = '(c.first_name LIKE ? OR c.last_name LIKE ? OR c.candidate_code LIKE ?)';
        $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%";
    }
    if ($status !== '') {
        if ($status === 'deployed') {
            $where[] = 'EXISTS (SELECT 1 FROM deployments d WHERE d.candidate_id=c.id AND d.status IN (\'active\',\'completed\'))';
        } else {
            // For non-deployed statuses, ensure the candidate is NOT deployed and has the specified status
            $where[] = 'c.status = ? AND NOT EXISTS (SELECT 1 FROM deployments d WHERE d.candidate_id=c.id AND d.status IN (\'active\',\'completed\'))';
            $params[] = $status;
        }
    }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    // Count total
    $countSql = "SELECT COUNT(*) AS cnt
                 FROM candidates c
                 LEFT JOIN agencies a ON a.id=c.agency_id
                 $whereSql";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetch()['cnt'];

    // Data
    $orderBy = $sort === 'agency' ? 'a.name' : ('c.' . ($sort === 'created_at' ? 'created_at' : $sort));
    $lim = (int)$perPage; $off = (int)$offset;
    $sql = "SELECT c.id, c.candidate_code, c.first_name, c.last_name, c.email,
                   CASE WHEN EXISTS (SELECT 1 FROM deployments d WHERE d.candidate_id=c.id AND d.status IN ('active','completed')) THEN 'deployed' ELSE c.status END AS status,
                   a.name AS agency, c.created_at
            FROM candidates c
            LEFT JOIN agencies a ON a.id=c.agency_id
            $whereSql
            ORDER BY $orderBy $dir
            LIMIT $lim OFFSET $off";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    $pages = (int)ceil($total / $perPage);
    return view('candidates_list', [
        'rows' => $rows,
        'q' => $q,
        'status' => $status,
        'sort' => $sort,
        'dir' => strtolower($dir),
        'page' => $page,
        'pages' => $pages,
        'per' => $perPage,
        'total' => $total,
    ]);
}
