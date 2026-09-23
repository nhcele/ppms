<?php
// app/controllers/templates.php

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);

function create_action(): string {
    $pdo = db();
    $errors = [];
    $data = [
        'template_name' => '',
        'template_type' => '',
        'template_category' => '',
        'description' => ''
    ];

    if (is_post()) {
        csrf_verify();
        
        $data = [
            'template_name' => trim($_POST['template_name'] ?? ''),
            'template_type' => trim($_POST['template_type'] ?? ''),
            'template_category' => trim($_POST['template_category'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        $errors = validate_template($data);
        
        if (!$errors) {
            try {
                $sql = "INSERT INTO document_templates (
                    template_name, template_type, template_category, description, created_by
                ) VALUES (
                    :name, :type, :category, :description, :uid
                )";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':name' => $data['template_name'],
                    ':type' => $data['template_type'],
                    ':category' => $data['template_category'],
                    ':description' => $data['description'] ?: null,
                    ':uid' => current_user()['id']
                ]);
                
                redirect(base_url('index.php?page=templates&action=view&id=' . $pdo->lastInsertId()));
                
            } catch (Exception $e) {
                error_log('Template creation error: ' . $e->getMessage());
                $errors['general'] = 'Failed to create template';
            }
        }
    }

    return view('templates_form', [
        'data' => $data, 
        'errors' => $errors,
        'mode' => 'create'
    ]);
}

function validate_template(array $data): array {
    $errors = [];
    
    if (empty($data['template_name'])) {
        $errors['template_name'] = 'Template name is required';
    }
    
    if (empty($data['template_type'])) {
        $errors['template_type'] = 'Template type is required';
    }
    
    if (empty($data['template_category'])) {
        $errors['template_category'] = 'Template category is required';
    }
    
    return $errors;
}

function view_action(): string {
    $pdo = db();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid ID'; }
    
    $stmt = $pdo->prepare("SELECT dt.*, u.username AS created_by_user 
                          FROM document_templates dt
                          LEFT JOIN users u ON dt.created_by = u.id
                          WHERE dt.id = :id");
    $stmt->execute([':id' => $id]);
    $template = $stmt->fetch();
    
    if (!$template) { http_response_code(404); return 'Template not found'; }
    
    // Get field mappings
    $mappingStmt = $pdo->prepare("SELECT * FROM template_field_mappings WHERE template_id = :id ORDER BY field_order");
    $mappingStmt->execute([':id' => $id]);
    $mappings = $mappingStmt->fetchAll();
    
    return view('templates_view', [
        'template' => $template,
        'mappings' => $mappings
    ]);
}

function list_action(): string {
    $pdo = db();
    $type = trim($_GET['type'] ?? '');
    $category = trim($_GET['category'] ?? '');
    
    // Build WHERE clause
    $where = [];
    $params = [];
    
    if ($type !== '') {
        $where[] = 'template_type = ?';
        $params[] = $type;
    }
    
    if ($category !== '') {
        $where[] = 'template_category = ?';
        $params[] = $category;
    }
    
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    
    $sql = "SELECT dt.*, u.username AS created_by_user
            FROM document_templates dt
            LEFT JOIN users u ON dt.created_by = u.id
            $whereSql
            ORDER BY dt.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $templates = $stmt->fetchAll();
    
    return view('templates_list', [
        'templates' => $templates,
        'type' => $type,
        'category' => $category
    ]);
}

function get_friendly_mapping_options(): array {
    return [
        'template_fields' => [
            'full_name' => 'Full Name',
            'surname' => 'Surname',
            'date_of_birth' => 'Date of Birth',
            'place_of_birth' => 'Place of Birth',
            'citizenship' => 'Citizenship / Nationality',
            'former_citizenship' => 'Former Citizenship',
            'phone' => 'Phone Number',
            'email' => 'Email Address',
            'marital_status' => 'Marital Status',
            'marriage_date' => 'Marriage Date',
            'height' => 'Height',
            'weight' => 'Weight',
            'address' => 'Home Address',
            'home_address' => 'Home Address (full)',
            'lithuania_address' => 'Lithuania Address',
            'video_link' => 'Video Link',
            'languages' => 'Languages Known',
            'health_condition' => 'Health Condition',
            'relatives_europe' => 'Relatives/Friends in Europe',
            'previous_europe_work' => 'Previous Work in Europe',
            'work_experience' => 'Work Experience',
            'education' => 'Education',
            'personal_statement' => 'Personal Statement',
            'computer_skills' => 'Computer Skills',
            'convicted_crime' => 'Criminal Conviction',
            'crime_details' => 'Crime Details',
            'visa_denied' => 'Visa Denied',
            'denial_details' => 'Visa Denial Details',
            'visas_last_5_years' => 'Visas/Residence Permits (Last 5 Years)',
            'travel_history' => 'Travel History',
            'emergency_contact_phone' => 'Emergency Contact Phone',
            'other' => 'Other (custom field)'
        ],
        'candidate_sources' => [
            'first_name' => 'Candidate: First Name',
            'last_name' => 'Candidate: Last Name',
            'full_name' => 'Candidate: Full Name (auto)',
            'dob' => 'Candidate: Date of Birth',
            'place_of_birth' => 'Candidate: Place of Birth',
            'passport_number' => 'Candidate: Passport Number',
            'passport_validity' => 'Candidate: Passport Validity',
            'phone' => 'Candidate: Phone',
            'email' => 'Candidate: Email',
            'nationality' => 'Candidate: Nationality',
            'gender' => 'Candidate: Gender',
            'marital_status' => 'Candidate: Marital Status',
            'height_cm' => 'Candidate: Height (cm)',
            'weight_kg' => 'Candidate: Weight (kg)',
            'religion' => 'Candidate: Religion',
            'address' => 'Candidate: Full Address (auto)',
            'emergency_contact_phone' => 'Candidate: Emergency Contact Phone',
            'computer_skills_level' => 'Candidate: Computer Skills Level',
            'personal_statement' => 'Candidate: Personal Statement',
            'work_experience' => 'Candidate: Work Experience History',
            'education' => 'Candidate: Education History',
            'is_smoker' => 'Candidate: Smoker?'
        ],
        'questionnaire_sources' => [
            'personal_info.date_of_marriage' => 'Questionnaire: Marriage Date',
            'additional_info.video_link' => 'Questionnaire: Video Link',
            'additional_info.languages' => 'Questionnaire: Languages Known',
            'additional_info.health_condition' => 'Questionnaire: Health Condition',
            'additional_info.relatives_europe' => 'Questionnaire: Relatives/Friends in Europe',
            'additional_info.previous_europe_work' => 'Questionnaire: Previous Work in Europe',
            'additional_info.daily_prayers' => 'Questionnaire: Daily Prayers',
            'additional_info.work_men_women' => 'Questionnaire: Work with Men & Women',
            'additional_info.work_pork' => 'Questionnaire: Work with Pork/Meat',
            'additional_info.wear_uniform' => 'Questionnaire: Wear Uniform',
            'additional_info.willingness_work_conditions' => 'Questionnaire: Work Conditions',
            'additional_info.lithuania_address' => 'Questionnaire: Lithuania Address',
            'additional_info.former_citizenship' => 'Questionnaire: Former Citizenship',
            'additional_info.visas_last_5_years' => 'Questionnaire: Visas Last 5 Years',
            'declarations.criminal_conviction' => 'Questionnaire: Criminal Conviction',
            'declarations.criminal_details' => 'Questionnaire: Criminal Details',
            'declarations.visa_denied' => 'Questionnaire: Visa Denied',
            'declarations.visa_denied_details' => 'Questionnaire: Visa Denial Details',
            'travel_history.countries' => 'Questionnaire: Countries Visited'
        ],
        'special_sources' => [
            'work_experience' => 'Candidate Work Experience (table)',
            'education' => 'Candidate Education (table)',
            'address' => 'Candidate Full Address (CONCAT)',
            'full_name' => 'Candidate Full Name (CONCAT)'
        ]
    ];
}

function resolve_mapping_source(string $sourceType, string $sourceSelection, string $customSource): array {
    $resolved = [
        'source_type' => $sourceType,
        'database_source' => '',
        'static_value' => ''
    ];
    
    if ($sourceType === 'static') {
        $resolved['static_value'] = $customSource;
        return $resolved;
    }
    
    // If user chose "Other", use the custom source path they typed
    if ($sourceSelection === 'other') {
        $resolved['database_source'] = $customSource;
        return $resolved;
    }
    
    if ($sourceType === 'candidate') {
        if ($sourceSelection === 'address') {
            $resolved['database_source'] = 'CONCAT(address_line1, " ", address_line2, " ", address_city, " ", address_state, " ", address_postal_code, " ", address_country)';
        } elseif ($sourceSelection === 'full_name') {
            $resolved['database_source'] = 'CONCAT(candidates.first_name, " ", candidates.last_name)';
        } else {
            $resolved['database_source'] = $sourceSelection;
        }
    } elseif ($sourceType === 'questionnaire') {
        $resolved['database_source'] = $sourceSelection;
    }
    
    return $resolved;
}

function add_mapping_action(): string {
    $pdo = db();
    $templateId = (int)($_GET['id'] ?? 0);
    if ($templateId <= 0) { http_response_code(400); return 'Invalid Template ID'; }
    
    $errors = [];
    $data = [
        'template_field' => '',
        'custom_template_field' => '',
        'source_type' => 'candidate',
        'source_selection' => '',
        'custom_source' => '',
        'static_value' => '',
        'field_order' => 0,
        'is_required' => 1
    ];
    $mappingOptions = get_friendly_mapping_options();

    if (is_post()) {
        csrf_verify();
        
        $templateField = trim($_POST['template_field'] ?? '');
        $customTemplateField = trim($_POST['custom_template_field'] ?? '');
        $sourceType = trim($_POST['source_type'] ?? 'candidate');
        $sourceSelection = trim($_POST['source_selection'] ?? '');
        $customSource = trim($_POST['custom_source'] ?? '');
        
        $templateFieldName = $templateField === 'other' ? $customTemplateField : $templateField;
        
        $resolved = resolve_mapping_source($sourceType, $sourceSelection, $customSource);
        
        $data = [
            'template_field' => $templateField,
            'custom_template_field' => $customTemplateField,
            'source_type' => $sourceType,
            'source_selection' => $sourceSelection,
            'custom_source' => $customSource,
            'static_value' => $resolved['static_value'],
            'field_order' => (int)($_POST['field_order'] ?? 0),
            'is_required' => isset($_POST['is_required']) ? 1 : 0,
            // Internal values for saving
            'template_field_name' => $templateFieldName,
            'database_source' => $resolved['database_source']
        ];

        $errors = validate_mapping($data);
        
        if (!$errors) {
            try {
                $sql = "INSERT INTO template_field_mappings (
                    template_id, template_field_name, database_source, source_type, static_value, field_order, is_required
                ) VALUES (
                    :template_id, :field_name, :db_source, :source_type, :static_value, :field_order, :is_required
                )";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':template_id' => $templateId,
                    ':field_name' => $data['template_field_name'],
                    ':db_source' => $data['database_source'],
                    ':source_type' => $data['source_type'],
                    ':static_value' => $data['source_type'] === 'static' ? $data['static_value'] : null,
                    ':field_order' => $data['field_order'],
                    ':is_required' => $data['is_required']
                ]);
                
                redirect(base_url('index.php?page=templates&action=view&id=' . $templateId));
                
            } catch (Exception $e) {
                error_log('Mapping creation error: ' . $e->getMessage());
                $errors['general'] = 'Failed to create field mapping';
            }
        }
    }

    return view('templates_mapping_form', [
        'template_id' => $templateId,
        'data' => $data,
        'errors' => $errors,
        'mappingOptions' => $mappingOptions
    ]);
}

function validate_mapping(array $data): array {
    $errors = [];
    
    $templateFieldName = $data['template_field_name'] ?? '';
    if (empty($templateFieldName)) {
        $errors['template_field'] = 'Template field is required';
    }
    
    if ($data['source_type'] !== 'static' && empty($data['database_source'])) {
        $errors['source_selection'] = 'Please select a data source';
    }
    
    if ($data['source_type'] === 'static' && empty($data['static_value'])) {
        $errors['static_value'] = 'Static value is required for static fields';
    }
    
    return $errors;
}

function delete_mapping_action(): string {
    $pdo = db();
    $mappingId = (int)($_GET['mapping_id'] ?? 0);
    $templateId = (int)($_GET['id'] ?? 0);
    
    if ($mappingId <= 0 || $templateId <= 0) { 
        http_response_code(400); 
        return 'Invalid IDs'; 
    }
    
    if (is_post()) {
        csrf_verify();
        
        $pdo->prepare("DELETE FROM template_field_mappings WHERE id = :id")
            ->execute([':id' => $mappingId]);
        
        redirect(base_url('index.php?page=templates&action=view&id=' . $templateId));
    }
    
    return view('templates_delete_mapping', [
        'mapping_id' => $mappingId,
        'template_id' => $templateId
    ]);
}

function generate_document_action(): string {
    $pdo = db();
    $templateId = (int)($_GET['template_id'] ?? 0);
    $candidateId = (int)($_GET['candidate_id'] ?? 0);
    
    if ($templateId <= 0 || $candidateId <= 0) { 
        http_response_code(400); 
        return 'Invalid IDs'; 
    }
    
    try {
        // Get template
        $templateStmt = $pdo->prepare("SELECT * FROM document_templates WHERE id = :id");
        $templateStmt->execute([':id' => $templateId]);
        $template = $templateStmt->fetch();
        
        if (!$template) { http_response_code(404); return 'Template not found'; }
        
        // Get candidate
        $candidateStmt = $pdo->prepare("SELECT candidate_code FROM candidates WHERE id = :id");
        $candidateStmt->execute([':id' => $candidateId]);
        $candidate = $candidateStmt->fetch();
        
        if (!$candidate) { http_response_code(404); return 'Candidate not found'; }
        
        // Find the most recent questionnaire request for this candidate
        $qrStmt = $pdo->prepare("SELECT id FROM questionnaire_requests WHERE candidate_id = :cid ORDER BY created_at DESC LIMIT 1");
        $qrStmt->execute([':cid' => $candidateId]);
        $questionnaireRequest = $qrStmt->fetch();
        $questionnaireRequestId = $questionnaireRequest ? (int)$questionnaireRequest['id'] : null;
        
        // Build document data from candidate + questionnaire + mappings
        require_once __DIR__ . '/../lib/document_generator.php';
        $documentData = DocumentGenerator::buildDataFromMappings($pdo, $templateId, $candidateId, $questionnaireRequestId);
        
        $generator = new DocumentGenerator($template, $documentData);
        $documentContent = $generator->generate();
        
        // Save generated document and version history
        $savedDoc = DocumentGenerator::saveGeneratedDocument(
            $pdo,
            $candidateId,
            $questionnaireRequestId,
            $templateId,
            $template['template_type'] ?? 'generic',
            $documentContent,
            null,
            current_user()['id'] ?? null
        );
        
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
        error_log('Document generation error: ' . $e->getMessage());
        http_response_code(500);
        return 'Error generating document: ' . $e->getMessage();
    }
}

function index_action(): string {
    return list_action();
}