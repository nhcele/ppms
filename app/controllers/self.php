<?php
// app/controllers/self.php - Candidate portal dashboard

declare(strict_types=1);

function self_require_candidate(): array {
    $u = current_user();
    if (!$u || $u['role'] !== 'candidate') {
        redirect(base_url('index.php?page=self-auth&action=login'));
    }
    // Find candidate row by user_id
    $stmt = db()->prepare('SELECT * FROM candidates WHERE user_id=:uid LIMIT 1');
    $stmt->execute([':uid' => $u['id']]);
    $cand = $stmt->fetch();
    if (!$cand) {
        http_response_code(404);
        exit('Candidate profile not found');
    }
    return $cand;
}

function dashboard_action(): string {
    $cand = self_require_candidate();
    // For now, show basic stats (documents count etc.)
    $docs = db()->prepare('SELECT COUNT(*) AS cnt FROM candidate_documents WHERE candidate_id=:id AND is_active=1');
    $docs->execute([':id' => $cand['id']]);
    $docCount = (int)$docs->fetch()['cnt'];
    return view('self_dashboard', ['cand' => $cand, 'docCount' => $docCount]);
}
