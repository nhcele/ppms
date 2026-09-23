<?php

class CandidatePDFGenerator {
    private $candidate;
    private $workExperience;
    private $education;
    private $documents;
    private $roles;
    private const SUPPORTED_IMAGE_EXTS = ['jpg','jpeg','png','gif','webp','bmp'];

    public function __construct($candidate, $workExperience, $education, $documents, $roles) {
        $this->candidate = $candidate;
        $this->workExperience = $workExperience;
        $this->education = $education;
        $this->documents = $documents;
        $this->roles = $roles;
    }

    // Safe HTML escape helper
    private function h($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    // Resolve image src: URLs as-is; local files returned as file paths or public URLs (no base64 embedding).
    private function resolveImageSrc(?string $path): string {
        if (!$path) return '';
        if (preg_match('#^https?://#i', $path)) return $path;
        $real = file_exists($path) ? (realpath($path) ?: $path) : $path;
        $realNorm = str_replace('\\', '/', (string)$real);
        $ext = strtolower(pathinfo((string)$realNorm, PATHINFO_EXTENSION));

        // Prefer mapping upload paths to public URLs to avoid inlining
        if (defined('UPLOAD_DIR') && defined('BASE_URL')) {
            $uploadRoot = realpath(UPLOAD_DIR);
            if ($uploadRoot) {
                $uploadRoot = str_replace('\\', '/', $uploadRoot);
                if (str_starts_with($realNorm, $uploadRoot)) {
                    $rel = ltrim(substr($realNorm, strlen($uploadRoot)), '/');
                    return rtrim(BASE_URL, '/') . '/uploads/' . $rel;
                }
            }
        }

        // If file exists, return absolute path (mPDF supports local files)
        if (file_exists($real) && in_array($ext, self::SUPPORTED_IMAGE_EXTS, true)) {
            return $real;
        }

        return '';
    }

    public function generate(): string {
        require_once __DIR__ . '/../../vendor/autoload.php';

        try {
            // Initialize mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_left'   => 10,
                'margin_right'  => 10,
                'margin_top'    => 10,
                'margin_bottom' => 10,
            ]);

            $first = $this->candidate['first_name'] ?? '';
            $last = $this->candidate['last_name'] ?? '';
            $mpdf->SetTitle('CV - ' . trim($first . ' ' . $last));
            $mpdf->SetAuthor('Recruitment System');

            // Slightly increase PCRE backtrack limit for complex HTML while still chunking content
            @ini_set('pcre.backtrack_limit', '5000000');

            // Load CSS separately
            $mpdf->WriteHTML($this->getCSS(), \Mpdf\HTMLParserMode::HEADER_CSS);

            // Stream HTML in smaller chunks
            $mpdf->WriteHTML('<div class="main-container">');

            // Header with photo
            $mpdf->WriteHTML('<div class="header-section"><div class="photo-container">' . $this->getPhotoSection() . '</div></div>');

            // Personal details
            $mpdf->WriteHTML($this->getPersonalDetailsSection());

            // Education
            $mpdf->WriteHTML($this->getEducationSection());

            // Work experience
            $mpdf->WriteHTML($this->getWorkExperienceSection());

            // Languages
            $mpdf->WriteHTML($this->getLanguagesSection());

            // Activities
            $mpdf->WriteHTML($this->getActivitiesSection());

            // Documents (write in small pieces per image)
            $this->writeDocumentImages($mpdf);

            $mpdf->WriteHTML('</div>'); // end main-container
            
            return $mpdf->Output('', 'S'); // Return as string
            
        } catch (Exception $e) {
            throw new Exception('PDF generation failed: ' . $e->getMessage());
        }
    }

    private function buildHTML(): string {
        $html = $this->getCSS();
        $html .= '<div class="main-container">';
        $html .= $this->getMainContent();
        $html .= '</div>';
        
        return $html;
    }

    private function getCSS(): string {
        return '
        <style>
            body { font-family: DejaVuSansCondensed; font-size: 11px; line-height: 1.3; margin: 0; padding: 0; }
            .main-container { width: 100%; }
            .header-section { width: 100%; margin-bottom: 15px; position: relative; }
            .photo-container { position: absolute; top: 0; right: 0; }
            .photo { width: 35mm; height: 45mm; object-fit: cover; border: 2px solid #000; }
            .no-photo { width: 35mm; height: 45mm; border: 2px solid #000; background: #f5f5f5; display: flex; align-items: center; justify-content: center; font-size: 10px; }
            .content-area { width: 100%; }
            .section-title { background: #f0f0f0; font-weight: bold; padding: 6px; border: 1px solid #000; margin-bottom: 5px; font-size: 12px; width: 100%; }
            .personal-details { width: 100%; margin-bottom: 15px; }
            .field-row { margin-bottom: 3px; width: 100%; }
            .field-label { font-weight: bold; display: inline-block; width: 150px; vertical-align: top; }
            .field-value { display: inline-block; width: calc(100% - 160px); }
            .table-section { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
            .table-section th, .table-section td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 10px; }
            .table-section th { background: #f0f0f0; font-weight: bold; }
            .languages-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
            .languages-table td { border: 1px solid #000; padding: 6px; text-align: center; font-size: 10px; }
            .languages-table .lang-header { background: #f0f0f0; font-weight: bold; }
            .activities-section { width: 100%; }
            .documents-section { width: 100%; margin-top: 20px; }
            .document-images { width: 100%; text-align: center; margin-top: 10px; }
            .document-image { max-width: 45%; height: auto; margin: 5px; border: 1px solid #000; display: inline-block; vertical-align: top; }
            .document-label { font-size: 10px; font-weight: bold; text-align: center; margin-bottom: 5px; }
            .no-documents { text-align: center; font-style: italic; color: #666; padding: 20px; }
        </style>';
    }

    private function getMainContent(): string {
        return '
        <div class="header-section">
            <div class="photo-container">
                ' . $this->getPhotoSection() . '
            </div>
        </div>
        <div class="content-area">
            ' . $this->getPersonalDetailsSection() . '
            ' . $this->getEducationSection() . '
            ' . $this->getWorkExperienceSection() . '
            ' . $this->getLanguagesSection() . '
            ' . $this->getActivitiesSection() . '
            ' . $this->getDocumentImagesSection() . '
        </div>';
    }

    // Stream the documents/images in chunks to avoid huge HTML strings
    private function writeDocumentImages(\Mpdf\Mpdf $mpdf): void {
        // Header for documents section
        $mpdf->WriteHTML('<div class="documents-section"><div class="section-title">Documents / Документы</div><div class="document-images">');

        // Reuse the selection logic from getDocumentImagesSection(), but stream per image
        $imageDocuments = [];
        foreach ($this->documents as $doc) {
            if (!($doc['is_active'] ?? false)) continue;
            $fp = $doc['file_path'] ?? '';
            if (!$fp) continue;
            $ext = strtolower(pathinfo((string)$fp, PATHINFO_EXTENSION));
            if (in_array($ext, self::SUPPORTED_IMAGE_EXTS, true)) {
                $imageDocuments[] = $doc;
            }
        }

        usort($imageDocuments, function($a, $b) {
            $order = ['passport' => 1, 'certificate' => 2, 'education' => 3, 'photo' => 4];
            $aOrder = $order[$a['doc_type']] ?? 5;
            $bOrder = $order[$b['doc_type']] ?? 5;
            if ($aOrder === $bOrder) { return strtotime($b['uploaded_at']) - strtotime($a['uploaded_at']); }
            return $aOrder - $bOrder;
        });

        if (empty($imageDocuments)) {
            $mpdf->WriteHTML('<div class="no-documents">No image documents available</div>');
        } else {
            foreach ($imageDocuments as $index => $doc) {
                $docType = ucwords(str_replace('_', ' ', $doc['doc_type']));
                $filePath = $doc['file_path'];
                $imgSrc = $this->resolveImageSrc($filePath);
                if ($imgSrc === '' && !empty($doc['id']) && defined('BASE_URL')) {
                    $imgSrc = rtrim(BASE_URL, '/') . '/index.php?page=documents&action=serve&id=' . (int)$doc['id'];
                }
                if ($index > 0) { $mpdf->WriteHTML('<pagebreak />'); }
                $chunk = '<div style="page-break-inside: avoid; text-align: center; width: 100%; height: 100%;">'
                       . '  <div style="font-size: 16px; font-weight: bold; margin-bottom: 20px; text-align: center;">' . htmlspecialchars($docType) . '</div>'
                       . '  <div style="text-align: center; width: 100%;">'
                       . ( $imgSrc !== '' ? '<img src="' . $imgSrc . '" style="max-width: 100%; max-height: 90%; object-fit: contain;" />'
                                          : '<div class="no-documents">Image not found</div>' )
                       . '  </div>'
                       . '</div>';
                $mpdf->WriteHTML($chunk);
            }
        }

        // Close containers
        $mpdf->WriteHTML('</div></div>');
    }

    private function getPhotoSection(): string {
        $html = '';
        
        // Look for a dedicated 'photo' document first (most recent), then fallback to any image, then profile_photo_path
        $profilePhoto = null;
        $profileDocId = 0;
        
        // 1) Prefer explicit 'photo' doc_type
        $photoDocs = array_filter($this->documents, function($d){
            if (!($d['is_active'] ?? false)) return false;
            if (($d['doc_type'] ?? '') !== 'photo') return false;
            $fp = $d['file_path'] ?? '';
            $ext = strtolower(pathinfo((string)$fp, PATHINFO_EXTENSION));
            return $fp && in_array($ext, self::SUPPORTED_IMAGE_EXTS, true);
        });
        if (!empty($photoDocs)) {
            usort($photoDocs, function($a, $b){
                return strtotime($b['uploaded_at'] ?? '1970-01-01') - strtotime($a['uploaded_at'] ?? '1970-01-01');
            });
            $best = $photoDocs[0];
            $profilePhoto = $best['file_path'] ?? null;
            $profileDocId = (int)($best['id'] ?? 0);
        }
        
        // 2) If no 'photo' doc, pick any other active image (newest first)
        if (!$profilePhoto) {
            $imageDocs = array_filter($this->documents, function($d){
                if (!($d['is_active'] ?? false)) return false;
                $fp = $d['file_path'] ?? '';
                $ext = strtolower(pathinfo((string)$fp, PATHINFO_EXTENSION));
                return $fp && in_array($ext, self::SUPPORTED_IMAGE_EXTS, true);
            });
            if (!empty($imageDocs)) {
                usort($imageDocs, function($a, $b){
                    return strtotime($b['uploaded_at'] ?? '1970-01-01') - strtotime($a['uploaded_at'] ?? '1970-01-01');
                });
                $best = $imageDocs[0];
                $profilePhoto = $best['file_path'] ?? null;
                $profileDocId = (int)($best['id'] ?? 0);
            }
        }
        
        // Fallback to profile_photo_path if no document photo found
        if (!$profilePhoto && !empty($this->candidate['profile_photo_path'])) {
            $tryPath = __DIR__ . '/../../' . ltrim($this->candidate['profile_photo_path'], '\/');
            if (file_exists($tryPath)) $profilePhoto = $tryPath;
        }
        
        if ($profilePhoto) {
            $src = $this->resolveImageSrc($profilePhoto);
            if ($src === '' && !empty($profileDocId) && defined('BASE_URL')) {
                $src = rtrim(BASE_URL, '/') . '/index.php?page=documents&action=serve&id=' . (int)$profileDocId;
            }
            if ($src !== '') {
                $html .= '<img src="' . $src . '" class="photo" />';
            } else {
                $html .= '<div class="no-photo">No Photo</div>';
            }
        } else {
            $html .= '<div class="no-photo">No Photo</div>';
        }
        
        return $html;
    }

    private function getPersonalDetailsSection(): string {
        $fullName = $this->h(trim(($this->candidate['first_name'] ?? '') . ' ' . ($this->candidate['last_name'] ?? '')));
        $position = !empty($this->roles) ? $this->h($this->roles[0]) : 'N/A';
        $height = isset($this->candidate['height_cm']) && $this->candidate['height_cm'] !== '' ? $this->h($this->candidate['height_cm']) : 'N/A';
        $weight = isset($this->candidate['weight_kg']) && $this->candidate['weight_kg'] !== '' ? $this->h($this->candidate['weight_kg']) : 'N/A';
        $dob = !empty($this->candidate['dob']) ? $this->h(date('d-m-Y', strtotime($this->candidate['dob']))) : 'N/A';
        $marital = $this->h(ucfirst($this->candidate['marital_status'] ?? 'N/A'));
        $passport = $this->h($this->candidate['passport_number'] ?? 'N/A');
        $phone = $this->h($this->candidate['phone'] ?? 'N/A');
        $father = $this->h($this->candidate['fathers_name'] ?? 'N/A');
        $mother = $this->h($this->candidate['mothers_name'] ?? 'N/A');
        
        // Build address
        $address = [];
        if (!empty($this->candidate['address_line1'])) $address[] = $this->candidate['address_line1'];
        if (!empty($this->candidate['address_line2'])) $address[] = $this->candidate['address_line2'];
        if (!empty($this->candidate['address_city'])) $address[] = $this->candidate['address_city'];
        $addressStr = $this->h(implode(', ', $address) ?: 'N/A');

        return '
        <div class="section-title">Personal Details / Личная информация</div>
        <div class="field-row"><span class="field-label">Full name / ФИО:</span> <span class="field-value">' . $fullName . '</span></div>
        <div class="field-row"><span class="field-label">Position:</span> <span class="field-value">' . $position . '</span></div>
        <div class="field-row"><span class="field-label">Height / Рост:</span> <span class="field-value">Height: ' . $height . '</span></div>
        <div class="field-row"><span class="field-label">Weight / Вес:</span> <span class="field-value">' . $weight . '</span></div>
        <div class="field-row"><span class="field-label">Date of birth:</span> <span class="field-value">' . $dob . '</span></div>
        <div class="field-row"><span class="field-label">Marital status:</span> <span class="field-value">' . $marital . '</span></div>
        <div class="field-row"><span class="field-label">Passport Number:</span> <span class="field-value">' . $passport . '</span></div>
        <div class="field-row"><span class="field-label">Phone Number:</span> <span class="field-value">' . $phone . '</span></div>
        <div class="field-row"><span class="field-label">Parents Names:</span></div>
        <div class="field-row" style="margin-left: 10px;">Father\'s Name: ' . $father . '</div>
        <div class="field-row" style="margin-left: 10px;">Mother\'s Name: ' . $mother . '</div>
        <div class="field-row"><span class="field-label">Home Address:</span> <span class="field-value">' . $addressStr . '</span></div>
        <br/>';
    }

    private function getEducationSection(): string {
        $html = '
        <div class="section-title">Academic qualifications / Образование</div>
        <table class="table-section">
        <tr><th>Date / Дата</th><th>Name and address of School</th><th>Majority (Program) / Специальность</th></tr>';
        
        if (empty($this->education)) {
            $html .= '<tr><td colspan="3">No education records found</td></tr>';
        } else {
            foreach ($this->education as $edu) {
                $year = 'N/A';
                if (!empty($edu['start_date'])) {
                    $year = date('Y', strtotime($edu['start_date']));
                } elseif (!empty($edu['graduation_year'])) {
                    $year = (string)$edu['graduation_year'];
                }
                $institution = $this->h($edu['institution_name'] ?? $edu['institution'] ?? '');
                $degree = $this->h($edu['degree'] ?? '');
                $html .= "<tr><td>$year</td><td>$institution</td><td>$degree</td></tr>";
            }
        }
        
        $html .= '</table><br/>';
        return $html;
    }

    private function getWorkExperienceSection(): string {
        $html = '
        <div class="section-title">Work experience / Опыт работы</div>
        <table class="table-section">
        <tr><th>Date / Дата</th><th>Name and address of employer</th><th>Position / Должность</th><th>Working Period / Срок работы</th></tr>';
        
        if (empty($this->workExperience)) {
            $html .= '<tr><td colspan="4">No work experience records found</td></tr>';
        } else {
            foreach ($this->workExperience as $work) {
                $startYear = !empty($work['start_date']) ? date('Y', strtotime($work['start_date'])) : '';
                $employer = $this->h($work['employer_name'] ?? $work['company'] ?? '');
                $position = $this->h($work['position'] ?? '');
                
                // Calculate working period in years
                $workingPeriod = 'N/A';
                if (!empty($work['start_date'])) {
                    $startDate = new DateTime($work['start_date']);
                    $endDate = !empty($work['end_date']) ? new DateTime($work['end_date']) : new DateTime();
                    $interval = $startDate->diff($endDate);
                    $years = $interval->y;
                    $months = $interval->m;
                    
                    if ($years > 0) {
                        $workingPeriod = $years . ' year' . ($years > 1 ? 's' : '');
                        if ($months > 0) {
                            $workingPeriod .= ', ' . $months . ' month' . ($months > 1 ? 's' : '');
                        }
                    } else {
                        $workingPeriod = $months . ' month' . ($months > 1 ? 's' : '');
                    }
                }
                
                $html .= "<tr><td>$startYear</td><td>$employer</td><td>$position</td><td>$workingPeriod</td></tr>";
            }
        }
        
        $html .= '</table><br/>';
        return $html;
    }

    private function getLanguagesSection(): string {
        // For now, we'll create a basic languages section
        // You can extend this based on your database structure
        $html = '
        <div class="section-title">Languages / Языки</div>
        <table class="languages-table">
        <tr>
            <td class="lang-header">Language</td>
            <td class="lang-header">Russian</td>
            <td class="lang-header">English</td>
            <td class="lang-header">Turkish</td>
            <td class="lang-header">Other</td>
        </tr>
        <tr>
            <td>Level</td>
            <td>Basic</td>
            <td>Yes</td>
            <td>No</td>
            <td>Shona</td>
        </tr>
        </table><br/>';
        
        return $html;
    }

    private function getActivitiesSection(): string {
        $computerSkills = $this->candidate['computer_skills_level'] ?? 'Basic';
        $computerNotes = $this->candidate['computer_skills_notes'] ?? 'Microsoft Word';
        
        $html = '
        <div class="section-title">Activities / interests / certificates / Увлечения, дополнительные навыки</div>
        <div class="field-row"><span class="field-label">PC skills:</span> <span class="field-value">' . htmlspecialchars(ucfirst($computerSkills)) . '</span></div>
        <div class="field-row"><span class="field-label">Software:</span> <span class="field-value">' . htmlspecialchars($computerNotes) . '</span></div>';
        
        if ($this->candidate['personal_statement']) {
            $html .= '<div class="field-row"><span class="field-label">Interests:</span> <span class="field-value">' . htmlspecialchars($this->candidate['personal_statement']) . '</span></div>';
        }
        
        return $html;
    }

    private function getDocumentImagesSection(): string {
        if (empty($this->documents)) {
            return '
            <div class="documents-section">
                <div class="section-title">Documents / Документы</div>
                <div class="no-documents">No documents uploaded</div>
            </div>';
        }

        $html = '
        <div class="documents-section">
            <div class="section-title">Documents / Документы</div>
            <div class="document-images">';

        // Filter for image documents by extension (any doc_type)
        $imageDocuments = [];
        $otherDocuments = [];
        foreach ($this->documents as $doc) {
            if (!($doc['is_active'] ?? false)) continue;
            $fp = $doc['file_path'] ?? '';
            if (!$fp) continue;
            $ext = strtolower(pathinfo((string)$fp, PATHINFO_EXTENSION));
            if (in_array($ext, self::SUPPORTED_IMAGE_EXTS, true)) {
                $imageDocuments[] = $doc;
            } else {
                $otherDocuments[] = $doc;
            }
        }

        // Sort documents: passport first, then certificates, then others
        usort($imageDocuments, function($a, $b) {
            $order = ['passport' => 1, 'certificate' => 2, 'education' => 3, 'photo' => 4];
            $aOrder = $order[$a['doc_type']] ?? 5;
            $bOrder = $order[$b['doc_type']] ?? 5;
            
            if ($aOrder === $bOrder) {
                // If same type, sort by upload date (newest first)
                return strtotime($b['uploaded_at']) - strtotime($a['uploaded_at']);
            }
            
            return $aOrder - $bOrder;
        });

        if (empty($imageDocuments)) {
            $html .= '<div class="no-documents">No image documents available</div>';
        } else {
            foreach ($imageDocuments as $index => $doc) {
                $docType = ucwords(str_replace('_', ' ', $doc['doc_type']));
                $filePath = $doc['file_path'];
                $imgSrc = $this->resolveImageSrc($filePath);
                if ($imgSrc === '' && !empty($doc['id']) && defined('BASE_URL')) {
                    $imgSrc = rtrim(BASE_URL, '/') . '/index.php?page=documents&action=serve&id=' . (int)$doc['id'];
                }
                
                // Add page break before each image (except the first one)
                if ($index > 0) {
                    $html .= '<pagebreak />';
                }
                
                $html .= '
                <div style="page-break-inside: avoid; text-align: center; width: 100%; height: 100%;">
                    <div style="font-size: 16px; font-weight: bold; margin-bottom: 20px; text-align: center;">
                        ' . htmlspecialchars($docType) . '
                    </div>
                    <div style="text-align: center; width: 100%;">
                        ' . ($imgSrc !== '' ? '<img src="' . $imgSrc . '" style="max-width: 100%; max-height: 90%; object-fit: contain;" />' : '<div class="no-documents">Image not found</div>') . '
                </div>
                </div>';
            }
        }

        // Add table of other (non-image) documents with links
        if (!empty($otherDocuments)) {
            $html .= '<pagebreak />';
            $html .= '<div class="section-title">Other Documents</div>';
            $html .= '<table class="table-section">';
            $html .= '<tr><th>Type</th><th>Name</th><th>Link</th></tr>';
            foreach ($otherDocuments as $doc) {
                $type = ucwords(str_replace('_',' ', (string)($doc['doc_type'] ?? 'Document')));
                $name = $this->h($doc['original_name'] ?? basename((string)($doc['file_path'] ?? '')));
                $id = (int)($doc['id'] ?? 0);
                $url = ($id > 0 && defined('BASE_URL')) ? (rtrim(BASE_URL, '/') . '/index.php?page=documents&action=serve&id=' . $id) : '#';
                $html .= '<tr>';
                $html .= '<td>' . $this->h($type) . '</td>';
                $html .= '<td>' . $name . '</td>';
                $html .= '<td><a href="' . $this->h($url) . '">' . $this->h($url) . '</a></td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
        }

        $html .= '
            </div>
        </div>';

        return $html;
    }
}