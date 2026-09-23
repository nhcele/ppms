<?php

class DocumentGenerator {
    private $template;
    private $data;
    
    public function __construct($template, $data) {
        $this->template = $template;
        $this->data = $data;
    }

    /**
     * Build document data from candidate record, questionnaire responses, and template mappings.
     */
    public static function buildDataFromMappings($pdo, int $templateId, int $candidateId, ?int $questionnaireRequestId = null): array {
        // Load mappings
        $mappingStmt = $pdo->prepare("SELECT * FROM template_field_mappings WHERE template_id = :id ORDER BY field_order");
        $mappingStmt->execute([':id' => $templateId]);
        $mappings = $mappingStmt->fetchAll();

        // Load candidate data
        $candidateStmt = $pdo->prepare("SELECT * FROM candidates WHERE id = :id");
        $candidateStmt->execute([':id' => $candidateId]);
        $candidate = $candidateStmt->fetch();

        if (!$candidate) {
            throw new Exception('Candidate not found');
        }

        // Load related data
        $workExpStmt = $pdo->prepare('SELECT employer_name as company, position, start_date, end_date, description FROM candidate_work_experience WHERE candidate_id = :id ORDER BY start_date DESC');
        $workExpStmt->execute([':id' => $candidateId]);
        $workExperience = $workExpStmt->fetchAll(PDO::FETCH_ASSOC);

        $eduStmt = $pdo->prepare('SELECT institution_name as institution, degree, field_of_study, YEAR(end_date) as year, description as grade FROM candidate_education WHERE candidate_id = :id ORDER BY end_date DESC');
        $eduStmt->execute([':id' => $candidateId]);
        $education = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

        // Load questionnaire responses
        $questionnaireResponses = [];
        if ($questionnaireRequestId) {
            $respStmt = $pdo->prepare("SELECT section, field_name, field_value, data_type FROM questionnaire_responses WHERE questionnaire_request_id = :id");
            $respStmt->execute([':id' => $questionnaireRequestId]);
            $responses = $respStmt->fetchAll();
            foreach ($responses as $response) {
                $questionnaireResponses[$response['section']][$response['field_name']] = $response['field_value'];
            }
        }

        // Build document data
        $documentData = [];
        foreach ($mappings as $mapping) {
            $value = '';

            switch ($mapping['source_type']) {
                case 'candidate':
                    $fieldName = $mapping['database_source'];
                    if (strpos($fieldName, 'CONCAT') !== false) {
                        // Evaluate simple CONCAT expressions
                        $value = self::evaluateConcat($fieldName, $candidate);
                    } elseif ($fieldName === 'work_experience') {
                        $value = $workExperience;
                    } elseif ($fieldName === 'education') {
                        $value = $education;
                    } elseif (strpos($fieldName, '.') !== false) {
                        $parts = explode('.', $fieldName);
                        $relatedTable = $parts[0];
                        $relatedField = $parts[1];
                        if ($relatedTable === 'work_experience') {
                            $value = array_column($workExperience, $relatedField);
                            $value = implode(', ', $value);
                        } elseif ($relatedTable === 'education') {
                            $value = array_column($education, $relatedField);
                            $value = implode(', ', $value);
                        } else {
                            $value = $candidate[$relatedField] ?? '';
                        }
                    } else {
                        $value = $candidate[$fieldName] ?? '';
                    }
                    break;

                case 'questionnaire':
                    $fieldName = $mapping['database_source'];
                    if (strpos($fieldName, '.') !== false) {
                        $parts = explode('.', $fieldName);
                        $section = $parts[0];
                        $field = $parts[1];
                        $value = $questionnaireResponses[$section][$field] ?? '';
                    } else {
                        // Search across all sections
                        foreach ($questionnaireResponses as $section => $fields) {
                            if (isset($fields[$fieldName])) {
                                $value = $fields[$fieldName];
                                break;
                            }
                        }
                    }
                    break;

                case 'static':
                    $value = $mapping['static_value'];
                    break;
            }

            $documentData[$mapping['template_field_name']] = $value;
        }

        // Add common computed fields
        $documentData['full_name'] = $documentData['full_name'] ?? trim(($candidate['first_name'] ?? '') . ' ' . ($candidate['last_name'] ?? ''));
        $documentData['date_of_birth'] = $documentData['date_of_birth'] ?? ($candidate['dob'] ?? '');
        $documentData['place_of_birth'] = $documentData['place_of_birth'] ?? ($candidate['place_of_birth'] ?? '');
        $documentData['citizenship'] = $documentData['citizenship'] ?? ($candidate['nationality'] ?? '');
        $documentData['phone'] = $documentData['phone'] ?? ($candidate['phone'] ?? '');
        $documentData['email'] = $documentData['email'] ?? ($candidate['email'] ?? '');
        $documentData['marital_status'] = $documentData['marital_status'] ?? ($candidate['marital_status'] ?? '');
        $documentData['height'] = $documentData['height'] ?? ($candidate['height_cm'] ?? '');
        $documentData['weight'] = $documentData['weight'] ?? ($candidate['weight_kg'] ?? '');
        $documentData['religion'] = $documentData['religion'] ?? ($candidate['religion'] ?? '');
        $documentData['work_experience'] = $documentData['work_experience'] ?? $workExperience;
        $documentData['education'] = $documentData['education'] ?? $education;
        $documentData['computer_skills'] = $documentData['computer_skills'] ?? ($candidate['computer_skills_level'] ?? '');
        $documentData['languages'] = $documentData['languages'] ?? ($candidate['languages'] ?? '');
        $documentData['personal_statement'] = $documentData['personal_statement'] ?? ($candidate['personal_statement'] ?? '');

        return $documentData;
    }

    private static function evaluateConcat(string $expression, array $candidate): string {
        // Handle CONCAT(candidates.first_name, " ", candidates.last_name)
        if (preg_match_all('/(?:candidates\.)?([a-z_]+)/', $expression, $matches)) {
            $values = [];
            foreach ($matches[1] as $field) {
                $val = $candidate[$field] ?? '';
                if ($val !== '') {
                    $values[] = $val;
                }
            }
            return implode(' ', $values);
        }
        return '';
    }
    
    public function generate(): string {
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 15,
                'margin_bottom' => 15,
            ]);
            
            $mpdf->SetTitle($this->template['template_name']);
            $mpdf->SetAuthor('Recruitment System');
            
            // Generate content based on template type
            $content = $this->generateContent();
            
            $mpdf->WriteHTML($content);
            
            return $mpdf->Output('', 'S');
            
        } catch (Exception $e) {
            throw new Exception('Document generation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Save a generated document to disk and record its version history.
     */
    public static function saveGeneratedDocument(PDO $pdo, int $candidateId, ?int $questionnaireRequestId, int $templateId, string $destination, string $content, ?string $customFilename = null, ?int $generatedBy = null): array {
        $templateStmt = $pdo->prepare("SELECT template_name FROM document_templates WHERE id = :id");
        $templateStmt->execute([':id' => $templateId]);
        $templateName = $templateStmt->fetchColumn() ?: 'document';
        
        // Determine version number
        $versionStmt = $pdo->prepare("SELECT COUNT(*) + 1 FROM generated_documents WHERE candidate_id = :cid AND template_id = :tid");
        $versionStmt->execute([':cid' => $candidateId, ':tid' => $templateId]);
        $versionNumber = (int)$versionStmt->fetchColumn();
        
        $uploadDir = __DIR__ . '/../../uploads/generated_docs';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $filename = $customFilename ?: ($templateName . '_v' . $versionNumber . '_' . date('Ymd_His') . '.pdf');
        $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
        $filepath = $uploadDir . '/' . $filename;
        file_put_contents($filepath, $content);
        
        $stmt = $pdo->prepare("INSERT INTO generated_documents 
            (candidate_id, questionnaire_request_id, template_id, template_name, destination, file_name, file_path, file_size, version_number, generated_by, generated_at)
            VALUES (:cid, :qrid, :tid, :tname, :dest, :fname, :fpath, :fsize, :ver, :uid, NOW())");
        $stmt->execute([
            ':cid' => $candidateId,
            ':qrid' => $questionnaireRequestId,
            ':tid' => $templateId,
            ':tname' => $templateName,
            ':dest' => $destination,
            ':fname' => $filename,
            ':fpath' => 'uploads/generated_docs/' . $filename,
            ':fsize' => strlen($content),
            ':ver' => $versionNumber,
            ':uid' => $generatedBy
        ]);
        
        return [
            'id' => (int)$pdo->lastInsertId(),
            'version_number' => $versionNumber,
            'file_path' => $filepath,
            'filename' => $filename
        ];
    }
    
    private function generateContent(): string {
        $templateType = $this->template['template_type'] ?? $this->template['template_name'] ?? 'generic';
        switch ($templateType) {
            case 'cv_lithuania':
                return $this->generateLithuaniaCV();
            case 'cv_turkey':
                return $this->generateTurkeyCV();
            case 'cv_generic':
                return $this->generateGenericCV();
            case 'kandidato_anketa':
                return $this->generateKandidatoAnketa();
            default:
                return $this->generateGenericCV();
        }
    }
    
    private function h($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
    
    private function generateLithuaniaCV(): string {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.3; }
            .header { text-align: left; margin-bottom: 15px; }
            .header h2 { font-size: 18px; font-weight: bold; margin-bottom: 15px; border-bottom: 1px solid #000; padding-bottom: 5px; }
            .field-row { margin-bottom: 8px; }
            .field-label { font-weight: bold; display: inline; }
            .field-value { display: inline; }
            .numbered { margin-bottom: 10px; }
            .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 8px; font-size: 12px; }
            .table-section { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
            .table-section th, .table-section td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
            .table-section th { background: #f0f0f0; font-weight: bold; }
        </style>
        
        <div class="header">
            <h2>Candidate Application Form – Employment in Lithuania</h2>
        </div>';
        
        $html .= '
        <div class="field-row"><span class="field-label">NAME</span> <span class="field-value">' . strtoupper($this->h($this->data['full_name'] ?? '')) . '</span></div>';
        $html .= '
        <div class="field-row"><span class="field-label">VIDEO LINK:</span> <span class="field-value">' . $this->h($this->data['video_link'] ?? '') . '</span></div>';
        $html .= '
        <div class="field-row"><span class="field-label">CONTACT NUMBER WHATSAPP:</span> <span class="field-value">' . $this->h($this->data['phone'] ?? '') . '</span></div>';
        
        $html .= '
        <div class="numbered"><span class="field-label">1. Understanding of job conditions and duties:</span> <span class="field-value">' . nl2br($this->h($this->data['personal_statement'] ?? '')) . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">2. Purpose of travel to Lithuania:</span> <span class="field-value">' . $this->h($this->data['purpose_of_travel'] ?? 'work') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">3. Physical health condition:</span> <span class="field-value">' . $this->h($this->data['health_condition'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">4.1. Professional training:</span> <span class="field-value">' . $this->h($this->data['degree_course'] ?? ($this->data['professional_training'] ?? '')) . '</span></div>';
        
        $html .= '
        <div class="numbered"><span class="field-label">4.2. Work experience (company name, position, duration, responsibilities)</span></div>';
        $html .= '
        <table class="table-section">
            <tr>
                <th>COMPANY NAME</th>
                <th>POSITION</th>
                <th>DURATION</th>
                <th>RESPONSIBILITIES</th>
            </tr>';
        
        if (!empty($this->data['work_experience'])) {
            $workExperience = is_array($this->data['work_experience']) ? $this->data['work_experience'] : json_decode($this->data['work_experience'], true);
            if (is_array($workExperience)) {
                foreach ($workExperience as $work) {
                    $html .= '
                    <tr>
                        <td>' . $this->h($work['company'] ?? '') . '</td>
                        <td>' . $this->h($work['position'] ?? '') . '</td>
                        <td>' . $this->h($work['duration'] ?? '') . '</td>
                        <td>' . nl2br($this->h($work['description'] ?? '')) . '</td>
                    </tr>';
                }
            }
        }
        
        $html .= '
        </table>';
        
        $age = $this->calculateAge($this->data['date_of_birth'] ?? '');
        $html .= '
        <div class="numbered"><span class="field-label">5. Height / weight / age / marital status:</span> <span class="field-value">' . 
            $this->h($this->data['height'] ?? '') . ($this->data['height'] ? 'cm' : '') . ', ' . 
            $this->h($this->data['weight'] ?? '') . ($this->data['weight'] ? 'kg' : '') . ', ' . 
            ($age ? $age . 'year' : '') . ', ' . 
            $this->h($this->data['marital_status'] ?? '') . '</span></div>';
        
        $html .= '
        <div class="numbered"><span class="field-label">6. Citizenship and country of residence:</span> <span class="field-value">' . $this->h($this->data['citizenship'] ?? ($this->data['nationality'] ?? '')) . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">7. Relatives or friends in Europe:</span> <span class="field-value">' . $this->h($this->data['relatives_europe'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">8. Previous work experience in Europe:</span> <span class="field-value">' . $this->h($this->data['previous_europe_work'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">9. Smoking / alcohol use:</span> <span class="field-value">' . $this->h($this->data['smoking_alcohol'] ?? ((($this->data['smoking'] ?? '') !== '' ? $this->data['smoking'] : '') . ' / ' . (($this->data['alcohol_use'] ?? '') !== '' ? $this->data['alcohol_use'] : ''))) . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">10. Religion:</span> <span class="field-value">' . $this->h($this->data['religion'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">11. Daily prayers:</span> <span class="field-value">' . $this->h($this->data['daily_prayers'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">12. Ability to work with men and women:</span> <span class="field-value">' . $this->h($this->data['work_men_women'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">13. Ability to work with pork or meat products (if required):</span> <span class="field-value">' . $this->h($this->data['work_pork'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">14. Readiness to wear work uniform without religious elements (for safety reasons):</span> <span class="field-value">' . $this->h($this->data['wear_uniform'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">15. Willingness to work conditions:</span> <span class="field-value">' . $this->h($this->data['willingness_work_conditions'] ?? '') . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">16. Languages known:</span> <span class="field-value">' . strtoupper($this->h($this->data['languages'] ?? '')) . '</span></div>';
        $html .= '
        <div class="numbered"><span class="field-label">17. Contact of close relative (WhatsApp):</span> <span class="field-value">' . $this->h($this->data['emergency_contact_phone'] ?? ($this->data['relative_contact'] ?? '')) . '</span></div>';
        
        return $html;
    }

    private function calculateAge(string $dob): int {
        if (empty($dob)) return 0;
        try {
            $birthDate = new DateTime($dob);
            $today = new DateTime();
            return $today->diff($birthDate)->y;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    private function generateKandidatoAnketa(): string {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; line-height: 1.3; }
            .header { background: #a8c4e8; border: 1px solid #000; padding: 5px; margin-bottom: 0; }
            .header h2 { font-size: 12px; font-weight: bold; margin: 0; }
            .data-table { width: 100%; border-collapse: collapse; }
            .data-table td { border: 1px solid #000; padding: 6px; vertical-align: top; }
            .data-table .label { width: 60%; font-weight: normal; font-style: italic; }
            .data-table .value { width: 40%; }
            .section-title { font-weight: bold; background: #a8c4e8; border: 1px solid #000; padding: 5px; margin-top: 15px; margin-bottom: 0; font-size: 12px; }
        </style>
        
        <div class="header">
            <h2>Personal data:</h2>
        </div>
        
        <table class="data-table">
            <tr>
                <td class="label">Name</td>
                <td class="value">' . $this->h($this->data['full_name'] ?? '') . '</td>
            </tr>
            <tr>
                <td class="label">Surname</td>
                <td class="value">' . $this->h($this->data['surname'] ?? ($this->data['last_name'] ?? '')) . '</td>
            </tr>
            <tr>
                <td class="label">Birthday date</td>
                <td class="value">' . $this->h($this->data['date_of_birth'] ?? '') . '</td>
            </tr>
            <tr>
                <td class="label">Place of birth (country, city)</td>
                <td class="value">' . $this->h($this->data['place_of_birth'] ?? '') . '</td>
            </tr>
            <tr>
                <td class="label">Citizenship</td>
                <td class="value">' . $this->h($this->data['citizenship'] ?? ($this->data['nationality'] ?? '')) . '</td>
            </tr>
            <tr>
                <td class="label">Former citizenship (if had such)</td>
                <td class="value">' . $this->h($this->data['former_citizenship'] ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td class="label">Residence address in the country You are coming from</td>
                <td class="value">' . nl2br($this->h($this->data['home_address'] ?? ($this->data['address'] ?? ''))) . '</td>
            </tr>
            <tr>
                <td class="label">Residence address in Lithuania (if applicable)</td>
                <td class="value">' . $this->h($this->data['lithuania_address'] ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td class="label">E-mail</td>
                <td class="value">' . $this->h($this->data['email'] ?? '') . '</td>
            </tr>
            <tr>
                <td class="label">Phone number</td>
                <td class="value">' . $this->h($this->data['phone'] ?? '') . '</td>
            </tr>
            <tr>
                <td class="label">Marital status <i>(single / married / divorced / concluded partnership agreement / terminated partnership agreement / widow-widower)</i></td>
                <td class="value">' . ucfirst($this->h($this->data['marital_status'] ?? '')) . '</td>
            </tr>
            <tr>
                <td class="label">Date of marriage/divorce</td>
                <td class="value">' . $this->h($this->data['marriage_date'] ?? '') . '</td>
            </tr>
            <tr>
                <td class="label">Have You been convicted of a crime?</td>
                <td class="value">' . ucfirst($this->h($this->data['convicted_crime'] ?? '')) . '</td>
            </tr>
            <tr>
                <td class="label">If YES - when and for what crime(s) have You been convicted? What was the penalty(s) imposed and was it executed?</td>
                <td class="value">' . $this->h($this->data['crime_details'] ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td class="label">Have you been denied a visa or residence permit or banned from entering a foreign country?</td>
                <td class="value">' . ucfirst($this->h($this->data['visa_denied'] ?? '')) . '</td>
            </tr>
            <tr>
                <td class="label">If YES - indicate the reason and the country that has responded to you by denying a visa or residence permit or prohibiting you from entering this country.</td>
                <td class="value">' . $this->h($this->data['denial_details'] ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td class="label">Please specify the countries that have issued you a visa or residence permit for the last 5 years <i>(document type, country and years of validity)</i></td>
                <td class="value">' . nl2br($this->h($this->data['visas_last_5_years'] ?? '')) . '</td>
            </tr>
            <tr>
                <td class="label">Have you visited and / or resided in foreign states before? <i>Please check your passport carefully and write down all the trips abroad with dates.</i></td>
                <td class="value">' . nl2br($this->h($this->data['travel_history'] ?? '')) . '</td>
            </tr>
        </table>';
        
        return $html;
    }
    
    private function generateTurkeyCV(): string {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .section { margin-bottom: 15px; }
            .section-title { background: #f0f0f0; font-weight: bold; padding: 8px; border: 1px solid #000; margin-bottom: 10px; }
            .field-row { margin-bottom: 5px; }
            .field-label { font-weight: bold; display: inline-block; width: 180px; }
            .table-section { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            .table-section th, .table-section td { border: 1px solid #000; padding: 6px; text-align: left; }
            .table-section th { background: #f0f0f0; font-weight: bold; }
        </style>
        
        <div class="header">
            <h2>Candidate Application Form - Employment in Turkey</h2>
        </div>';
        
        // Personal Information
        $html .= '
        <div class="section">
            <div class="section-title">Personal Information</div>
            <div class="field-row"><span class="field-label">Name:</span> ' . $this->h($this->data['full_name'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Date of Birth:</span> ' . $this->h($this->data['date_of_birth'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Place of Birth:</span> ' . $this->h($this->data['place_of_birth'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Citizenship:</span> ' . $this->h($this->data['citizenship'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Contact Number:</span> ' . $this->h($this->data['phone'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Email:</span> ' . $this->h($this->data['email'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Marital Status:</span> ' . $this->h($this->data['marital_status'] ?? '') . '</div>
        </div>';
        
        // Physical Attributes
        $html .= '
        <div class="section">
            <div class="section-title">Physical Information</div>
            <div class="field-row"><span class="field-label">Height:</span> ' . $this->h($this->data['height'] ?? '') . ' cm</div>
            <div class="field-row"><span class="field-label">Weight:</span> ' . $this->h($this->data['weight'] ?? '') . ' kg</div>
            <div class="field-row"><span class="field-label">Age:</span> ' . $this->h($this->data['age'] ?? '') . '</div>
        </div>';
        
        // Work Experience
        $html .= '
        <div class="section">
            <div class="section-title">Work Experience</div>
            <table class="table-section">
                <tr>
                    <th>Company Name</th>
                    <th>Position</th>
                    <th>Duration</th>
                    <th>Responsibilities</th>
                </tr>';
        
        if (!empty($this->data['work_experience'])) {
            $workExperience = is_array($this->data['work_experience']) ? $this->data['work_experience'] : json_decode($this->data['work_experience'], true);
            if (is_array($workExperience)) {
                foreach ($workExperience as $work) {
                    $html .= '
                    <tr>
                        <td>' . $this->h($work['company'] ?? '') . '</td>
                        <td>' . $this->h($work['position'] ?? '') . '</td>
                        <td>' . $this->h($work['duration'] ?? '') . '</td>
                        <td>' . $this->h($work['description'] ?? '') . '</td>
                    </tr>';
                }
            }
        }
        
        $html .= '
            </table>
        </div>';
        
        // Education
        $html .= '
        <div class="section">
            <div class="section-title">Education</div>
            <table class="table-section">
                <tr>
                    <th>Institution</th>
                    <th>Degree</th>
                    <th>Year</th>
                </tr>';
        
        if (!empty($this->data['education'])) {
            $education = is_array($this->data['education']) ? $this->data['education'] : json_decode($this->data['education'], true);
            if (is_array($education)) {
                foreach ($education as $edu) {
                    $html .= '
                    <tr>
                        <td>' . $this->h($edu['institution'] ?? '') . '</td>
                        <td>' . $this->h($edu['degree'] ?? '') . '</td>
                        <td>' . $this->h($edu['year'] ?? '') . '</td>
                    </tr>';
                }
            }
        }
        
        $html .= '
            </table>
        </div>';
        
        // Skills and Languages
        $html .= '
        <div class="section">
            <div class="section-title">Skills & Languages</div>
            <div class="field-row"><span class="field-label">Languages Known:</span> ' . $this->h($this->data['languages'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Computer Skills:</span> ' . $this->h($this->data['computer_skills'] ?? '') . '</div>
        </div>';
        
        // Additional Information
        $html .= '
        <div class="section">
            <div class="section-title">Additional Information</div>
            <div class="field-row"><span class="field-label">Purpose of Travel:</span> ' . $this->h($this->data['purpose_of_travel'] ?? 'work') . '</div>
            <div class="field-row"><span class="field-label">Health Condition:</span> ' . $this->h($this->data['health_condition'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Relatives in Europe:</span> ' . $this->h($this->data['relatives_europe'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Previous Work in Europe:</span> ' . $this->h($this->data['previous_europe_work'] ?? '') . '</div>
        </div>';
        
        return $html;
    }
    
    private function generateGenericCV(): string {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .section { margin-bottom: 15px; }
            .section-title { background: #f0f0f0; font-weight: bold; padding: 8px; border: 1px solid #000; margin-bottom: 10px; }
            .field-row { margin-bottom: 5px; }
            .field-label { font-weight: bold; display: inline-block; width: 150px; }
            .table-section { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            .table-section th, .table-section td { border: 1px solid #000; padding: 6px; text-align: left; }
            .table-section th { background: #f0f0f0; font-weight: bold; }
        </style>
        
        <div class="header">
            <h2>Curriculum Vitae</h2>
        </div>';
        
        // Personal Information
        $html .= '
        <div class="section">
            <div class="section-title">Personal Information</div>
            <div class="field-row"><span class="field-label">Full Name:</span> ' . $this->h($this->data['full_name'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Date of Birth:</span> ' . $this->h($this->data['date_of_birth'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Nationality:</span> ' . $this->h($this->data['nationality'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Email:</span> ' . $this->h($this->data['email'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Phone:</span> ' . $this->h($this->data['phone'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Address:</span> ' . $this->h($this->data['address'] ?? '') . '</div>
        </div>';
        
        // Professional Summary
        $html .= '
        <div class="section">
            <div class="section-title">Professional Summary</div>
            <p>' . $this->h($this->data['personal_statement'] ?? '') . '</p>
        </div>';
        
        // Work Experience
        $html .= '
        <div class="section">
            <div class="section-title">Work Experience</div>
            <table class="table-section">
                <tr>
                    <th>Company</th>
                    <th>Position</th>
                    <th>Period</th>
                    <th>Description</th>
                </tr>';
        
        if (!empty($this->data['work_experience'])) {
            $workExperience = is_array($this->data['work_experience']) ? $this->data['work_experience'] : json_decode($this->data['work_experience'], true);
            if (is_array($workExperience)) {
                foreach ($workExperience as $work) {
                    $html .= '
                    <tr>
                        <td>' . $this->h($work['company'] ?? '') . '</td>
                        <td>' . $this->h($work['position'] ?? '') . '</td>
                        <td>' . $this->h($work['duration'] ?? '') . '</td>
                        <td>' . $this->h($work['description'] ?? '') . '</td>
                    </tr>';
                }
            }
        }
        
        $html .= '
            </table>
        </div>';
        
        // Education
        $html .= '
        <div class="section">
            <div class="section-title">Education</div>
            <table class="table-section">
                <tr>
                    <th>Institution</th>
                    <th>Degree</th>
                    <th>Year</th>
                </tr>';
        
        if (!empty($this->data['education'])) {
            $education = is_array($this->data['education']) ? $this->data['education'] : json_decode($this->data['education'], true);
            if (is_array($education)) {
                foreach ($education as $edu) {
                    $html .= '
                    <tr>
                        <td>' . $this->h($edu['institution'] ?? '') . '</td>
                        <td>' . $this->h($edu['degree'] ?? '') . '</td>
                        <td>' . $this->h($edu['year'] ?? '') . '</td>
                    </tr>';
                }
            }
        }
        
        $html .= '
            </table>
        </div>';
        
        // Skills
        $html .= '
        <div class="section">
            <div class="section-title">Skills</div>
            <div class="field-row"><span class="field-label">Computer Skills:</span> ' . $this->h($this->data['computer_skills'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Languages:</span> ' . $this->h($this->data['languages'] ?? '') . '</div>
            <div class="field-row"><span class="field-label">Other Skills:</span> ' . $this->h($this->data['other_skills'] ?? '') . '</div>
        </div>';
        
        return $html;
    }
}