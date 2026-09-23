<?php
$title = 'Questionnaire Request: ' . htmlspecialchars($request['request_code']);
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Questionnaire Request: <?= htmlspecialchars($request['request_code']) ?></h1>
      <div class="text-muted small">View and manage questionnaire details</div>
    </div>
    <div>
      <a href="<?php echo base_url('index.php?page=questionnaire'); ?>" class="btn btn-secondary">Back to List</a>
      <?php if ($request['status'] === 'link_created' || $request['status'] === 'link_sent'): ?>
        <a href="<?php echo base_url('index.php?page=questionnaire&action=send-link&id=' . $request['id']); ?>" class="btn btn-info">Send Link</a>
        <a href="<?php echo base_url('index.php?page=questionnaire&action=regenerate-link&id=' . $request['id']); ?>" class="btn btn-warning">Regenerate Link</a>
      <?php endif; ?>
      <?php if ($request['status'] === 'submitted'): ?>
        <a href="<?php echo base_url('index.php?page=questionnaire&action=request-correction&id=' . $request['id']); ?>" class="btn btn-warning">Request Correction</a>
        <form method="post" action="<?php echo base_url('index.php?page=questionnaire&action=approve&id=' . $request['id']); ?>" class="d-inline" onsubmit="return confirm('Approve this questionnaire?');">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-success">Approve</button>
        </form>
      <?php endif; ?>
      <?php if (!in_array($request['status'], ['submitted', 'completed', 'revoked'])): ?>
        <form method="post" action="<?php echo base_url('index.php?page=questionnaire&action=revoke&id=' . $request['id']); ?>" class="d-inline" onsubmit="return confirm('Revoke this questionnaire link? The candidate will no longer be able to access it.');">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-danger">Revoke Link</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Status and Basic Info -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Request Details</h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <p><strong>Status:</strong> 
          <span class="badge bg-<?= get_status_badge_class($request['status']) ?>">
            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $request['status']))) ?>
          </span>
        </p>
        <p><strong>Position:</strong> <?= htmlspecialchars($request['position']) ?></p>
        <p><strong>Recruitment Destination:</strong> <?= htmlspecialchars($request['recruitment_destination']) ?></p>
        <p><strong>Expiry Time:</strong> <?= htmlspecialchars($request['expiry_time']) ?></p>
      </div>
      <div class="col-md-6">
        <p><strong>Candidate:</strong> <?= htmlspecialchars($request['candidate_name'] ?? 'Not specified') ?></p>
        <p><strong>Created By:</strong> <?= htmlspecialchars($request['created_by_user']) ?></p>
        <p><strong>Created At:</strong> <?= htmlspecialchars($request['created_at']) ?></p>
        <?php if ($request['submitted_at']): ?>
          <p><strong>Submitted At:</strong> <?= htmlspecialchars($request['submitted_at']) ?></p>
        <?php endif; ?>
      </div>
    </div>
    
    <?php if ($request['instructions']): ?>
      <div class="mt-3">
        <strong>Instructions:</strong>
        <p><?= htmlspecialchars($request['instructions']) ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Questionnaire Link -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Questionnaire Link</h5>
  </div>
  <div class="card-body">
    <div class="input-group">
      <input type="text" class="form-control" value="<?= htmlspecialchars($questionnaire_link) ?>" readonly>
      <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">Copy Link</button>
      </div>
    </div>
    <small class="form-text">
      This link is valid until <?= htmlspecialchars($request['expiry_time']) ?>. 
      Only the candidate should use this link.
    </small>
  </div>
</div>

<!-- Requirements Status -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Requirements Status</h5>
  </div>
  <div class="card-body">
    <?php
    $missingRequired = [];
    foreach ($requirements as $req) {
      if (!$req['is_required']) continue;
      $isComplete = false;
      if ($req['document_type']) {
        foreach ($documents as $doc) {
          if ($doc['document_type'] === $req['document_type'] && $doc['is_active']) {
            $isComplete = true;
            break;
          }
        }
      } elseif ($req['field_name']) {
        foreach ($responses as $resp) {
          if ($resp['field_name'] === $req['field_name'] && !empty($resp['field_value'])) {
            $isComplete = true;
            break;
          }
        }
      }
      if (!$isComplete) {
        $missingRequired[] = $req['document_type'] ? ucfirst(str_replace('_', ' ', $req['document_type'])) : ucfirst(str_replace('_', ' ', $req['field_name']));
      }
    }
    ?>
    
    <?php if (!empty($missingRequired)): ?>
      <div class="alert alert-warning">
        <strong>Missing Required Items:</strong> <?= htmlspecialchars(implode(', ', $missingRequired)) ?>
      </div>
    <?php elseif (!empty($requirements)): ?>
      <div class="alert alert-success">
        <strong>All required items have been received.</strong>
      </div>
    <?php endif; ?>
    
    <?php if (empty($requirements)): ?>
      <p class="text-muted">No requirements configured for this questionnaire.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Type</th>
              <th>Document/Field</th>
              <th>Required</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requirements as $req): ?>
              <tr>
                <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $req['requirement_type']))) ?></td>
                <td>
                  <?php if ($req['document_type']): ?>
                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $req['document_type']))) ?>
                  <?php elseif ($req['field_name']): ?>
                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $req['field_name']))) ?>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($req['is_required']): ?>
                    <span class="badge bg-danger">Required</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Optional</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php 
                  $status = 'pending';
                  if ($req['document_type']) {
                    $docExists = false;
                    foreach ($documents as $doc) {
                      if ($doc['document_type'] === $req['document_type'] && $doc['is_active']) {
                        $docExists = true;
                        break;
                      }
                    }
                    $status = $docExists ? 'completed' : 'pending';
                  } elseif ($req['field_name']) {
                    $fieldExists = false;
                    foreach ($responses as $resp) {
                      if ($resp['field_name'] === $req['field_name'] && !empty($resp['field_value'])) {
                        $fieldExists = true;
                        break;
                      }
                    }
                    $status = $fieldExists ? 'completed' : 'pending';
                  }
                  ?>
                  <?php if ($status === 'completed'): ?>
                    <span class="badge bg-success">Completed</span>
                  <?php else: ?>
                    <span class="badge bg-warning">Pending</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function copyLink() {
    const linkInput = document.querySelector('input[type="text"]');
    linkInput.select();
    document.execCommand('copy');
    alert('Link copied to clipboard!');
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
