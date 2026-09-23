<?php
$title = 'Forgot Password';
ob_start();
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <h1 class="h4 mb-3">Reset your password</h1>
    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
          <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="post" action="<?php echo base_url('index.php?page=self-auth&action=forgot'); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required>
      </div>
      <button class="btn btn-primary" type="submit">Send reset link</button>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
