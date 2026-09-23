<?php
// app/controllers/documents.php - Candidate documents upload/delete

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);
require_once __DIR__ . '/../lib/file_upload.php';
require_once __DIR__ . '/../lib/video_helper.php';

// Document types from our migration
const DOCUMENT_TYPES = [
    'passport', 'cv', 'certificate', 'contract', 'medical', 'education',
    'work_reference', 'police_clearance', 'medical_certificate', 'photo',
    'video', 'other'
];

function upload_action(): string {
    $candidate_id = (int)($_GET['candidate_id'] ?? 0);
    if ($candidate_id <= 0) { http_response_code(400); return 'Invalid candidate'; }

    // Ensure candidate exists
    $stmt = db()->prepare('SELECT id, first_name, last_name FROM candidates WHERE id=:id');
    $stmt->execute([':id' => $candidate_id]);
    $cand = $stmt->fetch();
    if (!$cand) { http_response_code(404); return 'Candidate not found'; }

    $errors = [];
    $success = null;

    if (is_post()) {
        csrf_verify();
        $doc_type = $_POST['doc_type'] ?? '';
        
        // Validate document type
        if (!in_array($doc_type, DOCUMENT_TYPES, true)) {
            $errors[] = 'Invalid document type';
        }
        
        // Handle multiple files for certificates or single file for others
        $files_to_process = [];
        
        if ($doc_type === 'certificate' && isset($_FILES['files'])) {
            // Multiple certificate files
            $file_count = count($_FILES['files']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK && !empty($_FILES['files']['name'][$i])) {
                    if ($_FILES['files']['size'][$i] > MAX_UPLOAD_BYTES) {
                        $maxMb = (int)floor(MAX_UPLOAD_BYTES / (1024*1024));
                        $errors[] = 'File "' . $_FILES['files']['name'][$i] . '" too large (max ' . $maxMb . 'MB)';
                    } else {
                        $files_to_process[] = [
                            'name' => $_FILES['files']['name'][$i],
                            'type' => $_FILES['files']['type'][$i],
                            'tmp_name' => $_FILES['files']['tmp_name'][$i],
                            'error' => $_FILES['files']['error'][$i],
                            'size' => $_FILES['files']['size'][$i]
                        ];
                    }
                }
            }
            
            if (empty($files_to_process)) {
                $errors[] = 'At least one certificate file is required';
            }
        } else {
            // Single file upload for other document types
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'File upload failed';
            } elseif ($_FILES['file']['size'] > MAX_UPLOAD_BYTES) {
                $maxMb = (int)floor(MAX_UPLOAD_BYTES / (1024*1024));
                $errors[] = 'File too large (max ' . $maxMb . 'MB)';
            } else {
                $files_to_process[] = $_FILES['file'];
            }
        }
        
        if (!$errors && !empty($files_to_process)) {
            try {
                // Create candidate-specific upload directory if it doesn't exist
                $dest = UPLOAD_DIR . '/candidates/' . $candidate_id;
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
                
                $uploaded_count = 0;
                $sql = 'INSERT INTO candidate_documents (
                    candidate_id, doc_type, file_path, original_name, 
                    mime_type, size_bytes, uploaded_by
                ) VALUES (
                    :cid, :type, :path, :orig, :mime, :size, :uid
                )';
                $stmt = db()->prepare($sql);
                
                foreach ($files_to_process as $file) {
                    // Handle the file upload
                    $res = handle_upload($file, $dest);
                    
                    // Generate thumbnail for video files
                    if (is_video_file($res['original'])) {
                        $thumbnailPath = get_video_thumbnail_path($res['path']);
                        generate_video_thumbnail($res['path'], $thumbnailPath);
                    }
                    
                    // Insert document record
                    $stmt->execute([
                        ':cid' => $candidate_id,
                        ':type' => $doc_type,
                        ':path' => $res['path'],
                        ':orig' => $res['original'],
                        ':mime' => $res['mime'],
                        ':size' => $res['size'],
                        ':uid' => current_user()['id'],
                    ]);
                    
                    $uploaded_count++;
                }
                
                if ($uploaded_count === 1) {
                    $success = 'Document uploaded successfully';
                } else {
                    $success = $uploaded_count . ' documents uploaded successfully';
                }
                
                // Update candidate progress if needed
                update_candidate_progress($candidate_id);
                
            } catch (Throwable $e) {
                $errors[] = 'Upload failed: ' . $e->getMessage();
            }
        }
    }

    $preselect = $_GET['doc_type'] ?? $_GET['type'] ?? '';

    return view('documents_upload', [
        'cand' => $cand,
        'errors' => $errors,
        'success' => $success,
        'candidate_id' => $candidate_id,
        'document_types' => DOCUMENT_TYPES,
        'preselect' => $preselect
    ]);
}

function delete_action(): string {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid id'; }
    
    $stmt = db()->prepare('SELECT * FROM candidate_documents WHERE id=:id');
    $stmt->execute([':id' => $id]);
    $doc = $stmt->fetch();
    
    if (!$doc) { http_response_code(404); return 'Not found'; }
    
    // Soft delete
    $upd = db()->prepare('UPDATE candidate_documents SET is_active=0 WHERE id=:id');
    $upd->execute([':id' => $id]);
    
    // Update candidate progress
    update_candidate_progress((int)$doc['candidate_id']);
    
    redirect(base_url('index.php?page=candidates&action=view&id=' . (int)$doc['candidate_id']));
}

function serve_action(): void {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { 
        http_response_code(400); 
        echo 'Invalid document ID'; 
        return; 
    }
    
    // Fetch document info
    $stmt = db()->prepare('SELECT * FROM candidate_documents WHERE id=:id AND is_active=1');
    $stmt->execute([':id' => $id]);
    $doc = $stmt->fetch();
    
    if (!$doc) { 
        http_response_code(404); 
        echo 'Document not found'; 
        return; 
    }
    
    // Resolve file path with normalization and fallback between legacy roots
    $path = $doc['file_path'];
    $norm = str_replace(['\\'], '/', (string)$path);
    $candidates = [$norm];
    // Try switching between public/uploads and uploads roots
    $candidates[] = str_replace('/public/uploads/', '/uploads/', $norm);
    $candidates[] = str_replace('/uploads/', '/public/uploads/', $norm);
    // Also try resolving relative to project root if path contains app/../
    $candidates[] = realpath(str_replace('/app/../', '/', $norm)) ?: str_replace('/app/../', '/', $norm);

    $usePath = '';
    foreach (array_unique($candidates) as $cand) {
        $fs = str_replace('/', DIRECTORY_SEPARATOR, $cand);
        if (file_exists($fs)) { $usePath = $fs; break; }
    }
    if ($usePath === '') {
        http_response_code(404);
        echo 'File not found on server';
        return;
    }
    
    // Security check - ensure user has access to this candidate's documents
    // You can add additional permission checks here if needed
    
    // Get file info
    $fileSize = filesize($usePath);
    $mimeType = $doc['mime_type'] ?: 'application/octet-stream';
    
    // Set headers for file serving
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . $fileSize);
    header('Content-Disposition: inline; filename="' . addslashes($doc['original_name']) . '"');
    header('Cache-Control: private, max-age=3600');
    header('Pragma: private');
    
    // Serve the file
    readfile($usePath);
    exit;
}

function update_candidate_progress(int $candidate_id): void {
    // This will trigger our before_update trigger which recalculates progress
    $stmt = db()->prepare('UPDATE candidates SET updated_by=:uid WHERE id=:id');
    $stmt->execute([
        ':uid' => current_user()['id'],
        ':id' => $candidate_id
    ]);
}
