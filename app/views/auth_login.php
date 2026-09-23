<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($title ?? 'PPMS', ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body, html {
      background-color: #ffffff !important;
      margin: 0;
      padding: 0;
      height: 100%;
    }
    .container-fluid, .row, .col-md-6 {
      background-color: #ffffff !important;
    }
    .min-vh-100 {
      min-height: 100vh;
    }
    .login-container {
      height: 100vh;
      display: flex;
      align-items: center;
    }
  </style>
</head>
<body>
<div class="container-fluid p-0">
  <div class="row g-0 min-vh-100 login-container">
    <!-- Left Column with Logo -->
    <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center" style="background-color: #ffffff;">
      <div class="text-center p-5">
        <img src="<?php echo base_url('images/logo.png'); ?>" alt="PPMS" style="max-height: 120px; width: auto; display: block; margin: 0 auto;">
        <h1 class="h3 mt-4">Welcome to PPMS</h1>
        <p class="text-muted">Professional Placement Management System</p>
      </div>
    </div>
    
    <!-- Right Column with Login Form -->
    <div class="col-md-6 d-flex align-items-center justify-content-center p-4">
      <div class="w-100" style="max-width: 400px;">
        <div class="text-center mb-4 d-md-none">
          <img src="<?php echo base_url('images/logo.png'); ?>" alt="PPMS" style="height: 60px; width: auto;" class="mb-3">
        </div>
        <h2 class="h4 mb-4">Sign In</h2>
        
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $e): ?>
              <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo base_url('index.php?page=auth&action=login'); ?>" class="needs-validation" novalidate>
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                </svg>
              </span>
              <input type="text" class="form-control" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars(old('username'), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
          </div>
          
          <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                </svg>
              </span>
              <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
            </div>
          </div>
          
          <div class="d-grid gap-2">
            <button class="btn btn-primary btn-lg" type="submit">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0v-2z"/>
                <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L6.707 7.5H15a.5.5 0 0 1 0 1H6.707l1.147 1.146a.5.5 0 0 1-.708.708l-3-3z"/>
              </svg>
              Sign In
            </button>
          </div>
          
          <div class="text-center mt-3">
            <a href="#" class="text-decoration-none small">Forgot password?</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
body, html {
  background-color: #ffffff !important;
  height: 100%;
}
.container-fluid, .row, .col-md-6 {
  background-color: #ffffff !important;
}
.min-vh-100 {
  min-height: 100vh;
  background-color: #ffffff !important;
}
</style>

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
</body>
</html>
<?php
echo ob_get_clean();
