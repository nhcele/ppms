<?php
// app/controllers/alerts.php - List and dismiss compliance alerts

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);

function list_action(): string {
    $pdo = db();
    $rows = $pdo->query("SELECT a.*, c.first_name, c.last_name FROM alerts a JOIN candidates c ON c.id=a.candidate_id WHERE a.is_dismissed=0 ORDER BY a.alert_date ASC LIMIT 500")->fetchAll();
    return view('alerts_list', ['rows' => $rows]);
}

function dismiss_action(): string {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) { http_response_code(400); return 'Invalid id'; }
    $stmt = db()->prepare('UPDATE alerts SET is_dismissed=1 WHERE id=:id');
    $stmt->execute([':id' => $id]);
    redirect(base_url('index.php?page=alerts&action=list'));
}
