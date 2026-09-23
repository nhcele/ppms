<?php
$title = 'Admin Seeds';
ob_start();
?>
<h1 class="h4 mb-3">Admin Seeds</h1>
<?php foreach (($messages ?? []) as $m): ?>
  <div class="alert alert-info"><?php echo htmlspecialchars($m, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endforeach; ?>
<form method="post" action="">
  <?php echo csrf_field(); ?>
  <div class="row g-2 mb-3">
    <div class="col-auto"><button class="btn btn-outline-primary" name="seed_agency" value="1" type="submit">Seed Default Agency</button></div>
    <div class="col-auto"><button class="btn btn-outline-primary" name="seed_employer" value="1" type="submit">Seed Default Employer</button></div>
    <div class="col-auto"><button class="btn btn-outline-primary" name="seed_job" value="1" type="submit">Seed Sample Job Post</button></div>
    <div class="col-auto"><button class="btn btn-outline-success" name="seed_candidate" value="1" type="submit">Seed Sample Candidate</button></div>
  </div>
</form>
<hr>
<ul>
  <li>Agencies: <?php echo (int)($counts['agencies'] ?? 0); ?></li>
  <li>Employers: <?php echo (int)($counts['employers'] ?? 0); ?></li>
  <li>Job Posts: <?php echo (int)($counts['job_posts'] ?? 0); ?></li>
  <li>Candidates: <?php echo (int)($counts['candidates'] ?? 0); ?></li>
</ul>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
