<?php
// app/views/layout.php

// Detect current page for active menu highlighting
$current_uri = $_SERVER['REQUEST_URI'] ?? '';
$current_page = 'dashboard'; // Default

// Extract page from query string if available
if (isset($_GET['page'])) {
    $current_page = $_GET['page'];
    // Remove action part if present (e.g., 'candidates&action=list' -> 'candidates')
    if (strpos($current_page, '&') !== false) {
        $current_page = strstr($current_page, '&', true);
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($title ?? 'PPMS', ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="icon" type="image/x-icon" href="<?php echo base_url('images/favicon.ico'); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Mindelta Theme: Bootstrap override layer -->
  <link rel="stylesheet" href="<?php echo base_url('css/mindelta-theme.css'); ?>">
  <!-- Removed local test override to match production theme -->
  <!-- Early theme init to avoid flash -->
  <script>(function(){try{var t=localStorage.getItem('ppms-theme');if(t==='dark'){document.documentElement.setAttribute('data-theme','dark');}}catch(e){}})();</script>
</head>
<body>
<div class="app-shell d-flex">
  <?php if (current_user()): ?>
    <!-- Sidebar (responsive offcanvas) -->
    <div id="app-sidebar" class="offcanvas-lg offcanvas-start sidebar bg-white shadow-sm" tabindex="-1" data-bs-scroll="true" aria-labelledby="sidebarLabel">
      <!-- Mobile-only close/header -->
      <div class="offcanvas-header d-lg-none border-bottom">
        <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#app-sidebar" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body p-0 d-flex flex-column" style="min-height: 100vh;">
      <!-- Logo -->
      <div class="p-4 border-bottom text-center">
        <a href="<?php echo base_url('index.php?page=dashboard'); ?>" class="d-inline-flex align-items-center text-decoration-none">
          <img src="<?php echo base_url('images/logo.png'); ?>" alt="PPMS" height="100" class="mx-auto">
        </a>
      </div>
      
      
      <!-- Navigation -->
      <div class="p-3" style="padding-bottom: 140px;">
        <?php if (current_user()['role'] !== 'candidate'): ?>
          <a href="<?php echo base_url('index.php?page=dashboard'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'dashboard') ? 'bg-light' : ''; ?>">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
          </a>
          <?php if (current_user()['role'] === 'admin'): ?>
          <a href="<?php echo base_url('index.php?page=users&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'users') ? 'bg-light' : ''; ?>">
            <i class="bi bi-people me-2"></i> User Management
          </a>
          <a href="<?php echo base_url('index.php?page=roles&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'roles') ? 'bg-light' : ''; ?>">
            <i class="bi bi-tags me-2"></i> Roles
          </a>
          <?php endif; ?>
          <a href="<?php echo base_url('index.php?page=candidates&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'candidates') ? 'bg-light' : ''; ?>">
            <i class="bi bi-people me-2"></i> Candidates
          </a>
          <a href="<?php echo base_url('index.php?page=agencies&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'agencies') ? 'bg-light' : ''; ?>">
            <i class="bi bi-building me-2"></i> Agencies
          </a>
          <a href="<?php echo base_url('index.php?page=employers&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'employers') ? 'bg-light' : ''; ?>">
            <i class="bi bi-building-gear me-2"></i> Employers
          </a>
          <a href="<?php echo base_url('index.php?page=deployments&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'deployments') ? 'bg-light' : ''; ?>">
            <i class="bi bi-briefcase me-2"></i> Deployments
          </a>
          <a href="<?php echo base_url('index.php?page=interviews&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'interviews') ? 'bg-light' : ''; ?>">
            <i class="bi bi-calendar-event me-2"></i> Interviews
          </a>
          <a href="<?php echo base_url('index.php?page=alerts&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'alerts') ? 'bg-light' : ''; ?>">
            <i class="bi bi-bell me-2"></i> Alerts
          </a>
          <a href="<?php echo base_url('index.php?page=reports&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'reports') ? 'bg-light' : ''; ?>">
            <i class="bi bi-graph-up me-2"></i> Reports
          </a>
          <a href="<?php echo base_url('index.php?page=questionnaire'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'questionnaire') ? 'bg-light' : ''; ?>">
            <i class="bi bi-file-earmark-text me-2"></i> Questionnaires
          </a>
          <a href="<?php echo base_url('index.php?page=templates'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1 <?php echo ($current_page === 'templates') ? 'bg-light' : ''; ?>">
            <i class="bi bi-file-earmark-code me-2"></i> Templates
          </a>
        <?php else: ?>
          <a href="<?php echo base_url('index.php?page=self&action=dashboard'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1">
            <i class="bi bi-speedometer2 me-2"></i> My Dashboard
          </a>
          <a href="<?php echo base_url('index.php?page=self-profile&action=edit'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1">
            <i class="bi bi-person me-2"></i> Profile
          </a>
          <a href="<?php echo base_url('index.php?page=self-docs&action=list'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1">
            <i class="bi bi-file-earmark-text me-2"></i> Documents
          </a>
        <?php endif; ?>
      </div>
      
      <!-- Bottom Links -->
      <div class="mt-auto w-100 p-3 border-top">
        <!-- Moved User Profile Here -->
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; flex-shrink: 0;">
            <i class="bi bi-person-fill text-muted"></i>
          </div>
          <div class="ms-2 overflow-hidden">
            <div class="fw-bold text-truncate" style="max-width: 150px;">&nbsp;<?php echo htmlspecialchars(current_user()['full_name'] ?? current_user()['username'], ENT_QUOTES, 'UTF-8'); ?></div>
            <small class="text-muted"><?php echo ucfirst(current_user()['role']); ?></small>
          </div>
        </div>
        
        <a href="<?php echo base_url('index.php?page=auth&action=logout'); ?>" class="d-flex align-items-center text-decoration-none text-dark p-2 rounded mb-1">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button id="theme-toggle" class="btn btn-sm btn-outline-secondary" aria-label="Toggle dark mode">
            <i class="bi bi-moon"></i>
          </button>
          <small class="text-muted">v1.0.0</small>
        </div>
      </div>
      </div>
    </div>
    
    <!-- Mobile header with menu button -->
    <div class="d-lg-none w-100 border-bottom bg-white position-sticky top-0 z-1">
      <div class="d-flex align-items-center justify-content-between p-2">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#app-sidebar" aria-controls="app-sidebar" aria-label="Open menu">
          <i class="bi bi-list" aria-hidden="true"></i>
        </button>
        <!-- Removed extra 'PPMS' label next to Menu button on mobile header -->
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 content px-0">
      <?php echo $content ?? ''; ?>
    </div>
  <?php else: ?>
    <!-- Public Pages (No Sidebar) -->
    <div class="w-100">
      <?php echo $content ?? ''; ?>
    </div>
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url('js/theme.js'); ?>" defer></script>
</body>
</html>
