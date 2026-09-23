<?php
$title = ($mode ?? 'create') === 'edit' ? 'Edit Employer' : 'New Employer';
ob_start();
?>
<div class="hero p-4 mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1"><?php echo ($mode ?? 'create') === 'edit' ? 'Edit Employer' : 'New Employer'; ?></h1>
      <div class="text-muted small"><?php echo ($mode ?? 'create') === 'edit' ? 'Update employer details' : 'Add a new employer'; ?></div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=employers&action=list'); ?>" class="btn btn-outline-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Back to Employers
      </a>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="post" action="" class="needs-validation" novalidate>
      <?php echo csrf_field(); ?>
      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Employer Information</h5>
        <div class="row g-3">
          <div class="col-md-12">
            <label for="name" class="form-label">Employer Name <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M6.5 0a.5.5 0 0 0-.5.5V1H2.5A1.5 1.5 0 0 0 1 2.5V15h14V2.5A1.5 1.5 0 0 0 13.5 1H10v-.5a.5.5 0 0 0-1 0V1H7v-.5a.5.5 0 0 0-.5-.5zM2 14V5h12v9H2z"/>
                </svg>
              </span>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter employer name" value="<?php echo htmlspecialchars(($data['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
              <div class="invalid-feedback">Please provide a valid employer name.</div>
            </div>
            <?php if (!empty($errors['name'])): ?>
              <div class="text-danger small mt-1"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Contact Details</h5>
        <div class="row g-3">
          <div class="col-md-6">
            <label for="contact_person" class="form-label">Contact Person</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                </svg>
              </span>
              <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Contact person's name" value="<?php echo htmlspecialchars(($data['contact_person'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
          <div class="col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h6zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H5z"/>
                  <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                </svg>
              </span>
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone number" value="<?php echo htmlspecialchars(($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
              <?php if (!empty($errors['phone'])): ?>
                <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8'); ?></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-12">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text">@</span>
              <input type="email" class="form-control" id="email" name="email" placeholder="employer@example.com" value="<?php echo htmlspecialchars(($data['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
              <?php if (!empty($errors['email'])): ?>
                <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-12">
            <label for="address" class="form-label">Address</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.69 6-10A6 6 0 0 0 2 6c0 4.31 6 10 6 10z"/>
                  <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
              </span>
              <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full address"><?php echo htmlspecialchars(($data['address'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <?php if (($mode ?? 'create') === 'edit'): ?>
        <div class="mb-4">
          <h5 class="mb-3 border-bottom pb-2">Settings</h5>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo !empty($data['is_active']) ? 'checked' : ''; ?> style="width: 2.5em; height: 1.5em;">
            <label class="form-check-label ms-2" for="is_active">Active Employer</label>
            <div class="form-text">Deactivating will prevent this employer from being used in new deployments.</div>
          </div>
        </div>
      <?php endif; ?>

      <div class="d-flex justify-content-between align-items-center pt-3 border-top">
        <a href="<?php echo base_url('index.php?page=employers&action=list'); ?>" class="btn btn-outline-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
          </svg>
          Cancel
        </a>
        <button class="btn btn-primary" type="submit">
          <?php if (($mode ?? 'create') === 'edit'): ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5z"/>
            </svg>
            Save Changes
          <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Create Employer
          <?php endif; ?>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Form validation
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
