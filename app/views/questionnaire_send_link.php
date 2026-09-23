<?php
$title = 'Send Questionnaire Link';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Send Questionnaire Link</h1>
      <div class="text-muted small">Send questionnaire link to candidate</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=questionnaire&action=view&id=' . $id); ?>" class="btn btn-secondary">Back to Request</a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo base_url('index.php?page=questionnaire&action=send-link&id=' . $id); ?>">
      <?php echo csrf_field(); ?>
      
      <div class="alert alert-info">
        <strong>Note:</strong> This will update the questionnaire status to "Link Sent" and log the action. 
        You should then send the questionnaire link to the candidate via your preferred communication method.
      </div>
      
      <div class="mb-3">
        <label for="recipient_email" class="form-label">Recipient Email</label>
        <input type="email" class="form-control" id="recipient_email" name="recipient_email" placeholder="candidate@example.com">
        <div class="form-text">Enter the candidate's email address to send the link (email functionality to be implemented)</div>
      </div>
      
      <div class="mb-3">
        <label for="email_subject" class="form-label">Email Subject</label>
        <input type="text" class="form-control" id="email_subject" name="email_subject" 
               value="Complete Your Questionnaire - Recruitment Application">
      </div>
      
      <div class="mb-3">
        <label for="email_message" class="form-label">Email Message</label>
        <textarea class="form-control" id="email_message" name="email_message" rows="6">Dear Candidate,

Please complete the questionnaire using the link below. This will help us process your application more efficiently.

The questionnaire should take approximately 15-20 minutes to complete. Your progress will be automatically saved, so you can return to it later if needed.

If you have any questions, please don't hesitate to contact us.

Best regards,
Recruitment Team</textarea>
      </div>
      
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Mark as Sent</button>
        <a href="<?php echo base_url('index.php?page=questionnaire&action=view&id=' . $id); ?>" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
