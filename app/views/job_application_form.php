<?php
$title = ($mode === 'edit' ? 'Edit Job Application' : 'Add Job Application');
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
        <label class="form-label">Job *</label>
        <select class="form-select <?php echo isset($errors['job_post_id']) ? 'is-invalid' : ''; ?>" name="job_post_id" required>
          <option value="">-- Select Job --</option>
          <?php foreach ($jobs as $job): ?>
            <option value="<?php echo (int)$job['id']; ?>" 
              <?php echo ((int)$data['job_post_id'] === (int)$job['id']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($job['title'], ENT_QUOTES, 'UTF-8'); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if(isset($errors['job_post_id'])): ?>
          <div class="invalid-feedback"><?php echo htmlspecialchars($errors['job_post_id'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Status *</label>
        <select class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" name="status" required>
          <option value="applied" <?php echo $data['status'] === 'applied' ? 'selected' : ''; ?>>Applied</option>
          <option value="shortlisted" <?php echo $data['status'] === 'shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
          <option value="interviewed" <?php echo $data['status'] === 'interviewed' ? 'selected' : ''; ?>>Interviewed</option>
          <option value="offered" <?php echo $data['status'] === 'offered' ? 'selected' : ''; ?>>Offered</option>
          <option value="hired" <?php echo $data['status'] === 'hired' ? 'selected' : ''; ?>>Hired</option>
          <option value="rejected" <?php echo $data['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
        </select>
        <?php if(isset($errors['status'])): ?>
          <div class="invalid-feedback"><?php echo htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea class="form-control" name="notes" rows="3"><?php 
          echo htmlspecialchars($data['notes'], ENT_QUOTES, 'UTF-8'); 
        ?></textarea>
      </div>
      
      <div class="d-flex justify-content-between">
        <button class="btn btn-primary" type="submit">
          <?php echo $mode === 'edit' ? 'Save Changes' : 'Add Application'; ?>
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

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
