<?php
$title = 'Deployments';
ob_start();

// Initialize filters array if not set
$filters = $filters ?? [];

// Status badge colors
$statusColors = [
  'Active' => 'success',
  'Completed' => 'primary',
  'Terminated' => 'danger',
  'On Hold' => 'warning',
  'Pending' => 'info',
  'Draft' => 'secondary'
];
?>

<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Deployments</h1>
      <div class="text-muted small">Manage candidate deployments and assignments</div>
    </div>
    <div>
      <a class="btn btn-primary" href="<?php echo base_url('index.php?page=deployments&action=create'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        New Deployment
      </a>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body p-3">
    <form method="get" action="<?php echo base_url('index.php'); ?>" class="row g-2 align-items-center">
      <input type="hidden" name="page" value="deployments">
      <input type="hidden" name="action" value="list">
      
      <div class="col-md-3">
        <label class="visually-hidden" for="statusFilter">Status</label>
        <select class="form-select form-select-sm" id="statusFilter" name="status">
          <option value="">All Statuses</option>
          <?php foreach ($statusColors as $status => $color): ?>
            <option value="<?php echo $status; ?>" <?php echo (($_GET['status'] ?? '') === $status) ? 'selected' : ''; ?>>
              <?php echo $status; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="col-md-2">
        <label class="visually-hidden" for="perPage">Items per page</label>
        <select class="form-select form-select-sm" id="perPage" name="per">
          <?php foreach ([20, 50, 100] as $opt): ?>
            <option value="<?php echo $opt; ?>" <?php echo ((int)($per ?? 20) === $opt) ? 'selected' : ''; ?>>
              Show <?php echo $opt; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="col-md-4">
        <div class="input-group input-group-sm">
          <input type="text" class="form-control" name="search" placeholder="Search deployments..." value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <button class="btn btn-outline-secondary" type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
            </svg>
          </button>
        </div>
      </div>
      
      <div class="col-md-3 text-end">
        <a href="<?php echo base_url('index.php?page=deployments&action=list'); ?>" class="btn btn-sm btn-outline-secondary me-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
          Clear
        </a>
        <button class="btn btn-sm btn-primary" type="submit">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
          </svg>
          Apply Filters
        </button>
      </div>
    </form>
  </div>
</div>
<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <?php
              $mk = function($label, $key, $width = '') use($sort, $dir, $per, $page, $filters) {
                $newDir = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
                $url = base_url('index.php?page=deployments&action=list&sort='.$key.'&dir='.$newDir.'&per='.$per.'&page='.$page);
                
                // Preserve filters
                foreach (['status', 'search'] as $filter) {
                  if (!empty($filters[$filter])) {
                    $url .= '&' . $filter . '=' . urlencode($filters[$filter]);
                  }
                }
                
                $sortIcon = '';
                if ($sort === $key) {
                  $sortIcon = $dir === 'asc' ? 
                    '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1" viewBox="0 0 16 16"><path d="M3.5 12.5a.5.5 0 0 1-1 0v-9a.5.5 0 0 1 1 0v9zm3.5-9a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0v-6a.5.5 0 0 1 .5-.5zm3 0a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-1 0v-9a.5.5 0 0 1 .5-.5zm3 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0v-6a.5.5 0 0 1 .5-.5z"/></svg>' : 
                    '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1" viewBox="0 0 16 16"><path d="M3.5 3.5a.5.5 0 0 0-1 0v9a.5.5 0 0 0 1 0v-9zm3.5 0a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0v-6zm3 0a.5.5 0 0 0-1 0v9a.5.5 0 0 0 1 0v-9zm3 0a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0v-6z"/></svg>';
                } else {
                  $sortIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1 text-muted" viewBox="0 0 16 16"><path d="M3.5 3.5a.5.5 0 0 0-1 0v9a.5.5 0 0 0 1 0v-9zm3.5 0a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0v-6zm3 0a.5.5 0 0 0-1 0v9a.5.5 0 0 0 1 0v-9zm3 0a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0v-6z"/></svg>';
                }
                
                echo '<th class="' . ($width ? $width : '') . '"><a href="' . $url . '" class="text-decoration-none text-dark">' . 
                     htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . $sortIcon . '</a></th>';
              };
            ?>
            <?php $mk('Candidate', 'candidate', 'min-width: 180px'); ?>
            <?php $mk('Employer', 'employer', 'min-width: 150px'); ?>
            <?php $mk('Agency', 'agency', 'min-width: 150px'); ?>
            <?php $mk('Start Date', 'start_date', 'min-width: 100px'); ?>
            <?php $mk('End Date', 'end_date', 'min-width: 100px'); ?>
            <?php $mk('Status', 'status', 'min-width: 120px'); ?>
            <?php $mk('Salary', 'salary_amount', 'min-width: 120px'); ?>
            <th class="text-end" style="min-width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $r): 
              $status = $r['status'] ?? 'Draft';
              $statusClass = $statusColors[$status] ?? 'secondary';
              $startDate = !empty($r['start_date']) ? new DateTime($r['start_date']) : null;
              $endDate = !empty($r['end_date']) ? new DateTime($r['end_date']) : null;
              $today = new DateTime();
              $isActive = $startDate && $today >= $startDate && (!$endDate || $today <= $endDate);
            ?>
            <tr class="cursor-pointer" onclick="window.location='<?php echo base_url('index.php?page=deployments&action=view&id='.(int)$r['id']); ?>'">
              <td>
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-2">
                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                      <span class="text-muted fw-medium"><?php echo strtoupper(substr($r['first_name'] ?? '?', 0, 1)); ?></span>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <div class="fw-medium"><?php echo htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="text-muted small">ID: <?php echo (int)$r['id']; ?></div>
                  </div>
                </div>
              </td>
              <td><?php echo !empty($r['employer']) ? htmlspecialchars($r['employer'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?></td>
              <td><?php echo !empty($r['agency']) ? htmlspecialchars($r['agency'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?></td>
              <td>
                <?php if ($startDate): ?>
                  <div class="small"><?php echo $startDate->format('M d, Y'); ?></div>
                  <div class="text-muted small"><?php echo $startDate->diff($today)->format('%a'); ?> days <?php echo $isActive ? 'in' : 'ago'; ?></div>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($endDate): ?>
                  <div class="small"><?php echo $endDate->format('M d, Y'); ?></div>
                  <div class="text-muted small"><?php echo $endDate > $today ? 'in ' . $today->diff($endDate)->format('%a days') : $endDate->diff($today)->format('%a days ago'); ?></div>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-<?php echo $statusClass; ?> bg-opacity-10 text-<?php echo $statusClass; ?> border border-<?php echo $statusClass; ?> border-opacity-25">
                  <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
                </span>
              </td>
              <td>
                <?php if (!empty($r['salary_amount'])): ?>
                  <div class="fw-medium">
                    <?php 
                      echo htmlspecialchars(($r['salary_currency'] ?? '') . ' ' . 
                        number_format($r['salary_amount'], 2), ENT_QUOTES, 'UTF-8');
                    ?>
                  </div>
                  <div class="text-muted small">
                    <?php 
                      $frequency = $r['salary_frequency'] ?? 'month';
                      echo ucfirst($frequency) . 'ly';
                    ?>
                  </div>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <div class="btn-group">
                  <a href="<?php echo base_url('index.php?page=deployments&action=view&id='.(int)$r['id']); ?>" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                    </svg>
                    <span class="d-none d-md-inline">View</span>
                  </a>
                  <a href="<?php echo base_url('index.php?page=deployments&action=edit&id='.(int)$r['id']); ?>" class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                      <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5z"/>
                    </svg>
                    <span class="d-none d-md-inline">Edit</span>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="text-center py-5">
                <div class="py-4">
                  <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="text-muted mb-3" viewBox="0 0 16 16">
                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                    <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8 2.5a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5zm-8-2a.5.5 0 0 1 .5-.5H4a.5.5 0 0 1 0 1h-.5a.5.5 0 0 1-.5-.5z"/>
                  </svg>
                  <h5 class="mb-2">No deployments found</h5>
                  <p class="text-muted mb-0">Get started by creating a new deployment</p>
                  <a href="<?php echo base_url('index.php?page=deployments&action=create'); ?>" class="btn btn-primary mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                      <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    New Deployment
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

<?php if (($pages ?? 1) > 1): ?>
<nav class="mt-4">
  <ul class="pagination justify-content-center">
    <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="<?php echo $link = base_url('index.php?page=deployments&action=list&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page='.($page-1)); ?>">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
    <?php endif; ?>
    
    <?php 
    $start = max(1, $page - 2);
    $end = min($pages, $start + 4);
    $start = max(1, $end - 4);
    
    if ($start > 1): ?>
      <li class="page-item"><a class="page-link" href="<?php echo base_url('index.php?page=deployments&action=list&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page=1'); ?>">1</a></li>
      <?php if ($start > 2): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
      <?php endif; ?>
    <?php endif; ?>
    
    <?php for ($p = $start; $p <= $end; $p++): 
      $link = base_url('index.php?page=deployments&action=list&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page='.$p);
      
      // Preserve filters
      foreach (['status', 'search'] as $filter) {
        if (!empty($filters[$filter])) {
          $link .= '&' . $filter . '=' . urlencode($filters[$filter]);
        }
      }
    ?>
      <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
        <a class="page-link" href="<?php echo $link; ?>"><?php echo $p; ?></a>
      </li>
    <?php endfor; ?>
    
    <?php if ($end < $pages): ?>
      <?php if ($end < $pages - 1): ?>
        <li class="page-item disabled"><span class="page-link">...</span></li>
      <?php endif; ?>
      <li class="page-item"><a class="page-link" href="<?php echo base_url('index.php?page=deployments&action=list&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page='.$pages); ?>"><?php echo $pages; ?></a></li>
    <?php endif; ?>
    
    <?php if ($page < $pages): ?>
      <li class="page-item">
        <a class="page-link" href="<?php echo base_url('index.php?page=deployments&action=list&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page='.($page+1)); ?>">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    <?php endif; ?>
  </ul>
  <div class="text-center text-muted small">
    Showing <?php echo $page * $per - $per + 1; ?> to <?php echo min($page * $per, $total ?? 0); ?> of <?php echo $total ?? 0; ?> entries
  </div>
</nav>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
