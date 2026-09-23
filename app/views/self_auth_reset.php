<?php
$title = 'Reset Password';
ob_start();
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <h1 class="h4 mb-3">Choose a new password</h1>
    <?php if (!empty($errors['general'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo base_url('index.php?page=self-auth&action=reset&token=' . urlencode($token)); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label">New password</label>
        <input class="form-control <?php echo isset($errors['password'])?'is-invalid':''; ?>" type="password" name="password" required>
        <?php if(isset($errors['password'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm password</label>
        <input class="form-control <?php echo isset($errors['confirm'])?'is-invalid':''; ?>" type="password" name="confirm" required>
        <?php if(isset($errors['confirm'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
      </div>
      <button class="btn btn-primary" type="submit">Update Password</button>
      <a class="btn btn-outline-secondary" href="<?php echo base_url('index.php?page=self-auth&action=login'); ?>">Back to login</a>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
