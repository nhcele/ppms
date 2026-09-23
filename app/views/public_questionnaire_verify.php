<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Identity</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 40px; }
        .verify-container { max-width: 500px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container verify-container">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Verify Your Identity</h4>
                <p class="text-muted text-center">Before accessing your questionnaire, please confirm your phone number and date of birth.</p>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="post" action="<?= base_url('index.php?page=public-questionnaire&action=verify&token=' . urlencode($token)) ?>">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required placeholder="Enter your registered phone number">
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Verify & Continue</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>