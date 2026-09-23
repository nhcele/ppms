<?php
$title = ($mode === 'edit' ? 'Edit Work Experience' : 'Add Work Experience');
ob_start();
?>
<div class="card">
  <div class="card-header">
    <h1 class="h5 mb-0"><?php echo htmlspecialchars($title . ' for ' . $cand['first_name'] . ' ' . $cand['last_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
  </div>
  <div class="card-body">
    <form method="post" action="">
      <?php echo csrf_field(); ?>
      
      <div class="mb-3">
        <label class="form-label">Employer Name *</label>
        <input class="form-control <?php echo isset($errors['employer_name']) ? 'is-invalid' : ''; ?>" 
               type="text" name="employer_name" 
               value="<?php echo htmlspecialchars($data['employer_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        <?php if(isset($errors['employer_name'])): ?>
          <div class="invalid-feedback"><?php echo htmlspecialchars($errors['employer_name'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
      </div>
      
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Position *</label>
          <input class="form-control <?php echo isset($errors['position']) ? 'is-invalid' : ''; ?>" 
                 type="text" name="position" 
                 value="<?php echo htmlspecialchars($data['position'], ENT_QUOTES, 'UTF-8'); ?>" required>
          <?php if(isset($errors['position'])): ?>
            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['position'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Location</label>
          <input class="form-control" type="text" name="location" 
                 value="<?php echo htmlspecialchars($data['location'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
      </div>
      
      <div class="row g-3 mt-2">
        <div class="col-md-6">
          <label class="form-label">Start Date</label>
          <input class="form-control" type="date" name="start_date" 
                 value="<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">End Date</label>
          <input class="form-control" type="date" name="end_date" 
                 value="<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>"
                 <?php echo $data['is_current'] ? 'disabled' : ''; ?>>
        </div>
      </div>
      
      <div class="form-check mt-3">
        <input class="form-check-input" type="checkbox" name="is_current" id="is_current" 
               <?php echo $data['is_current'] ? 'checked' : ''; ?>>
        <label class="form-check-label" for="is_current">Currently working here</label>
      </div>
      
      <div class="mb-3 mt-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="3"><?php 
          echo htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8'); 
        ?></textarea>
      </div>
      
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary" type="submit">
          <?php echo $mode === 'edit' ? 'Save Changes' : 'Add Experience'; ?>
        </button>
        <a class="btn btn-outline-secondary" href="<?php 
          echo base_url('index.php?page=candidates&action=view&id=' . (int)$cand['id']); 
        ?>">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<script>
// Toggle end date based on current checkbox
document.getElementById('is_current').addEventListener('change', function() {
  document.querySelector('input[name="end_date"]').disabled = this.checked;
  if (this.checked) {
    document.querySelector('input[name="end_date"]').value = '';
  }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
