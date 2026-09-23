<?php
// app/jobs/run_alerts.php - stub
require __DIR__ . '/../../app/config.php';
require __DIR__ . '/../../app/lib/db.php';

// Generate alerts for visa/work permit/contract expiries at 90/60/30 days
function generate_alerts(string $field, string $type): void {
    $pdo = db();
    $sql = "SELECT c.id AS candidate_id, comp.$field AS expiry
            FROM compliance comp
            JOIN candidates c ON c.id = comp.candidate_id
            WHERE comp.$field IS NOT NULL
              AND comp.$field BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)";
    $rows = $pdo->query($sql)->fetchAll();
    foreach ($rows as $r) {
        $expiry = new DateTime($r['expiry']);
        $today = new DateTime('today');
        $diff = (int)$today->diff($expiry)->format('%a');
        // Round to the nearest configured thresholds
        foreach ([90,60,30] as $d) {
            if ($diff === $d) {
                $ins = $pdo->prepare('INSERT IGNORE INTO alerts (candidate_id, alert_type, alert_date, due_in_days) VALUES (:cid,:type,:date,:days)');
                $ins->execute([
                    ':cid' => (int)$r['candidate_id'],
                    ':type' => $type,
                    ':date' => $expiry->format('Y-m-d'),
                    ':days' => $d,
                ]);
            }
        }
    }
}

generate_alerts('visa_expiry', 'visa_expiry');
generate_alerts('work_permit_expiry', 'work_permit_expiry');
generate_alerts('contract_expiry', 'contract_expiry');

echo "[" . date('c') . "] alerts generated\n";
