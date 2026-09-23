<?php
$title = ($mode === 'edit' ? 'Edit Deployment' : 'New Deployment');
ob_start();

// Status options with colors
$statusOptions = [
  'Draft' => 'secondary',
  'Pending' => 'info',
  'Active' => 'success',
  'On Hold' => 'warning',
  'Completed' => 'primary',
  'Terminated' => 'danger',
  'Cancelled' => 'dark'
];
?>

<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
      <div class="text-muted small"><?php echo $mode === 'edit' ? 'Update deployment details' : 'Create a new candidate deployment'; ?></div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=deployments&action=list'); ?>" class="btn btn-outline-secondary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Back to Deployments
      </a>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="post" action="" class="needs-validation" novalidate enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      
      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Deployment Information</h5>
        <div class="row g-3">
          <?php if ($mode === 'create'): ?>
          <div class="col-md-6">
            <label for="candidate_id" class="form-label">Candidate <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                </svg>
              </span>
              <select class="form-select <?php echo isset($errors['candidate_id'])?'is-invalid':''; ?>" id="candidate_id" name="candidate_id" required>
                <option value="">Select a candidate</option>
                <?php foreach (($cands ?? []) as $c): ?>
                <option value="<?php echo (int)$c['id']; ?>" <?php echo ((int)($data['candidate_id'] ?? 0)===(int)$c['id'])?'selected':''; ?>>
                  <?php echo htmlspecialchars($c['first_name'].' '.$c['last_name'] . ' (ID: ' . $c['id'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
              </select>
              <?php if(isset($errors['candidate_id'])): ?>
                <div class="invalid-feedback">
                  <?php echo htmlspecialchars($errors['candidate_id'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
          
          <div class="col-md-6">
            <label for="employer_id" class="form-label">Employer <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H4zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                  <path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.373 5.373 0 0 1 1.285-2.37L9.04 3.602a.5.5 0 0 1 .653-.533L13.81 4.5a2.5 2.5 0 0 1 .94 4.784 5.966 5.966 0 0 0-1.01 1.124.5.5 0 0 1-.5.276.5.5 0 0 1-.5-.5V9.5a.5.5 0 0 1 .5-.5h1.306l-1.5-2H12a.5.5 0 0 1 .5.5v.5a.5.5 0 1 0 1 0v-.5a1.5 1.5 0 0 0-1.5-1.5h-1.5l-1.5 2H8a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h1.5l.5-.75a.5.5 0 0 1 .8.6L9.5 8.5H10a.5.5 0 0 1 .5.5v.5a.5.5 0 1 0 1 0v-.5a1.5 1.5 0 0 0-1.5-1.5H8.5l-.5-.75A1.5 1.5 0 0 0 6.5 6H4.5a1.5 1.5 0 0 0-1.5 1.5v9.85a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v.5a.5.5 0 1 0 1 0V3a1.5 1.5 0 0 0-1.5-1.5H2z"/>
                </svg>
              </span>
              <select class="form-select <?php echo isset($errors['employer_id'])?'is-invalid':''; ?>" id="employer_id" name="employer_id" required>
                <option value="">Select an employer</option>
                <?php foreach (($emps ?? []) as $e): ?>
                <option value="<?php echo (int)$e['id']; ?>" <?php echo ((int)($data['employer_id'] ?? 0)===(int)$e['id'])?'selected':''; ?>>
                  <?php echo htmlspecialchars($e['name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
              </select>
              <?php if(isset($errors['employer_id'])): ?>
                <div class="invalid-feedback">
                  <?php echo htmlspecialchars($errors['employer_id'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="col-md-6">
            <label for="agency_id" class="form-label">Agency</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M4.5 5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM3 4.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                  <path d="M0 4a2 2 0 0 1 2-2h11.5a2 2 0 0 1 1.983 2.199c.044.534.18.92.334 1.206.145.266.314.473.525.689.28.287.68.416 1.158.466.785.084 1.301.622 1.333 1.39.02.534-.151 1.12-.416 1.755-.264.633-.65 1.35-1.098 2.04-.465.71-.99 1.383-1.553 1.987-.25.26-.51.505-.775.74-.167.15-.33.29-.49.421-.14.116-.276.223-.41.32H14.5a.5.5 0 0 1 0 1H2a2 2 0 0 1-1.994-2.169A1.99 1.99 0 0 1 0 12v-2a6 6 0 0 1 1.5-4.5V4zm1.707 1.5A5.99 5.99 0 0 1 2 10v2c0 .14.012.275.035.404A1.001 1.001 0 0 0 2 12h11.5a.5.5 0 0 0 0-1h-.5a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h.5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1.25a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h.5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H4.5a.5.5 0 0 0-.5.5v.085a6.037 6.037 0 0 1 1.707-.585zM1 10a5 5 0 0 0 10 0h-1a4 4 0 0 1-8 0H1z"/>
                </svg>
              </span>
              <select class="form-select" id="agency_id" name="agency_id">
                <option value="">Select an agency (optional)</option>
                <?php foreach (($agencies ?? []) as $a): ?>
                <option value="<?php echo (int)$a['id']; ?>" <?php echo ((int)($data['agency_id'] ?? 0)===(int)$a['id'])?'selected':''; ?>>
                  <?php echo htmlspecialchars($a['name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          
          <?php if ($mode === 'edit'): ?>
          <div class="col-md-6">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <?php foreach ($statusOptions as $status => $color): ?>
                <option value="<?php echo $status; ?>" <?php echo (($data['status'] ?? 'Draft') === $status) ? 'selected' : ''; ?>>
                  <?php echo $status; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Location Details</h5>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="location_country" class="form-label">Country</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.69 6-10A6 6 0 0 0 2 6c0 4.31 6 10 6 10z"/>
                  <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                </svg>
              </span>
              <input type="text" class="form-control" id="location_country" name="location_country" placeholder="e.g. United States" value="<?php echo htmlspecialchars($data['location_country'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
          
          <div class="col-md-4">
            <label for="location_city" class="form-label">City</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                </svg>
              </span>
              <input type="text" class="form-control" id="location_city" name="location_city" placeholder="e.g. New York" value="<?php echo htmlspecialchars($data['location_city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
          
          <div class="col-md-4">
            <label for="location_site" class="form-label">Site/Office</label>
            <input type="text" class="form-control" id="location_site" name="location_site" placeholder="e.g. Headquarters" value="<?php echo htmlspecialchars($data['location_site'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
      </div>
      
      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Deployment Period</h5>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
              </span>
              <input type="date" class="form-control <?php echo isset($errors['start_date'])?'is-invalid':''; ?>" id="start_date" name="start_date" value="<?php echo htmlspecialchars($data['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
              <?php if(isset($errors['start_date'])): ?>
                <div class="invalid-feedback">
                  <?php echo htmlspecialchars($errors['start_date'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="col-md-4">
            <label for="end_date" class="form-label">End Date</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
              </span>
              <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($data['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-text">Leave empty for ongoing deployment</div>
          </div>
        </div>
      </div>
      
      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Compensation</h5>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="salary_amount" class="form-label">Salary Amount</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" step="0.01" min="0" class="form-control" id="salary_amount" name="salary_amount" placeholder="0.00" value="<?php echo htmlspecialchars($data['salary_amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>
          
          <div class="col-md-4">
            <label for="salary_currency" class="form-label">Currency</label>
            <select class="form-select" id="salary_currency" name="salary_currency">
              <option value="USD" <?php echo (($data['salary_currency'] ?? 'USD') === 'USD') ? 'selected' : ''; ?>>USD - US Dollar</option>
              <option value="EUR" <?php echo (($data['salary_currency'] ?? '') === 'EUR') ? 'selected' : ''; ?>>EUR - Euro</option>
              <option value="GBP" <?php echo (($data['salary_currency'] ?? '') === 'GBP') ? 'selected' : ''; ?>>GBP - British Pound</option>
              <option value="JPY" <?php echo (($data['salary_currency'] ?? '') === 'JPY') ? 'selected' : ''; ?>>JPY - Japanese Yen</option>
              <option value="CAD" <?php echo (($data['salary_currency'] ?? '') === 'CAD') ? 'selected' : ''; ?>>CAD - Canadian Dollar</option>
              <option value="AUD" <?php echo (($data['salary_currency'] ?? '') === 'AUD') ? 'selected' : ''; ?>>AUD - Australian Dollar</option>
            </select>
          </div>
          
          <div class="col-md-4">
            <label for="salary_frequency" class="form-label">Payment Frequency</label>
            <select class="form-select" id="salary_frequency" name="salary_frequency">
              <option value="hour" <?php echo (($data['salary_frequency'] ?? '') === 'hour') ? 'selected' : ''; ?>>Per Hour</option>
              <option value="day" <?php echo (($data['salary_frequency'] ?? '') === 'day') ? 'selected' : ''; ?>>Per Day</option>
              <option value="week" <?php echo (($data['salary_frequency'] ?? '') === 'week') ? 'selected' : ''; ?>>Per Week</option>
              <option value="month" <?php echo (($data['salary_frequency'] ?? 'month') === 'month') ? 'selected' : ''; ?>>Per Month</option>
              <option value="year" <?php echo (($data['salary_frequency'] ?? '') === 'year') ? 'selected' : ''; ?>>Per Year</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Work Permit</h5>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="work_permit_status" class="form-label">Work Permit Status</label>
            <?php $wps = $data['work_permit_status'] ?? 'not_applied'; ?>
            <select class="form-select" id="work_permit_status" name="work_permit_status">
              <option value="not_applied" <?php echo $wps==='not_applied'?'selected':''; ?>>Not Applied</option>
              <option value="applied" <?php echo $wps==='applied'?'selected':''; ?>>Applied</option>
              <option value="approved" <?php echo $wps==='approved'?'selected':''; ?>>Approved</option>
            </select>
          </div>
          <div class="col-md-4">
            <label for="work_permit_expiry" class="form-label">Work Permit Expiry</label>
            <input type="date" class="form-control" id="work_permit_expiry" name="work_permit_expiry" value="<?php echo htmlspecialchars($data['work_permit_expiry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
          <div class="col-md-4">
            <label for="contract_expiry" class="form-label">Contract Expiry</label>
            <input type="date" class="form-control" id="contract_expiry" name="contract_expiry" value="<?php echo htmlspecialchars($data['contract_expiry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
      </div>
      
      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Document Uploads</h5>
        <div class="row g-3">
          <div class="col-md-6">
            <label for="visa_reference" class="form-label">Visa Reference</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                  <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
              </span>
              <input type="file" class="form-control <?php echo isset($errors['visa_reference'])?'is-invalid':''; ?>" id="visa_reference" name="visa_reference" accept="image/*">
              <?php if(isset($errors['visa_reference'])): ?>
                <div class="invalid-feedback">
                  <?php echo htmlspecialchars($errors['visa_reference'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="form-text">Upload visa reference image (JPG, PNG)</div>
            <?php if ($mode === 'edit' && !empty($data['visa_reference_file'])): ?>
              <div class="mt-2">
                <small class="text-muted">Current file: </small>
                <a href="<?php echo base_url($data['visa_reference_file']); ?>" target="_blank" class="text-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path d="M8.5 6a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5V10a.5.5 0 0 0 1 0V8.5H10a.5.5 0 0 0 0-1H8.5V6z"/>
                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                  </svg>
                  View current visa reference
                </a>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="col-md-6">
            <label for="signed_contract" class="form-label">Signed Contract</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                  <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
              </span>
              <input type="file" class="form-control <?php echo isset($errors['signed_contract'])?'is-invalid':''; ?>" id="signed_contract" name="signed_contract" accept="image/*,application/pdf">
              <?php if(isset($errors['signed_contract'])): ?>
                <div class="invalid-feedback">
                  <?php echo htmlspecialchars($errors['signed_contract'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="form-text">Upload signed contract (JPG, PNG, PDF)</div>
            <?php if ($mode === 'edit' && !empty($data['signed_contract_file'])): ?>
              <div class="mt-2">
                <small class="text-muted">Current file: </small>
                <a href="<?php echo base_url($data['signed_contract_file']); ?>" target="_blank" class="text-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path d="M8.5 6a.5.5 0 0 0-1 0v1.5H6a.5.5 0 0 0 0 1h1.5V10a.5.5 0 0 0 1 0V8.5H10a.5.5 0 0 0 0-1H8.5V6z"/>
                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                  </svg>
                  View current signed contract
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <?php if ($mode === 'edit'): ?>
      <div class="mb-4">
        <h5 class="mb-3 border-bottom pb-2">Additional Information</h5>
        <div class="mb-3">
          <label for="notes" class="form-label">Notes</label>
          <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any additional notes about this deployment"><?php echo htmlspecialchars($data['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </div>
      <?php endif; ?>
      
      <div class="d-flex justify-content-between align-items-center pt-3 border-top">
        <a href="<?php echo base_url('index.php?page=deployments&action=list'); ?>" class="btn btn-outline-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
          </svg>
          Cancel
        </a>
        <button type="submit" class="btn btn-primary">
          <?php if ($mode === 'edit'): ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
            </svg>
            Update Deployment
          <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Create Deployment
          <?php endif; ?>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Form validation
(function () {
  'use strict'
  
  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')
  
  // Loop over them and prevent submission
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
