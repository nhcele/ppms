<?php
$title = 'Candidate Registration';
ob_start();
?>
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <h1 class="h4 mb-3">Create your candidate account</h1>
    <?php if (!empty($errors['general'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo base_url('index.php?page=self-auth&action=register'); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <label class="form-label">Full name</label>
        <input class="form-control <?php echo isset($errors['full_name'])?'is-invalid':''; ?>" type="text" name="full_name" value="<?php echo htmlspecialchars($data['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        <?php if(isset($errors['full_name'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['full_name'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control <?php echo isset($errors['email'])?'is-invalid':''; ?>" type="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        <?php if(isset($errors['email'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input class="form-control <?php echo isset($errors['password'])?'is-invalid':''; ?>" type="password" name="password" required>
        <?php if(isset($errors['password'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input class="form-control <?php echo isset($errors['confirm'])?'is-invalid':''; ?>" type="password" name="confirm" required>
        <?php if(isset($errors['confirm'])): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm'], ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
      </div>
      <button class="btn btn-primary w-100" type="submit">Register</button>
      <div class="text-center mt-2">
        <a href="<?php echo base_url('index.php?page=self-auth&action=login'); ?>">Already have an account? Log in</a>
      </div>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
