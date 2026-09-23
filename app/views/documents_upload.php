<?php
$title = 'Upload Document';
ob_start();
?>
<div class="card">
  <div class="card-header">
    <h1 class="h5 mb-0">Upload Document for <?php echo htmlspecialchars($cand['first_name'] . ' ' . $cand['last_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
  </div>
  <div class="card-body">
    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
            <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('index.php?page=documents&action=upload&candidate_id='.(int)$candidate_id); ?>">
      <?php echo csrf_field(); ?>
      
      <?php if (!empty($preselect) && $preselect !== 'other'): ?>
        <!-- Fixed document type -->
        <div class="mb-3">
          <label class="form-label">Document Type</label>
          <input type="hidden" name="doc_type" value="<?php echo htmlspecialchars($preselect, ENT_QUOTES, 'UTF-8'); ?>">
          <div class="form-control-plaintext">
            <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $preselect)), ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
        </div>
      <?php else: ?>
        <!-- Dropdown for other document types -->
        <div class="mb-3">
          <label class="form-label">Document Type</label>
          <select class="form-select" name="doc_type" required>
            <option value="">-- Select Document Type --</option>
            <?php foreach ($document_types as $type): ?>
              <?php $sel = (!empty($preselect) && $preselect === $type) ? 'selected' : ''; ?>
              <option value="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $sel; ?>>
                <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $type)), ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>
      
      <div class="mb-3">
        <label class="form-label">File<?php echo ($preselect === 'certificate') ? 's' : ''; ?> (Max 10MB each)</label>
        <?php if ($preselect === 'certificate'): ?>
          <!-- Multiple file upload for certificates -->
          <div id="certificate-files">
            <div class="file-input-group mb-2">
              <div class="input-group">
                <input class="form-control" type="file" name="files[]" required>
                <button type="button" class="btn btn-outline-success" onclick="addCertificateFile()" title="Add another certificate">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="form-text">Supported formats: PDF, DOC/DOCX, JPG/PNG. You can upload multiple certificates.</div>
        <?php else: ?>
          <!-- Single file upload for other document types -->
          <input class="form-control" type="file" name="file" required>
          <div class="form-text">Supported formats: PDF, DOC/DOCX, JPG/PNG, MP4/MOV</div>
        <?php endif; ?>
      </div>
      
      <div class="d-flex justify-content-between align-items-center">
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-upload me-1"></i> Upload
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo base_url('index.php?page=candidates&action=view&id='.(int)$candidate_id); ?>">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<?php if ($preselect === 'certificate'): ?>
<script>
function addCertificateFile() {
    const container = document.getElementById('certificate-files');
    const newFileGroup = document.createElement('div');
    newFileGroup.className = 'file-input-group mb-2';
    newFileGroup.innerHTML = `
        <div class="input-group">
            <input class="form-control" type="file" name="files[]">
            <button type="button" class="btn btn-outline-danger" onclick="removeCertificateFile(this)" title="Remove this certificate">
                <i class="bi bi-dash"></i>
            </button>
        </div>
    `;
    container.appendChild(newFileGroup);
}

function removeCertificateFile(button) {
    const fileGroup = button.closest('.file-input-group');
    const container = document.getElementById('certificate-files');
    
    // Don't remove if it's the last file input
    if (container.children.length > 1) {
        fileGroup.remove();
    }
}
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
