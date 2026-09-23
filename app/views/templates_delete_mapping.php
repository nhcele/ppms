<?php
$title = 'Delete Field Mapping';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Delete Field Mapping</h1>
      <div class="text-muted small">Confirm deletion of field mapping</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=templates&action=view&id=' . $template_id); ?>" class="btn btn-secondary">Back to Template</a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="alert alert-warning">
      <strong>Warning:</strong> This will permanently delete this field mapping. This action cannot be undone.
    </div>
    
    <form method="post" action="<?php echo base_url('index.php?page=templates&action=delete-mapping&id=' . $template_id . '&mapping_id=' . $mapping_id); ?>">
      <?php echo csrf_field(); ?>
      
      <div class="mb-3">
        <button type="submit" class="btn btn-danger">Confirm Delete</button>
        <a href="<?php echo base_url('index.php?page=templates&action=view&id=' . $template_id); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
