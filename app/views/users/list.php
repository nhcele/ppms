<div class="hero p-4 mb-3 bg-light rounded-3">
  <div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold">User Management</h1>
        <p class="col-md-8 fs-5 text-muted">Manage system users and their permissions</p>
      </div>
      <div>
        <a href="<?php echo base_url('index.php?page=users&action=add'); ?>" class="btn btn-primary btn-lg">
          <i class="bi bi-plus-lg me-1"></i> New User
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <?php
          $mk = function($label, $key) use($q, $role, $status, $sort, $dir, $per, $page) {
            $newDir = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
            $url = base_url('index.php?page=users&action=list&sort='.$key.'&dir='.$newDir.'&q='.urlencode($q).'&role='.urlencode($role).'&status='.urlencode($status).'&per='.$per.'&page='.$page);
            $icon = '';
            
            if ($sort === $key) {
              $icon = $dir === 'asc' 
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                    <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
                  </svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                    <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                  </svg>';
            } else {
              $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1 text-muted" viewBox="0 0 16 16">
                        <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                      </svg>';
            }
            
            echo '<th class="border-top-0">
                    <a href="'.$url.'" class="text-decoration-none text-dark d-flex align-items-center">
                      <span>'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</span>
                      <span class="d-inline-flex flex-column">'.$icon.'</span>
                    </a>
                  </th>';
          };
          ?>
          <?php $mk('Name', 'full_name'); ?>
          <?php $mk('Username', 'username'); ?>
          <?php $mk('Email', 'email'); ?>
          <?php $mk('Role', 'role'); ?>
          <?php $mk('Status', 'is_active'); ?>
          <th class="border-top-0 text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr>
            <td colspan="6" class="text-center py-5">
              <div class="py-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="text-muted mb-3" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                  <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                <h5 class="mb-2">No users found</h5>
                <p class="text-muted mb-0">Get started by adding a new user</p>
                <a href="<?php echo base_url('index.php?page=users&action=add'); ?>" class="btn btn-primary mt-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                  </svg>
                  Add New User
                </a>
              </div>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($rows as $user): ?>
          <tr class="cursor-pointer" onclick="window.location='<?php echo base_url('index.php?page=users&action=edit&id=' . (int)$user['id']); ?>'">
            <td class="align-middle">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                  <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                    <span class="text-muted fw-medium"><?php echo strtoupper(substr($user['full_name'] ?? '', 0, 1) . substr($user['username'] ?? '', 0, 1)); ?></span>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-medium"><?php echo htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
              </div>
            </td>
            <td class="align-middle"><?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            <td class="align-middle">
              <?php if (!empty($user['email'])): ?>
                <a href="mailto:<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                  <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td class="align-middle">
              <?php 
              $roleClasses = [
                'admin' => 'bg-primary',
                'recruiter' => 'bg-info text-dark',
                'employer' => 'bg-warning text-dark',
                'candidate' => 'bg-secondary'
              ];
              $roleClass = $roleClasses[strtolower($user['role'])] ?? 'bg-secondary';
              ?>
              <span class="badge <?php echo $roleClass; ?>">
                <?php echo ucfirst($user['role']); ?>
              </span>
            </td>
            <td class="align-middle">
              <?php if ($user['is_active']): ?>
                <span class="badge bg-success">Active</span>
              <?php else: ?>
                <span class="badge bg-danger">Inactive</span>
              <?php endif; ?>
            </td>
            <td class="text-end align-middle">
              <div class="btn-group">
                <a href="<?php echo base_url('index.php?page=users&action=edit&id=' . (int)$user['id']); ?>" class="btn btn-sm btn-outline-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                  </svg>
                  <span class="d-none d-md-inline ms-1">Edit</span>
                </a>
                <button type="button" 
                        class="btn btn-sm btn-outline-secondary" 
                        title="Reset Password"
                        onclick="event.stopPropagation(); resetPassword(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars(addslashes($user['full_name']), ENT_QUOTES, 'UTF-8'); ?>')">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                  </svg>
                  <span class="d-none d-md-inline ms-1">Reset</span>
                </button>
                <?php if ($user['id'] != current_user()['id']): ?>
                  <button type="button" 
                          class="btn btn-sm btn-outline-danger" 
                          title="Delete"
                          onclick="event.stopPropagation(); confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars(addslashes($user['full_name']), ENT_QUOTES, 'UTF-8'); ?>')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                      <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                    <span class="d-none d-md-inline ms-1">Delete</span>
                  </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if (($pages ?? 1) > 1): ?>
<div class="d-flex justify-content-between align-items-center mt-4">
  <div class="text-muted small">
    Showing page <?php echo $page; ?> of <?php echo $pages; ?> • 
    <?php echo $total ?? 0; ?> total users
  </div>
  
  <nav aria-label="Page navigation">
    <ul class="pagination pagination-sm mb-0">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="<?php echo base_url('index.php?page=users&action=list&sort='.urlencode($sort).'&dir='.urlencode($dir).'&q='.urlencode($q).'&role='.urlencode($role).'&status='.urlencode($status).'&per='.$per.'&page='.($page-1)); ?>">
            Previous
          </a>
        </li>
      <?php else: ?>
        <li class="page-item disabled">
          <span class="page-link">Previous</span>
        </li>
      <?php endif; ?>
      
      <?php
      $startPage = max(1, $page - 2);
      $endPage = min($pages, $page + 2);
      
      if ($startPage > 1) {
          echo '<li class="page-item"><a class="page-link" href="'.base_url('index.php?page=users&action=list&sort='.urlencode($sort).'&dir='.urlencode($dir).'&q='.urlencode($q).'&role='.urlencode($role).'&status='.urlencode($status).'&per='.$per.'&page=1').'">1</a></li>';
          if ($startPage > 2) {
              echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
          }
      }
      
      for ($i = $startPage; $i <= $endPage; $i++): ?>
        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
          <a class="page-link" href="<?php echo base_url('index.php?page=users&action=list&sort='.urlencode($sort).'&dir='.urlencode($dir).'&q='.urlencode($q).'&role='.urlencode($role).'&status='.urlencode($status).'&per='.$per.'&page='.$i); ?>">
            <?php echo $i; ?>
          </a>
        </li>
      <?php endfor; ?>
      
      <?php if ($endPage < $pages): ?>
          <?php if ($endPage < $pages - 1): ?>
              <li class="page-item disabled"><span class="page-link">...</span></li>
          <?php endif; ?>
          <li class="page-item">
              <a class="page-link" href="<?php echo base_url('index.php?page=users&action=list&sort='.urlencode($sort).'&dir='.urlencode($dir).'&q='.urlencode($q).'&role='.urlencode($role).'&status='.urlencode($status).'&per='.$per.'&page='.$pages); ?>">
                  <?php echo $pages; ?>
              </a>
          </li>
      <?php endif; ?>
      
      <?php if ($page < $pages): ?>
        <li class="page-item">
          <a class="page-link" href="<?php echo base_url('index.php?page=users&action=list&sort='.urlencode($sort).'&dir='.urlencode($dir).'&q='.urlencode($q).'&role='.urlencode($role).'&status='.urlencode($status).'&per='.$per.'&page='.($page+1)); ?>">
            Next
          </a>
        </li>
      <?php else: ?>
        <li class="page-item disabled">
          <span class="page-link">Next</span>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
  
  <div class="d-flex align-items-center">
    <span class="text-muted small me-2">Items per page:</span>
    <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
      <?php foreach ([20, 50, 100] as $opt): ?>
        <option value="<?php echo base_url('index.php?page=users&action=list&sort='.urlencode($sort).'&dir='.urlencode($dir).'&q='.urlencode($q).'&role='.urlencode($role).'&status='.urlencode($status).'&per='.$opt.'&page=1'); ?>" <?php echo $per==$opt?'selected':''; ?>>
          <?php echo $opt; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Confirmation Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset the password for <strong id="resetPasswordUserName"></strong>?</p>
                <p class="text-muted">A new random password will be generated and should be changed on first login.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="resetPasswordForm" method="post" style="display: inline;">
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId, userName) {
    event.stopPropagation();
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteForm').action = '<?php echo base_url('index.php?page=users&action=delete&id='); ?>' + userId;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function resetPassword(userId, userName) {
    event.stopPropagation();
    document.getElementById('resetPasswordUserName').textContent = userName;
    document.getElementById('resetPasswordForm').action = '<?php echo base_url('index.php?page=users&action=reset-password&id='); ?>' + userId;
    
    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    modal.show();
}
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
