<?php
$title = 'Candidate Login';
ob_start();
?>
<div class="row justify-content-center">
  <div class="col-md-4">
    <h1 class="h4 mb-3">Candidate Login</h1>
    <?php if ($m = flash('success')): ?><div class="alert alert-success"><?php echo htmlspecialchars($m, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
          <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" action="<?php echo base_url('index.php?page=self-auth&action=login'); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required>
      </div>
      <button class="btn btn-primary w-100" type="submit">Log in</button>
      <div class="d-flex justify-content-between mt-2">
        <a href="<?php echo base_url('index.php?page=self-auth&action=register'); ?>">Register</a>
        <a href="<?php echo base_url('index.php?page=self-auth&action=forgot'); ?>">Forgot password?</a>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
