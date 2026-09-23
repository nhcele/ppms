<?php
$title = 'Questionnaire Requests';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Questionnaire Requests</h1>
      <div class="text-muted small">Manage candidate questionnaire requests</div>
    </div>
    <div>
      <a class="btn btn-primary" href="<?php echo base_url('index.php?page=questionnaire&action=create'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Create New Request
      </a>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-4">
  <div class="card-body">
    <form method="get" action="<?php echo base_url('index.php?page=questionnaire'); ?>">
      <div class="row">
        <div class="col-md-3">
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="">All Statuses</option>
              <option value="link_created" <?= $status === 'link_created' ? 'selected' : '' ?>>Link Created</option>
              <option value="link_sent" <?= $status === 'link_sent' ? 'selected' : '' ?>>Link Sent</option>
              <option value="opened" <?= $status === 'opened' ? 'selected' : '' ?>>Opened</option>
              <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
              <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Submitted</option>
              <option value="under_review" <?= $status === 'under_review' ? 'selected' : '' ?>>Under Review</option>
              <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
              <option value="correction_required" <?= $status === 'correction_required' ? 'selected' : '' ?>>Correction Required</option>
              <option value="expired" <?= $status === 'expired' ? 'selected' : '' ?>>Expired</option>
              <option value="revoked" <?= $status === 'revoked' ? 'selected' : '' ?>>Revoked</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label for="destination" class="form-label">Recruitment Destination</label>
            <select class="form-select" id="destination" name="destination">
              <option value="">All Destinations</option>
              <option value="Lithuania" <?= $destination === 'Lithuania' ? 'selected' : '' ?>>Lithuania</option>
              <option value="Turkey" <?= $destination === 'Turkey' ? 'selected' : '' ?>>Turkey</option>
              <option value="Generic" <?= $destination === 'Generic' ? 'selected' : '' ?>>Generic/Other</option>
            </select>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="<?php echo base_url('index.php?page=questionnaire'); ?>" class="btn btn-secondary">Clear</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Questionnaire List -->
<div class="card">
  <div class="card-body">
    <?php if (empty($rows)): ?>
      <p class="text-muted">No questionnaire requests found.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Request Code</th>
              <th>Position</th>
              <th>Destination</th>
              <th>Candidate</th>
              <th>Status</th>
              <th>Missing Required</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['request_code']) ?></td>
                <td><?= htmlspecialchars($row['position']) ?></td>
                <td><?= htmlspecialchars($row['recruitment_destination']) ?></td>
                <td><?= htmlspecialchars($row['candidate_name'] ?? 'Not specified') ?></td>
                <td>
                  <span class="badge bg-<?= get_status_badge_class($row['status']) ?>">
                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $row['status']))) ?>
                  </span>
                </td>
                <td>
                  <?php 
                  $missing = (int)($row['missing_required_documents'] ?? 0) + (int)($row['missing_required_fields'] ?? 0);
                  if ($missing > 0): 
                  ?>
                    <span class="badge bg-warning text-dark" title="<?= $missing ?> missing required items"><?= $missing ?></span>
                  <?php else: ?>
                    <span class="badge bg-success">0</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                  <a href="<?php echo base_url('index.php?page=questionnaire&action=view&id=' . $row['id']); ?>" 
                     class="btn btn-sm btn-primary">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <?php if ($pages > 1): ?>
        <nav>
          <ul class="pagination">
            <?php if ($page > 1): ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo base_url('index.php?page=questionnaire&page=' . ($page - 1) . '&status=' . urlencode($status) . '&destination=' . urlencode($destination)); ?>">Previous</a>
              </li>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $pages; $i++): ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="<?php echo base_url('index.php?page=questionnaire&page=' . $i . '&status=' . urlencode($status) . '&destination=' . urlencode($destination)); ?>">
                  <?= $i ?>
                </a>
              </li>
            <?php endfor; ?>
            
            <?php if ($page < $pages): ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo base_url('index.php?page=questionnaire&page=' . ($page + 1) . '&status=' . urlencode($status) . '&destination=' . urlencode($destination)); ?>">Next</a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
