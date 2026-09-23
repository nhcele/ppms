<?php
// app/controllers/dashboard.php

declare(strict_types=1);

function index_action(): string {
    require_login();
    $user = current_user();

    $pdo = db();

    // Pipeline counts by candidate status (consistent with candidates list filtering)
    $pipeline = [
        'basic_profile_created' => 0,
        'profile_in_progress' => 0,
        'ready_for_selection' => 0,
        'shortlisted' => 0,
        'selected' => 0,
        'deployed' => 0,
        'completed' => 0,
    ];

    // Count deployed candidates (those with active or completed deployments)
    $deployedCount = (int)$pdo->query("SELECT COUNT(DISTINCT candidate_id) AS cnt FROM deployments WHERE status IN ('active','completed')")->fetch()['cnt'];
    $pipeline['deployed'] = $deployedCount;

    // Count non-deployed candidates by their actual status
    $nonDeployedStmt = $pdo->query("
        SELECT status, COUNT(*) AS cnt 
        FROM candidates c 
        WHERE NOT EXISTS (
            SELECT 1 FROM deployments d 
            WHERE d.candidate_id = c.id 
            AND d.status IN ('active','completed')
        )
        GROUP BY status
    ");
    $nonDeployedRaw = $nonDeployedStmt->fetchAll();
    
    foreach ($nonDeployedRaw as $row) {
        if (isset($pipeline[$row['status']])) {
            $pipeline[$row['status']] = (int)$row['cnt'];
        }
    }

        // Upcoming starts (next 90 days)
        $upcoming = $pdo->query("SELECT d.id, d.start_date, c.first_name, c.last_name, e.name AS employer
                                 FROM deployments d
                                 JOIN candidates c ON c.id=d.candidate_id
                                 JOIN employers e ON e.id=d.employer_id
                                 WHERE d.start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)
                                 ORDER BY d.start_date ASC
                                 LIMIT 10")->fetchAll();

    // Expiring work permits (next 90 days) - latest compliance per candidate
    $expiringPermits = $pdo->query("SELECT cand.id, cand.first_name, cand.last_name, comp.work_permit_expiry
                                    FROM candidates cand
                                    JOIN (
                                        SELECT c1.*
                                        FROM compliance c1
                                        JOIN (
                                            SELECT candidate_id, MAX(updated_at) AS max_updated
                                            FROM compliance
                                            GROUP BY candidate_id
                                        ) AS latest ON latest.candidate_id = c1.candidate_id AND latest.max_updated = c1.updated_at
                                    ) AS comp ON comp.candidate_id = cand.id
                                    WHERE comp.work_permit_expiry IS NOT NULL
                                      AND comp.work_permit_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)
                                    ORDER BY comp.work_permit_expiry ASC
                                    LIMIT 10")->fetchAll();

    // Expiring contracts (next 90 days) - latest compliance per candidate
    $expiringContracts = $pdo->query("SELECT cand.id, cand.first_name, cand.last_name, comp.contract_expiry
                                      FROM candidates cand
                                      JOIN (
                                          SELECT c1.*
                                          FROM compliance c1
                                          JOIN (
                                              SELECT candidate_id, MAX(updated_at) AS max_updated
                                              FROM compliance
                                              GROUP BY candidate_id
                                          ) AS latest ON latest.candidate_id = c1.candidate_id AND latest.max_updated = c1.updated_at
                                      ) AS comp ON comp.candidate_id = cand.id
                                      WHERE comp.contract_expiry IS NOT NULL
                                        AND comp.contract_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)
                                      ORDER BY comp.contract_expiry ASC
                                      LIMIT 10")->fetchAll();

    // Alerts count (undismissed)
    $alertsCount = (int)$pdo->query("SELECT COUNT(*) AS c FROM alerts WHERE is_dismissed=0")->fetch()['c'];

    // Alerts expiring next 30 days by type
    $alertsByType = $pdo->query("SELECT alert_type, COUNT(*) AS cnt
                                 FROM alerts
                                 WHERE is_dismissed=0 AND alert_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                                 GROUP BY alert_type")
                         ->fetchAll();
    $alerts30 = ['visa_expiry'=>0,'work_permit_expiry'=>0,'contract_expiry'=>0];
    foreach ($alertsByType as $r) { $alerts30[$r['alert_type']] = (int)$r['cnt']; }

    // Salary snapshot: sum by employer for planned/active deployments (GROUP BY name for ONLY_FULL_GROUP_BY)
    $salary = $pdo->query("SELECT e.name AS employer, SUM(d.salary_amount) AS total, COALESCE(d.salary_currency, 'USD') AS currency
                           FROM deployments d
                           JOIN employers e ON e.id=d.employer_id
                           WHERE d.status IN ('planned','active') AND d.salary_amount IS NOT NULL
                           GROUP BY e.id, e.name, currency
                           ORDER BY total DESC
                           LIMIT 5")->fetchAll();

    // Agency grouping counts (top 8). MySQL doesn't support NULLS LAST; emulate via agency IS NULL
    $agency = $pdo->query("SELECT a.name AS agency, COUNT(*) AS cnt
                           FROM candidates c LEFT JOIN agencies a ON a.id=c.agency_id
                           GROUP BY a.id, a.name
                           ORDER BY cnt DESC, (a.name IS NULL) ASC, agency ASC
                           LIMIT 8")->fetchAll();

    // Questionnaire statistics
    $questionnaireStats = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = 'opened' THEN 1 ELSE 0 END) as opened,
            SUM(CASE WHEN status = 'link_created' OR status = 'link_sent' THEN 1 ELSE 0 END) as not_started,
            SUM(CASE WHEN expiry_time < NOW() AND status NOT IN ('submitted', 'completed', 'revoked') THEN 1 ELSE 0 END) as expired
        FROM questionnaire_requests
    ")->fetch();

    // Recent questionnaire activity
    $recentQuestionnaireActivity = $pdo->query("
        SELECT qr.request_code, qr.status, qr.created_at,
               CONCAT(c.first_name, ' ', c.last_name) as candidate_name,
               qal.action_type
        FROM questionnaire_requests qr
        LEFT JOIN candidates c ON qr.candidate_id = c.id
        LEFT JOIN questionnaire_audit_log qal ON qr.id = qal.questionnaire_request_id
        ORDER BY qal.created_at DESC
        LIMIT 5
    ")->fetchAll();

    return view('dashboard_index', [
        'user' => $user,
        'pipeline' => $pipeline,
        'upcoming' => $upcoming,
        'expiringPermits' => $expiringPermits,
        'expiringContracts' => $expiringContracts,
        'alertsCount' => $alertsCount,
        'alerts30' => $alerts30,
        'salary' => $salary,
        'agency' => $agency,
        'questionnaireStats' => $questionnaireStats,
        'recentQuestionnaireActivity' => $recentQuestionnaireActivity,
    ]);
}
