<?php
$title = 'Request Correction';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Request Correction</h1>
      <div class="text-muted small">Request corrections from candidate</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=questionnaire&action=view&id=' . $id); ?>" class="btn btn-secondary">Back to Request</a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo base_url('index.php?page=questionnaire&action=request-correction&id=' . $id); ?>">
      <?php echo csrf_field(); ?>
      
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <div class="alert alert-info">
        <strong>Note:</strong> This will change the questionnaire status to "Correction Required" and allow the candidate to access the questionnaire again to make the requested changes.
      </div>
      
      <div class="mb-3">
        <label for="correction_details" class="form-label">Correction Details *</label>
        <textarea class="form-control" id="correction_details" name="correction_details" rows="6" required
                  placeholder="Specify exactly what information or documents need to be corrected..."></textarea>
        <div class="form-text">Be specific about which fields or documents need attention</div>
      </div>
      
      <div class="mb-3">
        <button type="submit" class="btn btn-warning">Request Correction</button>
        <a href="<?php echo base_url('index.php?page=questionnaire&action=view&id=' . $id); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
