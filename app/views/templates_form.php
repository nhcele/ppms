<?php
$title = $mode === 'create' ? 'Create New Template' : 'Edit Template';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1"><?= $mode === 'create' ? 'Create New Template' : 'Edit Template' ?></h1>
      <div class="text-muted small">Manage document templates</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=templates'); ?>" class="btn btn-secondary">Back to List</a>
    </div>
  </div>
</div>

<?php if (!empty($errors['general'])): ?>
<div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo base_url('index.php?page=templates&action=' . $mode); ?>">
      <?php echo csrf_field(); ?>
      
      <div class="mb-3">
        <label for="template_name" class="form-label">Template Name *</label>
        <input type="text" class="form-control" id="template_name" name="template_name" 
               value="<?= htmlspecialchars($data['template_name']) ?>" required>
        <?php if (!empty($errors['template_name'])): ?>
          <div class="text-danger"><?= htmlspecialchars($errors['template_name']) ?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <label for="template_type" class="form-label">Template Type *</label>
        <select class="form-select" id="template_type" name="template_type" required>
          <option value="">-- Select Type --</option>
          <option value="cv_lithuania" <?= $data['template_type'] === 'cv_lithuania' ? 'selected' : '' ?>>Lithuania CV</option>
          <option value="cv_turkey" <?= $data['template_type'] === 'cv_turkey' ? 'selected' : '' ?>>Turkey CV</option>
          <option value="cv_generic" <?= $data['template_type'] === 'cv_generic' ? 'selected' : '' ?>>Generic CV</option>
          <option value="kandidato_anketa" <?= $data['template_type'] === 'kandidato_anketa' ? 'selected' : '' ?>>Kandidato Anketa</option>
          <option value="other" <?= $data['template_type'] === 'other' ? 'selected' : '' ?>>Other</option>
        </select>
        <?php if (!empty($errors['template_type'])): ?>
          <div class="text-danger"><?= htmlspecialchars($errors['template_type']) ?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <label for="template_category" class="form-label">Template Category *</label>
        <select class="form-select" id="template_category" name="template_category" required>
          <option value="">-- Select Category --</option>
          <option value="CV" <?= $data['template_category'] === 'CV' ? 'selected' : '' ?>>CV</option>
          <option value="Forms" <?= $data['template_category'] === 'Forms' ? 'selected' : '' ?>>Forms</option>
          <option value="Documents" <?= $data['template_category'] === 'Documents' ? 'selected' : '' ?>>Documents</option>
        </select>
        <?php if (!empty($errors['template_category'])): ?>
          <div class="text-danger"><?= htmlspecialchars($errors['template_category']) ?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($data['description']) ?></textarea>
      </div>
      
      <div class="mb-3">
        <button type="submit" class="btn btn-primary"><?= $mode === 'create' ? 'Create Template' : 'Update Template' ?></button>
        <a href="<?php echo base_url('index.php?page=templates'); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
