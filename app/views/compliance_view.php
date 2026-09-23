<?php
$title = 'Compliance';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Compliance — <?php echo htmlspecialchars($cand['first_name'].' '.$cand['last_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
  <div>
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=candidates&action=view&id='.(int)$cand['id']); ?>">Back to Candidate</a>
  </div>
</div>
<?php if ($m = flash('success')): ?><div class="alert alert-success"><?php echo htmlspecialchars($m, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
<form method="post" action="<?php echo base_url('index.php?page=compliance&action=save&candidate_id='.(int)$cand['id']); ?>">
  <?php echo csrf_field(); ?>
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Visa Expiry</label>
      <input class="form-control" type="date" name="visa_expiry" value="<?php echo htmlspecialchars($comp['visa_expiry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Visa Status</label>
      <?php $vs = $comp['visa_status'] ?? ''; ?>
      <select class="form-select" name="visa_status">
        <option value="">--</option>
        <option value="not_applied" <?php echo $vs==='not_applied'?'selected':''; ?>>Not Applied</option>
        <option value="applied" <?php echo $vs==='applied'?'selected':''; ?>>Applied</option>
        <option value="approved" <?php echo $vs==='approved'?'selected':''; ?>>Approved</option>
      </select>
    </div>
  </div>
  <div class="row g-3 mt-1">
    <div class="col-md-4">
      <label class="form-label">Work Permit Expiry</label>
      <input class="form-control" type="date" name="work_permit_expiry" value="<?php echo htmlspecialchars($comp['work_permit_expiry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="col-md-4">
      <label class="form-label">Work Permit Status</label>
      <?php $wps = $comp['work_permit_status'] ?? ''; ?>
      <select class="form-select" name="work_permit_status">
        <option value="">--</option>
        <option value="not_applied" <?php echo $wps==='not_applied'?'selected':''; ?>>Not Applied</option>
        <option value="applied" <?php echo $wps==='applied'?'selected':''; ?>>Applied</option>
        <option value="approved" <?php echo $wps==='approved'?'selected':''; ?>>Approved</option>
      </select>
    </div>
  </div>
  <div class="row g-3 mt-1">
    <div class="col-md-4">
      <label class="form-label">Contract Expiry</label>
      <input class="form-control" type="date" name="contract_expiry" value="<?php echo htmlspecialchars($comp['contract_expiry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
  </div>
  <div class="mt-3">
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-outline-secondary" href="<?php echo base_url('index.php?page=candidates&action=view&id='.(int)$cand['id']); ?>">Cancel</a>
  </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
