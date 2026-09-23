<?php
$title = 'Employer: ' . htmlspecialchars($emp['name'] ?? 'View', ENT_QUOTES, 'UTF-8');
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1"><?php echo htmlspecialchars($emp['name'] ?? 'Employer', ENT_QUOTES, 'UTF-8'); ?></h1>
      <div class="text-muted small">Employer details and usage</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="<?php echo base_url('index.php?page=employers&action=list'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Back to Employers
      </a>
      <a class="btn btn-primary" href="<?php echo base_url('index.php?page=employers&action=edit&id=' . (int)$emp['id']); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5z"/>
        </svg>
        Edit Employer
      </a>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">Profile</h5>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <tbody>
              <tr>
                <th style="width: 220px;">Name</th>
                <td><?php echo htmlspecialchars($emp['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
              <tr>
                <th>Contact Person</th>
                <td><?php echo !empty($emp['contact_person']) ? htmlspecialchars($emp['contact_person'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?></td>
              </tr>
              <tr>
                <th>Email</th>
                <td><?php echo !empty($emp['email']) ? '<a href="mailto:' . htmlspecialchars($emp['email'], ENT_QUOTES, 'UTF-8') . '" class="text-decoration-none">' . htmlspecialchars($emp['email'], ENT_QUOTES, 'UTF-8') . '</a>' : '<span class="text-muted">-</span>'; ?></td>
              </tr>
              <tr>
                <th>Phone</th>
                <td><?php echo !empty($emp['phone']) ? '<a href="tel:' . htmlspecialchars($emp['phone'], ENT_QUOTES, 'UTF-8') . '" class="text-decoration-none">' . htmlspecialchars($emp['phone'], ENT_QUOTES, 'UTF-8') . '</a>' : '<span class="text-muted">-</span>'; ?></td>
              </tr>
              <tr>
                <th>Address</th>
                <td>
                  <?php echo !empty($emp['address']) ? nl2br(htmlspecialchars($emp['address'], ENT_QUOTES, 'UTF-8')) : '<span class="text-muted">-</span>'; ?>
                </td>
              </tr>
              <tr>
                <th>Status</th>
                <td>
                  <?php $isActive = (int)($emp['is_active'] ?? 0) === 1; ?>
                  <span class="badge <?php echo $isActive ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $isActive ? 'Active' : 'Inactive'; ?></span>
                </td>
              </tr>
              <tr>
                <th>Created</th>
                <td><?php echo !empty($emp['created_at']) ? htmlspecialchars((new DateTime($emp['created_at']))->format('M d, Y H:i'), ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?></td>
              </tr>
              <tr>
                <th>Updated</th>
                <td><?php echo !empty($emp['updated_at']) ? htmlspecialchars((new DateTime($emp['updated_at']))->format('M d, Y H:i'), ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="row g-3">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0 me-2">
                <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-briefcase"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold">Deployments</div>
                <div class="text-muted small">Count linked to this employer</div>
              </div>
              <div class="ms-auto fs-5 fw-bold"><?php echo (int)$deployments_count; ?></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0 me-2">
                <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-calendar-event"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <div class="fw-bold">Interviews</div>
                <div class="text-muted small">Count linked to this employer</div>
              </div>
              <div class="ms-auto fs-5 fw-bold"><?php echo (int)$interviews_count; ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
