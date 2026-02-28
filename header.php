<?php
// includes/header.php
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Alumni Management System' ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    :root {
      --bs-primary: #7c3aed;
      --bs-primary-rgb: 124, 58, 237;
      --bs-success: #10b981;
      --bs-success-rgb: 16, 185, 129;
      --bs-link-color: #7c3aed;
    }
    .bg-primary { background-color: #7c3aed !important; }
    .text-primary { color: #7c3aed !important; }
    .border-primary { border-color: #7c3aed !important; }
    .text-warning { color: #10b981 !important; }
    .border-warning { border-color: #10b981 !important; }
    .bg-primary { background-color: #7c3aed !important; }
    .badge.bg-primary { background-color: #7c3aed !important; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="fas fa-graduation-cap me-2"></i>AlumniConnect
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item">
          <a class="nav-link <?= $current=='index.php'?'active':'' ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current=='directory.php'?'active':'' ?>" href="directory.php">Discover</a>
        </li>
        <?php if (isLoggedIn()): ?>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link <?= $current=='admin_dashboard.php'?'active':'' ?>" href="admin_dashboard.php">Admin Panel</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link <?= $current=='dashboard.php'?'active':'' ?>" href="dashboard.php">Dashboard</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="btn btn-warning btn-sm px-3 ms-2" href="logout.php">
              <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link <?= $current=='login.php'?'active':'' ?>" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-warning btn-sm px-3 ms-2" href="register.php">
              <i class="fas fa-user-plus me-1"></i>Register
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
