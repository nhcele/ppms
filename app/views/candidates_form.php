<?php
$title = ($mode === 'edit' ? 'Edit Candidate' : 'New Candidate');
ob_start();
?>
<h1 class="h4 mb-3"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
<?php if (($mode ?? '') === 'edit' && !empty($data['candidate_code'])): ?>
<div class="mb-3">
  <span class="badge bg-secondary">Candidate ID: <?php echo htmlspecialchars($data['candidate_code'], ENT_QUOTES, 'UTF-8'); ?></span>
</div>
<?php endif; ?>

<?php if (!empty($errors['duplicate'])): ?>
<div class="alert alert-warning">
  <?php echo htmlspecialchars($errors['duplicate'], ENT_QUOTES, 'UTF-8'); ?>
</div>
<?php endif; ?>

<div class="progress mb-4">
  <div class="progress-bar" role="progressbar" style="width: <?php echo ($data['progress_percent'] ?? 0); ?>%;" 
       aria-valuenow="<?php echo ($data['progress_percent'] ?? 0); ?>" aria-valuemin="0" aria-valuemax="100">
    <?php echo ($data['progress_percent'] ?? 0); ?>%
  </div>
</div>

<form method="post" action="" novalidate enctype="multipart/form-data">
  <?php echo csrf_field(); ?>
  
  <!-- Section 1: Personal Information -->
  <div class="card mb-4">
    <div class="card-header bg-light">
      <h2 class="h6 mb-0">Personal Information</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">First Name *</label>
          <input class="form-control <?php echo isset($errors['first_name'])?'is-invalid':''; ?>" 
                 type="text" name="first_name" id="first_name"
                 value="<?php echo htmlspecialchars($data['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
          <?php if(isset($errors['first_name'])): ?>
            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['first_name'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Last Name *</label>
          <input class="form-control <?php echo isset($errors['last_name'])?'is-invalid':''; ?>" 
                 type="text" name="last_name" id="last_name"
                 value="<?php echo htmlspecialchars($data['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
          <?php if(isset($errors['last_name'])): ?>
            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['last_name'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>
        
        <div class="col-md-12">
          <label class="form-label">Full Name (auto)</label>
          <input class="form-control" type="text" id="full_name_display" value="<?php echo htmlspecialchars(trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>" readonly>
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Date of Birth</label>
          <input class="form-control <?php echo isset($errors['dob'])?'is-invalid':''; ?>" type="date" name="dob" value="<?php echo htmlspecialchars($data['dob'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <?php if(isset($errors['dob'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['dob'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Place of Birth</label>
          <input class="form-control" type="text" name="place_of_birth" value="<?php echo htmlspecialchars($data['place_of_birth'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Mother's Name</label>
          <input class="form-control" type="text" name="mothers_name" value="<?php echo htmlspecialchars($data['mothers_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Father's Name</label>
          <input class="form-control" type="text" name="fathers_name" value="<?php echo htmlspecialchars($data['fathers_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Marital Status</label>
          <select class="form-select" name="marital_status">
            <?php $ms = $data['marital_status'] ?? ''; ?>
            <option value="">--</option>
            <option value="single" <?php echo $ms==='single'?'selected':''; ?>>Single</option>
            <option value="married" <?php echo $ms==='married'?'selected':''; ?>>Married</option>
            <option value="divorced" <?php echo $ms==='divorced'?'selected':''; ?>>Divorced</option>
            <option value="widowed" <?php echo $ms==='widowed'?'selected':''; ?>>Widowed</option>
          </select>
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Nationality</label>
          <input class="form-control" type="text" name="nationality" value="<?php echo htmlspecialchars($data['nationality'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Gender</label>
          <select class="form-select" name="gender">
            <?php $g = $data['gender'] ?? ''; ?>
            <option value="">--</option>
            <option value="male" <?php echo $g==='male'?'selected':''; ?>>Male</option>
            <option value="female" <?php echo $g==='female'?'selected':''; ?>>Female</option>
            <option value="other" <?php echo $g==='other'?'selected':''; ?>>Other</option>
          </select>
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Number of Children</label>
          <input class="form-control" type="number" name="num_children" min="0" value="<?php echo htmlspecialchars($data['num_children'] ?? 0, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Height (cm)</label>
          <input class="form-control" type="number" name="height_cm" min="50" max="250" value="<?php echo htmlspecialchars($data['height_cm'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Weight (kg)</label>
          <input class="form-control" type="number" name="weight_kg" step="0.1" min="30" max="200" value="<?php echo htmlspecialchars($data['weight_kg'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_smoker" id="is_smoker" <?php echo ($data['is_smoker'] ?? 0) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_smoker">Smoker</label>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="has_drivers_license" id="has_drivers_license" <?php echo ($data['has_drivers_license'] ?? 0) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="has_drivers_license">Has Driver's License</label>
          </div>
        </div>
        
        <div class="col-md-4" id="drivers_license_number_group">
          <label class="form-label">License Number</label>
          <input class="form-control" type="text" name="drivers_license_number" id="drivers_license_number" value="<?php echo htmlspecialchars($data['drivers_license_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Religion</label>
          <input class="form-control" type="text" name="religion" value="<?php echo htmlspecialchars($data['religion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
      </div>
    </div>
  </div>
  
  <!-- Section 2: Contact Information -->
  <div class="card mb-4">
    <div class="card-header bg-light">
      <h2 class="h6 mb-0">Contact Information</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input class="form-control <?php echo isset($errors['email'])?'is-invalid':''; ?>" 
                 type="email" name="email" 
                 value="<?php echo htmlspecialchars($data['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <?php if(isset($errors['email'])): ?>
            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Phone</label>
          <input class="form-control" type="text" name="phone" value="<?php echo htmlspecialchars($data['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Agency</label>
          <select class="form-select" name="agency_id">
            <option value="">-- None --</option>
            <?php foreach (($agencies ?? []) as $a): ?>
              <option value="<?php echo (int)$a['id']; ?>" <?php echo ((int)($data['agency_id'] ?? 0)===(int)$a['id'])?'selected':''; ?>><?php echo htmlspecialchars($a['name'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-12">
          <label class="form-label">Roles Available For</label>
          <select class="form-select" name="role_ids[]" multiple size="6">
            <?php foreach (($roles ?? []) as $r): ?>
              <?php $sel = in_array((int)$r['id'], ($selected_roles ?? []), true) ? 'selected' : ''; ?>
              <option value="<?php echo (int)$r['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Hold Ctrl/Cmd to select multiple roles</div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Section 3: Address Information -->
  <div class="card mb-4">
    <div class="card-header bg-light">
      <h2 class="h6 mb-0">Address Information</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Address Line 1</label>
          <input class="form-control" type="text" name="address_line1" value="<?php echo htmlspecialchars($data['address_line1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Address Line 2</label>
          <input class="form-control" type="text" name="address_line2" value="<?php echo htmlspecialchars($data['address_line2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">City</label>
          <input class="form-control" type="text" name="address_city" value="<?php echo htmlspecialchars($data['address_city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">State/Province</label>
          <input class="form-control" type="text" name="address_state" value="<?php echo htmlspecialchars($data['address_state'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-4">
          <label class="form-label">Postal Code</label>
          <input class="form-control" type="text" name="address_postal_code" value="<?php echo htmlspecialchars($data['address_postal_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Country</label>
          <input class="form-control" type="text" name="address_country" value="<?php echo htmlspecialchars($data['address_country'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
      </div>
    </div>
  </div>
  
  <!-- Section 4: Emergency Contact -->
  <div class="card mb-4">
    <div class="card-header bg-light">
      <h2 class="h6 mb-0">Emergency Contact</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Full Name</label>
          <input class="form-control" type="text" name="emergency_contact_name" value="<?php echo htmlspecialchars($data['emergency_contact_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Relationship</label>
          <input class="form-control" type="text" name="emergency_contact_relation" value="<?php echo htmlspecialchars($data['emergency_contact_relation'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Phone</label>
          <input class="form-control" type="text" name="emergency_contact_phone" value="<?php echo htmlspecialchars($data['emergency_contact_phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Address</label>
          <input class="form-control" type="text" name="emergency_contact_address" value="<?php echo htmlspecialchars($data['emergency_contact_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
      </div>
    </div>
  </div>
  
  <!-- Section 5: Documents & Employment -->
  <div class="card mb-4">
    <div class="card-header bg-light">
      <h2 class="h6 mb-0">Documents & Employment</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Passport Number</label>
          <input class="form-control <?php echo isset($errors['passport_number'])?'is-invalid':''; ?>" 
                 type="text" 
                 name="passport_number" 
                 pattern="[A-Za-z]{2}[0-9]{6}"
                 placeholder="e.g., FN123456"
                 title="Enter 2 letters followed by 6 digits (e.g., FN123456)"
                 value="<?php echo htmlspecialchars($data['passport_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <?php if(isset($errors['passport_number'])): ?>
            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['passport_number'], ENT_QUOTES, 'UTF-8'); ?></div>
          <?php endif; ?>
          <div class="form-text">Format: 2 letters + 6 digits (e.g., FN123456)</div>
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Passport Validity</label>
          <input class="form-control <?php echo isset($errors['passport_validity'])?'is-invalid':''; ?>" type="date" name="passport_validity" value="<?php echo htmlspecialchars($data['passport_validity'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <?php if(isset($errors['passport_validity'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['passport_validity'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Section 6: Skills & Additional Info -->
  <div class="card mb-4">
    <div class="card-header bg-light">
      <h2 class="h6 mb-0">Skills & Additional Information</h2>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Computer Skills Level</label>
          <select class="form-select" name="computer_skills_level">
            <?php $level = $data['computer_skills_level'] ?? ''; ?>
            <option value="">-- Select Level --</option>
            <option value="none" <?php echo $level==='none'?'selected':''; ?>>None</option>
            <option value="basic" <?php echo $level==='basic'?'selected':''; ?>>Basic</option>
            <option value="intermediate" <?php echo $level==='intermediate'?'selected':''; ?>>Intermediate</option>
            <option value="advanced" <?php echo $level==='advanced'?'selected':''; ?>>Advanced</option>
          </select>
        </div>
        
        <div class="col-12">
          <label class="form-label">Computer Skills Notes</label>
          <textarea class="form-control" name="computer_skills_notes" rows="2"><?php echo htmlspecialchars($data['computer_skills_notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        
        <div class="col-12">
          <label class="form-label">Personal Statement</label>
          <textarea class="form-control" name="personal_statement" rows="4"><?php echo htmlspecialchars($data['personal_statement'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Section 7: Work Experience -->
  <div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h2 class="h6 mb-0">Work Experience</h2>
      <button type="button" class="btn btn-sm btn-outline-primary" onclick="addWorkExperience()">
        <i class="fas fa-plus"></i> Add Experience
      </button>
    </div>
    <div class="card-body">
      <div id="work-experience-container">
        <?php
        // Handle both JSON string (from form submission) and array (from database)
        $work_experiences = $data['work_experience'] ?? [];
        if (is_string($work_experiences)) {
          $work_experiences = json_decode($work_experiences, true) ?: [];
        }
        if (empty($work_experiences)) {
          $work_experiences = [['company' => '', 'position' => '', 'start_date' => '', 'end_date' => '', 'description' => '']];
        }
        foreach ($work_experiences as $index => $exp):
        ?>
        <div class="work-experience-row border rounded p-3 mb-3" data-index="<?php echo $index; ?>">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Experience #<?php echo $index + 1; ?></h6>
            <?php if ($index > 0): ?>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeWorkExperience(this)">
              <i class="fas fa-trash"></i>
            </button>
            <?php endif; ?>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company/Organization</label>
              <input class="form-control" type="text" name="work_experience[<?php echo $index; ?>][company]" 
                     value="<?php echo htmlspecialchars($exp['company'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Position/Job Title</label>
              <input class="form-control" type="text" name="work_experience[<?php echo $index; ?>][position]" 
                     value="<?php echo htmlspecialchars($exp['position'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Start Date</label>
              <input class="form-control" type="date" name="work_experience[<?php echo $index; ?>][start_date]" 
                     value="<?php echo htmlspecialchars($exp['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">End Date</label>
              <input class="form-control" type="date" name="work_experience[<?php echo $index; ?>][end_date]" 
                     value="<?php echo htmlspecialchars($exp['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
              <div class="form-text">Leave blank if current position</div>
            </div>
            <div class="col-12">
              <label class="form-label">Job Description</label>
              <textarea class="form-control" name="work_experience[<?php echo $index; ?>][description]" rows="3"><?php echo htmlspecialchars($exp['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  
  <!-- Section 8: Academic Qualifications -->
  <div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h2 class="h6 mb-0">Academic Qualifications</h2>
      <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAcademicQualification()">
        <i class="fas fa-plus"></i> Add Qualification
      </button>
    </div>
    <div class="card-body">
      <div id="academic-qualifications-container">
        <?php
        // Handle both JSON string (from form submission) and array (from database)
        $academic_qualifications = $data['academic_qualifications'] ?? [];
        if (is_string($academic_qualifications)) {
          $academic_qualifications = json_decode($academic_qualifications, true) ?: [];
        }
        if (empty($academic_qualifications)) {
          $academic_qualifications = [['institution' => '', 'degree' => '', 'field_of_study' => '', 'graduation_year' => '', 'grade' => '']];
        }
        foreach ($academic_qualifications as $index => $qual):
        ?>
        <div class="academic-qualification-row border rounded p-3 mb-3" data-index="<?php echo $index; ?>">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Qualification #<?php echo $index + 1; ?></h6>
            <?php if ($index > 0): ?>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAcademicQualification(this)">
              <i class="fas fa-trash"></i>
            </button>
            <?php endif; ?>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Institution/School</label>
              <input class="form-control" type="text" name="academic_qualifications[<?php echo $index; ?>][institution]" 
                     value="<?php echo htmlspecialchars($qual['institution'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Degree/Certificate</label>
              <input class="form-control" type="text" name="academic_qualifications[<?php echo $index; ?>][degree]" 
                     value="<?php echo htmlspecialchars($qual['degree'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Field of Study</label>
              <input class="form-control" type="text" name="academic_qualifications[<?php echo $index; ?>][field_of_study]" 
                     value="<?php echo htmlspecialchars($qual['field_of_study'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Graduation Year</label>
              <input class="form-control" type="number" name="academic_qualifications[<?php echo $index; ?>][graduation_year]" 
                     min="1950" max="2030" value="<?php echo htmlspecialchars($qual['graduation_year'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Grade/GPA</label>
              <input class="form-control" type="text" name="academic_qualifications[<?php echo $index; ?>][grade]" 
                     value="<?php echo htmlspecialchars($qual['grade'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  
  <div class="mt-3">
    <button class="btn btn-primary" type="submit"><?php echo $mode==='edit'?'Save Changes':'Create Candidate'; ?></button>
    <a class="btn btn-outline-secondary" href="<?php echo base_url('index.php?page=candidates&action=list'); ?>">Cancel</a>
  </div>
</form>

<?php
$content = ob_get_clean();
?>
<?php ob_start(); ?>
<script>
(function(){
  function updateFullName(){
    var first = document.getElementById('first_name');
    var last = document.getElementById('last_name');
    var out = document.getElementById('full_name_display');
    if(first && last && out){ out.value = (first.value || '').trim() + (last.value? (' ' + last.value.trim()) : ''); }
  }
  function toggleLicense(){
    var chk = document.getElementById('has_drivers_license');
    var grp = document.getElementById('drivers_license_number_group');
    if(chk && grp){ grp.style.display = chk.checked ? '' : 'none'; }
  }
  
  // Work Experience Functions
  window.addWorkExperience = function() {
    var container = document.getElementById('work-experience-container');
    var rows = container.querySelectorAll('.work-experience-row');
    var newIndex = rows.length;
    
    var newRow = document.createElement('div');
    newRow.className = 'work-experience-row border rounded p-3 mb-3';
    newRow.setAttribute('data-index', newIndex);
    
    newRow.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">Experience #${newIndex + 1}</h6>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeWorkExperience(this)">
          <i class="fas fa-trash"></i>
        </button>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Company/Organization</label>
          <input class="form-control" type="text" name="work_experience[${newIndex}][company]" value="">
        </div>
        <div class="col-md-6">
          <label class="form-label">Position/Job Title</label>
          <input class="form-control" type="text" name="work_experience[${newIndex}][position]" value="">
        </div>
        <div class="col-md-6">
          <label class="form-label">Start Date</label>
          <input class="form-control" type="date" name="work_experience[${newIndex}][start_date]" value="">
        </div>
        <div class="col-md-6">
          <label class="form-label">End Date</label>
          <input class="form-control" type="date" name="work_experience[${newIndex}][end_date]" value="">
          <div class="form-text">Leave blank if current position</div>
        </div>
        <div class="col-12">
          <label class="form-label">Job Description</label>
          <textarea class="form-control" name="work_experience[${newIndex}][description]" rows="3"></textarea>
        </div>
      </div>
    `;
    
    container.appendChild(newRow);
    updateWorkExperienceNumbers();
  };
  
  window.removeWorkExperience = function(button) {
    var row = button.closest('.work-experience-row');
    row.remove();
    updateWorkExperienceNumbers();
  };
  
  function updateWorkExperienceNumbers() {
    var container = document.getElementById('work-experience-container');
    var rows = container.querySelectorAll('.work-experience-row');
    rows.forEach(function(row, index) {
      row.setAttribute('data-index', index);
      var title = row.querySelector('h6');
      if (title) title.textContent = 'Experience #' + (index + 1);
      
      // Update input names
      var inputs = row.querySelectorAll('input, textarea');
      inputs.forEach(function(input) {
        var name = input.getAttribute('name');
        if (name && name.includes('work_experience[')) {
          var newName = name.replace(/work_experience\[\d+\]/, 'work_experience[' + index + ']');
          input.setAttribute('name', newName);
        }
      });
    });
  }
  
  // Academic Qualifications Functions
  window.addAcademicQualification = function() {
    var container = document.getElementById('academic-qualifications-container');
    var rows = container.querySelectorAll('.academic-qualification-row');
    var newIndex = rows.length;
    
    var newRow = document.createElement('div');
    newRow.className = 'academic-qualification-row border rounded p-3 mb-3';
    newRow.setAttribute('data-index', newIndex);
    
    newRow.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">Qualification #${newIndex + 1}</h6>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAcademicQualification(this)">
          <i class="fas fa-trash"></i>
        </button>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Institution/School</label>
          <input class="form-control" type="text" name="academic_qualifications[${newIndex}][institution]" value="">
        </div>
        <div class="col-md-6">
          <label class="form-label">Degree/Certificate</label>
          <input class="form-control" type="text" name="academic_qualifications[${newIndex}][degree]" value="">
        </div>
        <div class="col-md-4">
          <label class="form-label">Field of Study</label>
          <input class="form-control" type="text" name="academic_qualifications[${newIndex}][field_of_study]" value="">
        </div>
        <div class="col-md-4">
          <label class="form-label">Graduation Year</label>
          <input class="form-control" type="number" name="academic_qualifications[${newIndex}][graduation_year]" min="1950" max="2030" value="">
        </div>
        <div class="col-md-4">
          <label class="form-label">Grade/GPA</label>
          <input class="form-control" type="text" name="academic_qualifications[${newIndex}][grade]" value="">
        </div>
      </div>
    `;
    
    container.appendChild(newRow);
    updateAcademicQualificationNumbers();
  };
  
  window.removeAcademicQualification = function(button) {
    var row = button.closest('.academic-qualification-row');
    row.remove();
    updateAcademicQualificationNumbers();
  };
  
  function updateAcademicQualificationNumbers() {
    var container = document.getElementById('academic-qualifications-container');
    var rows = container.querySelectorAll('.academic-qualification-row');
    rows.forEach(function(row, index) {
      row.setAttribute('data-index', index);
      var title = row.querySelector('h6');
      if (title) title.textContent = 'Qualification #' + (index + 1);
      
      // Update input names
      var inputs = row.querySelectorAll('input, textarea');
      inputs.forEach(function(input) {
        var name = input.getAttribute('name');
        if (name && name.includes('academic_qualifications[')) {
          var newName = name.replace(/academic_qualifications\[\d+\]/, 'academic_qualifications[' + index + ']');
          input.setAttribute('name', newName);
        }
      });
    });
  }
  
  document.addEventListener('input', function(e){
    if(e.target && (e.target.id === 'first_name' || e.target.id === 'last_name')){ updateFullName(); }
  });
  document.addEventListener('change', function(e){
    if(e.target && e.target.id === 'has_drivers_license'){ toggleLicense(); }
  });
  // init
  updateFullName();
  toggleLicense();
})();
</script>
<?php $content .= ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>
