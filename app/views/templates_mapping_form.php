<?php
$title = 'Add Field Mapping';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Add Field Mapping</h1>
      <div class="text-muted small">Choose what information appears in the document</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=templates&action=view&id=' . $template_id); ?>" class="btn btn-secondary">Back to Template</a>
    </div>
  </div>
</div>

<?php if (!empty($errors['general'])): ?>
<div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo base_url('index.php?page=templates&action=add-mapping&id=' . $template_id); ?>">
      <?php echo csrf_field(); ?>
      
      <div class="mb-3">
        <label for="template_field" class="form-label">Document Field *</label>
        <select class="form-select" id="template_field" name="template_field" required onchange="toggleCustomField()">
          <option value="">-- Choose a document field --</option>
          <?php foreach ($mappingOptions['template_fields'] as $key => $label): ?>
            <option value="<?= htmlspecialchars($key) ?>" <?= $data['template_field'] === $key ? 'selected' : '' ?>>
              <?= htmlspecialchars($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['template_field'])): ?>
          <div class="text-danger"><?= htmlspecialchars($errors['template_field']) ?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3" id="custom_template_field_group" style="display: none;">
        <label for="custom_template_field" class="form-label">Custom Field Name</label>
        <input type="text" class="form-control" id="custom_template_field" name="custom_template_field"
               value="<?= htmlspecialchars($data['custom_template_field']) ?>"
               placeholder="e.g., professional_summary">
        <div class="form-text">Enter the exact field name used in the template layout.</div>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Get Value From *</label>
        <div class="btn-group w-100" role="group">
          <input type="radio" class="btn-check" name="source_type" id="source_candidate" value="candidate" 
                 <?= $data['source_type'] === 'candidate' ? 'checked' : '' ?> onchange="toggleSourceType()">
          <label class="btn btn-outline-primary" for="source_candidate">Candidate Record</label>
          
          <input type="radio" class="btn-check" name="source_type" id="source_questionnaire" value="questionnaire" 
                 <?= $data['source_type'] === 'questionnaire' ? 'checked' : '' ?> onchange="toggleSourceType()">
          <label class="btn btn-outline-primary" for="source_questionnaire">Questionnaire</label>
          
          <input type="radio" class="btn-check" name="source_type" id="source_static" value="static" 
                 <?= $data['source_type'] === 'static' ? 'checked' : '' ?> onchange="toggleSourceType()">
          <label class="btn btn-outline-primary" for="source_static">Static Text</label>
        </div>
      </div>
      
      <div class="mb-3" id="candidate_source_group">
        <label for="candidate_source" class="form-label">Candidate Information</label>
        <select class="form-select" id="candidate_source" name="source_selection" onchange="clearCustomSource()">
          <option value="">-- Select candidate field --</option>
          <?php foreach ($mappingOptions['candidate_sources'] as $key => $label): ?>
            <option value="<?= htmlspecialchars($key) ?>" <?= $data['source_type'] === 'candidate' && $data['source_selection'] === $key ? 'selected' : '' ?>>
              <?= htmlspecialchars($label) ?>
            </option>
          <?php endforeach; ?>
          <option value="other" <?= $data['source_type'] === 'candidate' && $data['source_selection'] === 'other' ? 'selected' : '' ?>>Other (type column name)</option>
        </select>
      </div>
      
      <div class="mb-3" id="questionnaire_source_group" style="display: none;">
        <label for="questionnaire_source" class="form-label">Questionnaire Information</label>
        <select class="form-select" id="questionnaire_source" name="source_selection" onchange="clearCustomSource()">
          <option value="">-- Select questionnaire field --</option>
          <?php foreach ($mappingOptions['questionnaire_sources'] as $key => $label): ?>
            <option value="<?= htmlspecialchars($key) ?>" <?= $data['source_type'] === 'questionnaire' && $data['source_selection'] === $key ? 'selected' : '' ?>>
              <?= htmlspecialchars($label) ?>
            </option>
          <?php endforeach; ?>
          <option value="other" <?= $data['source_type'] === 'questionnaire' && $data['source_selection'] === 'other' ? 'selected' : '' ?>>Other (type section.field_name)</option>
        </select>
      </div>
      
      <div class="mb-3" id="custom_source_group" style="display: none;">
        <label for="custom_source" class="form-label">Custom Source Path</label>
        <input type="text" class="form-control" id="custom_source" name="custom_source"
               value="<?= htmlspecialchars($data['custom_source']) ?>"
               placeholder="e.g., first_name or additional_info.video_link">
        <div class="form-text">For candidate fields use the column name. For questionnaire use section.field_name.</div>
      </div>
      
      <div class="mb-3" id="static_value_group" style="display: none;">
        <label for="static_value" class="form-label">Static Text *</label>
        <textarea class="form-control" id="static_value" name="static_value" rows="3"><?= htmlspecialchars($data['static_value']) ?></textarea>
        <?php if (!empty($errors['static_value'])): ?>
          <div class="text-danger"><?= htmlspecialchars($errors['static_value']) ?></div>
        <?php endif; ?>
      </div>
      
      <?php if (!empty($errors['source_selection'])): ?>
        <div class="text-danger mb-3"><?= htmlspecialchars($errors['source_selection']) ?></div>
      <?php endif; ?>
      
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label for="field_order" class="form-label">Display Order</label>
            <input type="number" class="form-control" id="field_order" name="field_order" 
                   value="<?= htmlspecialchars($data['field_order']) ?>" min="0">
            <div class="form-text">Lower numbers appear first</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-check-label d-block mt-md-4">
              <input type="checkbox" name="is_required" value="1" <?= $data['is_required'] ? 'checked' : '' ?>>
              Required Field
            </label>
          </div>
        </div>
      </div>
      
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Add Mapping</button>
        <a href="<?php echo base_url('index.php?page=templates&action=view&id=' . $template_id); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
function toggleCustomField() {
    const templateField = document.getElementById('template_field').value;
    const customGroup = document.getElementById('custom_template_field_group');
    const customInput = document.getElementById('custom_template_field');
    
    if (templateField === 'other') {
        customGroup.style.display = 'block';
        customInput.required = true;
    } else {
        customGroup.style.display = 'none';
        customInput.required = false;
    }
}

function toggleSourceType() {
    const sourceType = document.querySelector('input[name="source_type"]:checked').value;
    const candidateGroup = document.getElementById('candidate_source_group');
    const questionnaireGroup = document.getElementById('questionnaire_source_group');
    const staticGroup = document.getElementById('static_value_group');
    
    // Disable all source_selection selects so only active one is submitted
    document.getElementById('candidate_source').disabled = true;
    document.getElementById('questionnaire_source').disabled = true;
    document.getElementById('static_value').required = false;
    
    if (sourceType === 'candidate') {
        candidateGroup.style.display = 'block';
        questionnaireGroup.style.display = 'none';
        staticGroup.style.display = 'none';
        document.getElementById('candidate_source').disabled = false;
    } else if (sourceType === 'questionnaire') {
        candidateGroup.style.display = 'none';
        questionnaireGroup.style.display = 'block';
        staticGroup.style.display = 'none';
        document.getElementById('questionnaire_source').disabled = false;
    } else {
        candidateGroup.style.display = 'none';
        questionnaireGroup.style.display = 'none';
        staticGroup.style.display = 'block';
        document.getElementById('static_value').required = true;
    }
    
    updateCustomSourceVisibility();
}

function clearCustomSource() {
    document.getElementById('custom_source').value = '';
    updateCustomSourceVisibility();
}

function updateCustomSourceVisibility() {
    const sourceType = document.querySelector('input[name="source_type"]:checked').value;
    const selectId = sourceType === 'candidate' ? 'candidate_source' : 'questionnaire_source';
    const select = document.getElementById(selectId);
    const customGroup = document.getElementById('custom_source_group');
    const customInput = document.getElementById('custom_source');
    
    if (select && select.value === 'other') {
        customGroup.style.display = 'block';
        customInput.required = true;
    } else {
        customGroup.style.display = 'none';
        customInput.required = false;
    }
}

// Initialize on page load
toggleCustomField();
toggleSourceType();

// Attach change listeners for custom source selects
document.getElementById('candidate_source').addEventListener('change', updateCustomSourceVisibility);
document.getElementById('questionnaire_source').addEventListener('change', updateCustomSourceVisibility);
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
