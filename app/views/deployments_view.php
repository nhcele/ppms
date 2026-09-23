<?php
$title = 'Deployment Details';
require_once __DIR__ . '/../lib/video_helper.php';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Deployment #<?php echo (int)$dep['id']; ?></h1>
  <div>
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=deployments&action=list'); ?>">Back</a>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">Candidate</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($dep['first_name'].' '.$dep['last_name'], ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Employer</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($dep['employer'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Agency</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($dep['agency'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Location</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars(($dep['location_country'] ?? '').', '.($dep['location_city'] ?? '').', '.($dep['location_site'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Dates</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars(($dep['start_date'] ?? '').' — '.($dep['end_date'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Status</dt>
      <dd class="col-sm-9"><span class="badge bg-secondary"><?php echo htmlspecialchars($dep['status'], ENT_QUOTES, 'UTF-8'); ?></span></dd>
      <dt class="col-sm-3">Salary</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars(($dep['salary_amount'] ?? '').' '.($dep['salary_currency'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Work Permit</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars(($dep['work_permit_status'] ?? 'not_applied').($dep['work_permit_expiry']? (' (exp: '.$dep['work_permit_expiry'].')') : ''), ENT_QUOTES, 'UTF-8'); ?></dd>
      <dt class="col-sm-3">Contract Expiry</dt>
      <dd class="col-sm-9"><?php echo htmlspecialchars($dep['contract_expiry'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
      
      <dt class="col-sm-3">Visa Reference</dt>
      <dd class="col-sm-9">
        <?php if (!empty($dep['visa_reference_file'])): ?>
          <?php
          $visa_ext = strtolower(pathinfo($dep['visa_reference_file'], PATHINFO_EXTENSION));
          $visa_filename = basename($dep['visa_reference_file']);
          $is_visa_video = is_video_file($visa_filename);
          // Build a normalized URL for direct-open fallback
          $visa_path_web = str_replace('\\', '/', (string)$dep['visa_reference_file']);
          $uploads_pos = stripos($visa_path_web, '/uploads/');
          if ($uploads_pos !== false) {
              $visa_path_web = ltrim(substr($visa_path_web, $uploads_pos + 1), '/');
          } else {
              $visa_path_web = ltrim($visa_path_web, '/');
          }
          // If the deployments path is missing the numeric candidate folder, inject it
          if (preg_match('~^uploads/deployments/(\d+)/~i', $visa_path_web) !== 1) {
              $visa_path_web = 'uploads/deployments/' . ((int)$dep['candidate_id']) . '/' . basename($visa_path_web);
          }
          $visa_url = base_url($visa_path_web);
          ?>
          <div class="d-flex align-items-center">
            <?php if ($is_visa_video): ?>
              <?php $thumbnailUrl = get_video_thumbnail_url($dep['visa_reference_file']); ?>
              <img src="<?php echo htmlspecialchars($thumbnailUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="me-2 rounded"
                   style="width: 50px; height: 38px; object-fit: cover;"
                   alt="Video thumbnail">
            <?php endif; ?>
            <div>
              <button class="btn btn-sm btn-outline-primary" onclick="viewDeploymentDocument('<?php echo htmlspecialchars($visa_url, ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($visa_filename, ENT_QUOTES, 'UTF-8'); ?>', 'Visa Reference')">
                <i class="fas fa-<?php echo $is_visa_video ? 'play' : 'eye'; ?>"></i>
                <?php echo $is_visa_video ? 'Play' : 'View'; ?> Visa Reference
                <?php if ($visa_ext === 'pdf'): ?>
                  <span class="badge bg-danger ms-1">PDF</span>
                <?php elseif ($is_visa_video): ?>
                  <span class="badge bg-success ms-1">VIDEO</span>
                <?php else: ?>
                  <span class="badge bg-info ms-1">IMAGE</span>
                <?php endif; ?>
              </button>
              <?php if ($is_visa_video): ?>
                <?php
                $duration = get_video_duration($dep['visa_reference_file']);
                if ($duration > 0): ?>
                  <br><small class="text-muted">Duration: <?php echo format_video_duration($duration); ?></small>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
        <?php else: ?>
          <span class="text-muted">Not uploaded</span>
        <?php endif; ?>
      </dd>
      
      <dt class="col-sm-3">Signed Contract</dt>
      <dd class="col-sm-9">
        <?php if (!empty($dep['signed_contract_file'])): ?>
          <?php
          $contract_ext = strtolower(pathinfo($dep['signed_contract_file'], PATHINFO_EXTENSION));
          $contract_filename = basename($dep['signed_contract_file']);
          $is_contract_video = is_video_file($contract_filename);
          // Build a normalized URL for direct-open fallback
          $contract_path_web = str_replace('\\', '/', (string)$dep['signed_contract_file']);
          $uploads_pos2 = stripos($contract_path_web, '/uploads/');
          if ($uploads_pos2 !== false) {
              $contract_path_web = ltrim(substr($contract_path_web, $uploads_pos2 + 1), '/');
          } else {
              $contract_path_web = ltrim($contract_path_web, '/');
          }
          // If the deployments path is missing the numeric candidate folder, inject it
          if (preg_match('~^uploads/deployments/(\d+)/~i', $contract_path_web) !== 1) {
              $contract_path_web = 'uploads/deployments/' . ((int)$dep['candidate_id']) . '/' . basename($contract_path_web);
          }
          $contract_url = base_url($contract_path_web);
          ?>
          <div class="d-flex align-items-center">
            <?php if ($is_contract_video): ?>
              <?php $thumbnailUrl = get_video_thumbnail_url($dep['signed_contract_file']); ?>
              <img src="<?php echo htmlspecialchars($thumbnailUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="me-2 rounded"
                   style="width: 50px; height: 38px; object-fit: cover;"
                   alt="Video thumbnail">
            <?php endif; ?>
            <div>
              <button class="btn btn-sm btn-outline-primary" onclick="viewDeploymentDocument('<?php echo htmlspecialchars($contract_url, ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($contract_filename, ENT_QUOTES, 'UTF-8'); ?>', 'Signed Contract')">
                <i class="fas fa-<?php echo $is_contract_video ? 'play' : 'eye'; ?>"></i>
                <?php echo $is_contract_video ? 'Play' : 'View'; ?> Signed Contract
                <?php if ($contract_ext === 'pdf'): ?>
                  <span class="badge bg-danger ms-1">PDF</span>
                <?php elseif ($is_contract_video): ?>
                  <span class="badge bg-success ms-1">VIDEO</span>
                <?php else: ?>
                  <span class="badge bg-info ms-1">IMAGE</span>
                <?php endif; ?>
              </button>
              <?php if ($is_contract_video): ?>
                <?php
                $duration = get_video_duration($dep['signed_contract_file']);
                if ($duration > 0): ?>
                  <br><small class="text-muted">Duration: <?php echo format_video_duration($duration); ?></small>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
        <?php else: ?>
          <span class="text-muted">Not uploaded</span>
        <?php endif; ?>
      </dd>
    </dl>
  </div>
</div>

<!-- Document Viewer Modal -->
<div class="modal fade" id="deploymentDocumentModal" tabindex="-1" aria-labelledby="deploymentDocumentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deploymentDocumentModalLabel">Document Viewer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="deploymentDocumentContent" style="height: 80vh; overflow: auto;">
          <!-- Document content will be loaded here -->
        </div>
      </div>
      <div class="modal-footer">
        <a id="deploymentDownloadLink" class="btn btn-primary" href="#" download>
          <i class="fas fa-download"></i> Download
        </a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function viewDeploymentDocument(filePath, originalName, docType) {
    // Create the serve URL for this document
    // Normalize path separators first
    const raw = String(filePath || '').trim();
    const unixy = raw.replace(/\\/g, '/');
    let serveUrl = '';

    // If already absolute URL, use as-is
    if (/^https?:\/\//i.test(unixy)) {
        serveUrl = unixy;
    } else {
        // Try to locate '/uploads/...' portion even if a full filesystem path was stored
        const ix = unixy.toLowerCase().indexOf('/uploads/');
        let rel = ix >= 0 ? unixy.substring(ix + 1) : unixy.replace(/^\/+/, '');
        // Ensure we don't double-prefix slashes
        serveUrl = `<?php echo base_url(); ?>/${rel}`;
    }
    // Note: Avoid adding cache-busting query parameters because some server setups may rewrite such requests.
    
    // Set modal title
    document.getElementById('deploymentDocumentModalLabel').textContent = `${docType}: ${originalName}`;
    
    // Set download link
    const downloadLink = document.getElementById('deploymentDownloadLink');
    downloadLink.href = serveUrl;
    downloadLink.download = originalName;
    
    // Get file extension to determine how to display
    const fileExtension = String(originalName).split('.').pop().toLowerCase();
    const contentDiv = document.getElementById('deploymentDocumentContent');
    
    // Clear previous content
    contentDiv.innerHTML = '';
    
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
        // Display image
        contentDiv.innerHTML = `
            <div class="text-center p-3">
                <img id="docImage" src="${serveUrl}" class="img-fluid" style="max-height: 70vh; object-fit: contain;" alt="${originalName}">
                <div class="mt-2"><a href="${serveUrl}" target="_blank">Open image in new tab</a></div>
            </div>
        `;
        const imgEl = document.getElementById('docImage');
        imgEl.onerror = function() {
            contentDiv.innerHTML = `
                <div class="text-center p-5">
                    <i class="fas fa-image fa-5x text-muted mb-3"></i>
                    <h5>Could not load image</h5>
                    <p class="text-muted">You can still view it directly:</p>
                    <a href="${serveUrl}" target="_blank" class="btn btn-primary">Open Image</a>
                </div>
            `;
        };
    } else if (fileExtension === 'pdf') {
        // Display PDF using <object> with fallback
        const pdfUrl = serveUrl + (serveUrl.includes('#') ? '' : '#toolbar=1');
        contentDiv.innerHTML = `
            <div class="p-0">
                <object id="pdfObject" data="${pdfUrl}" type="application/pdf" width="100%" height="100%" style="min-height: 70vh;">
                    <div class="text-center p-5">
                        <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                        <h5>PDF preview not available</h5>
                        <p class="text-muted">Your browser may not support inline PDF viewing.</p>
                        <a href="${serveUrl}" target="_blank" class="btn btn-primary">Open PDF in new tab</a>
                    </div>
                </object>
                <div class="text-center py-2">
                    <a href="${serveUrl}" target="_blank" class="btn btn-sm btn-link">Open PDF in new tab</a>
                </div>
            </div>
        `;
    } else if (['mp4', 'mov', 'avi', 'wmv', 'webm', 'mkv', 'flv', '3gp'].includes(fileExtension)) {
        // Display video with enhanced controls
        const videoType = fileExtension === 'mov' ? 'quicktime' : fileExtension;
        contentDiv.innerHTML = `
            <div class="text-center p-3">
                <video id="docVideo" controls preload="metadata" style="max-width: 100%; max-height: 70vh; background: #000;" controlsList="nodownload">
                    <source src="${serveUrl}" type="video/${videoType}">
                </video>
                <div class="mt-2">
                    <small class="text-muted">Video: ${originalName}</small>
                    <div><a href="${serveUrl}" target="_blank" class="btn btn-sm btn-link">Open video in new tab</a></div>
                </div>
            </div>
        `;
        const vidEl = document.getElementById('docVideo');
        vidEl.onerror = function() {
            contentDiv.innerHTML = `
                <div class="text-center p-5">
                    <i class="fas fa-video fa-5x text-muted mb-3"></i>
                    <h5>Could not load video</h5>
                    <a href="${serveUrl}" target="_blank" class="btn btn-primary">Open Video in new tab</a>
                </div>
            `;
        };
    } else if (['doc', 'docx', 'txt', 'rtf'].includes(fileExtension)) {
        // For document files, show a preview message and download option
        contentDiv.innerHTML = `
            <div class="text-center p-5">
                <i class="fas fa-file-alt fa-5x text-muted mb-3"></i>
                <h5>Document Preview Not Available</h5>
                <p class="text-muted">This document type cannot be previewed in the browser.</p>
                <p>File: <strong>${originalName}</strong></p>
                <p>Type: <strong>${docType}</strong></p>
                <a href="${serveUrl}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Open in New Tab
                </a>
            </div>
        `;
    } else {
        // Unknown file type
        contentDiv.innerHTML = `
            <div class="text-center p-5">
                <i class="fas fa-file fa-5x text-muted mb-3"></i>
                <h5>Preview Not Available</h5>
                <p class="text-muted">This file type cannot be previewed.</p>
                <p>File: <strong>${originalName}</strong></p>
                <p>Type: <strong>${docType}</strong></p>
                <a href="${serveUrl}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download"></i> Download File
                </a>
            </div>
        `;
    }
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('deploymentDocumentModal'));
    modal.show();
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
