<?php
$title = 'Document Templates';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Document Templates</h1>
      <div class="text-muted small">Manage document templates for CV generation</div>
    </div>
    <div>
      <a class="btn btn-primary" href="<?php echo base_url('index.php?page=templates&action=create'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Create New Template
      </a>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-4">
  <div class="card-body">
    <form method="get" action="<?php echo base_url('index.php?page=templates'); ?>">
      <div class="row">
        <div class="col-md-4">
          <div class="mb-3">
            <label for="type" class="form-label">Template Type</label>
            <select class="form-select" id="type" name="type">
              <option value="">All Types</option>
              <option value="cv_lithuania" <?= $type === 'cv_lithuania' ? 'selected' : '' ?>>Lithuania CV</option>
              <option value="cv_turkey" <?= $type === 'cv_turkey' ? 'selected' : '' ?>>Turkey CV</option>
              <option value="cv_generic" <?= $type === 'cv_generic' ? 'selected' : '' ?>>Generic CV</option>
              <option value="kandidato_anketa" <?= $type === 'kandidato_anketa' ? 'selected' : '' ?>>Kandidato Anketa</option>
              <option value="other" <?= $type === 'other' ? 'selected' : '' ?>>Other</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category">
              <option value="">All Categories</option>
              <option value="CV" <?= $category === 'CV' ? 'selected' : '' ?>>CV</option>
              <option value="Forms" <?= $category === 'Forms' ? 'selected' : '' ?>>Forms</option>
              <option value="Documents" <?= $category === 'Documents' ? 'selected' : '' ?>>Documents</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?php echo base_url('index.php?page=templates'); ?>" class="btn btn-secondary">Clear</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Templates List -->
<div class="card">
  <div class="card-body">
    <?php if (empty($templates)): ?>
      <p class="text-muted">No templates found.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Template Name</th>
              <th>Type</th>
              <th>Category</th>
              <th>Created By</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($templates as $template): ?>
              <tr>
                <td><?= htmlspecialchars($template['template_name']) ?></td>
                <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $template['template_type']))) ?></td>
                <td><?= htmlspecialchars($template['template_category']) ?></td>
                <td><?= htmlspecialchars($template['created_by_user']) ?></td>
                <td>
                  <?php if ($template['is_active']): ?>
                    <span class="badge bg-success">Active</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Inactive</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($template['created_at']) ?></td>
                <td>
                  <a href="<?php echo base_url('index.php?page=templates&action=view&id=' . $template['id']); ?>" 
                     class="btn btn-sm btn-primary">View</a>
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
