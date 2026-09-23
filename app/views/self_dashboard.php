<?php
$title = 'My Dashboard';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Candidate Dashboard</h1>
  <div>
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=self-profile&action=edit'); ?>">Edit Profile</a>
    <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=self-docs&action=list'); ?>">My Documents</a>
  </div>
</div>
<div class="card mb-3">
  <div class="card-body">
    <div class="mb-2"><strong>Progress</strong></div>
    <div class="progress" style="height: 8px;">
      <div class="progress-bar" role="progressbar" style="width: <?php echo (int)($cand['progress_percent'] ?? 0); ?>%"></div>
    </div>
    <div class="small text-muted mt-1"><?php echo (int)($cand['progress_percent'] ?? 0); ?>%</div>
  </div>
</div>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <div class="fw-bold">Profile</div>
      <div><?php echo htmlspecialchars($cand['first_name'] . ' ' . $cand['last_name'], ENT_QUOTES, 'UTF-8'); ?></div>
      <div class="text-muted small">Status: <?php echo htmlspecialchars($cand['status'], ENT_QUOTES, 'UTF-8'); ?></div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <div class="fw-bold">Documents</div>
      <div><?php echo (int)$docCount; ?> document(s) uploaded</div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <div class="fw-bold">Next Steps</div>
      <div class="small">Upload your CV and Passport copy to increase your profile completeness.</div>
    </div></div>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
