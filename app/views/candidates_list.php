<?php
$title = 'Candidates';
ob_start();
?>
<div class="hero p-4 mb-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h4 mb-1">Candidates</h1>
      <div class="text-muted small">Manage and track candidate profiles</div>
    </div>
    <div>
      <a class="btn btn-primary" href="<?php echo base_url('index.php?page=candidates&action=create'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        New Candidate
      </a>
    </div>
  </div>
</div>
<div class="card mb-4">
  <div class="card-body">
    <form class="row g-3" method="get" action="<?php echo base_url('index.php'); ?>">
      <input type="hidden" name="page" value="candidates">
      <input type="hidden" name="action" value="list">
      
      <div class="col-12 col-md-4">
        <div class="input-group">
          <span class="input-group-text bg-transparent">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
            </svg>
          </span>
          <input class="form-control" type="text" name="q" placeholder="Search by name or code..." value="<?php echo htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
      </div>
      
      <div class="col-12 col-md-3">
        <select class="form-select" name="status">
          <?php 
          $statuses = [
            '' => 'All Statuses',
            'basic_profile_created' => 'Basic Profile',
            'profile_in_progress' => 'In Progress',
            'ready_for_selection' => 'Ready for Selection',
            'shortlisted' => 'Shortlisted',
            'selected' => 'Selected',
            'deployed' => 'Deployed',
            'completed' => 'Completed'
          ]; 
          ?>
          <?php foreach ($statuses as $key => $label): ?>
            <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($status ?? '')===$key?'selected':''; ?>><?php echo $label; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="col-6 col-md-2">
        <select class="form-select" name="per">
          <option value="" disabled>Items per page</option>
          <?php foreach ([20,50,100] as $opt): ?>
            <option value="<?php echo $opt; ?>" <?php echo ((int)($per ?? 20)===$opt)?'selected':''; ?>>Show <?php echo $opt; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="col-6 col-md-3 d-flex gap-2">
        <button class="btn btn-primary flex-grow-1" type="submit">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
          </svg>
          Search
        </button>
        <a href="<?php echo base_url('index.php?page=candidates&action=list'); ?>" class="btn btn-outline-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
          Clear
        </a>
      </div>
    </form>
  </div>
</div>
<?php
// Status badge colors mapping
$statusColors = [
  'basic_profile_created' => 'bg-secondary',
  'profile_in_progress' => 'bg-info',
  'ready_for_selection' => 'bg-primary',
  'shortlisted' => 'bg-warning text-dark',
  'selected' => 'bg-success',
  'deployed' => 'bg-dark',
  'completed' => 'bg-light text-dark'
];
?>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <?php
            $mk = function($label, $key) use($q, $status, $sort, $dir, $per, $page) {
              $newDir = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
              $url = base_url('index.php?page=candidates&action=list&sort='.$key.'&dir='.$newDir.'&q='.urlencode($q).'&status='.urlencode($status).'&per='.$per.'&page='.$page);
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
          <?php $mk('Code', 'candidate_code'); ?>
          <?php $mk('Name', 'first_name'); ?>
          <?php $mk('Email', 'email'); ?>
          <?php $mk('Status', 'status'); ?>
          <?php $mk('Agency', 'agency'); ?>
          <?php $mk('Created', 'created_at'); ?>
          <th class="border-top-0 text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)): ?>
          <?php foreach ($rows as $r): ?>
          <tr class="cursor-pointer" onclick="window.location='<?php echo base_url('index.php?page=candidates&action=view&id=' . (int)$r['id']); ?>'">
            <td>
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                  <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                    <span class="text-muted fw-medium"><?php echo strtoupper(substr($r['first_name'] ?? '', 0, 1) . substr($r['last_name'] ?? '', 0, 1)); ?></span>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-medium"><?php echo htmlspecialchars($r['candidate_code'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
              </div>
            </td>
            <td class="fw-medium"><?php echo htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo !empty($r['email']) ? '<a href="mailto:'.htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8').'" class="text-decoration-none">'.htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8').'</a>' : '-'; ?></td>
            <td>
              <?php 
                $statusText = str_replace('_', ' ', $r['status']);
                $statusClass = $statusColors[$r['status']] ?? 'bg-secondary';
                echo '<span class="badge '.$statusClass.' text-capitalize">'.htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8').'</span>';
              ?>
            </td>
            <td><?php echo !empty($r['agency']) ? htmlspecialchars($r['agency'], ENT_QUOTES, 'UTF-8') : '<span class="text-muted">-</span>'; ?></td>
            <td>
              <div class="text-muted small">
                <?php 
                  $date = !empty($r['created_at']) ? new DateTime($r['created_at']) : null;
                  echo $date ? $date->format('M d, Y') : '-';
                ?>
              </div>
            </td>
            <td class="text-end">
              <div class="btn-group">
                <a href="<?php echo base_url('index.php?page=candidates&action=view&id=' . (int)$r['id']); ?>" class="btn btn-sm btn-outline-primary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                  </svg>
                  <span class="d-none d-md-inline ms-1">View</span>
                </a>
                <a href="<?php echo base_url('index.php?page=candidates&action=edit&id=' . (int)$r['id']); ?>" class="btn btn-sm btn-outline-secondary">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                  </svg>
                  <span class="d-none d-md-inline ms-1">Edit</span>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center py-5">
              <div class="py-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="text-muted mb-3" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                  <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                <h5 class="mb-2">No candidates found</h5>
                <p class="text-muted mb-0">Try adjusting your search or create a new candidate</p>
                <a href="<?php echo base_url('index.php?page=candidates&action=create'); ?>" class="btn btn-primary mt-3">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                  </svg>
                  Add New Candidate
                </a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php if (($pages ?? 1) > 1): ?>
<div class="d-flex justify-content-between align-items-center mt-4">
  <div class="text-muted small">
    Showing page <?php echo $page; ?> of <?php echo $pages; ?> • 
    <?php echo ($total ?? 0); ?> total <?php echo ($total == 1) ? 'record' : 'records'; ?>
  </div>
  
  <nav aria-label="Candidate pagination">
    <ul class="pagination pagination-sm mb-0">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="<?php echo base_url('index.php?page=candidates&action=list&q='.urlencode($q).'&status='.urlencode($status).'&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page='.($page-1)); ?>">
            <span aria-hidden="true">&laquo;</span>
            <span class="visually-hidden">Previous</span>
          </a>
        </li>
      <?php else: ?>
        <li class="page-item disabled">
          <span class="page-link">&laquo;</span>
        </li>
      <?php endif; ?>
      
      <?php 
      // Show first page, current page with neighbors, and last page
      $start = max(1, $page - 2);
      $end = min($pages, $page + 2);
      
      if ($start > 1) {
        echo '<li class="page-item"><a class="page-link" href="'.base_url('index.php?page=candidates&action=list&q='.urlencode($q).'&status='.urlencode($status).'&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page=1').'">1</a></li>';
        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
      }
      
      for ($p = $start; $p <= $end; $p++): 
        $link = base_url('index.php?page=candidates&action=list&q='.urlencode($q).'&status='.urlencode($status).'&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page='.$p);
      ?>
        <li class="page-item <?php echo $p==$page?'active':''; ?>">
          <a class="page-link" href="<?php echo $link; ?>"><?php echo $p; ?></a>
        </li>
      <?php 
      endfor;
      
      if ($end < $pages) {
        if ($end < $pages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        echo '<li class="page-item"><a class="page-link" href="'.base_url('index.php?page=candidates&action=list&q='.urlencode($q).'&status='.urlencode($status).'&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page='.$pages).'">'.$pages.'</a></li>';
      }
      ?>
      
      <?php if ($page < $pages): ?>
        <li class="page-item">
          <a class="page-link" href="<?php echo base_url('index.php?page=candidates&action=list&q='.urlencode($q).'&status='.urlencode($status).'&sort='.$sort.'&dir='.$dir.'&per='.$per.'&page='.($page+1)); ?>">
            <span aria-hidden="true">&raquo;</span>
            <span class="visually-hidden">Next</span>
          </a>
        </li>
      <?php else: ?>
        <li class="page-item disabled">
          <span class="page-link">&raquo;</span>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
</div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
