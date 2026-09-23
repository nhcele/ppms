<?php
$title = ($mode === 'edit' ? 'Edit Role' : 'Add Role');
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
      <div class="text-muted small"><?php echo $mode==='edit' ? 'Update existing role' : 'Create a new role'; ?></div>
    </div>
    <div>
      <a class="btn btn-outline-secondary" href="<?php echo base_url('index.php?page=roles&action=list'); ?>">Back to Roles</a>
    </div>
  </div>
</div>
<div class="card"><div class="card-body">
  <form method="post" action="">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
      <label class="form-label">Role Name</label>
      <input class="form-control <?php echo isset($errors['name'])?'is-invalid':''; ?>" type="text" name="name" value="<?php echo htmlspecialchars($data['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
      <?php if(isset($errors['name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo !empty($data['is_active']) ? 'checked' : ''; ?>>
      <label class="form-check-label" for="is_active">Active</label>
    </div>
    <button class="btn btn-primary" type="submit"><?php echo $mode==='edit'?'Save Changes':'Create Role'; ?></button>
  </form>
</div></div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
