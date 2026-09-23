<?php
// app/controllers/admin_seeds.php - Quick seed helpers (dev only)

declare(strict_types=1);

require_login();
require_role(['admin']);

function index_action(): string {
    $pdo = db();
    $out = [];

    if (is_post()) {
        csrf_verify();
        $did = [];
        // Seed default agency
        if (isset($_POST['seed_agency'])) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO agencies(name,is_active) VALUES ('Default Agency',1)");
            $stmt->execute();
            $did[] = 'Default Agency';
        }
        // Seed default employer
        if (isset($_POST['seed_employer'])) {
            // Try to find employer
            $exists = $pdo->query("SELECT id FROM employers WHERE name='Default Employer' LIMIT 1")->fetch();
            if (!$exists) {
                $ins = $pdo->prepare("INSERT INTO employers(name,is_active) VALUES ('Default Employer',1)");
                $ins->execute();
            }
            $did[] = 'Default Employer';
        }
        // Seed sample job post
        if (isset($_POST['seed_job'])) {
            $emp = $pdo->query("SELECT id FROM employers WHERE name='Default Employer' LIMIT 1")->fetch();
            if ($emp) {
                $ins = $pdo->prepare("INSERT INTO job_posts(employer_id,title,status,openings) VALUES (:eid,'General Worker','open',10)");
                $ins->execute([':eid' => (int)$emp['id']]);
                $did[] = 'Job Post';
            } else {
                $out[] = 'Create Default Employer first';
            }
        }
        // Seed sample candidate
        if (isset($_POST['seed_candidate'])) {
            // Build candidate code CAND-YYYY-XXXX
            $year = date('Y');
            $cntStmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM candidates WHERE candidate_code LIKE :y");
            $cntStmt->execute([':y' => 'CAND-' . $year . '-%']);
            $n = (int)$cntStmt->fetch()['cnt'] + 1;
            $code = sprintf('CAND-%s-%04d', $year, $n);

            $agencyId = $pdo->query("SELECT id FROM agencies WHERE name='Default Agency' LIMIT 1")->fetch()['id'] ?? null;
            $ins = $pdo->prepare("INSERT INTO candidates (candidate_code, first_name, last_name, email, phone, status, agency_id)
                                  VALUES (:code, 'John', 'Doe', 'john.doe@example.com', '+1000000000', 'basic_profile_created', :agency)");
            $ins->execute([':code' => $code, ':agency' => $agencyId]);
            $did[] = 'Sample Candidate ' . $code;
        }
        if ($did) { $out[] = 'Seeded: ' . implode(', ', $did); }
    }

    // Read simple counts
    $counts = [
        'agencies' => (int)$pdo->query('SELECT COUNT(*) FROM agencies')->fetchColumn(),
        'employers' => (int)$pdo->query('SELECT COUNT(*) FROM employers')->fetchColumn(),
        'job_posts' => (int)$pdo->query('SELECT COUNT(*) FROM job_posts')->fetchColumn(),
        'candidates' => (int)$pdo->query('SELECT COUNT(*) FROM candidates')->fetchColumn(),
    ];

    return view('admin_seeds', [
        'messages' => $out,
        'counts' => $counts,
    ]);
}
