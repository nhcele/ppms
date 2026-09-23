<?php
$title = ($mode === 'edit' ? 'Edit Interview' : 'Schedule Interview');
ob_start();
?>
<h1 class="h4 mb-3"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
<form method="post" action="">
  <?php echo csrf_field(); ?>
  <?php if ($mode === 'create'): ?>
  <div class="mb-3">
    <label class="form-label">Candidate</label>
    <select class="form-select <?php echo isset($errors['candidate_id'])?'is-invalid':''; ?>" name="candidate_id" required>
      <option value="">-- Select candidate --</option>
      <?php foreach (($cands ?? []) as $c): ?>
      <option value="<?php echo (int)$c['id']; ?>" <?php echo ((int)($data['candidate_id'] ?? 0)===(int)$c['id'])?'selected':''; ?>><?php echo htmlspecialchars($c['first_name'].' '.$c['last_name'], ENT_QUOTES, 'UTF-8'); ?></option>
      <?php endforeach; ?>
    </select>
    <?php if(isset($errors['candidate_id'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['candidate_id'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="mb-3">
    <label class="form-label">Employer</label>
    <select class="form-select <?php echo isset($errors['employer_id'])?'is-invalid':''; ?>" name="employer_id" required>
      <option value="">-- Select employer --</option>
      <?php foreach (($emps ?? []) as $e): ?>
      <option value="<?php echo (int)$e['id']; ?>" <?php echo ((int)($data['employer_id'] ?? 0)===(int)$e['id'])?'selected':''; ?>><?php echo htmlspecialchars($e['name'], ENT_QUOTES, 'UTF-8'); ?></option>
      <?php endforeach; ?>
    </select>
    <?php if(isset($errors['employer_id'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['employer_id'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
  </div>

  <div class="mb-3">
    <label class="form-label">Job Post (optional)</label>
    <select class="form-select" name="job_post_id">
      <option value="">-- None --</option>
      <?php foreach (($jobs ?? []) as $j): ?>
      <option value="<?php echo (int)$j['id']; ?>" <?php echo ((int)($data['job_post_id'] ?? 0)===(int)$j['id'])?'selected':''; ?>><?php echo htmlspecialchars($j['title'], ENT_QUOTES, 'UTF-8'); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Scheduled at (YYYY-MM-DD HH:MM:SS)</label>
      <input class="form-control <?php echo isset($errors['scheduled_at'])?'is-invalid':''; ?>" type="text" name="scheduled_at" value="<?php echo htmlspecialchars($data['scheduled_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="2025-09-05 14:00:00" required>
      <?php if(isset($errors['scheduled_at'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['scheduled_at'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
    </div>
    <div class="col-md-3">
      <label class="form-label">Duration (mins)</label>
      <input class="form-control" type="number" min="10" max="240" name="duration_mins" value="<?php echo (int)($data['duration_mins'] ?? 30); ?>">
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-md-6">
      <label class="form-label">Location (optional)</label>
      <input class="form-control <?php echo isset($errors['location'])?'is-invalid':''; ?>" type="text" name="location" value="<?php echo htmlspecialchars($data['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label">Meeting Link (optional)</label>
      <input class="form-control" type="url" name="meeting_link" value="<?php echo htmlspecialchars($data['meeting_link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
  </div>

  <div class="mb-3 mt-3">
    <label class="form-label">Notes</label>
    <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($data['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
  </div>

  <?php if ($mode === 'create'): ?>
  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="notify" value="1" id="notifyCheck" <?php echo ($data['notify'] ?? '0')==='1'?'checked':''; ?>>
    <label class="form-check-label" for="notifyCheck">Notify candidate by email</label>
  </div>
  <?php endif; ?>

  <div class="mt-2">
    <button class="btn btn-primary" type="submit"><?php echo $mode==='edit'?'Save Changes':'Create Interview'; ?></button>
    <a class="btn btn-outline-secondary" href="<?php echo base_url('index.php?page=interviews&action=list'); ?>">Cancel</a>
  </div>
</form>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
