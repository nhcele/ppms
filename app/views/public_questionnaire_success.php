<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questionnaire Submitted Successfully</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h2 class="mb-0">Questionnaire Submitted Successfully</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Thank You!</h4>
                            <p>Your questionnaire has been successfully submitted. Our recruitment team will review your information and contact you if any additional details are needed.</p>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Submission Details</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Request Code:</strong> <?= htmlspecialchars($request['request_code']) ?></p>
                                <p><strong>Position:</strong> <?= htmlspecialchars($request['position']) ?></p>
                                <p><strong>Recruitment Destination:</strong> <?= htmlspecialchars($request['recruitment_destination']) ?></p>
                                <p><strong>Submitted At:</strong> <?= htmlspecialchars($request['submitted_at']) ?></p>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Next Steps:</strong>
                            <ul>
                                <li>Your information will be reviewed by our recruitment team</li>
                                <li>If any corrections are needed, you will be contacted via the information provided</li>
                                <li>Please keep your contact information updated</li>
                                <li>You may be contacted for an interview based on your qualifications</li>
                            </ul>
                        </div>
                        
                        <div class="text-center">
                            <p>This window can be closed safely. Your submission has been recorded.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>