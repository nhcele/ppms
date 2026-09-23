<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire Submission Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h2 class="mb-0">Submission Error</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Unable to Submit Questionnaire</h4>
                            <p><?= htmlspecialchars($error) ?></p>
                        </div>
                        
                        <?php if (!empty($missing_items)): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0">Missing Required Information</h5>
                                </div>
                                <div class="card-body">
                                    <p>Please complete the following required items before submitting:</p>
                                    <ul>
                                        <?php foreach ($missing_items as $item): ?>
                                            <li><?= htmlspecialchars($item) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center">
                            <a href="<?= base_url('index.php?page=public-questionnaire&action=start&token=' . urlencode($token)) ?>" 
                               class="btn btn-primary">Return to Questionnaire</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>