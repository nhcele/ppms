<?php
$title = 'Candidate Profile';
require_once __DIR__ . '/../lib/video_helper.php';
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">
    <?php echo htmlspecialchars($cand['candidate_code'] . ' — ' . $cand['first_name'] . ' ' . $cand['last_name'], ENT_QUOTES, 'UTF-8'); ?>
    <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($cand['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
  </h1>
  <div>
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=candidates&action=list'); ?>">Back</a>
    <a class="btn btn-sm btn-info" href="<?php echo base_url('index.php?page=questionnaire&action=create&candidate_id=' . (int)$cand['id']); ?>">
      <i class="fas fa-envelope"></i> Send Questionnaire
    </a>
    <a class="btn btn-sm btn-success" href="<?php echo base_url('index.php?page=candidates&action=download-cv&id=' . (int)$cand['id']); ?>">
      <i class="fas fa-download"></i> Download Generic CV
    </a>
    <a class="btn btn-sm btn-primary" href="<?php echo base_url('index.php?page=candidates&action=edit&id=' . (int)$cand['id']); ?>">Edit</a>
    <?php if (($cand['status'] ?? '') !== 'completed'): ?>
      <form method="post" action="<?php echo base_url('index.php?page=candidates&action=advance-stage&id=' . (int)$cand['id']); ?>" class="d-inline" onsubmit="return confirm('Advance this candidate to the next recruitment stage?');">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm btn-warning">
          <i class="fas fa-arrow-right"></i> Move to Next Stage
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

<!-- Destination-specific CV Generation -->
<div class="card mt-3 mb-3">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h2 class="h6 mb-0">Generate Destination CV</h2>
  </div>
  <div class="card-body">
    <form method="get" action="<?php echo base_url('index.php?page=candidates&action=generate-destination-cv'); ?>" class="row g-3 align-items-end">
      <input type="hidden" name="page" value="candidates">
      <input type="hidden" name="action" value="generate-destination-cv">
      <input type="hidden" name="id" value="<?php echo (int)$cand['id']; ?>">
      <div class="col-md-4">
        <label for="destination" class="form-label">Select Destination</label>
        <?php $selectedDestination = strtolower($cand['recruitment_destination'] ?? 'generic'); ?>
        <select class="form-select" id="destination" name="destination" required>
          <option value="">-- Choose Destination --</option>
          <option value="lithuania" <?= $selectedDestination === 'lithuania' ? 'selected' : '' ?>>Lithuania</option>
          <option value="turkey" <?= $selectedDestination === 'turkey' ? 'selected' : '' ?>>Turkey</option>
          <option value="generic" <?= (!in_array($selectedDestination, ['lithuania', 'turkey'])) ? 'selected' : '' ?>>Generic / Other</option>
        </select>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-success w-100">
          <i class="fas fa-file-pdf"></i> Generate Destination CV
        </button>
      </div>
      <div class="col-md-4">
        <a class="btn btn-outline-primary w-100" href="<?php echo base_url('index.php?page=templates'); ?>">
          Manage Templates
        </a>
      </div>
    </form>
    <div class="form-text text-muted mt-2">
      This candidate can apply to multiple agencies/destinations. Select a destination to generate the appropriate CV format using the candidate's profile and questionnaire data.
    </div>
  </div>
</div>
<?php if (!empty($roles)): ?>
<div class="mb-3">
  <?php foreach ($roles as $r): ?>
    <span class="badge bg-outline-dark border me-1"><?php echo htmlspecialchars($r, ENT_QUOTES, 'UTF-8'); ?></span>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="progress mb-4">
  <div class="progress-bar" role="progressbar" style="width: <?php echo (int)($cand['progress_percent'] ?? 0); ?>%;" 
       aria-valuenow="<?php echo (int)($cand['progress_percent'] ?? 0); ?>" aria-valuemin="0" aria-valuemax="100">
    <?php echo (int)($cand['progress_percent'] ?? 0); ?>%
  </div>
</div>

<div class="row g-3">
  <!-- Personal Information -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-light">
        <h2 class="h6 mb-0">Personal Information</h2>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-5">Full Name</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['first_name'] . ' ' . $cand['last_name'], ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Date of Birth</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['dob'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Place of Birth</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['place_of_birth'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Roles</dt>
          <dd class="col-sm-7"><?php echo !empty($roles) ? htmlspecialchars(implode(', ', $roles), ENT_QUOTES, 'UTF-8') : 'Not specified'; ?></dd>
          
          <dt class="col-sm-5">Gender</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['gender'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Marital Status</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['marital_status'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Children</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['num_children'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Nationality</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['nationality'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Religion</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['religion'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Height/Weight</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars(($cand['height_cm'] ?? '') . 'cm / ' . ($cand['weight_kg'] ?? '') . 'kg', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Smoker</dt>
          <dd class="col-sm-7"><?php echo ($cand['is_smoker'] ?? 0) ? 'Yes' : 'No'; ?></dd>
          
          <dt class="col-sm-5">Driver's License</dt>
          <dd class="col-sm-7"><?php echo ($cand['has_drivers_license'] ?? 0) ? 'Yes (' . htmlspecialchars($cand['drivers_license_number'] ?? '', ENT_QUOTES, 'UTF-8') . ')' : 'No'; ?></dd>
          
          <dt class="col-sm-5">Computer Skills</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['computer_skills_level'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
        </dl>
      </div>
    </div>
  </div>
  
  <!-- Contact Information -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-light">
        <h2 class="h6 mb-0">Contact Information</h2>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-5">Email</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['email'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Phone</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['phone'] ?? 'Not specified', ENT_QUOTES, 'UTF-8'); ?></dd>
          
          <dt class="col-sm-5">Address</dt>
          <dd class="col-sm-7">
            <?php echo htmlspecialchars(implode(', ', array_filter([
              $cand['address_line1'] ?? '',
              $cand['address_line2'] ?? '',
              $cand['address_city'] ?? '',
              $cand['address_state'] ?? '',
              $cand['address_postal_code'] ?? '',
              $cand['address_country'] ?? ''
            ])), ENT_QUOTES, 'UTF-8'); ?>
          </dd>
          
          <dt class="col-sm-5">Emergency Contact</dt>
          <dd class="col-sm-7">
            <?php if (!empty($cand['emergency_contact_name'])): ?>
              <?php echo htmlspecialchars($cand['emergency_contact_name'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($cand['emergency_contact_relation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>)<br>
              <?php echo htmlspecialchars($cand['emergency_contact_phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
              <?php echo htmlspecialchars($cand['emergency_contact_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            <?php else: ?>
              Not specified
            <?php endif; ?>
          </dd>
        </dl>
      </div>
    </div>
  </div>
  
  <!-- Documents & Status -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-0">Documents & Status</h2>
        <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=documents&action=upload&candidate_id=' . (int)$cand['id'] . '&doc_type=photo'); ?>">
          <i class="fas fa-camera"></i> Upload Photo
        </a>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-5">Passport</dt>
          <dd class="col-sm-7">
            <?php echo htmlspecialchars(($cand['passport_number'] ?? 'Not specified') . ' (valid: ' . ($cand['passport_validity'] ?? '') . ')', ENT_QUOTES, 'UTF-8'); ?>
          </dd>
          
          <dt class="col-sm-5">Agency</dt>
          <dd class="col-sm-7"><?php echo htmlspecialchars($cand['agency_name'] ?? 'Not assigned', ENT_QUOTES, 'UTF-8'); ?></dd>
        </dl>
      </div>
    </div>
  </div>
</div>

<!-- Work Experience -->
<div class="card mt-3">
  <div class="card-header bg-light">
    <h2 class="h6 mb-0">Work Experience</h2>
  </div>
  <div class="card-body">
    <?php if (!empty($work_experience)): ?>
      <?php foreach ($work_experience as $exp): ?>
        <div class="border rounded p-3 mb-3">
          <div class="row">
            <div class="col-md-8">
              <h6 class="mb-1"><?php echo htmlspecialchars($exp['position'] ?? 'Position not specified', ENT_QUOTES, 'UTF-8'); ?></h6>
              <p class="mb-1 text-primary"><?php echo htmlspecialchars($exp['company'] ?? 'Company not specified', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="col-md-4 text-md-end">
              <small class="text-muted">
                <?php
                $start = !empty($exp['start_date']) ? date('M Y', strtotime($exp['start_date'])) : '';
                $end = !empty($exp['end_date']) ? date('M Y', strtotime($exp['end_date'])) : 'Present';
                echo htmlspecialchars($start . ($start ? ' - ' . $end : ''), ENT_QUOTES, 'UTF-8');
                ?>
              </small>
            </div>
          </div>
          <?php if (!empty($exp['description'])): ?>
            <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($exp['description'], ENT_QUOTES, 'UTF-8')); ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <em class="text-muted">No work experience provided</em>
    <?php endif; ?>
  </div>
</div>

<!-- Academic Qualifications -->
<div class="card mt-3">
  <div class="card-header bg-light">
    <h2 class="h6 mb-0">Academic Qualifications</h2>
  </div>
  <div class="card-body">
    <?php if (!empty($academic_qualifications)): ?>
      <?php foreach ($academic_qualifications as $qual): ?>
        <div class="border rounded p-3 mb-3">
          <div class="row">
            <div class="col-md-8">
              <h6 class="mb-1"><?php echo htmlspecialchars($qual['degree'] ?? 'Degree not specified', ENT_QUOTES, 'UTF-8'); ?></h6>
              <p class="mb-1 text-primary"><?php echo htmlspecialchars($qual['institution'] ?? 'Institution not specified', ENT_QUOTES, 'UTF-8'); ?></p>
              <?php if (!empty($qual['field_of_study'])): ?>
                <p class="mb-1"><small class="text-muted">Field of Study: <?php echo htmlspecialchars($qual['field_of_study'], ENT_QUOTES, 'UTF-8'); ?></small></p>
              <?php endif; ?>
            </div>
            <div class="col-md-4 text-md-end">
              <?php if (!empty($qual['graduation_year'])): ?>
                <small class="text-muted">Graduated: <?php echo htmlspecialchars($qual['graduation_year'], ENT_QUOTES, 'UTF-8'); ?></small><br>
              <?php endif; ?>
              <?php if (!empty($qual['grade'])): ?>
                <small class="text-muted">Grade: <?php echo htmlspecialchars($qual['grade'], ENT_QUOTES, 'UTF-8'); ?></small>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <em class="text-muted">No academic qualifications provided</em>
    <?php endif; ?>
  </div>
</div>

<!-- Personal Statement -->
<div class="card mt-3">
  <div class="card-header bg-light">
    <h2 class="h6 mb-0">Personal Statement</h2>
  </div>
  <div class="card-body">
    <?php echo !empty($cand['personal_statement']) ? nl2br(htmlspecialchars($cand['personal_statement'], ENT_QUOTES, 'UTF-8')) : '<em class="text-muted">No statement provided</em>'; ?>
  </div>
</div>

<!-- Documents Section -->
<div class="card mt-3">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h2 class="h6 mb-0">Documents</h2>
    <div class="btn-group">
      <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=documents&action=upload&candidate_id=' . (int)$cand['id'] . '&doc_type=cv'); ?>">Add CV</a>
      <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=documents&action=upload&candidate_id=' . (int)$cand['id'] . '&doc_type=video'); ?>">Add Video</a>
      <a class="btn btn-sm btn-outline-primary" href="<?php echo base_url('index.php?page=documents&action=upload&candidate_id=' . (int)$cand['id'] . '&doc_type=certificate'); ?>">Add Certificate</a>
      <a class="btn btn-sm btn-outline-secondary" href="<?php echo base_url('index.php?page=documents&action=upload&candidate_id=' . (int)$cand['id']); ?>">Add Other</a>
    </div>
  </div>
  <div class="card-body">
    <?php if (!empty($documents)): ?>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Type</th>
              <th>Name</th>
              <th>Uploaded</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($documents as $d): ?>
              <tr>
                <td>
                  <?php echo htmlspecialchars($d['doc_type'], ENT_QUOTES, 'UTF-8'); ?>
                  <?php if (is_video_file($d['original_name'])): ?>
                    <span class="badge bg-success ms-1">VIDEO</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <?php if (is_video_file($d['original_name'])): ?>
                      <?php
                      $thumbnailUrl = get_video_thumbnail_url($d['file_path']);
                      ?>
                      <img src="<?php echo htmlspecialchars($thumbnailUrl, ENT_QUOTES, 'UTF-8'); ?>"
                           class="me-2 rounded"
                           style="width: 40px; height: 30px; object-fit: cover;"
                           alt="Video thumbnail">
                    <?php endif; ?>
                    <div>
                      <?php echo htmlspecialchars($d['original_name'], ENT_QUOTES, 'UTF-8'); ?>
                      <?php if (is_video_file($d['original_name'])): ?>
                        <?php
                        $duration = get_video_duration($d['file_path']);
                        if ($duration > 0): ?>
                          <br><small class="text-muted">Duration: <?php echo format_video_duration($duration); ?></small>
                        <?php endif; ?>
                      <?php endif; ?>
                      
                    </div>
                  </div>
                </td>
                <td><?php echo htmlspecialchars($d['uploaded_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary me-1" onclick="viewDocument(<?php echo (int)$d['id']; ?>, '<?php echo htmlspecialchars($d['original_name'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($d['doc_type'], ENT_QUOTES, 'UTF-8'); ?>')">
                    <i class="fas fa-eye"></i>
                    <?php echo is_video_file($d['original_name']) ? 'Play' : 'View'; ?>
                  </button>
                  <a class="btn btn-sm btn-outline-danger" href="<?php echo base_url('index.php?page=documents&action=delete&id='.(int)$d['id']); ?>" onclick="return confirm('Delete this document?')">
                    <i class="fas fa-trash"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-muted">No documents uploaded yet</p>
    <?php endif; ?>
  </div>
</div>

<!-- Generated Documents / Version History -->
<div class="card mt-3">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h2 class="h6 mb-0">Generated Documents</h2>
  </div>
  <div class="card-body">
    <?php if (!empty($generatedDocuments)): ?>
      <div class="table-responsive">
        <table class="table table-sm table-striped mb-0">
          <thead>
            <tr>
              <th>Version</th>
              <th>Template</th>
              <th>Destination</th>
              <th>Generated</th>
              <th>By</th>
              <th>Size</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($generatedDocuments as $gd): ?>
              <tr>
                <td><?= (int)$gd['version_number'] ?></td>
                <td><?= htmlspecialchars($gd['template_name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars(ucfirst($gd['destination'] ?? '-')) ?></td>
                <td><?= htmlspecialchars($gd['generated_at']) ?></td>
                <td><?= htmlspecialchars($gd['generated_by_user'] ?? 'System') ?></td>
                <td><?= number_format((int)$gd['file_size']) ?> bytes</td>
                <td>
                  <a href="<?php echo base_url('uploads/generated_docs/' . rawurlencode($gd['file_name'])); ?>" class="btn btn-sm btn-primary" target="_blank">Download</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <em class="text-muted">No documents have been generated for this candidate yet.</em>
    <?php endif; ?>
  </div>
</div>

<!-- Document Viewer Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="documentModalLabel">Document Viewer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="documentContent" style="height: 80vh; overflow: auto;">
          <!-- Document content will be loaded here -->
        </div>
      </div>
      <div class="modal-footer">
        <a id="downloadLink" class="btn btn-primary" href="#" download>
          <i class="fas fa-download"></i> Download
        </a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function viewDocument(documentId, originalName, docType) {
    // Create the serve URL for this document
    const serveUrl = `<?php echo base_url('index.php?page=documents&action=serve&id='); ?>${documentId}`;
    
    // Set modal title
    document.getElementById('documentModalLabel').textContent = `${docType.toUpperCase()}: ${originalName}`;
    
    // Set download link
    const downloadLink = document.getElementById('downloadLink');
    downloadLink.href = serveUrl;
    downloadLink.download = originalName;
    
    // Get file extension to determine how to display
    const fileExtension = originalName.split('.').pop().toLowerCase();
    const contentDiv = document.getElementById('documentContent');
    
    // Clear previous content
    contentDiv.innerHTML = '';
    
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(fileExtension)) {
        // Display image
        contentDiv.innerHTML = `
            <div class="text-center p-3">
                <img src="${serveUrl}" class="img-fluid" style="max-height: 70vh; object-fit: contain;" alt="${originalName}">
            </div>
        `;
    } else if (fileExtension === 'pdf') {
        // Display PDF in iframe
        contentDiv.innerHTML = `
            <iframe src="${serveUrl}" width="100%" height="100%" style="border: none; min-height: 70vh;">
                <p>Your browser does not support PDFs. <a href="${serveUrl}" target="_blank">Click here to view the PDF</a>.</p>
            </iframe>
        `;
    } else if (['mp4', 'mov', 'avi', 'wmv', 'webm', 'mkv', 'flv', '3gp'].includes(fileExtension)) {
        // Display video with enhanced controls
        const videoType = fileExtension === 'mov' ? 'quicktime' : fileExtension;
        contentDiv.innerHTML = `
            <div class="text-center p-3">
                <video controls preload="metadata" style="max-width: 100%; max-height: 70vh; background: #000;" controlsList="nodownload">
                    <source src="${serveUrl}" type="video/${videoType}">
                    <p class="text-muted mt-3">Your browser does not support this video format.
                       <a href="${serveUrl}" target="_blank" class="btn btn-sm btn-primary">Download Video</a>
                    </p>
                </video>
                <div class="mt-2">
                    <small class="text-muted">Video: ${originalName}</small>
                </div>
            </div>
        `;
    } else if (['doc', 'docx', 'txt', 'rtf'].includes(fileExtension)) {
        // For document files, show a preview message and download option
        contentDiv.innerHTML = `
            <div class="text-center p-5">
                <i class="fas fa-file-alt fa-5x text-muted mb-3"></i>
                <h5>Document Preview Not Available</h5>
                <p class="text-muted">This document type cannot be previewed in the browser.</p>
                <p>File: <strong>${originalName}</strong></p>
                <p>Type: <strong>${docType.toUpperCase()}</strong></p>
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
                <p>Type: <strong>${docType.toUpperCase()}</strong></p>
                <a href="${serveUrl}" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download"></i> Download File
                </a>
            </div>
        `;
    }
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('documentModal'));
    modal.show();
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
