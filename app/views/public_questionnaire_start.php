<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Application Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .save-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            display: none;
        }
        .save-indicator.saving { background-color: #ffc107; }
        .save-indicator.saved { background-color: #28a745; }
        .save-indicator.error { background-color: #dc3545; }
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .progress-bar-custom {
            height: 30px;
            font-size: 16px;
            font-weight: bold;
        }
        .section-title {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 18px;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .work-history-item, .education-item, .reference-item, .travel-item {
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        .declaration-box {
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background: #fff9e6;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">PPMS APPLICATION FORM</h2>
                        <p class="mb-0">Complete your application for <?= htmlspecialchars($request['position']) ?> - <?= htmlspecialchars($request['recruitment_destination']) ?></p>
                    </div>
                    <div class="card-body">
                        <!-- Progress Indicator -->
                        <div class="mb-4">
                            <h5>Progress</h5>
                            <div class="progress progress-bar-custom">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%">0%</div>
                            </div>
                            <small class="form-text text-muted">Your progress is automatically saved as you complete each section.</small>
                        </div>
                        
                        <?php if ($request['instructions']): ?>
                            <div class="alert alert-info">
                                <strong>Instructions:</strong>
                                <p><?= htmlspecialchars($request['instructions']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <form id="questionnaireForm" method="post" action="<?= base_url('index.php?page=public-questionnaire&action=submit') ?>" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            
                            <!-- Personal Information Section -->
                            <div class="form-section" id="section-personal">
                                <div class="section-title">PERSONAL INFORMATION</div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="full_name" class="required-field">Full Name</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['full_name'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="full_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_of_birth" class="required-field">Date of Birth</label>
                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['date_of_birth'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="date_of_birth" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="place_of_birth" class="required-field">Birth Place</label>
                                            <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['place_of_birth'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="place_of_birth" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address" class="required-field">Address</label>
                                            <input type="text" class="form-control" id="address" name="address" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['address'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="address" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile_number" class="required-field">Mobile Number</label>
                                            <input type="tel" class="form-control" id="mobile_number" name="mobile_number" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['mobile_number'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="mobile_number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="required-field">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['email'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="email" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="passport_no" class="required-field">Passport No</label>
                                            <input type="text" class="form-control" id="passport_no" name="passport_no" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['passport_no'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="passport_no" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="passport_validity" class="required-field">Passport Validity</label>
                                            <input type="date" class="form-control" id="passport_validity" name="passport_validity" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['passport_validity'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="passport_validity" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mothers_name" class="required-field">Mother's Name</label>
                                            <input type="text" class="form-control" id="mothers_name" name="mothers_name" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['mothers_name'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="mothers_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fathers_name" class="required-field">Father's Name</label>
                                            <input type="text" class="form-control" id="fathers_name" name="fathers_name" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['fathers_name'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="fathers_name" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="religion">Religion</label>
                                            <input type="text" class="form-control" id="religion" name="religion" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['religion'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="religion">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="gender" class="required-field">Gender</label>
                                            <select class="form-control" id="gender" name="gender" 
                                                    data-section="personal_info" data-field="gender" required>
                                                <option value="">-- Select --</option>
                                                <option value="male" <?= ($responses['personal_info']['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                                <option value="female" <?= ($responses['personal_info']['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                                                <option value="other" <?= ($responses['personal_info']['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="height" class="required-field">Height (cm)</label>
                                            <input type="number" class="form-control" id="height" name="height" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['height'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="height" min="50" max="250" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="weight" class="required-field">Weight (kg)</label>
                                            <input type="number" class="form-control" id="weight" name="weight" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['weight'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="weight" min="30" max="200" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="marital_status" class="required-field">Marital Status</label>
                                            <select class="form-control" id="marital_status" name="marital_status" 
                                                    data-section="personal_info" data-field="marital_status" required>
                                                <option value="">-- Select --</option>
                                                <option value="single" <?= ($responses['personal_info']['marital_status'] ?? '') === 'single' ? 'selected' : '' ?>>Single</option>
                                                <option value="married" <?= ($responses['personal_info']['marital_status'] ?? '') === 'married' ? 'selected' : '' ?>>Married</option>
                                                <option value="divorced" <?= ($responses['personal_info']['marital_status'] ?? '') === 'divorced' ? 'selected' : '' ?>>Divorced</option>
                                                <option value="widowed" <?= ($responses['personal_info']['marital_status'] ?? '') === 'widowed' ? 'selected' : '' ?>>Widowed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="drivers_license">Driver's Licence</label>
                                            <input type="text" class="form-control" id="drivers_license" name="drivers_license" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['drivers_license'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="drivers_license">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row" id="maritalDetails" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_of_marriage">Date of Marriage</label>
                                            <input type="date" class="form-control" id="date_of_marriage" name="date_of_marriage" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['date_of_marriage'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="date_of_marriage">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="spouse_name">Name of Spouse</label>
                                            <input type="text" class="form-control" id="spouse_name" name="spouse_name" 
                                                   value="<?= htmlspecialchars($responses['personal_info']['spouse_name'] ?? '') ?>" 
                                                   data-section="personal_info" data-field="spouse_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Educational Background Section -->
                            <div class="form-section" id="section-education">
                                <div class="section-title">EDUCATIONAL BACKGROUND</div>
                                
                                <div class="form-group">
                                    <label for="degree_course" class="required-field">Degree/Course</label>
                                    <input type="text" class="form-control" id="degree_course" name="degree_course" 
                                           value="<?= htmlspecialchars($responses['education_info']['degree_course'] ?? '') ?>" 
                                           data-section="education_info" data-field="degree_course" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="university_institute" class="required-field">University / Institute</label>
                                    <input type="text" class="form-control" id="university_institute" name="university_institute" 
                                           value="<?= htmlspecialchars($responses['education_info']['university_institute'] ?? '') ?>" 
                                           data-section="education_info" data-field="university_institute" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="year_of_graduation" class="required-field">Year of Graduation</label>
                                    <input type="number" class="form-control" id="year_of_graduation" name="year_of_graduation" 
                                           value="<?= htmlspecialchars($responses['education_info']['year_of_graduation'] ?? '') ?>" 
                                           data-section="education_info" data-field="year_of_graduation" min="1950" max="<?= date('Y') ?>" required>
                                </div>
                            </div>
                            
                            <!-- Positions Being Applied For -->
                            <div class="form-section" id="section-positions">
                                <div class="section-title">POSITIONS BEING APPLIED FOR</div>
                                
                                <div class="form-group">
                                    <label for="position_1">Position 1</label>
                                    <input type="text" class="form-control" id="position_1" name="position_1" 
                                           value="<?= htmlspecialchars($responses['positions']['position_1'] ?? '') ?>" 
                                           data-section="positions" data-field="position_1">
                                </div>
                                
                                <div class="form-group">
                                    <label for="position_2">Position 2</label>
                                    <input type="text" class="form-control" id="position_2" name="position_2" 
                                           value="<?= htmlspecialchars($responses['positions']['position_2'] ?? '') ?>" 
                                           data-section="positions" data-field="position_2">
                                </div>
                                
                                <div class="form-group">
                                    <label for="position_3">Position 3</label>
                                    <input type="text" class="form-control" id="position_3" name="position_3" 
                                           value="<?= htmlspecialchars($responses['positions']['position_3'] ?? '') ?>" 
                                           data-section="positions" data-field="position_3">
                                </div>
                                
                                <div class="form-group">
                                    <label for="position_4">Position 4</label>
                                    <input type="text" class="form-control" id="position_4" name="position_4" 
                                           value="<?= htmlspecialchars($responses['positions']['position_4'] ?? '') ?>" 
                                           data-section="positions" data-field="position_4">
                                </div>
                            </div>
                            
                            <!-- Previous Employment -->
                            <div class="form-section" id="section-employment">
                                <div class="section-title">PREVIOUS EMPLOYMENT</div>
                                
                                <div id="workHistoryContainer">
                                    <?php if (!empty($responses['employment_info']['work_history'])): ?>
                                        <?php $workHistory = json_decode($responses['employment_info']['work_history'], true); ?>
                                        <?php foreach ($workHistory as $index => $job): ?>
                                            <div class="work-history-item">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Company Name</label>
                                                            <input type="text" class="form-control work-company" value="<?= htmlspecialchars($job['company'] ?? '') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Position</label>
                                                            <input type="text" class="form-control work-position" value="<?= htmlspecialchars($job['position'] ?? '') ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Start Date</label>
                                                            <input type="date" class="form-control work-start" value="<?= htmlspecialchars($job['start_date'] ?? '') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>End Date</label>
                                                            <input type="date" class="form-control work-end" value="<?= htmlspecialchars($job['end_date'] ?? '') ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger remove-work-history">Remove</button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-secondary mt-2" id="addWorkHistory">Add Work Experience</button>
                            </div>
                            
                            <!-- Supporting/Personal Statement -->
                            <div class="form-section" id="section-statement">
                                <div class="section-title">SUPPORTING / PERSONAL STATEMENT</div>
                                
                                <div class="form-group">
                                    <label for="personal_statement">Please tell us why you applied for this job and why you think you are the best person for the job.</label>
                                    <textarea class="form-control" id="personal_statement" name="personal_statement" rows="5"
                                              data-section="employment_info" data-field="personal_statement"
                                              placeholder="Describe your qualifications, experience, and motivation for applying..."><?= htmlspecialchars($responses['employment_info']['personal_statement'] ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <!-- IT / Any Other Skills -->
                            <div class="form-section" id="section-skills">
                                <div class="section-title">IT / ANY OTHER SKILLS</div>
                                
                                <div class="form-group">
                                    <label for="computer_skills">If you have any computer related skills.</label>
                                    <textarea class="form-control" id="computer_skills" name="computer_skills" rows="3"
                                              data-section="additional_info" data-field="computer_skills"
                                              placeholder="List your computer skills, software proficiency, certifications, etc."><?= htmlspecialchars($responses['additional_info']['computer_skills'] ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <!-- References -->
                            <div class="form-section" id="section-references">
                                <div class="section-title">REFERENCES</div>
                                
                                <div class="form-group">
                                    <label for="reference_1">Reference 1</label>
                                    <input type="text" class="form-control" id="reference_1" name="reference_1" 
                                           value="<?= htmlspecialchars($responses['references']['reference_1'] ?? '') ?>" 
                                           data-section="references" data-field="reference_1"
                                           placeholder="Name, position, company, contact details">
                                </div>
                                
                                <div class="form-group">
                                    <label for="reference_2">Reference 2</label>
                                    <input type="text" class="form-control" id="reference_2" name="reference_2" 
                                           value="<?= htmlspecialchars($responses['references']['reference_2'] ?? '') ?>" 
                                           data-section="references" data-field="reference_2"
                                           placeholder="Name, position, company, contact details">
                                </div>
                            </div>
                            
                            <!-- Travel History -->
                            <div class="form-section" id="section-travel">
                                <div class="section-title">COUNTRIES YOU HAVE BEEN TO IN THE PAST 5 YEARS</div>
                                <small class="form-text text-muted">As per your passport.</small>
                                
                                <div id="travelContainer">
                                    <?php if (!empty($responses['travel_history']['countries'])): ?>
                                        <?php $travelHistory = json_decode($responses['travel_history']['countries'], true); ?>
                                        <?php foreach ($travelHistory as $index => $trip): ?>
                                            <div class="travel-item">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Name of Country</label>
                                                            <input type="text" class="form-control travel-country" value="<?= htmlspecialchars($trip['country'] ?? '') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Purpose of Travel</label>
                                                            <input type="text" class="form-control travel-purpose" value="<?= htmlspecialchars($trip['purpose'] ?? '') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Arrival Date</label>
                                                            <input type="date" class="form-control travel-arrival" value="<?= htmlspecialchars($trip['arrival'] ?? '') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Departure Date</label>
                                                            <input type="date" class="form-control travel-departure" value="<?= htmlspecialchars($trip['departure'] ?? '') ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger remove-travel">Remove</button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-secondary mt-2" id="addTravel">Add Country</button>
                            </div>
                            
                            <!-- Visa/Criminal History -->
                            <div class="form-section" id="section-declarations">
                                <div class="section-title">DECLARATIONS</div>
                                
                                <div class="declaration-box">
                                    <h5>Visa Denial / Entry Ban</h5>
                                    <div class="form-group">
                                        <label for="visa_denied">Have you been denied a visa or banned from entering any foreign country?</label>
                                        <select class="form-control" id="visa_denied" name="visa_denied" 
                                                data-section="declarations" data-field="visa_denied">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['declarations']['visa_denied'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['declarations']['visa_denied'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="visaDeniedDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="visa_denied_details">If yes, name the country(s):</label>
                                            <textarea class="form-control" id="visa_denied_details" name="visa_denied_details" rows="3"
                                                      data-section="declarations" data-field="visa_denied_details"><?= htmlspecialchars($responses['declarations']['visa_denied_details'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="declaration-box">
                                    <h5>Criminal Conviction</h5>
                                    <div class="form-group">
                                        <label for="criminal_conviction">Have you been convicted of any crime?</label>
                                        <select class="form-control" id="criminal_conviction" name="criminal_conviction" 
                                                data-section="declarations" data-field="criminal_conviction">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['declarations']['criminal_conviction'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['declarations']['criminal_conviction'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="criminalDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="criminal_details">If yes, when and what crime? What was the penalty(s) imposed and was it executed?</label>
                                            <textarea class="form-control" id="criminal_details" name="criminal_details" rows="3"
                                                      data-section="declarations" data-field="criminal_details"><?= htmlspecialchars($responses['declarations']['criminal_details'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Health & Substance Use Declaration -->
                            <div class="form-section" id="section-health">
                                <div class="section-title">HEALTH & SUBSTANCE USE DECLARATION</div>
                                
                                <div class="declaration-box">
                                    <div class="form-group">
                                        <label for="chronic_disease">1. Do you currently suffer from any chronic disease or long-term medical condition?</label>
                                        <select class="form-control" id="chronic_disease" name="chronic_disease" 
                                                data-section="health" data-field="chronic_disease">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['health']['chronic_disease'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['health']['chronic_disease'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="chronicDiseaseDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="chronic_disease_details">If yes, please specify:</label>
                                            <input type="text" class="form-control" id="chronic_disease_details" name="chronic_disease_details" 
                                                   value="<?= htmlspecialchars($responses['health']['chronic_disease_details'] ?? '') ?>" 
                                                   data-section="health" data-field="chronic_disease_details">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="declaration-box">
                                    <div class="form-group">
                                        <label for="infectious_disease">2. Do you have, or have you previously had, any communicable/infectious disease that may affect your ability to perform your duties?</label>
                                        <select class="form-control" id="infectious_disease" name="infectious_disease" 
                                                data-section="health" data-field="infectious_disease">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['health']['infectious_disease'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['health']['infectious_disease'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="infectiousDiseaseDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="infectious_disease_details">If yes, please specify:</label>
                                            <input type="text" class="form-control" id="infectious_disease_details" name="infectious_disease_details" 
                                                   value="<?= htmlspecialchars($responses['health']['infectious_disease_details'] ?? '') ?>" 
                                                   data-section="health" data-field="infectious_disease_details">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="declaration-box">
                                    <div class="form-group">
                                        <label for="surgery">3. Have you ever undergone any surgical operation or major medical procedure?</label>
                                        <select class="form-control" id="surgery" name="surgery" 
                                                data-section="health" data-field="surgery">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['health']['surgery'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['health']['surgery'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="surgeryDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="surgery_details">If yes, please state the operation/procedure and approximate year:</label>
                                            <input type="text" class="form-control" id="surgery_details" name="surgery_details" 
                                                   value="<?= htmlspecialchars($responses['health']['surgery_details'] ?? '') ?>" 
                                                   data-section="health" data-field="surgery_details">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="declaration-box">
                                    <div class="form-group">
                                        <label for="medication">4. Are you currently undergoing any long-term medical treatment or taking prescribed medication on a regular basis?</label>
                                        <select class="form-control" id="medication" name="medication" 
                                                data-section="health" data-field="medication">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['health']['medication'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['health']['medication'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="medicationDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="medication_details">If yes, please provide details:</label>
                                            <input type="text" class="form-control" id="medication_details" name="medication_details" 
                                                   value="<?= htmlspecialchars($responses['health']['medication_details'] ?? '') ?>" 
                                                   data-section="health" data-field="medication_details">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="declaration-box">
                                    <div class="form-group">
                                        <label for="physical_condition">5. Do you have any physical or medical condition that may affect your ability to safely perform the duties of the position applied for?</label>
                                        <select class="form-control" id="physical_condition" name="physical_condition" 
                                                data-section="health" data-field="physical_condition">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['health']['physical_condition'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['health']['physical_condition'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="physicalConditionDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="physical_condition_details">If yes, please provide details:</label>
                                            <input type="text" class="form-control" id="physical_condition_details" name="physical_condition_details" 
                                                   value="<?= htmlspecialchars($responses['health']['physical_condition_details'] ?? '') ?>" 
                                                   data-section="health" data-field="physical_condition_details">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="declaration-box">
                                    <div class="form-group">
                                        <label for="substance_use">6. Do you currently use or have you previously had a dependency on alcohol, illegal drugs, or other substances that may affect your work performance?</label>
                                        <select class="form-control" id="substance_use" name="substance_use" 
                                                data-section="health" data-field="substance_use">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['health']['substance_use'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['health']['substance_use'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="substanceUseDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="substance_use_details">If yes, please provide details:</label>
                                            <input type="text" class="form-control" id="substance_use_details" name="substance_use_details" 
                                                   value="<?= htmlspecialchars($responses['health']['substance_use_details'] ?? '') ?>" 
                                                   data-section="health" data-field="substance_use_details">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="declaration-box">
                                    <div class="form-group">
                                        <label for="treatment">7. Have you ever received treatment, counselling, rehabilitation, or other professional assistance for drug or substance abuse/dependency?</label>
                                        <select class="form-control" id="treatment" name="treatment" 
                                                data-section="health" data-field="treatment">
                                            <option value="">-- Select --</option>
                                            <option value="no" <?= ($responses['health']['treatment'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                            <option value="yes" <?= ($responses['health']['treatment'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div id="treatmentDetails" style="display: none;">
                                        <div class="form-group">
                                            <label for="treatment_details">If yes, please provide details:</label>
                                            <input type="text" class="form-control" id="treatment_details" name="treatment_details" 
                                                   value="<?= htmlspecialchars($responses['health']['treatment_details'] ?? '') ?>" 
                                                   data-section="health" data-field="treatment_details">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Destination-Specific Information -->
                            <div class="form-section" id="section-destination-specific">
                                <div class="section-title">DESTINATION-SPECIFIC INFORMATION</div>
                                
                                <div class="form-group">
                                    <label for="video_link">Video Link (YouTube/Introduction video)</label>
                                    <input type="url" class="form-control" id="video_link" name="video_link" 
                                           value="<?= htmlspecialchars($responses['additional_info']['video_link'] ?? '') ?>" 
                                           data-section="additional_info" data-field="video_link"
                                           placeholder="https://youtube.com/...">
                                </div>
                                
                                <div class="form-group">
                                    <label for="languages" class="required-field">Languages Known</label>
                                    <input type="text" class="form-control" id="languages" name="languages" 
                                           value="<?= htmlspecialchars($responses['additional_info']['languages'] ?? '') ?>" 
                                           data-section="additional_info" data-field="languages" required
                                           placeholder="e.g., English, Shona">
                                </div>
                                
                                <div class="form-group">
                                    <label for="health_condition" class="required-field">Physical Health Condition</label>
                                    <input type="text" class="form-control" id="health_condition" name="health_condition" 
                                           value="<?= htmlspecialchars($responses['additional_info']['health_condition'] ?? '') ?>" 
                                           data-section="additional_info" data-field="health_condition" required
                                           placeholder="e.g., healthy and fit">
                                </div>
                                
                                <div class="form-group">
                                    <label for="relatives_europe">Relatives or Friends in Europe</label>
                                    <select class="form-control" id="relatives_europe" name="relatives_europe" 
                                            data-section="additional_info" data-field="relatives_europe">
                                        <option value="">-- Select --</option>
                                        <option value="no" <?= ($responses['additional_info']['relatives_europe'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                        <option value="yes" <?= ($responses['additional_info']['relatives_europe'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="previous_europe_work">Previous Work Experience in Europe</label>
                                    <select class="form-control" id="previous_europe_work" name="previous_europe_work" 
                                            data-section="additional_info" data-field="previous_europe_work">
                                        <option value="">-- Select --</option>
                                        <option value="no" <?= ($responses['additional_info']['previous_europe_work'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                        <option value="yes" <?= ($responses['additional_info']['previous_europe_work'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="daily_prayers">Daily Prayers</label>
                                    <select class="form-control" id="daily_prayers" name="daily_prayers" 
                                            data-section="additional_info" data-field="daily_prayers">
                                        <option value="">-- Select --</option>
                                        <option value="no" <?= ($responses['additional_info']['daily_prayers'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                        <option value="yes" <?= ($responses['additional_info']['daily_prayers'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="work_men_women">Ability to Work with Men and Women</label>
                                    <select class="form-control" id="work_men_women" name="work_men_women" 
                                            data-section="additional_info" data-field="work_men_women">
                                        <option value="">-- Select --</option>
                                        <option value="yes" <?= ($responses['additional_info']['work_men_women'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        <option value="no" <?= ($responses['additional_info']['work_men_women'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="work_pork">Ability to Work with Pork or Meat Products (if required)</label>
                                    <select class="form-control" id="work_pork" name="work_pork" 
                                            data-section="additional_info" data-field="work_pork">
                                        <option value="">-- Select --</option>
                                        <option value="yes" <?= ($responses['additional_info']['work_pork'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        <option value="no" <?= ($responses['additional_info']['work_pork'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="wear_uniform">Readiness to Wear Work Uniform without Religious Elements (for safety reasons)</label>
                                    <select class="form-control" id="wear_uniform" name="wear_uniform" 
                                            data-section="additional_info" data-field="wear_uniform">
                                        <option value="">-- Select --</option>
                                        <option value="yes" <?= ($responses['additional_info']['wear_uniform'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        <option value="no" <?= ($responses['additional_info']['wear_uniform'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="willingness_work_conditions">Willingness to Work Conditions</label>
                                    <select class="form-control" id="willingness_work_conditions" name="willingness_work_conditions" 
                                            data-section="additional_info" data-field="willingness_work_conditions">
                                        <option value="">-- Select --</option>
                                        <option value="yes" <?= ($responses['additional_info']['willingness_work_conditions'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
                                        <option value="no" <?= ($responses['additional_info']['willingness_work_conditions'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="lithuania_address">Residence Address in Lithuania (if applicable)</label>
                                    <input type="text" class="form-control" id="lithuania_address" name="lithuania_address" 
                                           value="<?= htmlspecialchars($responses['additional_info']['lithuania_address'] ?? '') ?>" 
                                           data-section="additional_info" data-field="lithuania_address"
                                           placeholder="N/A if not applicable">
                                </div>
                                
                                <div class="form-group">
                                    <label for="former_citizenship">Former Citizenship (if had such)</label>
                                    <input type="text" class="form-control" id="former_citizenship" name="former_citizenship" 
                                           value="<?= htmlspecialchars($responses['additional_info']['former_citizenship'] ?? '') ?>" 
                                           data-section="additional_info" data-field="former_citizenship"
                                           placeholder="N/A if not applicable">
                                </div>
                                
                                <div class="form-group">
                                    <label for="visas_last_5_years">Visas/Residence Permits Issued in the Last 5 Years</label>
                                    <textarea class="form-control" id="visas_last_5_years" name="visas_last_5_years" rows="3"
                                              data-section="additional_info" data-field="visas_last_5_years"
                                              placeholder="Example: Visa - Estonia - 2018-2019, Residence permit - Italy - 2020-2021"><?= htmlspecialchars($responses['additional_info']['visas_last_5_years'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <!-- Documents Section -->
                            <div class="form-section" id="section-documents">
                                <div class="section-title">DOCUMENT UPLOAD</div>
                                
                                <?php 
                                $requiredDocs = [];
                                foreach ($request['requirements'] as $req) {
                                    if ($req['requirement_type'] === 'documents' && $req['document_type']) {
                                        $requiredDocs[$req['document_type']] = $req['is_required'];
                                    }
                                }
                                ?>
                                
                                <?php foreach ($requiredDocs as $docType => $isRequired): ?>
                                    <div class="form-group">
                                        <label class="<?= $isRequired ? 'required-field' : '' ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $docType))) ?>
                                        </label>
                                        
                                        <?php 
                                        $existingDoc = null;
                                        foreach ($documents as $doc) {
                                            if ($doc['document_type'] === $docType && $doc['is_active']) {
                                                $existingDoc = $doc;
                                                break;
                                            }
                                        }
                                        ?>
                                        
                                        <?php if ($existingDoc): ?>
                                            <div class="alert alert-success">
                                                <strong>Uploaded:</strong> <?= htmlspecialchars($existingDoc['original_name']) ?>
                                                <button type="button" class="btn btn-sm btn-danger float-right delete-document" 
                                                        data-doc-id="<?= $existingDoc['id'] ?>" data-doc-type="<?= htmlspecialchars($docType) ?>">
                                                    Delete
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <input type="file" class="form-control document-upload" 
                                                   data-document-type="<?= htmlspecialchars($docType) ?>" 
                                                   <?= $isRequired ? 'required' : '' ?>>
                                            <small class="form-text text-muted">
                                                Accepted formats: PDF, JPG, PNG, DOC, DOCX (Max 10MB)
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Final Declaration -->
                            <div class="form-section" id="section-declaration">
                                <div class="section-title">DECLARATION</div>
                                
                                <div class="declaration-box">
                                    <p>I confirm that to the best of my knowledge the information I have provided on this form is correct and I accept that providing deliberately false information could result in my dismissal.</p>
                                    
                                    <div class="form-group">
                                        <label for="declaration_name">Name:</label>
                                        <input type="text" class="form-control" id="declaration_name" name="declaration_name" 
                                               value="<?= htmlspecialchars($responses['declaration']['declaration_name'] ?? '') ?>" 
                                               data-section="declaration" data-field="declaration_name" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="declaration_date">Date:</label>
                                        <input type="date" class="form-control" id="declaration_date" name="declaration_date" 
                                               value="<?= htmlspecialchars($responses['declaration']['declaration_date'] ?? date('Y-m-d')) ?>" 
                                               data-section="declaration" data-field="declaration_date" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="declaration" name="declaration" required>
                                            <label class="form-check-label" for="declaration">
                                                I confirm the above declaration
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <button type="button" id="saveContinueBtn" class="btn btn-secondary btn-lg btn-block">
                                            Save & Continue Later
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">Submit Application</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Save Indicator -->
    <div id="saveIndicator" class="save-indicator">Saving...</div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const TOKEN = '<?= htmlspecialchars($token) ?>';
        const SELECTED_FIELDS = <?= json_encode($selectedFields ?? []) ?>;
        const REQUIRED_FIELDS = <?= json_encode($requiredFields ?? []) ?>;
        const SELECTED_DOCUMENTS = <?= json_encode($selectedDocuments ?? []) ?>;
        const REQUIRED_DOCUMENTS = <?= json_encode($requiredDocuments ?? []) ?>;
        let saveTimeout = null;
        
        // Show save indicator
        function showSaveIndicator(status, message) {
            const indicator = document.getElementById('saveIndicator');
            indicator.className = 'save-indicator ' + status;
            indicator.textContent = message;
            indicator.style.display = 'block';
            
            setTimeout(() => {
                indicator.style.display = 'none';
            }, 3000);
        }
        
        // Show/hide marital details based on marital status
        document.getElementById('marital_status').addEventListener('change', function() {
            const maritalDetails = document.getElementById('maritalDetails');
            if (this.value === 'married') {
                maritalDetails.style.display = 'block';
            } else {
                maritalDetails.style.display = 'none';
            }
        });
        
        // Show/hide declaration details based on yes/no answers
        function setupConditionalFields(selectId, detailsId) {
            const select = document.getElementById(selectId);
            const details = document.getElementById(detailsId);
            
            select.addEventListener('change', function() {
                if (this.value === 'yes') {
                    details.style.display = 'block';
                } else {
                    details.style.display = 'none';
                }
            });
            
            // Initialize on page load
            if (select.value === 'yes') {
                details.style.display = 'block';
            }
        }
        
        // Initialize conditional fields
        setupConditionalFields('visa_denied', 'visaDeniedDetails');
        setupConditionalFields('criminal_conviction', 'criminalDetails');
        setupConditionalFields('chronic_disease', 'chronicDiseaseDetails');
        setupConditionalFields('infectious_disease', 'infectiousDiseaseDetails');
        setupConditionalFields('surgery', 'surgeryDetails');
        setupConditionalFields('medication', 'medicationDetails');
        setupConditionalFields('physical_condition', 'physicalConditionDetails');
        setupConditionalFields('substance_use', 'substanceUseDetails');
        setupConditionalFields('treatment', 'treatmentDetails');
        
        // Autosave function
        function autoSave() {
            const indicator = document.getElementById('saveIndicator');
            indicator.className = 'save-indicator saving';
            indicator.textContent = 'Saving...';
            indicator.style.display = 'block';
            
            // Collect all form data
            const formData = new FormData();
            formData.append('token', TOKEN);
            
            // Save each field individually
            document.querySelectorAll('[data-section]').forEach(field => {
                const section = field.dataset.section;
                const fieldName = field.dataset.field;
                const value = field.value;
                const dataType = field.type === 'date' ? 'date' : 'text';
                
                // Send individual field save request
                fetch('<?= base_url('index.php?page=public-questionnaire&action=save') ?>', {
                    method: 'POST',
                    body: new URLSearchParams({
                        token: TOKEN,
                        section: section,
                        field_name: fieldName,
                        value: value,
                        data_type: dataType
                    })
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          console.log('Saved:', fieldName);
                      } else {
                          console.error('Save failed:', fieldName, data.error);
                      }
                  });
            });
            
            // Save work history as JSON
            const workHistory = [];
            document.querySelectorAll('.work-history-item').forEach(item => {
                workHistory.push({
                    company: item.querySelector('.work-company').value,
                    position: item.querySelector('.work-position').value,
                    start_date: item.querySelector('.work-start').value,
                    end_date: item.querySelector('.work-end').value,
                    description: item.querySelector('.work-description').value
                });
            });
            
            fetch('<?= base_url('index.php?page=public-questionnaire&action=save') ?>', {
                method: 'POST',
                body: new URLSearchParams({
                    token: TOKEN,
                    section: 'employment_info',
                    field_name: 'work_history',
                    value: JSON.stringify(workHistory),
                    data_type: 'json'
                })
            });
            
            // Save travel history as JSON
            const travelHistory = [];
            document.querySelectorAll('.travel-item').forEach(item => {
                travelHistory.push({
                    country: item.querySelector('.travel-country').value,
                    purpose: item.querySelector('.travel-purpose').value,
                    arrival: item.querySelector('.travel-arrival').value,
                    departure: item.querySelector('.travel-departure').value
                });
            });
            
            fetch('<?= base_url('index.php?page=public-questionnaire&action=save') ?>', {
                method: 'POST',
                body: new URLSearchParams({
                    token: TOKEN,
                    section: 'travel_history',
                    field_name: 'countries',
                    value: JSON.stringify(travelHistory),
                    data_type: 'json'
                })
            });
            
            setTimeout(() => {
                showSaveIndicator('saved', 'All changes saved');
                updateProgress();
            }, 1000);
        }
        
        // Update progress bar
        function updateProgress() {
            const totalFields = document.querySelectorAll('[data-section]').length;
            const filledFields = document.querySelectorAll('[data-section]').filter(field => {
                if (field.type === 'checkbox') {
                    return field.checked;
                }
                return field.value.trim() !== '';
            }).length;
            const progress = Math.round((filledFields / totalFields) * 100);
            
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressBar').textContent = progress + '%';
        }
        
        // Add work history item
        document.getElementById('addWorkHistory').addEventListener('click', function() {
            const container = document.getElementById('workHistoryContainer');
            const newItem = document.createElement('div');
            newItem.className = 'work-history-item';
            newItem.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control work-company">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Position</label>
                            <input type="text" class="form-control work-position">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control work-start">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" class="form-control work-end">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-work-history">Remove</button>
            `;
            container.appendChild(newItem);
        });
        
        // Add travel item
        document.getElementById('addTravel').addEventListener('click', function() {
            const container = document.getElementById('travelContainer');
            const newItem = document.createElement('div');
            newItem.className = 'travel-item';
            newItem.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Name of Country</label>
                            <input type="text" class="form-control travel-country">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Purpose of Travel</label>
                            <input type="text" class="form-control travel-purpose">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Arrival Date</label>
                            <input type="date" class="form-control travel-arrival">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Departure Date</label>
                            <input type="date" class="form-control travel-departure">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-travel">Remove</button>
            `;
            container.appendChild(newItem);
        });
        
        // Remove work history and travel items
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-work-history')) {
                e.target.closest('.work-history-item').remove();
            }
            if (e.target.classList.contains('remove-travel')) {
                e.target.closest('.travel-item').remove();
            }
        });
        
        // Document upload
        document.querySelectorAll('.document-upload').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const formData = new FormData();
                    formData.append('token', TOKEN);
                    formData.append('document_type', this.dataset.documentType);
                    formData.append('document', this.files[0]);
                    
                    fetch('<?= base_url('index.php?page=public-questionnaire&action=upload-document') ?>', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              location.reload();
                          } else {
                              alert('Upload failed: ' + data.error);
                          }
                      });
                }
            });
        });
        
        // Delete document
        document.querySelectorAll('.delete-document').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this document?')) {
                    const formData = new FormData();
                    formData.append('token', TOKEN);
                    formData.append('document_id', this.dataset.docId);
                    
                    fetch('<?= base_url('index.php?page=public-questionnaire&action=delete-document') ?>', {
                        method: 'POST',
                        body: formData
                    }).then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              location.reload();
                          } else {
                              alert('Delete failed: ' + data.error);
                          }
                      });
                }
            });
        });
        
        // Autosave on field change
        document.querySelectorAll('[data-section]').forEach(field => {
            field.addEventListener('change', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(autoSave, 2000);
            });
        });
        
        // Hide fields and sections that are not selected by admin
        function applyFieldVisibility() {
            if (SELECTED_FIELDS.length === 0 && SELECTED_DOCUMENTS.length === 0) {
                return; // No requirements configured; show everything
            }
            
            var selectedSet = new Set(SELECTED_FIELDS);
            var selectedDocSet = new Set(SELECTED_DOCUMENTS);
            
            // Hide unselected regular fields
            document.querySelectorAll('[data-field]').forEach(function(field) {
                var fieldName = field.getAttribute('data-field');
                if (!selectedSet.has(fieldName)) {
                    var formGroup = field.closest('.form-group');
                    if (formGroup) {
                        formGroup.style.display = 'none';
                        formGroup.setAttribute('data-visibility-hidden', 'true');
                    }
                }
            });
            
            // Hide empty sections
            document.querySelectorAll('.form-section').forEach(function(section) {
                var visibleChildren = Array.from(section.querySelectorAll('.form-group, [data-field]')).filter(function(el) {
                    if (el.hasAttribute('data-visibility-hidden')) return false;
                    if (el.classList.contains('form-group') && el.style.display === 'none') return false;
                    return true;
                });
                if (visibleChildren.length === 0) {
                    section.style.display = 'none';
                }
            });
        }
        applyFieldVisibility();

        // Apply required attributes based on configured requirements
        function applyRequiredAttributes() {
            REQUIRED_FIELDS.forEach(function(fieldName) {
                document.querySelectorAll('[data-field="' + fieldName + '"]').forEach(function(field) {
                    field.required = true;
                    var label = document.querySelector('label[for="' + field.id + '"], label[for="' + field.name + '"], [data-field-label="' + fieldName + '"]');
                    if (label && !label.classList.contains('required-field')) {
                        label.classList.add('required-field');
                    }
                });
            });
        }
        applyRequiredAttributes();

        // Save & Continue Later button
        document.getElementById('saveContinueBtn').addEventListener('click', function() {
            const button = this;
            button.disabled = true;
            button.textContent = 'Saving...';
            
            let savePromises = [];
            document.querySelectorAll('[data-section][data-field]').forEach(function(field) {
                const section = field.getAttribute('data-section');
                const fieldName = field.getAttribute('data-field');
                let value = field.value;
                if (field.type === 'checkbox') {
                    value = field.checked ? '1' : '0';
                }
                if (field.type === 'radio' && !field.checked) {
                    return;
                }
                
                savePromises.push(new Promise(function(resolve) {
                    const formData = new FormData();
                    formData.append('token', TOKEN);
                    formData.append('section', section);
                    formData.append('field_name', fieldName);
                    formData.append('value', value);
                    
                    fetch('<?= base_url("index.php?page=public-questionnaire&action=save") ?>', {
                        method: 'POST',
                        body: formData
                    }).finally(resolve);
                }));
            });
            
            Promise.all(savePromises).then(function() {
                showSaveIndicator('saved', 'Progress saved. You can close this page and return later using the same link.');
                button.disabled = false;
                button.textContent = 'Save & Continue Later';
            }).catch(function() {
                showSaveIndicator('error', 'Unable to save. Please check your connection.');
                button.disabled = false;
                button.textContent = 'Save & Continue Later';
            });
        });

        // Form submission validation
        document.getElementById('questionnaireForm').addEventListener('submit', function(e) {
            var missingFields = [];
            REQUIRED_FIELDS.forEach(function(fieldName) {
                var field = document.querySelector('[data-field="' + fieldName + '"], [name="' + fieldName + '"]');
                if (field && !field.value.trim()) {
                    missingFields.push(fieldName.replace(/_/g, ' '));
                }
            });
            REQUIRED_DOCUMENTS.forEach(function(docType) {
                var hasDoc = document.querySelector('[data-document-type="' + docType + '"], .alert-success [data-doc-type="' + docType + '"]');
                if (!hasDoc) {
                    missingFields.push('Document: ' + docType.replace(/_/g, ' '));
                }
            });
            if (missingFields.length > 0) {
                e.preventDefault();
                alert('Please complete the following required items before submitting:\n\n' + missingFields.join('\n'));
            }
        });

        // Initial progress update
        updateProgress();
        
        // Initialize marital status on page load
        document.getElementById('marital_status').dispatchEvent(new Event('change'));
    </script>
</body>
</html>