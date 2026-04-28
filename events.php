<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireCollege();
$pageTitle = "Events & Reunions - AlumniConnect";
$college = $_SESSION['current_college'];

// Fetch all events for this college
$events = mysqli_query($conn, "SELECT * FROM events WHERE college_name='$college' ORDER BY event_date DESC");
?>
<?php include 'includes/header.php'; ?>

<div class="page-header position-relative overflow-hidden">
  <div class="blob-shape bg-warning" style="width:350px;height:350px;top:-80px;right:-80px;animation-delay:0.5s;"></div>
  <div class="container position-relative z-1">
    <h1 class="fw-bold"><i class="fas fa-calendar-star me-2"></i>Events & <span class="text-gradient text-white">Reunions</span></h1>
    <p class="fs-5 opacity-75">Stay updated with upcoming alumni events, reunions and meetups.</p>
  </div>
</div>

<div class="container py-5">

  <?php if (mysqli_num_rows($events) > 0): ?>
    <div class="row g-4">
      <?php while ($ev = mysqli_fetch_assoc($events)): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm border-0 hover-glow" style="border-radius:16px; overflow:hidden;">
            <!-- Color banner -->
            <div style="height:8px; background: linear-gradient(90deg,#7c3aed,#10b981);"></div>
            <div class="card-body p-4">
              <div class="d-flex align-items-center mb-3">
                <div class="me-3 d-flex flex-column align-items-center justify-content-center rounded-3 text-white fw-bold" style="width:56px;height:56px;background:var(--bs-primary);font-size:1.1rem;flex-shrink:0;">
                  <span style="font-size:1.4rem;"><?= date('d', strtotime($ev['event_date'])) ?></span>
                  <span style="font-size:0.7rem;text-transform:uppercase;"><?= date('M', strtotime($ev['event_date'])) ?></span>
                </div>
                <div>
                  <h6 class="fw-bold mb-0"><?= htmlspecialchars($ev['title']) ?></h6>
                  <small class="text-muted"><i class="fas fa-calendar me-1"></i><?= date('D, d M Y', strtotime($ev['event_date'])) ?></small>
                </div>
              </div>
              <p class="text-muted small mb-3"><?= nl2br(htmlspecialchars($ev['description'])) ?></p>
              <?php
                $today = date('Y-m-d');
                $evDate = $ev['event_date'];
                if ($evDate >= $today):
              ?>
                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="fas fa-clock me-1"></i>Upcoming</span>
              <?php else: ?>
                <span class="badge bg-secondary px-3 py-2 rounded-pill"><i class="fas fa-check me-1"></i>Completed</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

  <?php else: ?>
    <div class="text-center py-5">
      <i class="fas fa-calendar-times" style="font-size:5rem;color:#ddd6fe;"></i>
      <h4 class="mt-4 fw-bold text-muted">No Events Scheduled Yet</h4>
      <p class="text-muted">Check back soon! Alumni events and reunions will be posted here by your admin.</p>
      <?php if (isLoggedIn()): ?>
        <a href="dashboard.php" class="btn btn-primary mt-2"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</div>

<!-- Info Section -->
<section class="py-5 mt-2" style="background:linear-gradient(135deg,#7c3aed15,#10b98115); border-radius:24px; margin:0 24px;">
  <div class="container text-center">
    <h3 class="fw-bold mb-3">Want to organize an event?</h3>
    <p class="text-muted mb-4">If you're an admin, you can add and manage events directly from the Admin Panel.</p>
    <?php if (isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin'): ?>
      <a href="admin_dashboard.php" class="btn btn-primary px-5"><i class="fas fa-plus me-2"></i>Add Event</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-outline-primary px-5"><i class="fas fa-sign-in-alt me-2"></i>Login to view more</a>
    <?php endif; ?>
  </div>
</section>

<div class="py-4"></div>
<?php include 'includes/footer.php'; ?>
