<?php
// app/controllers/users.php

declare(strict_types=1);

require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';

function list_action() {
    check_permission('admin');
    
    // Get query parameters
    $q = $_GET['q'] ?? '';
    $role = $_GET['role'] ?? '';
    $status = $_GET['status'] ?? '';
    $sort = $_GET['sort'] ?? 'full_name';
    $dir = strtoupper($_GET['dir'] ?? 'ASC');
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per = (int)($_GET['per'] ?? 20);
    
    // Validate sort direction
    $dir = in_array($dir, ['ASC', 'DESC']) ? $dir : 'ASC';
    
    // Build base query
    $sql = "FROM users WHERE 1=1";
    $params = [];
    
    // Add search conditions
    if (!empty($q)) {
        $sql .= " AND (full_name LIKE ? OR username LIKE ? OR email LIKE ?)";
        $searchTerm = "%$q%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    // Add role filter
    if (!empty($role)) {
        $sql .= " AND role = ?";
        $params[] = $role;
    }
    
    // Add status filter
    if ($status !== '') {
        $sql .= " AND is_active = ?";
        $params[] = (int)$status;
    }
    
    // Get total count for pagination
    $countStmt = db()->prepare("SELECT COUNT(*) AS total $sql");
    $countStmt->execute($params);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $pages = max(1, ceil($total / $per));
    $offset = ($page - 1) * $per;
    
    // Validate page number
    $page = min($page, $pages);
    
    // Add sorting and pagination
    $validSorts = ['full_name', 'username', 'email', 'role', 'is_active', 'created_at'];
    $sort = in_array($sort, $validSorts) ? $sort : 'full_name';
    
    $sql .= " ORDER BY $sort $dir";
    $sql .= " LIMIT " . (int)$per . " OFFSET " . (int)$offset;
    
    // Fetch paginated results
    $stmt = db()->prepare("SELECT * $sql");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return view('users/list', [
        'title' => 'User Management',
        'rows' => $rows,
        'total' => $total,
        'page' => $page,
        'pages' => $pages,
        'per' => $per,
        'q' => $q,
        'role' => $role,
        'status' => $status,
        'sort' => $sort,
        'dir' => $dir
    ]);
}

function add_action() {
    check_permission('admin');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token first
        if (!verify_csrf_token($_POST[CSRF_TOKEN_KEY] ?? '')) {
            set_flash_message('Invalid CSRF token', 'danger');
            redirect('index.php?page=users&action=add');
            return;
        }
        
        // Basic validation
        $errors = [];
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'role' => $_POST['role'] ?? 'candidate',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Validate required fields
        if (empty($data['username'])) $errors[] = 'Username is required';
        if (empty($data['email'])) $errors[] = 'Email is required';
        if (empty($data['full_name'])) $errors[] = 'Full name is required';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';

        // Password validation (use submitted password, not a generated one)
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');
        if ($password === '') {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        } elseif ($password !== $confirm) {
            $errors[] = 'Passwords do not match';
        }
        
        // Check for duplicate username/email
        $stmt = db()->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$data['username'], $data['email'], $_POST['id'] ?? 0]);
        if ($stmt->fetch()) $errors[] = 'Username or email already exists';
        
        // If no errors, create user
        if (empty($errors)) {
            // Hash the password using configured algorithm and cost
            $password_hash = password_hash($password, PASSWORD_ALGO, ['cost' => PASSWORD_COST]);
            if ($password_hash === false) {
                $errors[] = 'Password system error';
            } else {
                try {
                    db()->beginTransaction();
                    $stmt = db()->prepare("INSERT INTO users (username, email, full_name, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $data['username'],
                        $data['email'],
                        $data['full_name'],
                        $password_hash,
                        $data['role'],
                        $data['is_active']
                    ]);
                    db()->commit();
                    set_flash_message('User created successfully', 'success');
                    redirect('index.php?page=users&action=list');
                } catch (Exception $e) {
                    db()->rollBack();
                    error_log('User creation failed: ' . $e->getMessage());
                    $errors[] = 'System error creating user';
                }
            }
        }
    } else {
        $data = [
            'username' => '',
            'email' => '',
            'full_name' => '',
            'role' => 'candidate',
            'is_active' => 1
        ];
    }
    
    return view('users/form', [
        'title' => 'Add New User',
        'user' => (object)$data,
        'errors' => $errors ?? []
    ]);
}

function edit_action($id) {
    check_permission('admin');
    
    $user = db()->query("SELECT * FROM users WHERE id = " . (int)$id)->fetch(PDO::FETCH_OBJ);
    
    if (!$user) {
        set_flash_message('User not found', 'danger');
        redirect('index.php?page=users&action=list');
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token first
        if (!verify_csrf_token($_POST[CSRF_TOKEN_KEY] ?? '')) {
            set_flash_message('Invalid CSRF token', 'danger');
            redirect('index.php?page=users&action=edit&id=' . $id);
            return;
        }
        
        $errors = [];
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'role' => $_POST['role'] ?? 'candidate',
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Validate required fields
        if (empty($data['username'])) $errors[] = 'Username is required';
        if (empty($data['email'])) $errors[] = 'Email is required';
        if (empty($data['full_name'])) $errors[] = 'Full name is required';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';

        // Optional password change
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');
        $password_hash = null;
        if ($password !== '') {
            if (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters';
            } elseif ($password !== $confirm) {
                $errors[] = 'Passwords do not match';
            } else {
                $password_hash = password_hash($password, PASSWORD_ALGO, ['cost' => PASSWORD_COST]);
                if ($password_hash === false) {
                    $errors[] = 'Password system error';
                }
            }
        }
        
        // Check for duplicate username/email
        $stmt = db()->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$data['username'], $data['email'], $id]);
        if ($stmt->fetch()) $errors[] = 'Username or email already exists';
        
        if (empty($errors)) {
            try {
                db()->beginTransaction();
                
                if ($password_hash !== null) {
                    $sql = "UPDATE users SET 
                            username = ?, 
                            email = ?, 
                            full_name = ?, 
                            role = ?, 
                            is_active = ?,
                            password_hash = ? 
                            WHERE id = ?";
                    $stmt = db()->prepare($sql);
                    $stmt->execute([
                        $data['username'],
                        $data['email'],
                        $data['full_name'],
                        $data['role'],
                        $data['is_active'],
                        $password_hash,
                        $id
                    ]);
                } else {
                    $sql = "UPDATE users SET 
                            username = ?, 
                            email = ?, 
                            full_name = ?, 
                            role = ?, 
                            is_active = ? 
                            WHERE id = ?";
                    $stmt = db()->prepare($sql);
                    $stmt->execute([
                        $data['username'],
                        $data['email'],
                        $data['full_name'],
                        $data['role'],
                        $data['is_active'],
                        $id
                    ]);
                }
                
                db()->commit();
                set_flash_message('User updated successfully', 'success');
                redirect('index.php?page=users&action=list');
                return;
                
            } catch (PDOException $e) {
                db()->rollBack();
                error_log('Error updating user: ' . $e->getMessage());
                $errors[] = 'Failed to update user. Please try again.';
            }
        }
    } else {
        $data = (array)$user;
    }
    
    return view('users/form', [
        'title' => 'Edit User',
        'user' => (object)$data,
        'errors' => $errors ?? [],
        'is_edit' => true
    ]);
}

function delete_action($id) {
    check_permission('admin');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token first
        if (!verify_csrf_token($_POST[CSRF_TOKEN_KEY] ?? '')) {
            set_flash_message('Invalid CSRF token', 'danger');
            redirect('index.php?page=users&action=list');
            return;
        }
        
        // Prevent deleting own account
        if ($id == current_user()['id']) {
            set_flash_message('You cannot delete your own account', 'danger');
            redirect('index.php?page=users&action=list');
            return;
        }
        
        try {
            db()->beginTransaction();
            
            $stmt = db()->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                db()->commit();
                set_flash_message('User deleted successfully', 'success');
            } else {
                db()->rollBack();
                set_flash_message('User not found or already deleted', 'warning');
            }
            
        } catch (PDOException $e) {
            db()->rollBack();
            error_log('Error deleting user: ' . $e->getMessage());
            set_flash_message('Failed to delete user. Please try again.', 'danger');
        }
    }
    
    redirect('index.php?page=users&action=list');
}

function reset_password_action($id) {
    check_permission('admin');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token first
        if (!verify_csrf_token($_POST[CSRF_TOKEN_KEY] ?? '')) {
            set_flash_message('Invalid CSRF token', 'danger');
            redirect('index.php?page=users&action=edit&id=' . (int)$id);
            return;
        }

        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');
        $errors = [];

        // Validate
        if ($password === '') {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        } elseif ($password !== $confirm) {
            $errors[] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            set_flash_message(implode('<br>', $errors), 'danger');
            redirect('index.php?page=users&action=edit&id=' . (int)$id);
            return;
        }

        // Hash new password using configured constants
        $password_hash = password_hash($password, PASSWORD_ALGO, ['cost' => PASSWORD_COST]);
        if ($password_hash === false) {
            set_flash_message('Password system error', 'danger');
            redirect('index.php?page=users&action=edit&id=' . (int)$id);
            return;
        }

        try {
            db()->beginTransaction();
            $stmt = db()->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$password_hash, $id]);
            db()->commit();
            set_flash_message('Password reset successfully', 'success');
        } catch (Exception $e) {
            db()->rollBack();
            error_log('Password reset failed: ' . $e->getMessage());
            set_flash_message('System error resetting password', 'danger');
        }
    }
    
    redirect('index.php?page=users&action=list');
}

// Helper function to generate random password
function generate_random_password($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    $chars_length = strlen($chars) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, $chars_length)];
    }
    
    return $password;
}
