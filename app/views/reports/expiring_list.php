<?php
$title = 'Expiring ' . htmlspecialchars(str_replace('_', ' ', $type), ENT_QUOTES, 'UTF-8');
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0"><?php echo $title; ?> (next <?php echo (int)$days; ?> days)</h1>
  <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=dashboard'); ?>">Back to Dashboard</a>
</div>
<div class="table-responsive">
  <table class="table table-sm table-striped">
    <thead>
      <tr>
        <th>Expiry Date</th>
        <th>Candidate</th>
        <th>Code</th>
        <th>Agency</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (($rows ?? []) as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['expiry_date'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($r['candidate_code'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($r['agency'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=candidates&action=view&id='.(int)$r['candidate_id']); ?>">View Candidate</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?>
      <tr><td colspan="5" class="text-center text-muted">No results</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; 