<?php
$title = 'Interviews';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Interviews</h1>
  <div>
    <a class="btn btn-sm btn-primary" href="<?php echo base_url('index.php?page=interviews&action=create'); ?>">Schedule Interview</a>
  </div>
</div>
<div class="table-responsive">
  <table class="table table-sm table-striped">
    <thead><tr><th>Date/Time</th><th>Candidate</th><th>Employer</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach (($rows ?? []) as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['scheduled_at'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($r['employer'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($r['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=interviews&action=view&id='.(int)$r['id']); ?>">View</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?><tr><td colspan="5" class="text-center text-muted">No interviews found</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
