<?php
$title = 'Reports';
ob_start();
?>
<h1 class="h4 mb-3">Reports</h1>
<div class="card mb-3">
  <div class="card-header">Candidate Master List (CSV)</div>
  <div class="card-body">
    <form class="row gy-2 gx-2" method="get" action="<?php echo base_url('index.php'); ?>">
      <input type="hidden" name="page" value="reports">
      <input type="hidden" name="action" value="export_candidates_csv">
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <?php $statuses = ['', 'basic_profile_created','profile_in_progress','ready_for_selection','shortlisted','selected','deployed','completed']; ?>
          <?php foreach ($statuses as $st): ?>
            <option value="<?php echo htmlspecialchars($st, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $st===''?'All':$st; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Agency</label>
        <select class="form-select" name="agency_id">
          <option value="">All</option>
          <?php foreach (($agencies ?? []) as $a): ?>
            <option value="<?php echo (int)$a['id']; ?>"><?php echo htmlspecialchars($a['name'], ENT_QUOTES, 'UTF-8'); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Employer</label>
        <select class="form-select" name="employer_id">
          <option value="">All</option>
          <?php foreach (($employers ?? []) as $e): ?>
            <option value="<?php echo (int)$e['id']; ?>"><?php echo htmlspecialchars($e['name'], ENT_QUOTES, 'UTF-8'); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 align-self-end">
        <button class="btn btn-primary" type="submit">Download CSV</button>
      </div>
    </form>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">Expiring Compliance (CSV)</div>
  <div class="card-body">
    <form class="row gy-2 gx-2" method="get" action="<?php echo base_url('index.php'); ?>">
      <input type="hidden" name="page" value="reports">
      <input type="hidden" name="action" value="export_expiring_compliance_csv">
      <div class="col-md-3">
        <label class="form-label">Type</label>
        <select class="form-select" name="type">
          <option value="visa_expiry">Visa</option>
          <option value="work_permit_expiry">Work Permit</option>
          <option value="contract_expiry">Contract</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Within (days)</label>
        <input class="form-control" type="number" name="days" value="30" min="1" max="365">
      </div>
      <div class="col-md-3">
        <label class="form-label">Agency</label>
        <select class="form-select" name="agency_id">
          <option value="">All</option>
          <?php foreach (($agencies ?? []) as $a): ?>
            <option value="<?php echo (int)$a['id']; ?>"><?php echo htmlspecialchars($a['name'], ENT_QUOTES, 'UTF-8'); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 align-self-end">
        <button class="btn btn-primary" type="submit">Download CSV</button>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
