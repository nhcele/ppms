<?php
$title = 'Manage Roles';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Roles</h1>
      <div class="text-muted small">Manage available job roles</div>
    </div>
    <div>
      <a class="btn btn-primary" href="<?php echo base_url('index.php?page=roles&action=create'); ?>">Add Role</a>
    </div>
  </div>
</div>
<div class="card h-100"><div class="card-body">
  <div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
      <thead>
        <tr><th>Name</th><th>Status</th><th>Updated</th><th class="text-end"></th></tr>
      </thead>
      <tbody>
        <?php foreach (($rows ?? []) as $r): ?>
        <tr>
          <td><?php echo htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo ((int)$r['is_active']===1) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Disabled</span>'; ?></td>
          <td><?php echo htmlspecialchars($r['updated_at'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=roles&action=edit&id='.(int)$r['id']); ?>">Edit</a>
            <a class="btn btn-sm btn-outline-danger" href="<?php echo base_url('index.php?page=roles&action=delete&id='.(int)$r['id']); ?>" onclick="return confirm('Delete this role?')">Delete</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="4" class="text-center text-muted">No roles</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div></div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
