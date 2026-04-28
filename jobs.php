<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireCollege();
$pageTitle = "Job Portal - AlumniConnect";
$college = $_SESSION['current_college'];

// Fetch all jobs for this college
$jobs = mysqli_query($conn, "SELECT j.*, u.name as poster_name FROM jobs j LEFT JOIN users u ON j.posted_by = u.id WHERE j.college_name='$college' ORDER BY j.posted_date DESC");
?>
<?php include 'includes/header.php'; ?>

<div class="page-header position-relative overflow-hidden">
  <div class="blob-shape" style="width:300px;height:300px;top:-60px;left:-60px;background:#10b981;animation-delay:1s;"></div>
  <div class="container position-relative z-1">
    <h1 class="fw-bold"><i class="fas fa-briefcase me-2"></i>Job <span class="text-gradient text-white">Portal</span></h1>
    <p class="fs-5 opacity-75">Opportunities shared by your alumni network. Grow your career.</p>
  </div>
</div>

<div class="container py-5">

  <!-- Stats bar -->
  <div class="row g-3 mb-5">
    <?php
      $totalJobs = mysqli_num_rows($jobs);
      mysqli_data_seek($jobs, 0);
    ?>
    <div class="col-md-4">
      <div class="card text-center border-0 shadow-sm p-4" style="border-radius:16px;border-top:4px solid #7c3aed !important;">
        <i class="fas fa-briefcase mb-2" style="font-size:2rem;color:#7c3aed;"></i>
        <h3 class="fw-bold"><?= $totalJobs ?></h3>
        <p class="text-muted mb-0">Total Openings</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center border-0 shadow-sm p-4" style="border-radius:16px;border-top:4px solid #10b981 !important;">
        <i class="fas fa-users mb-2" style="font-size:2rem;color:#10b981;"></i>
        <h3 class="fw-bold"><?= $totalJobs > 0 ? 'Active' : '0' ?></h3>
        <p class="text-muted mb-0">Posted by Alumni</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center border-0 shadow-sm p-4" style="border-radius:16px;border-top:4px solid #f59e0b !important;">
        <i class="fas fa-star mb-2" style="font-size:2rem;color:#f59e0b;"></i>
        <h3 class="fw-bold">Free</h3>
        <p class="text-muted mb-0">Post a Job</p>
      </div>
    </div>
  </div>

  <!-- Post a job CTA for logged-in alumni -->
  <?php if (isLoggedIn() && ($_SESSION['role'] ?? '') === 'alumni'): ?>
  <div class="card border-0 shadow-sm mb-5 p-4" style="border-radius:16px;background:linear-gradient(135deg,#7c3aed10,#10b98110);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-plus-circle me-2 text-primary"></i>Share a Job Opportunity</h5>
        <p class="text-muted mb-0">Help fellow alumni by sharing job openings from your company.</p>
      </div>
      <a href="dashboard.php?tab=jobs" class="btn btn-primary px-4"><i class="fas fa-paper-plane me-2"></i>Post a Job</a>
    </div>
  </div>
  <?php endif; ?>

  <!-- Job listings -->
  <?php if ($totalJobs > 0): ?>
    <div class="row g-4">
      <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 border-0 shadow-sm hover-glow" style="border-radius:16px;overflow:hidden;">
            <div style="height:6px;background:linear-gradient(90deg,#7c3aed,#10b981);"></div>
            <div class="card-body p-4">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center justify-content-center rounded-3 text-white fw-bold me-3" style="width:50px;height:50px;background:var(--bs-primary);flex-shrink:0;font-size:1.4rem;">
                  <i class="fas fa-building"></i>
                </div>
                <span class="badge rounded-pill px-3" style="background:#7c3aed20;color:#7c3aed;font-size:0.75rem;">
                  <i class="fas fa-clock me-1"></i><?= date('d M Y', strtotime($job['posted_date'])) ?>
                </span>
              </div>
              <h6 class="fw-bold mb-1 fs-5"><?= htmlspecialchars($job['position']) ?></h6>
              <p class="fw-semibold mb-2" style="color:#7c3aed;"><?= htmlspecialchars($job['company']) ?></p>
              <p class="text-muted small mb-3"><?= $job['description'] ? nl2br(htmlspecialchars($job['description'])) : '<em>No description provided.</em>' ?></p>
              <hr class="my-2">
              <small class="text-muted"><i class="fas fa-user-graduate me-1"></i>Posted by: <strong><?= htmlspecialchars($job['poster_name'] ?? 'Alumni') ?></strong></small>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

  <?php else: ?>
    <div class="text-center py-5">
      <i class="fas fa-briefcase" style="font-size:5rem;color:#ddd6fe;"></i>
      <h4 class="mt-4 fw-bold text-muted">No Job Postings Yet</h4>
      <p class="text-muted">Be the first to share an opportunity with your alumni network!</p>
      <?php if (isLoggedIn()): ?>
        <a href="dashboard.php?tab=jobs" class="btn btn-primary mt-2"><i class="fas fa-plus me-2"></i>Post a Job</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-primary mt-2"><i class="fas fa-sign-in-alt me-2"></i>Login to Post</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<div class="py-4"></div>
<?php include 'includes/footer.php'; ?>
