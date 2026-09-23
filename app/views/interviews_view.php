<?php
$title = 'Interview Details';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Interview #<?php echo (int)$iv['id']; ?></h1>
  <div>
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=interviews&action=list'); ?>">Back</a>
    <a class="btn btn-sm btn-primary" href="<?php echo base_url('index.php?page=interviews&action=edit&id='.(int)$iv['id']); ?>">Edit</a>
    <a class="btn btn-sm btn-outline-danger" href="<?php echo base_url('index.php?page=interviews&action=cancel&id='.(int)$iv['id']); ?>" onclick="return confirm('Cancel this interview?')">Cancel</a>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">Candidate</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($iv['first_name'].' '.$iv['last_name'], ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Employer</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($iv['employer'], ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Scheduled at</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($iv['scheduled_at'], ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Duration</dt>
      <dd class="col-sm-9"><?php echo (int)$iv['duration_mins']; ?> minutes</dd>
      <dt class="col-sm-3">Location</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($iv['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Meeting Link</dt>
      <dd class="col-sm-9"><a href="<?php echo htmlspecialchars($iv['meeting_link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($iv['meeting_link'] ?? '', ENT_QUOTES, 'UTF-8'); ?></a></dd>
      <dt class="col-sm-3">Status</dt>
      <dd class="col-sm-9"><span class="badge bg-secondary"><?php echo htmlspecialchars($iv['status'], ENT_QUOTES, 'UTF-8'); ?></span></dd>
      <dt class="col-sm-3">Notes</dt>
      <dd class="col-sm-9"><?php echo nl2br(htmlspecialchars($iv['notes'] ?? '', ENT_QUOTES, 'UTF-8')); ?></dd>
      <dt class="col-sm-3">Calendar</dt>
      <dd class="col-sm-9"><a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=calendar&action=ics&interview_id='.(int)$iv['id']); ?>">Download ICS</a></dd>
    </dl>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
