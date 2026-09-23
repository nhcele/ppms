<?php
$title = ($mode === 'edit' ? 'Edit Education' : 'Add Education');
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
        <label class="form-label">Institution Name *</label>
        <input class="form-control <?php echo isset($errors['institution_name']) ? 'is-invalid' : ''; ?>" 
               type="text" name="institution_name" 
               value="<?php echo htmlspecialchars($data['institution_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        <?php if(isset($errors['institution_name'])): ?>
          <div class="invalid-feedback"><?php echo htmlspecialchars($errors['institution_name'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
      </div>
      
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Degree *</label>
          <input class="form-control <?php echo isset($errors['degree']) ? 'is-invalid' : ''; ?>" 
                 type="text" name="degree" 
                 value="<?php echo htmlspecialchars($data['degree'], ENT_QUOTES, 'UTF-8'); ?>" required>
          <?php if(isset($errors['degree'])): ?>
            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['degree'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Field of Study</label>
          <input class="form-control" type="text" name="field_of_study" 
                 value="<?php echo htmlspecialchars($data['field_of_study'], ENT_QUOTES, 'UTF-8'); ?>">
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
        <label class="form-check-label" for="is_current">Currently studying here</label>
      </div>
      
      <div class="mb-3 mt-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="3"><?php 
          echo htmlspecialchars($data['description'], ENT_QUOTES, 'UTF-8'); 
        ?></textarea>
      </div>
      
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary" type="submit">
          <?php echo $mode === 'edit' ? 'Save Changes' : 'Add Education'; ?>
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
