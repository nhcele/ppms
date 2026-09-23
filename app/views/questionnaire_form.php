<?php
$title = $mode === 'create' ? 'Create Questionnaire Request' : 'Edit Questionnaire Request';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1"><?= $mode === 'create' ? 'Create Questionnaire Request' : 'Edit Questionnaire Request' ?></h1>
      <div class="text-muted small">Manage candidate questionnaire requests</div>
    </div>
    <div>
      <a class="btn btn-secondary" href="<?php echo base_url('index.php?page=questionnaire'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L15 5.293l.354.353a.5.5 0 0 1-.708.708L14.293 6l-3.646-3.646a.5.5 0 0 1 0-.708l3.646 3.646-.354.353a.5.5 0 0 1-.708-.708L10.5 5.293 5.646 10.146a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 0-.708z"/>
        </svg>
        Back to List
      </a>
    </div>
  </div>
</div>

<?php if (!empty($errors['general'])): ?>
<div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Basic Information</h5>
  </div>
  <div class="card-body">
    <form method="post" action="<?php echo base_url('index.php?page=questionnaire&action=' . $mode); ?>">
      <?php echo csrf_field(); ?>
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="candidate_id" class="form-label">Candidate (Optional)</label>
            <select class="form-select" id="candidate_id" name="candidate_id">
              <option value="">-- Select Candidate --</option>
              <?php foreach ($candidates as $candidate): ?>
                <option value="<?= $candidate['id'] ?>" <?= $data['candidate_id'] == $candidate['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($candidate['name']) ?> (<?= htmlspecialchars($candidate['candidate_code']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Leave empty if candidate is not yet in the system</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="position" class="form-label">Position *</label>
            <input type="text" class="form-control" id="position" name="position" 
                   value="<?= htmlspecialchars($data['position']) ?>" required>
            <?php if (!empty($errors['position'])): ?>
              <div class="text-danger"><?= htmlspecialchars($errors['position']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="recruitment_destination" class="form-label">Recruitment Destination *</label>
            <select class="form-select" id="recruitment_destination" name="recruitment_destination" required>
              <option value="">-- Select Destination --</option>
              <option value="Lithuania" <?= $data['recruitment_destination'] === 'Lithuania' ? 'selected' : '' ?>>Lithuania</option>
              <option value="Turkey" <?= $data['recruitment_destination'] === 'Turkey' ? 'selected' : '' ?>>Turkey</option>
              <option value="Generic" <?= $data['recruitment_destination'] === 'Generic' ? 'selected' : '' ?>>Generic/Other</option>
            </select>
            <?php if (!empty($errors['recruitment_destination'])): ?>
              <div class="text-danger"><?= htmlspecialchars($errors['recruitment_destination']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="expiry_hours" class="form-label">Link Expiry (Hours) *</label>
            <input type="number" class="form-control" id="expiry_hours" name="expiry_hours" 
                   value="<?= htmlspecialchars($data['expiry_hours']) ?>" min="1" max="168" required>
            <div class="form-text">Between 1 and 168 hours (1 week)</div>
            <?php if (!empty($errors['expiry_hours'])): ?>
              <div class="text-danger"><?= htmlspecialchars($errors['expiry_hours']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <div class="mb-3">
        <label for="instructions" class="form-label">Instructions for Candidate</label>
        <textarea class="form-control" id="instructions" name="instructions" rows="4"><?= htmlspecialchars($data['instructions']) ?></textarea>
        <div class="form-text">Optional instructions that will be shown to the candidate</div>
      </div>
      
      <!-- Configurable Requirements -->
      <div class="card mb-4">
        <div class="card-header bg-light">
          <h5 class="mb-0">Required Information & Documents</h5>
        </div>
        <div class="card-body">
          <p class="text-muted small">Select the information and documents the candidate must provide. Tick <strong>Required</strong> to make it mandatory.</p>
          
          <?php
          $sections = [];
          foreach ($requirementDefinitions as $def) {
              $sections[$def['requirement_type']][] = $def;
          }
          $sectionLabels = [
              'personal_info' => 'Personal Information',
              'education_info' => 'Education & Qualifications',
              'positions' => 'Positions Being Applied For',
              'employment_info' => 'Employment Information',
              'additional_info' => 'Additional Information',
              'references' => 'References',
              'travel_history' => 'Travel History',
              'declarations' => 'Declarations',
              'health' => 'Health Information',
              'declaration' => 'Final Declaration',
              'documents' => 'Documents',
          ];
          ?>
          
          <?php foreach ($sections as $section => $items): ?>
            <div class="mb-4">
              <h6 class="border-bottom pb-2"><?= htmlspecialchars($sectionLabels[$section] ?? ucfirst(str_replace('_', ' ', $section))) ?></h6>
              <div class="row g-2">
                <?php foreach ($items as $item): 
                  $key = $item['item_type'] === 'document' ? ('doc:' . $item['document_type']) : ('field:' . $item['field_name']);
                  $isSelected = true;
                  $isRequired = $item['is_required'];
                ?>
                  <div class="col-md-6">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input req-selected" type="checkbox" 
                             id="<?= htmlspecialchars($key) ?>_selected" 
                             name="requirements[<?= htmlspecialchars($key) ?>][selected]" 
                             value="1" <?= $isSelected ? 'checked' : '' ?>
                             data-required-checkbox="<?= htmlspecialchars($key) ?>_required">
                      <label class="form-check-label" for="<?= htmlspecialchars($key) ?>_selected"><?= htmlspecialchars($item['label']) ?></label>
                    </div>
                    <div class="form-check form-check-inline ms-3">
                      <input class="form-check-input" type="checkbox" 
                             id="<?= htmlspecialchars($key) ?>_required" 
                             name="requirements[<?= htmlspecialchars($key) ?>][required]" 
                             value="1" <?= $isRequired ? 'checked' : '' ?>
                             data-parent-checkbox="<?= htmlspecialchars($key) ?>_selected">
                      <label class="form-check-label small" for="<?= htmlspecialchars($key) ?>_required">Required</label>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      
      <script>
        // Ensure required checkbox is disabled if item is not selected, and auto-checks selected when required is checked
        document.querySelectorAll('[data-parent-checkbox]').forEach(function(reqBox) {
          function update() {
            var parentId = reqBox.getAttribute('data-parent-checkbox');
            var parent = document.getElementById(parentId);
            if (reqBox.checked && parent && !parent.checked) {
              parent.checked = true;
            }
          }
          reqBox.addEventListener('change', update);
          update();
        });
        document.querySelectorAll('[data-required-checkbox]').forEach(function(selBox) {
          function update() {
            var reqId = selBox.getAttribute('data-required-checkbox');
            var reqBox = document.getElementById(reqId);
            if (reqBox) {
              reqBox.disabled = !selBox.checked;
              if (!selBox.checked) {
                reqBox.checked = false;
              }
            }
          }
          selBox.addEventListener('change', update);
          update();
        });
      </script>
      
      <div class="mb-3">
        <button type="submit" class="btn btn-primary"><?= $mode === 'create' ? 'Create Questionnaire Request' : 'Update Questionnaire Request' ?></button>
        <a href="<?php echo base_url('index.php?page=questionnaire'); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
