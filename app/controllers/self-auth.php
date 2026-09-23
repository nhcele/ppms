<?php
// app/controllers/self-auth.php - Candidate self-service authentication

declare(strict_types=1);

require_once __DIR__ . '/../lib/validators.php';
require_once __DIR__ . '/../lib/mailer.php';

function register_action(): string {
    if (current_user()) { redirect(base_url('index.php?page=self&action=dashboard')); }
    $errors = [];
    $data = ['full_name' => '', 'email' => '', 'password' => '', 'confirm' => ''];

    if (is_post()) {
        csrf_verify();
        $data['full_name'] = trim($_POST['full_name'] ?? '');
        $data['email'] = trim($_POST['email'] ?? '');
        $data['password'] = $_POST['password'] ?? '';
        $data['confirm'] = $_POST['confirm'] ?? '';

        if ($data['full_name'] === '') $errors['full_name'] = 'Full name is required';
        if (!v_email($data['email'])) $errors['email'] = 'Valid email required';
        if (strlen($data['password']) < 8) $errors['password'] = 'Password must be at least 8 characters';
        if ($data['password'] !== $data['confirm']) $errors['confirm'] = 'Passwords do not match';

        if (!$errors) {
            // Ensure unique email
            $stmt = db()->prepare('SELECT id FROM users WHERE email=:e LIMIT 1');
            $stmt->execute([':e' => $data['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'Email already registered';
            } else {
                $pdo = db();
                $pdo->beginTransaction();
                try {
                    $ins = $pdo->prepare('INSERT INTO users (username,password_hash,full_name,email,role,is_active) VALUES (:u,:p,:n,:e,\'candidate\',1)');
                    $username = $data['email'];
                    $ins->execute([
                        ':u' => $username,
                        ':p' => password_hash($data['password'], PASSWORD_DEFAULT),
                        ':n' => $data['full_name'],
                        ':e' => $data['email'],
                    ]);
                    $user_id = (int)$pdo->lastInsertId();

                    // Create linked candidate shell
                    $code = sprintf('CAND-%s-%04d', date('Y'), random_int(1, 9999));
                    $cins = $pdo->prepare('INSERT INTO candidates (candidate_code, first_name, last_name, email, status, progress_percent, user_id, created_by, updated_by) VALUES (:code,:fn,:ln,:email,\'basic_profile_created\',0,:uid,:uid,:uid)');
                    // Split name naively
                    $parts = preg_split('/\s+/', $data['full_name']);
                    $first = array_shift($parts) ?: '';
                    $last = implode(' ', $parts);
                    $cins->execute([
                        ':code' => $code,
                        ':fn' => $first,
                        ':ln' => $last,
                        ':email' => $data['email'],
                        ':uid' => $user_id,
                    ]);

                    // Email verification token (optional enforcement)
                    $token = bin2hex(random_bytes(16));
                    $exp = (new DateTime('+1 day'))->format('Y-m-d H:i:s');
                    $tins = $pdo->prepare('INSERT INTO email_verification_tokens (user_id, token, expires_at) VALUES (:uid,:t,:exp)');
                    $tins->execute([':uid' => $user_id, ':t' => $token, ':exp' => $exp]);
                    $pdo->commit();

                    $verifyUrl = base_url('index.php?page=self-auth&action=verify&token=' . urlencode($token));
                    $html = '<p>Hello ' . htmlspecialchars($data['full_name']) . ',</p><p>Please verify your email:</p><p><a href="' . $verifyUrl . '">Verify</a></p>';
                    send_mail($data['email'], $data['full_name'], 'Verify your email', $html, "Verify: $verifyUrl");

                    flash('success', 'Registration successful. Check your email to verify your account.');
                    redirect(base_url('index.php?page=self-auth&action=login'));
                } catch (Throwable $e) {
                    $pdo->rollBack();
                    $errors['general'] = 'Registration failed. Please try again.';
                }
            }
        }
    }

    return view('self_auth_register', ['errors' => $errors, 'data' => $data]);
}

function verify_action(): string {
    $token = $_GET['token'] ?? '';
    if ($token === '') { http_response_code(400); return 'Invalid token'; }
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM email_verification_tokens WHERE token=:t AND used_at IS NULL AND expires_at > NOW()');
    $stmt->execute([':t' => $token]);
    $row = $stmt->fetch();
    if (!$row) { return 'Token invalid or expired'; }
    $pdo->beginTransaction();
    try {
        $upd = $pdo->prepare('UPDATE email_verification_tokens SET used_at=NOW() WHERE id=:id');
        $upd->execute([':id' => $row['id']]);
        // Optionally mark user verified; here we keep is_active already true
        $pdo->commit();
        flash('success', 'Email verified. You can now log in.');
        redirect(base_url('index.php?page=self-auth&action=login'));
    } catch (Throwable $e) {
        $pdo->rollBack();
        return 'Verification failed.';
    }
}

function login_action(): string {
    if (current_user()) { redirect(base_url('index.php?page=self&action=dashboard')); }
    $errors = [];
    if (is_post()) {
        csrf_verify();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        // login by username (email used as username at registration)
        if ($email && $password) {
            if (login($email, $password)) {
                if (!has_role('candidate')) {
                    logout();
                    $errors[] = 'This login is for candidates only.';
                } else {
                    redirect(base_url('index.php?page=self&action=dashboard'));
                }
            } else {
                $errors[] = 'Invalid credentials';
            }
        } else {
            $errors[] = 'Email and password are required';
        }
    }
    return view('self_auth_login', ['errors' => $errors]);
}

function logout_action(): string {
    logout();
    redirect(base_url('index.php?page=self-auth&action=login'));
}

function forgot_action(): string {
    $msg = null; $errors = [];
    if (is_post()) {
        csrf_verify();
        $email = trim($_POST['email'] ?? '');
        if (!v_email($email)) { $errors[] = 'Enter a valid email'; }
        if (!$errors) {
            $stmt = db()->prepare('SELECT id, full_name FROM users WHERE email=:e AND role=\'candidate\' LIMIT 1');
            $stmt->execute([':e' => $email]);
            if ($u = $stmt->fetch()) {
                $token = bin2hex(random_bytes(16));
                $exp = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');
                $ins = db()->prepare('INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (:uid,:t,:exp)');
                $ins->execute([':uid' => $u['id'], ':t' => $token, ':exp' => $exp]);
                $url = base_url('index.php?page=self-auth&action=reset&token=' . urlencode($token));
                send_mail($email, $u['full_name'], 'Reset your password', '<p>Reset: <a href="'.$url.'">link</a></p>', 'Reset: '.$url);
            }
            $msg = 'If the email exists, a reset link has been sent.';
        }
    }
    return view('self_auth_forgot', ['message' => $msg, 'errors' => $errors]);
}

function reset_action(): string {
    $token = $_GET['token'] ?? '';
    $pdo = db();
    $stmt = $pdo->prepare('SELECT * FROM password_reset_tokens WHERE token=:t AND used_at IS NULL AND expires_at > NOW()');
    $stmt->execute([':t' => $token]);
    $row = $stmt->fetch();
    if (!$row) { return 'Token invalid or expired'; }

    $errors = [];
    if (is_post()) {
        csrf_verify();
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters';
        if ($password !== $confirm) $errors['confirm'] = 'Passwords do not match';
        if (!$errors) {
            $pdo->beginTransaction();
            try {
                $upd = $pdo->prepare('UPDATE users SET password_hash=:p WHERE id=:id');
                $upd->execute([':p' => password_hash($password, PASSWORD_DEFAULT), ':id' => $row['user_id']]);
                $mark = $pdo->prepare('UPDATE password_reset_tokens SET used_at=NOW() WHERE id=:id');
                $mark->execute([':id' => $row['id']]);
                $pdo->commit();
                flash('success', 'Password updated. Please log in.');
                redirect(base_url('index.php?page=self-auth&action=login'));
            } catch (Throwable $e) {
                $pdo->rollBack();
                $errors['general'] = 'Failed to reset password.';
            }
        }
    }
    return view('self_auth_reset', ['token' => $token, 'errors' => $errors]);
}
