<?php
$title = 'Compliance Alerts';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Compliance Alerts</h1>
</div>
<div class="table-responsive">
  <table class="table table-sm table-striped">
    <thead><tr><th>Date</th><th>Type</th><th>Candidate</th><th>Due in (days)</th><th></th></tr></thead>
    <tbody>
      <?php foreach (($rows ?? []) as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['alert_date'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($r['alert_type'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo (int)$r['due_in_days']; ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=candidates&action=view&id='.(int)$r['candidate_id']); ?>">View Candidate</a>
          <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=alerts&action=dismiss&id='.(int)$r['id']); ?>">Dismiss</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?><tr><td colspan="5" class="text-center text-muted">No alerts</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
