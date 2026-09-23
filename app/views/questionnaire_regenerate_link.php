<?php
$title = 'Regenerate Questionnaire Link';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Regenerate Questionnaire Link</h1>
      <div class="text-muted small">Generate new secure token for questionnaire</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=questionnaire&action=view&id=' . $id); ?>" class="btn btn-secondary">Back to Request</a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo base_url('index.php?page=questionnaire&action=regenerate-link&id=' . $id); ?>">
      <?php echo csrf_field(); ?>
      
      <div class="alert alert-warning">
        <strong>Warning:</strong> This will generate a new secure token for the questionnaire link. 
        The old link will no longer work. This action will be logged in the audit trail.
      </div>
      
      <div class="mb-3">
        <label for="expiry_hours" class="form-label">New Expiry Time (Hours)</label>
        <input type="number" class="form-control" id="expiry_hours" name="expiry_hours" 
               value="1" min="1" max="168">
        <div class="form-text">Between 1 and 168 hours (1 week)</div>
      </div>
      
      <div class="mb-3">
        <label for="reason" class="form-label">Reason for Regeneration</label>
        <textarea class="form-control" id="reason" name="reason" rows="3" 
                  placeholder="Explain why you need to regenerate the link..."></textarea>
      </div>
      
      <div class="mb-3">
        <button type="submit" class="btn btn-warning">Regenerate Link</button>
        <a href="<?php echo base_url('index.php?page=questionnaire&action=view&id=' . $id); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
