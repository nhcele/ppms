<?php
$title = 'Dashboard';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Dashboard</h1>
      <div class="text-muted">Insights and activity at a glance</div>
    </div>
    <div class="fw-medium">Welcome, <?php echo htmlspecialchars($user['full_name'] ?? $user['username'], ENT_QUOTES, 'UTF-8'); ?>!</div>
  </div>
</div>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Candidates by Agency (Top 8)</strong>
      </div>
      <div class="row small">
        <?php foreach (($agency ?? []) as $ag): ?>
        <div class="col-6 col-md-3 mb-2 d-flex justify-content-between">
          <span><?php echo htmlspecialchars($ag['agency'] ?? 'Unassigned', ENT_QUOTES, 'UTF-8'); ?></span>
          <span class="badge bg-secondary"><?php echo (int)$ag['cnt']; ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (empty($agency)): ?>
        <div class="col-12 text-muted">No data</div>
        <?php endif; ?>
      </div>
    </div></div>
  </div>
</div>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Pipeline</strong>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=candidates&action=list'); ?>">View</a>
      </div>
      <div class="row text-center small">
        <div class="col-6 mb-2">Basic: <a href="<?php echo base_url('index.php?page=candidates&action=list&status=basic_profile_created'); ?>" class="badge bg-secondary"><?php echo (int)($pipeline['basic_profile_created'] ?? 0); ?></a></div>
        <div class="col-6 mb-2">In Progress: <a href="<?php echo base_url('index.php?page=candidates&action=list&status=profile_in_progress'); ?>" class="badge bg-info"><?php echo (int)($pipeline['profile_in_progress'] ?? 0); ?></a></div>
        <div class="col-6 mb-2">Ready: <a href="<?php echo base_url('index.php?page=candidates&action=list&status=ready_for_selection'); ?>" class="badge bg-primary"><?php echo (int)($pipeline['ready_for_selection'] ?? 0); ?></a></div>
        <div class="col-6 mb-2">Shortlisted: <a href="<?php echo base_url('index.php?page=candidates&action=list&status=shortlisted'); ?>" class="badge bg-warning text-dark"><?php echo (int)($pipeline['shortlisted'] ?? 0); ?></a></div>
        <div class="col-6 mb-2">Selected: <a href="<?php echo base_url('index.php?page=candidates&action=list&status=selected'); ?>" class="badge bg-success"><?php echo (int)($pipeline['selected'] ?? 0); ?></a></div>
        <div class="col-6 mb-2">Deployed: <a href="<?php echo base_url('index.php?page=candidates&action=list&status=deployed'); ?>" class="badge bg-dark"><?php echo (int)($pipeline['deployed'] ?? 0); ?></a></div>
        <div class="col-12">Completed: <a href="<?php echo base_url('index.php?page=candidates&action=list&status=completed'); ?>" class="badge bg-light text-dark"><?php echo (int)($pipeline['completed'] ?? 0); ?></a></div>
      </div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Upcoming Starts (90 days)</strong>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=deployments&action=list'); ?>">View</a>
      </div>
      <ul class="list-group list-group-flush small">
        <?php foreach (($upcoming ?? []) as $u): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?php echo htmlspecialchars($u['first_name'].' '.$u['last_name'].' — '.$u['employer'], ENT_QUOTES, 'UTF-8'); ?></span>
          <span class="text-muted"><?php echo htmlspecialchars($u['start_date'], ENT_QUOTES, 'UTF-8'); ?></span>
        </li>
        <?php endforeach; ?>
        <?php if (empty($upcoming)): ?><li class="list-group-item text-muted">No upcoming starts</li><?php endif; ?>
      </ul>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Alerts</strong>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=alerts&action=list'); ?>">View</a>
      </div>
      <div class="display-6"><?php echo (int)($alertsCount ?? 0); ?></div>
      <div class="text-muted small">Undismissed compliance alerts</div>
      <div class="row small mt-2">
        <div class="col-4">Permit: <span class="badge bg-outline-dark border"><?php echo (int)($alerts30['work_permit_expiry'] ?? 0); ?></span></div>
        <div class="col-4">Contract: <span class="badge bg-outline-dark border"><?php echo (int)($alerts30['contract_expiry'] ?? 0); ?></span></div>
      </div>
      <hr>
      <strong>Salary Snapshot</strong>
      <ul class="list-group list-group-flush small">
        <?php foreach (($salary ?? []) as $s): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?php echo htmlspecialchars($s['employer'], ENT_QUOTES, 'UTF-8'); ?></span>
          <span><?php echo htmlspecialchars(number_format((float)$s['total'], 2).' '.$s['currency'], ENT_QUOTES, 'UTF-8'); ?></span>
        </li>
        <?php endforeach; ?>
        <?php if (empty($salary)): ?><li class="list-group-item text-muted">No data</li><?php endif; ?>
      </ul>
    </div></div>
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Work Permits Expiring (90 days)</strong>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=reports&action=expiring_list&type=work_permit_expiry&days=90'); ?>">View</a>
      </div>
      <ul class="list-group list-group-flush small">
        <?php foreach (($expiringPermits ?? []) as $p): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?php echo htmlspecialchars($p['first_name'].' '.$p['last_name'], ENT_QUOTES, 'UTF-8'); ?></span>
          <span class="text-muted"><?php echo htmlspecialchars($p['work_permit_expiry'], ENT_QUOTES, 'UTF-8'); ?></span>
        </li>
        <?php endforeach; ?>
        <?php if (empty($expiringPermits)): ?><li class="list-group-item text-muted">No permits expiring</li><?php endif; ?>
      </ul>
    </div></div>
  </div>
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Contracts Expiring (90 days)</strong>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=reports&action=expiring_list&type=contract_expiry&days=90'); ?>">View</a>
      </div>
      <ul class="list-group list-group-flush small">
        <?php foreach (($expiringContracts ?? []) as $c): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?php echo htmlspecialchars($c['first_name'].' '.$c['last_name'], ENT_QUOTES, 'UTF-8'); ?></span>
          <span class="text-muted"><?php echo htmlspecialchars($c['contract_expiry'], ENT_QUOTES, 'UTF-8'); ?></span>
        </li>
        <?php endforeach; ?>
        <?php if (empty($expiringContracts)): ?><li class="list-group-item text-muted">No contracts expiring</li><?php endif; ?>
      </ul>
    </div></div>
  </div>
</div>

<!-- Questionnaire Section -->
<div class="row g-3 mt-1">
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Questionnaire Overview</strong>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=questionnaire'); ?>">View</a>
      </div>
      <div class="row text-center small">
        <div class="col-6 mb-2">Total: <span class="badge bg-primary"><?php echo (int)($questionnaireStats['total'] ?? 0); ?></span></div>
        <div class="col-6 mb-2">Submitted: <span class="badge bg-success"><?php echo (int)($questionnaireStats['submitted'] ?? 0); ?></span></div>
        <div class="col-6 mb-2">In Progress: <span class="badge bg-warning text-dark"><?php echo (int)($questionnaireStats['in_progress'] ?? 0); ?></span></div>
        <div class="col-6 mb-2">Expired: <span class="badge bg-danger"><?php echo (int)($questionnaireStats['expired'] ?? 0); ?></span></div>
      </div>
      <div class="mt-3">
        <a href="<?php echo base_url('index.php?page=questionnaire&action=create'); ?>" class="btn btn-primary btn-sm w-100">Create New Questionnaire</a>
      </div>
    </div></div>
  </div>
  <div class="col-md-6">
    <div class="card h-100"><div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Recent Questionnaire Activity</strong>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=questionnaire-dashboard'); ?>">View</a>
      </div>
      <ul class="list-group list-group-flush small">
        <?php foreach (($recentQuestionnaireActivity ?? []) as $activity): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $activity['action_type'] ?? 'Action'))); ?> - <?php echo htmlspecialchars($activity['request_code'] ?? ''); ?></span>
          <span class="text-muted"><?php echo htmlspecialchars($activity['created_at'] ?? ''); ?></span>
        </li>
        <?php endforeach; ?>
        <?php if (empty($recentQuestionnaireActivity)): ?><li class="list-group-item text-muted">No recent activity</li><?php endif; ?>
      </ul>
    </div></div>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
