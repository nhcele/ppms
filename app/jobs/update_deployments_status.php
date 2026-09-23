<?php
// app/jobs/update_deployments_status.php - stub
require __DIR__ . '/../../app/config.php';
require __DIR__ . '/../../app/lib/db.php';

$pdo = db();

// 1) planned -> active where start_date <= today and (end_date is null or end_date >= today)
$sql1 = "UPDATE deployments SET status='active', updated_at=NOW()
         WHERE status='planned' AND start_date <= CURDATE() AND (end_date IS NULL OR end_date >= CURDATE())";
$pdo->exec($sql1);

// 2) active -> completed where end_date < today
$sql2 = "UPDATE deployments SET status='completed', updated_at=NOW()
         WHERE status='active' AND end_date IS NOT NULL AND end_date < CURDATE()";
$pdo->exec($sql2);

// 3) Update candidate status derived from deployments
// If any active deployment -> candidate.status = 'deployed'
$sql3 = "UPDATE candidates c
         JOIN (
           SELECT DISTINCT candidate_id FROM deployments WHERE status='active'
         ) d ON d.candidate_id=c.id
         SET c.status='deployed'";
$pdo->exec($sql3);

// If candidate has no active deployments but has any completed deployments -> set completed (only if currently deployed or selected)
$sql4 = "UPDATE candidates c
         LEFT JOIN (
           SELECT candidate_id, SUM(status='active') AS has_active, SUM(status='completed') AS has_completed
           FROM deployments GROUP BY candidate_id
         ) d ON d.candidate_id=c.id
         SET c.status='completed'
         WHERE d.candidate_id IS NOT NULL AND d.has_active=0 AND d.has_completed>0 AND c.status IN ('deployed','selected')";
$pdo->exec($sql4);

echo "[" . date('c') . "] deployments status updated\n";
