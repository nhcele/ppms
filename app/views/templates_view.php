<?php
$title = 'Template: ' . htmlspecialchars($template['template_name']);
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Template: <?= htmlspecialchars($template['template_name']) ?></h1>
      <div class="text-muted small">View and manage template details</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=templates'); ?>" class="btn btn-secondary">Back to List</a>
      <a href="<?php echo base_url('index.php?page=templates&action=add-mapping&id=' . $template['id']); ?>" class="btn btn-primary">Add Field Mapping</a>
    </div>
  </div>
</div>

<!-- Template Details -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Template Details</h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <p><strong>Type:</strong> <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $template['template_type']))) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($template['template_category']) ?></p>
        <p><strong>Status:</strong> 
          <?php if ($template['is_active']): ?>
            <span class="badge bg-success">Active</span>
          <?php else: ?>
            <span class="badge bg-secondary">Inactive</span>
          <?php endif; ?>
        </p>
      </div>
      <div class="col-md-6">
        <p><strong>Created By:</strong> <?= htmlspecialchars($template['created_by_user']) ?></p>
        <p><strong>Created At:</strong> <?= htmlspecialchars($template['created_at']) ?></p>
        <p><strong>Last Updated:</strong> <?= htmlspecialchars($template['updated_at']) ?></p>
      </div>
    </div>
    
    <?php if ($template['description']): ?>
      <div class="mt-3">
        <strong>Description:</strong>
        <p><?= htmlspecialchars($template['description']) ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Field Mappings -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Field Mappings (<?= count($mappings) ?>)</h5>
  </div>
  <div class="card-body">
    <?php if (empty($mappings)): ?>
      <p class="text-muted">No field mappings configured yet.</p>
      <p><a href="<?php echo base_url('index.php?page=templates&action=add-mapping&id=' . $template['id']); ?>" class="btn btn-primary">Add First Field Mapping</a></p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Order</th>
              <th>Template Field</th>
              <th>Source</th>
              <th>Static Value</th>
              <th>Required</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($mappings as $mapping): ?>
              <tr>
                <td><?= htmlspecialchars($mapping['field_order']) ?></td>
                <td><?= htmlspecialchars($mapping['template_field_name']) ?></td>
                <td>
                  <span class="badge bg-<?= $mapping['source_type'] === 'static' ? 'info' : 'primary' ?>">
                    <?= htmlspecialchars(ucfirst($mapping['source_type'])) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars(substr($mapping['static_value'] ?? '', 0, 50)) ?><?= strlen($mapping['static_value'] ?? '') > 50 ? '...' : '' ?></td>
                <td>
                  <?php if ($mapping['is_required']): ?>
                    <span class="badge bg-danger">Required</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Optional</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="<?php echo base_url('index.php?page=templates&action=delete-mapping&id=' . $template['id'] . '&mapping_id=' . $mapping['id']); ?>" 
                     class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this mapping?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Generate Document Section -->
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Generate Document</h5>
  </div>
  <div class="card-body">
    <form method="get" action="<?php echo base_url('index.php?page=templates&action=generate-document'); ?>">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="candidate_id" class="form-label">Select Candidate</label>
            <select class="form-select" id="candidate_id" name="candidate_id" required>
              <option value="">-- Select Candidate --</option>
              <?php 
              $candidates = db()->query('SELECT id, CONCAT(first_name, " ", last_name) as name, candidate_code FROM candidates ORDER BY first_name, last_name')->fetchAll();
              foreach ($candidates as $candidate): ?>
                <option value="<?= $candidate['id'] ?>">
                  <?= htmlspecialchars($candidate['name']) ?> (<?= htmlspecialchars($candidate['candidate_code']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label>&nbsp;</label>
            <input type="hidden" name="template_id" value="<?= $template['id'] ?>">
            <button type="submit" class="btn btn-success">Generate Document</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
