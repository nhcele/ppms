<?php
// app/controllers/reports.php - Reports list and exports

declare(strict_types=1);

require_login();
require_role(['admin','recruiter']);

function list_action(): string {
    $pdo = db();
    $agencies = $pdo->query('SELECT id, name FROM agencies ORDER BY name')->fetchAll();
    $employers = $pdo->query('SELECT id, name FROM employers ORDER BY name')->fetchAll();
    return view('reports_list', ['agencies' => $agencies, 'employers' => $employers]);
}

function export_candidates_csv_action(): string {
    $pdo = db();
    $status = trim($_GET['status'] ?? '');
    $agency_id = ($_GET['agency_id'] ?? '') !== '' ? (int)$_GET['agency_id'] : null;
    $employer_id = ($_GET['employer_id'] ?? '') !== '' ? (int)$_GET['employer_id'] : null;

    $sql = "SELECT c.candidate_code, c.first_name, c.last_name, c.email, c.phone, c.status,
                   a.name AS agency, e.name AS employer, d.location_country, d.location_city
            FROM candidates c
            LEFT JOIN agencies a ON a.id=c.agency_id
            LEFT JOIN deployments d ON d.candidate_id=c.id AND d.status IN ('planned','active')
            LEFT JOIN employers e ON e.id=d.employer_id
            WHERE (:status='' OR c.status=:status)
              AND (:agency_id IS NULL OR c.agency_id=:agency_id)
              AND (:employer_id IS NULL OR e.id=:employer_id)
            ORDER BY c.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':status' => $status,
        ':agency_id' => $agency_id,
        ':employer_id' => $employer_id,
    ]);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="candidate_master_list.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Candidate Code','First Name','Last Name','Email','Phone','Status','Agency','Employer','Country','City']);
    while ($row = $stmt->fetch()) {
        fputcsv($out, [
            $row['candidate_code'], $row['first_name'], $row['last_name'], $row['email'], $row['phone'], $row['status'],
            $row['agency'], $row['employer'], $row['location_country'], $row['location_city']
        ]);
    }
    fclose($out);
    exit;
}

function export_expiring_compliance_csv_action(): string {
    $pdo = db();
    $type = $_GET['type'] ?? 'visa_expiry'; // visa_expiry | work_permit_expiry | contract_expiry
    $days = (int)($_GET['days'] ?? 30);
    $days = ($days > 0 && $days <= 365) ? $days : 30;
    $agency_id = ($_GET['agency_id'] ?? '') !== '' ? (int)$_GET['agency_id'] : null;

    // Map type to field
    $field = match($type) {
        'work_permit_expiry' => 'work_permit_expiry',
        'contract_expiry' => 'contract_expiry',
        default => 'visa_expiry',
    };

    $sql = "SELECT cnd.candidate_code, cnd.first_name, cnd.last_name, a.name AS agency, comp.$field AS expiry_date
            FROM compliance comp
            JOIN candidates cnd ON cnd.id=comp.candidate_id
            LEFT JOIN agencies a ON a.id=cnd.agency_id
            WHERE comp.$field BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
              AND (:agency_id IS NULL OR cnd.agency_id=:agency_id)
            ORDER BY comp.$field ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
    $stmt->bindValue(':agency_id', $agency_id, $agency_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->execute();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="expiring_"'.$type.'"_next_"'.$days.'"_days.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Candidate Code','First Name','Last Name','Agency','Expiry Date','Type']);
    while ($row = $stmt->fetch()) {
        fputcsv($out, [
            $row['candidate_code'], $row['first_name'], $row['last_name'], $row['agency'], $row['expiry_date'], $type
        ]);
    }
    fclose($out);
    exit;
}

function expiring_list_action(): string {
    $pdo = db();
    $type = $_GET['type'] ?? 'work_permit_expiry'; // work_permit_expiry | contract_expiry
    $days = (int)($_GET['days'] ?? 90);
    $days = ($days > 0 && $days <= 365) ? $days : 90;
    $agency_id = ($_GET['agency_id'] ?? '') !== '' ? (int)$_GET['agency_id'] : null;

    $field = match($type) {
        'contract_expiry' => 'contract_expiry',
        default => 'work_permit_expiry',
    };

    $sql = "SELECT cnd.id AS candidate_id, cnd.candidate_code, cnd.first_name, cnd.last_name,
                   a.name AS agency, comp.$field AS expiry_date
            FROM candidates cnd
            LEFT JOIN agencies a ON a.id=cnd.agency_id
            JOIN (
                SELECT c1.*
                FROM compliance c1
                JOIN (
                    SELECT candidate_id, MAX(updated_at) AS max_updated
                    FROM compliance
                    GROUP BY candidate_id
                ) latest ON latest.candidate_id=c1.candidate_id AND latest.max_updated=c1.updated_at
            ) comp ON comp.candidate_id=cnd.id
            WHERE comp.$field BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
              AND (:agency_id IS NULL OR cnd.agency_id=:agency_id)
            ORDER BY comp.$field ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':days', $days, PDO::PARAM_INT);
    $stmt->bindValue(':agency_id', $agency_id, $agency_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    return view('reports/expiring_list', [
        'type' => $type,
        'days' => $days,
        'rows' => $rows,
    ]);
}
