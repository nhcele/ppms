<?php
$title = 'Employers';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Employers</h1>
      <div class="text-muted small">Manage client employers</div>
    </div>
    <div>
      <a class="btn btn-primary" href="<?php echo base_url('index.php?page=employers&action=create'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        New Employer
      </a>
    </div>
  </div>
</div>
<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Employer</th>
            <th>Contact</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $r): 
              $createdDate = !empty($r['created_at']) ? new DateTime($r['created_at']) : null;
              $isActive = (int)($r['is_active'] ?? 0) === 1;
            ?>
            <tr class="cursor-pointer" onclick="window.location='<?php echo base_url('index.php?page=employers&action=view&id=' . (int)$r['id']); ?>'">
              <td class="fw-medium">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-2">
                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                      <span class="text-muted fw-medium"><?php echo strtoupper(substr($r['name'] ?? 'E', 0, 1)); ?></span>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <div class="fw-medium"><a class="text-decoration-none" href="<?php echo base_url('index.php?page=employers&action=view&id=' . (int)$r['id']); ?>"><?php echo htmlspecialchars($r['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></a></div>
                  </div>
                </div>
              </td>
              <td><?php echo !empty($r['contact_person']) ? htmlspecialchars($r['contact_person'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?></td>
              <td><?php echo !empty($r['phone']) ? '<a href="tel:' . htmlspecialchars($r['phone'], ENT_QUOTES, 'UTF-8') . '" class="text-decoration-none">' . htmlspecialchars($r['phone'], ENT_QUOTES, 'UTF-8') . '</a>' : '<span class="text-muted">-</span>'; ?></td>
              <td><?php echo !empty($r['email']) ? '<a href="mailto:' . htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8') . '" class="text-decoration-none">' . htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8') . '</a>' : '<span class="text-muted">-</span>'; ?></td>
              <td>
                <span class="badge <?php echo $isActive ? 'bg-success' : 'bg-secondary'; ?>">
                  <?php echo $isActive ? 'Active' : 'Inactive'; ?>
                </span>
              </td>
              <td class="text-end">
                <div class="btn-group">
                  <a href="<?php echo base_url('index.php?page=employers&action=edit&id=' . (int)$r['id']); ?>" class="btn btn-sm btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5z"/>
                    </svg>
                    <span class="d-none d-md-inline ms-1">Edit</span>
                  </a>
                  <form method="post" action="<?php echo base_url('index.php?page=employers&action=' . ($isActive ? 'deactivate' : 'activate')); ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to ' + (<?php echo $isActive ? '"deactivate"' : '"activate"'; ?> + ' this employer?'));">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                    <button type="submit" class="btn btn-sm <?php echo $isActive ? 'btn-outline-warning' : 'btn-outline-success'; ?>">
                      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                        <?php if ($isActive): ?>
                          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                          <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                        <?php else: ?>
                          <path d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                          <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                        <?php endif; ?>
                      </svg>
                      <span class="d-none d-md-inline ms-1"><?php echo $isActive ? 'Deactivate' : 'Activate'; ?></span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-5">
                <div class="py-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="text-muted mb-3" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                  </svg>
                  <h5 class="mb-2">No employers found</h5>
                  <p class="text-muted mb-0">Get started by adding your first employer</p>
                  <a href="<?php echo base_url('index.php?page=employers&action=create'); ?>" class="btn btn-primary mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                      <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Add New Employer
                  </a>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
