<?php
// app/controllers/self-docs.php - Candidate self-service document management

declare(strict_types=1);

require_once __DIR__ . '/self.php';
require_once __DIR__ . '/../lib/file_upload.php';

function list_action(): string {
    $cand = self_require_candidate();
    $stmt = db()->prepare('SELECT * FROM candidate_documents WHERE candidate_id=:id AND is_active=1 ORDER BY uploaded_at DESC');
    $stmt->execute([':id' => $cand['id']]);
    $docs = $stmt->fetchAll();
    return view('self_docs_list', ['cand' => $cand, 'documents' => $docs]);
}

function upload_action(): string {
    $cand = self_require_candidate();
    $errors = []; $success = null;
    if (is_post()) {
        csrf_verify();
        $doc_type = $_POST['doc_type'] ?? '';
        if (!in_array($doc_type, ['passport','cv','certificate','medical','other'], true)) {
            $errors[] = 'Invalid document type';
        }
        if (!isset($_FILES['file'])) {
            $errors[] = 'File is required';
        }
        if (!$errors) {
            try {
                $dest = UPLOAD_DIR . '/candidates/' . $cand['id'];
                $res = handle_upload($_FILES['file'], $dest);
                $sql = 'INSERT INTO candidate_documents (candidate_id, doc_type, file_path, original_name, mime_type, size_bytes, uploaded_by) VALUES (:cid,:type,:path,:orig,:mime,:size,:uid)';
                $stmt = db()->prepare($sql);
                $stmt->execute([
                    ':cid' => $cand['id'],
                    ':type' => $doc_type,
                    ':path' => $res['path'],
                    ':orig' => $res['original'],
                    ':mime' => $res['mime'],
                    ':size' => $res['size'],
                    ':uid' => current_user()['id'],
                ]);
                $success = 'Document uploaded successfully';
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }
    }
    return view('self_docs_upload', ['cand' => $cand, 'errors' => $errors, 'success' => $success]);
}

function delete_action(): string {
    $cand = self_require_candidate();
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid id'; }
    $stmt = db()->prepare('SELECT * FROM candidate_documents WHERE id=:id AND candidate_id=:cid');
    $stmt->execute([':id' => $id, ':cid' => $cand['id']]);
    $doc = $stmt->fetch();
    if (!$doc) { http_response_code(404); return 'Not found'; }
    $upd = db()->prepare('UPDATE candidate_documents SET is_active=0 WHERE id=:id');
    $upd->execute([':id' => $id]);
    redirect(base_url('index.php?page=self-docs&action=list'));
}
