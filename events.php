<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireCollege();
$pageTitle = "Events & Reunions - AlumniConnect";
$college = $_SESSION['current_college'];

$success = $error = '';

// Handle Add Event form submission (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    if (isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin') {
        $title   = trim($_POST['title']);
        $desc    = trim($_POST['description']);
        $edate   = $_POST['event_date'];
        if ($title && $edate) {
            $stmt = mysqli_prepare($conn, "INSERT INTO events (title, description, event_date, college_name) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssss', $title, $desc, $edate, $college);
            if (mysqli_stmt_execute($stmt)) {
                $success = "Event \"$title\" added successfully!";
            } else {
                $error = "Failed to add event. Please try again.";
            }
        } else {
            $error = "Title and Date are required.";
        }
    } else {
        $error = "Only admins can add events.";
    }
}

// Fetch all events for this college
$events = mysqli_query($conn, "SELECT * FROM events WHERE college_name='$college' ORDER BY event_date DESC");
$totalEvents = mysqli_num_rows($events);
?>
<?php include 'includes/header.php'; ?>

<div class="page-header position-relative overflow-hidden">
  <div class="blob-shape bg-warning" style="width:350px;height:350px;top:-80px;right:-80px;animation-delay:0.5s;"></div>
  <div class="container position-relative z-1">
    <h1 class="fw-bold"><i class="fas fa-calendar-alt me-2"></i>Events & <span class="text-gradient text-white">Reunions</span></h1>
    <p class="fs-5 opacity-75">Stay updated with upcoming alumni events, reunions and meetups.</p>
  </div>
</div>

<div class="container py-5">

  <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
      <i class="fas fa-check-circle me-2"></i><?= $success ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- ADD EVENT FORM (Admin only) -->
  <?php if (isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin'): ?>
  <div class="card border-0 shadow-sm mb-5" style="border-radius:20px;overflow:hidden;">
    <div style="height:6px;background:linear-gradient(90deg,#7c3aed,#10b981);"></div>
    <div class="card-body p-4 p-md-5">
      <div class="d-flex align-items-center mb-4">
        <div class="d-flex align-items-center justify-content-center rounded-3 text-white me-3" style="width:48px;height:48px;background:var(--bs-primary);flex-shrink:0;">
          <i class="fas fa-plus fs-5"></i>
        </div>
        <div>
          <h5 class="fw-bold mb-0">Add New Event</h5>
          <small class="text-muted">Fill in the details below to schedule an event for your alumni.</small>
        </div>
      </div>
      <form method="POST" id="addEventForm">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Event Title <span class="text-danger">*</span></label>
            <input type="text" name="title" id="eventTitle" class="form-control form-control-lg" placeholder="e.g. Annual Alumni Reunion 2025" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Event Date <span class="text-danger">*</span></label>
            <input type="date" name="event_date" id="eventDate" class="form-control form-control-lg" min="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Describe the event — venue, schedule, what to expect..."></textarea>
          </div>
          <div class="col-12 d-flex gap-2 flex-wrap">
            <button type="submit" name="add_event" class="btn btn-primary px-5">
              <i class="fas fa-calendar-plus me-2"></i>Add Event
            </button>
            <button type="reset" class="btn btn-outline-secondary px-4">
              <i class="fas fa-times me-2"></i>Clear
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>

  <!-- Events Grid -->
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i>All Events <span class="badge rounded-pill ms-2" style="background:#7c3aed20;color:#7c3aed;"><?= $totalEvents ?></span></h4>
    <?php if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'admin'): ?>
      <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Events are managed by your institution admin.</small>
    <?php endif; ?>
  </div>

  <?php if ($totalEvents > 0): ?>
    <div class="row g-4">
      <?php while ($ev = mysqli_fetch_assoc($events)): ?>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm border-0 hover-glow" style="border-radius:16px; overflow:hidden;">
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
              <p class="text-muted small mb-3"><?= $ev['description'] ? nl2br(htmlspecialchars($ev['description'])) : '<em>No description provided.</em>' ?></p>
              <?php
                $today = date('Y-m-d');
                if ($ev['event_date'] >= $today):
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
      <p class="text-muted">
        <?php if (isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin'): ?>
          Use the form above to add the first event!
        <?php else: ?>
          Check back soon! Alumni events and reunions will be posted here by your admin.
        <?php endif; ?>
      </p>
      <?php if (isLoggedIn() && ($_SESSION['role'] ?? '') !== 'admin'): ?>
        <a href="dashboard.php" class="btn btn-primary mt-2"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</div>

<!-- Info banner for non-admins -->
<?php if (!isLoggedIn() || ($_SESSION['role'] ?? '') !== 'admin'): ?>
<section class="py-5 mt-2" style="background:linear-gradient(135deg,#7c3aed15,#10b98115); border-radius:24px; margin:0 24px;">
  <div class="container text-center">
    <h3 class="fw-bold mb-3">Want to organize an event?</h3>
    <p class="text-muted mb-4">Events are managed by the institution admin. Login with admin credentials to add events.</p>
    <?php if (!isLoggedIn()): ?>
      <a href="login.php" class="btn btn-primary px-5"><i class="fas fa-sign-in-alt me-2"></i>Admin Login</a>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<div class="py-4"></div>
<?php include 'includes/footer.php'; ?>
