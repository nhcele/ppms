<?php
$title = 'Questionnaire Dashboard';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Questionnaire Dashboard</h1>
      <div class="text-muted small">Overview of questionnaire system</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=questionnaire&action=create'); ?>" class="btn btn-primary">Create New Questionnaire</a>
      <a href="<?php echo base_url('index.php?page=questionnaire'); ?>" class="btn btn-secondary">View All Requests</a>
      <a href="<?php echo base_url('index.php?page=templates'); ?>" class="btn btn-info">Manage Templates</a>
    </div>
  </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card bg-primary text-white">
      <div class="card-body">
        <h5 class="card-title">Total Requests</h5>
        <h2 class="card-text"><?= $summary['total'] ?? 0 ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-success text-white">
      <div class="card-body">
        <h5 class="card-title">Submitted</h5>
        <h2 class="card-text"><?= $summary['submitted'] ?? 0 ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-warning text-white">
      <div class="card-body">
        <h5 class="card-title">In Progress</h5>
        <h2 class="card-text"><?= $summary['in_progress'] ?? 0 ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card bg-danger text-white">
      <div class="card-body">
        <h5 class="card-title">Expired</h5>
        <h2 class="card-text"><?= $summary['expired'] ?? 0 ?></h2>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Quick Actions</h5>
  </div>
  <div class="card-body">
    <a href="<?php echo base_url('index.php?page=questionnaire&action=create'); ?>" class="btn btn-primary">Create New Questionnaire</a>
    <a href="<?php echo base_url('index.php?page=questionnaire'); ?>" class="btn btn-secondary">View All Requests</a>
    <a href="<?php echo base_url('index.php?page=templates'); ?>" class="btn btn-info">Manage Templates</a>
  </div>
</div>

<!-- Recent Activity -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Recent Activity</h5>
  </div>
  <div class="card-body">
    <?php if (empty($recent_activity)): ?>
      <p class="text-muted">No recent activity.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Action</th>
              <th>Request Code</th>
              <th>Candidate</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_activity as $activity): ?>
              <tr>
                <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $activity['action_type']))) ?></td>
                <td><?= htmlspecialchars($activity['request_code']) ?></td>
                <td><?= htmlspecialchars($activity['candidate_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($activity['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Pending Actions -->
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Pending Actions</h5>
  </div>
  <div class="card-body">
    <?php if (empty($pending_actions)): ?>
      <p class="text-muted">No pending actions.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Request Code</th>
              <th>Candidate</th>
              <th>Status</th>
              <th>Action Required</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pending_actions as $action): ?>
              <tr>
                <td><?= htmlspecialchars($action['request_code']) ?></td>
                <td><?= htmlspecialchars($action['candidate_name'] ?? 'N/A') ?></td>
                <td>
                  <span class="badge bg-<?= get_status_badge_class($action['status']) ?>">
                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $action['status']))) ?>
                  </span>
                </td>
                <td>
                  <?php if ($action['status'] === 'submitted'): ?>
                    <a href="<?php echo base_url('index.php?page=questionnaire&action=view&id=' . $action['id']); ?>" class="btn btn-sm btn-primary">Review</a>
                  <?php elseif ($action['status'] === 'link_created'): ?>
                    <a href="<?php echo base_url('index.php?page=questionnaire&action=send-link&id=' . $action['id']); ?>" class="btn btn-sm btn-info">Send Link</a>
                  <?php elseif (strtotime($action['expiry_time']) < time()): ?>
                    <a href="<?php echo base_url('index.php?page=questionnaire&action=regenerate-link&id=' . $action['id']); ?>" class="btn btn-sm btn-warning">Regenerate</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
