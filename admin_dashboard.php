<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAdmin();
requireCollege();
$pageTitle = "Admin Panel - AlumniConnect";

$aid = $_SESSION['user_id'];
$college = $_SESSION['current_college'];
$success = $error = '';

// ── ACTIONS ──

// Approve / Reject alumni
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    mysqli_query($conn, "UPDATE users SET status='approved' WHERE id=$id");
    mysqli_query($conn, "INSERT INTO admin_logs (admin_id, action) VALUES ($aid, 'Approved alumni ID $id')");
    $success = "Alumni approved.";
}
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    mysqli_query($conn, "UPDATE users SET status='rejected' WHERE id=$id");
    mysqli_query($conn, "INSERT INTO admin_logs (admin_id, action) VALUES ($aid, 'Rejected alumni ID $id')");
    $success = "Alumni rejected.";
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role='alumni'");
    $success = "Alumni deleted.";
}

// Add event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $title = trim($_POST['ev_title']);
    $desc  = trim($_POST['ev_desc']);
    $date  = $_POST['ev_date'];
    if ($title && $date) {
        $stmt = mysqli_prepare($conn, "INSERT INTO events (title, description, event_date, created_by, college_name) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sssis', $title, $desc, $date, $aid, $college);
        mysqli_stmt_execute($stmt);
        mysqli_query($conn, "INSERT INTO admin_logs (admin_id, action) VALUES ($aid, 'Added event: $title')");
        $success = "Event added!";
    } else { $error = "Event title and date are required."; }
}

// Add job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_job'])) {
    $company  = trim($_POST['job_company']);
    $position = trim($_POST['job_position']);
    $desc     = trim($_POST['job_desc']);
    if ($company && $position) {
        $stmt = mysqli_prepare($conn, "INSERT INTO jobs (company, position, description, posted_by, posted_date, college_name) VALUES (?, ?, ?, ?, CURDATE(), ?)");
        mysqli_stmt_bind_param($stmt, 'sssis', $company, $position, $desc, $aid, $college);
        mysqli_stmt_execute($stmt);
        $success = "Job posted!";
    } else { $error = "Company and position are required."; }
}

// Delete event
if (isset($_GET['del_event'])) {
    $id = intval($_GET['del_event']);
    mysqli_query($conn, "DELETE FROM events WHERE id=$id");
    $success = "Event deleted.";
}
// Delete job
if (isset($_GET['del_job'])) {
    $id = intval($_GET['del_job']);
    mysqli_query($conn, "DELETE FROM jobs WHERE id=$id");
    $success = "Job deleted.";
}

// ── COUNTS ──
$total    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND college_name='$college'"))[0];
$approved = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status='approved' AND college_name='$college'"))[0];
$pending  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status='pending' AND college_name='$college'"))[0];
$evCount  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM events WHERE college_name='$college'"))[0];

// Search / filter alumni
$search = trim($_GET['search'] ?? '');
$filterDept  = $_GET['dept'] ?? '';
$filterYear  = $_GET['year'] ?? '';
$filterStatus= $_GET['status_filter'] ?? '';

$where = ["u.role='alumni'", "u.college_name='".mysqli_real_escape_string($conn, $college)."'"];
$params = [];
if ($search) { $s = "%$search%"; $where[] = "(u.name LIKE '$s' OR u.email LIKE '$s')"; }
if ($filterDept)  { $where[] = "p.department='".mysqli_real_escape_string($conn,$filterDept)."'"; }
if ($filterYear)  { $where[] = "p.passing_year=".intval($filterYear); }
if ($filterStatus){ $where[] = "u.status='".mysqli_real_escape_string($conn,$filterStatus)."'"; }

$whereStr = implode(' AND ', $where);
$alumniQ = mysqli_query($conn, "SELECT u.id, u.name, u.email, u.status, p.department, p.passing_year, p.company FROM users u LEFT JOIN alumni_profiles p ON u.id=p.user_id WHERE $whereStr ORDER BY u.created_at DESC");

$events = mysqli_query($conn, "SELECT * FROM events WHERE college_name='$college' ORDER BY event_date DESC");
$jobs   = mysqli_query($conn, "SELECT * FROM jobs WHERE college_name='$college' ORDER BY posted_date DESC");
$logs   = mysqli_query($conn, "SELECT al.*, u.name FROM admin_logs al JOIN users u ON al.admin_id=u.id ORDER BY action_time DESC LIMIT 8");

$activeTab = $_GET['tab'] ?? 'alumni';
$departments = ['Computer Science', 'Electronics', 'Mechanical', 'Civil', 'Business Administration', 'Arts & Humanities', 'Science', 'Law', 'Medicine', 'Architecture'];
$currentYear = date('Y');
?>
<?php include 'includes/header.php'; ?>

<div class="page-header position-relative overflow-hidden">
  <div class="blob-shape bg-warning" style="width:250px; height:250px; top:-50px; left:-50px; animation-delay:0.5s;"></div>
  <div class="container position-relative z-1">
    <h1 class="fw-bold text-white"><i class="fas fa-user-shield me-2"></i>Admin <span class="text-gradient text-white">Panel</span></h1>
    <p class="fs-5 opacity-75">Manage alumni, events, and job postings seamlessly</p>
  </div>
</div>

<div class="container pb-5">

  <?php if ($success): ?>
    <div class="alert alert-success auto-dismiss"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger auto-dismiss"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- Stats row -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="icon blue"><i class="fas fa-users"></i></div>
        <div class="number"><?= $total ?></div>
        <div class="label">Total Alumni</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="icon green"><i class="fas fa-check-circle"></i></div>
        <div class="number"><?= $approved ?></div>
        <div class="label">Approved</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="icon yellow"><i class="fas fa-clock"></i></div>
        <div class="number"><?= $pending ?></div>
        <div class="label">Pending</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="icon red"><i class="fas fa-calendar"></i></div>
        <div class="number"><?= $evCount ?></div>
        <div class="label">Events</div>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-pills mb-4 gap-2">
    <?php
    $tabs = ['alumni'=>'<i class="fas fa-users me-2"></i>Alumni', 'events'=>'<i class="fas fa-calendar me-2"></i>Events', 'jobs'=>'<i class="fas fa-briefcase me-2"></i>Jobs', 'logs'=>'<i class="fas fa-list me-2"></i>Logs'];
    foreach ($tabs as $k => $label):
    ?>
    <li class="nav-item">
      <a class="nav-link rounded-pill px-4 fw-bold shadow-sm <?= $activeTab===$k?'active bg-gradient text-white':'bg-white text-dark hover-glow' ?>" href="?tab=<?= $k ?>"><?= $label ?></a>
    </li>
    <?php endforeach; ?>
  </ul>

  <!-- ── ALUMNI TAB ── -->
  <?php if ($activeTab === 'alumni'): ?>

  <!-- Search/filter bar -->
  <form method="GET" action="">
    <input type="hidden" name="tab" value="alumni">
    <div class="search-bar">
      <div class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label mb-1 small fw-semibold">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Name or email…" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1 small fw-semibold">Department</label>
          <select name="dept" class="form-select">
            <option value="">All Depts</option>
            <?php foreach ($departments as $d): ?>
              <option <?= $filterDept===$d?'selected':'' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1 small fw-semibold">Year</label>
          <select name="year" class="form-select">
            <option value="">All Years</option>
            <?php for ($y = $currentYear; $y >= 1990; $y--): ?>
              <option <?= $filterYear==$y?'selected':'' ?>><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label mb-1 small fw-semibold">Status</label>
          <select name="status_filter" class="form-select">
            <option value="">All</option>
            <option <?= $filterStatus==='pending'?'selected':'' ?>>pending</option>
            <option <?= $filterStatus==='approved'?'selected':'' ?>>approved</option>
            <option <?= $filterStatus==='rejected'?'selected':'' ?>>rejected</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
          <a href="?tab=alumni" class="btn btn-outline-secondary w-100"><i class="fas fa-times"></i></a>
        </div>
      </div>
    </div>
  </form>

  <div class="table-card">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>#</th><th>Name</th><th>Email</th><th>Department</th><th>Year</th><th>Company</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while ($r = mysqli_fetch_assoc($alumniQ)): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($r['name']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($r['email']) ?></td>
          <td><?= htmlspecialchars($r['department'] ?? '—') ?></td>
          <td><?= $r['passing_year'] ?? '—' ?></td>
          <td><?= htmlspecialchars($r['company'] ?? '—') ?></td>
          <td>
            <?php if ($r['status'] === 'approved'): ?>
              <span class="badge-approved">Approved</span>
            <?php elseif ($r['status'] === 'pending'): ?>
              <span class="badge-pending">Pending</span>
            <?php else: ?>
              <span class="badge-rejected">Rejected</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($r['status'] !== 'approved'): ?>
              <a href="?tab=alumni&approve=<?= $r['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this alumni?')">
                <i class="fas fa-check"></i>
              </a>
            <?php endif; ?>
            <?php if ($r['status'] !== 'rejected'): ?>
              <a href="?tab=alumni&reject=<?= $r['id'] ?>" class="btn btn-warning btn-sm" onclick="return confirm('Reject this alumni?')">
                <i class="fas fa-times"></i>
              </a>
            <?php endif; ?>
            <a href="?tab=alumni&delete=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Delete this alumni permanently?')">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($i === 1): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No alumni found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- ── EVENTS TAB ── -->
  <?php elseif ($activeTab === 'events'): ?>
  <div class="row g-4">
    <div class="col-md-5">
      <div class="card">
        <div class="card-header-yellow"><i class="fas fa-plus me-2"></i>Add New Event</div>
        <div class="card-body p-4">
          <form method="POST" action="?tab=events">
            <div class="mb-3">
              <label class="form-label">Event Title <span class="text-danger">*</span></label>
              <input type="text" name="ev_title" class="form-control" placeholder="Event name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Date <span class="text-danger">*</span></label>
              <input type="date" name="ev_date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="ev_desc" class="form-control" rows="3" placeholder="Details…"></textarea>
            </div>
            <button type="submit" name="add_event" class="btn btn-primary w-100">
              <i class="fas fa-plus me-2"></i>Add Event
            </button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="table-card">
        <table class="table mb-0">
          <thead><tr><th>Title</th><th>Description</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
            <?php
            $evCount2 = 0;
            while ($ev = mysqli_fetch_assoc($events)):
              $evCount2++;
            ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($ev['title']) ?></td>
              <td class="text-muted small"><?= htmlspecialchars(substr($ev['description'] ?? '', 0, 60)) . (strlen($ev['description'] ?? '') > 60 ? '…' : '') ?></td>
              <td><span class="badge bg-primary"><?= date('d M Y', strtotime($ev['event_date'])) ?></span></td>
              <td>
                <a href="?tab=events&del_event=<?= $ev['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this event?')">
                  <i class="fas fa-trash"></i> Delete
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
            <?php if ($evCount2 === 0): ?>
            <tr><td colspan="4" class="text-center py-4 text-muted"><i class="fas fa-calendar-times me-2"></i>No events added yet. Use the form to add one!</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ── JOBS TAB ── -->
  <?php elseif ($activeTab === 'jobs'): ?>
  <div class="row g-4">
    <div class="col-md-5">
      <div class="card">
        <div class="card-header-yellow"><i class="fas fa-plus me-2"></i>Post a Job</div>
        <div class="card-body p-4">
          <form method="POST" action="?tab=jobs">
            <div class="mb-3">
              <label class="form-label">Company <span class="text-danger">*</span></label>
              <input type="text" name="job_company" class="form-control" placeholder="Company name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Position <span class="text-danger">*</span></label>
              <input type="text" name="job_position" class="form-control" placeholder="Job title" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="job_desc" class="form-control" rows="3" placeholder="Job details…"></textarea>
            </div>
            <button type="submit" name="add_job" class="btn btn-primary w-100">
              <i class="fas fa-paper-plane me-2"></i>Post Job
            </button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
      <div class="table-card">
        <table class="table mb-0">
          <thead><tr><th>Position</th><th>Company</th><th>Description</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
            <?php
            $jobCount2 = 0;
            while ($job = mysqli_fetch_assoc($jobs)):
              $jobCount2++;
            ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($job['position']) ?></td>
              <td><span class="job-company"><?= htmlspecialchars($job['company']) ?></span></td>
              <td class="text-muted small"><?= htmlspecialchars(substr($job['description'] ?? '', 0, 60)) . (strlen($job['description'] ?? '') > 60 ? '…' : '') ?></td>
              <td><span class="badge bg-primary"><?= date('d M Y', strtotime($job['posted_date'])) ?></span></td>
              <td>
                <a href="?tab=jobs&del_job=<?= $job['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this job?')">
                  <i class="fas fa-trash"></i> Delete
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
            <?php if ($jobCount2 === 0): ?>
            <tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-briefcase me-2"></i>No jobs posted yet. Use the form to post one!</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ── LOGS TAB ── -->
  <?php elseif ($activeTab === 'logs'): ?>
  <div class="table-card">
    <table class="table mb-0">
      <thead><tr><th>Admin</th><th>Action</th><th>Time</th></tr></thead>
      <tbody>
        <?php while ($log = mysqli_fetch_assoc($logs)): ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($log['name']) ?></td>
          <td><?= htmlspecialchars($log['action']) ?></td>
          <td class="text-muted"><?= date('d M Y, h:i A', strtotime($log['action_time'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
