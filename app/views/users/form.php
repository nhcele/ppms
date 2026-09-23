<?php
$is_edit = isset($user->id);
$title = $is_edit ? 'Edit User' : 'New User';
ob_start();
?>

<div class="hero p-4 mb-3 bg-light rounded-3">
  <div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold"><?php echo $is_edit ? 'Edit User' : 'New User'; ?></h1>
        <p class="col-md-8 fs-5 text-muted">
          <?php echo $is_edit ? 'Update user details' : 'Add a new user to the system'; ?>
        </p>
      </div>
      <div>
        <a href="<?php echo base_url('index.php?page=users&action=list'); ?>" class="btn btn-outline-secondary btn-lg">
          <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </svg>
        <div>
            <h6 class="alert-heading mb-1">Please fix the following errors:</h6>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="post" action="<?php echo base_url('index.php?page=users&action=' . ($is_edit ? 'edit&id=' . $user->id : 'add')); ?>" class="needs-validation" novalidate>
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" class="form-control <?php echo isset($errors['full_name']) ? 'is-invalid' : ''; ?>" 
                               id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user->full_name ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                               required>
                        <div class="invalid-feedback">
                            Please provide a full name.
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">@</span>
                        <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                               id="username" name="username" 
                               value="<?php echo htmlspecialchars($user->username ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                               <?php echo !$is_edit ? 'required' : ''; ?>>
                        <div class="invalid-feedback">
                            Please choose a username.
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" 
                               value="<?php echo htmlspecialchars($user->email ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                               required>
                        <div class="invalid-feedback">
                            Please provide a valid email address.
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person-badge"></i>
                        </span>
                        <select class="form-select <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" 
                                id="role" name="role" required>
                            <option value="">Select Role</option>
                            <?php 
                            $roles = [
                                'admin' => 'Administrator', 
                                'recruiter' => 'Recruiter', 
                                'employer' => 'Employer', 
                                'candidate' => 'Candidate'
                            ];
                            $currentRole = $user->role ?? '';
                            foreach ($roles as $value => $label): 
                                $selected = $currentRole === $value ? 'selected' : '';
                            ?>
                                <option value="<?php echo $value; ?>" <?php echo $selected; ?>>
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a role.
                        </div>
                    </div>
                </div>
                
                <?php if (!$is_edit): ?>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-key"></i>
                            </span>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                   id="password" name="password" required
                                   placeholder="Minimum 8 characters">
                            <div class="invalid-feedback">
                                Password is required and must be at least 8 characters.
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-key-fill"></i>
                            </span>
                            <input type="password" class="form-control" 
                                   id="confirm_password" name="confirm_password" required
                                   placeholder="Retype password">
                            <div class="invalid-feedback">
                                Passwords must match.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" 
                               id="is_active" name="is_active" value="1"
                               <?php echo ($user->is_active ?? 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Active User</label>
                        <div class="form-text">
                            Inactive users cannot log in to the system.
                        </div>
                    </div>
                </div>
                
                <?php if ($is_edit): ?>
                    <div class="col-12 mt-4">
                        <div class="accordion" id="passwordAccordion">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="passwordHeading">
                                    <button class="accordion-button collapsed px-0 py-2" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#passwordCollapse" 
                                            aria-expanded="false" aria-controls="passwordCollapse">
                                        <i class="bi bi-key me-2"></i> Change Password
                                    </button>
                                </h2>
                                <div id="passwordCollapse" class="accordion-collapse collapse" 
                                     aria-labelledby="passwordHeading" data-bs-parent="#passwordAccordion">
                                    <div class="accordion-body px-0 pt-3">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Leave these fields blank to keep the current password.
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="password" class="form-label">New Password</label>
                                                <input type="password" class="form-control" id="password" name="password"
                                                       placeholder="Leave blank to keep current">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" id="confirm_password" 
                                                       name="confirm_password" placeholder="Retype new password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="col-12 mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo base_url('index.php?page=users&action=list'); ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> 
                            <?php echo $is_edit ? 'Update User' : 'Create User'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($is_edit && $user->id != current_user()['id']): ?>
<div class="card border-danger mt-4">
    <div class="card-header bg-danger text-white d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <span>Danger Zone</span>
    </div>
    <div class="card-body">
        <h5 class="card-title text-danger">Delete User</h5>
        <p class="card-text">Once deleted, this user will be permanently removed from the system. This action cannot be undone.</p>
        <button type="button" class="btn btn-outline-danger" 
                onclick="confirmDelete(<?php echo $user->id; ?>, '<?php echo addslashes($user->full_name); ?>')">
            <i class="bi bi-trash me-1"></i> Delete User
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You are about to delete the user <strong id="userName"></strong>. This action cannot be undone.</p>
                <p class="mb-0">Are you sure you want to continue?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cancel
                </button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i> Delete User
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')
    
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            
            // Check if passwords match
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords don't match");
                confirmPassword.classList.add('is-invalid');
                event.preventDefault();
                event.stopPropagation();
            } else if (confirmPassword) {
                confirmPassword.setCustomValidity('');
            }
            
            form.classList.add('was-validated')
        }, false)
    })
})();

// Delete confirmation
function confirmDelete(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('confirmDeleteBtn').href = 'index.php?page=users&action=delete&id=' + userId;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Password toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.querySelector(`[data-password-toggle="${inputId}"] i`);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
