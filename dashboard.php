<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAlumni();
requireCollege();
$pageTitle = "Dashboard - AlumniConnect";

$uid = $_SESSION['user_id'];
$college = $_SESSION['current_college'];

// Handle profile update
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone   = trim($_POST['phone']);
    $company = trim($_POST['company']);
    $loc     = trim($_POST['location']);
    $dept    = trim($_POST['department']);
    $year    = intval($_POST['passing_year']);

    $stmt = mysqli_prepare($conn, "UPDATE alumni_profiles SET phone=?, department=?, passing_year=?, company=?, location=? WHERE user_id=?");
    mysqli_stmt_bind_param($stmt, 'ssissi', $phone, $dept, $year, $company, $loc, $uid);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Update failed. Try again.";
    }
}

// Handle alumni posting a job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_job'])) {
    $jcompany  = trim($_POST['job_company']);
    $jposition = trim($_POST['job_position']);
    $jdesc     = trim($_POST['job_desc']);
    if ($jcompany && $jposition) {
        $stmt = mysqli_prepare($conn, "INSERT INTO jobs (company, position, description, posted_by, posted_date, college_name) VALUES (?, ?, ?, ?, CURDATE(), ?)");
        mysqli_stmt_bind_param($stmt, 'sssis', $jcompany, $jposition, $jdesc, $uid, $college);
        mysqli_stmt_execute($stmt);
        $success = "Job posted successfully!";
    } else {
        $error = "Company and position are required.";
    }
}

// Fetch user & profile
$uq = mysqli_prepare($conn, "SELECT u.name, u.email, u.created_at, p.phone, p.department, p.passing_year, p.company, p.location FROM users u LEFT JOIN alumni_profiles p ON u.id=p.user_id WHERE u.id=?");
mysqli_stmt_bind_param($uq, 'i', $uid);
mysqli_stmt_execute($uq);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($uq));

// Fetch events
$events = mysqli_query($conn, "SELECT * FROM events WHERE college_name='$college' ORDER BY event_date DESC LIMIT 5");

// Fetch jobs
$jobs = mysqli_query($conn, "SELECT * FROM jobs WHERE college_name='$college' ORDER BY posted_date DESC LIMIT 5");

$departments = ['Computer Science', 'Electronics', 'Mechanical', 'Civil', 'Business Administration', 'Arts & Humanities', 'Science', 'Law', 'Medicine', 'Architecture'];
$currentYear = date('Y');
$activeTab = $_GET['tab'] ?? 'profile';
?>
<?php include 'includes/header.php'; ?>

<div class="page-header position-relative overflow-hidden">
  <div class="blob-shape bg-warning" style="width:300px; height:300px; top:-50px; left:-50px; animation-delay:1s;"></div>
  <div class="container position-relative z-1">
    <h1 class="fw-bold"><i class="fas fa-th-large me-2"></i>My <span class="text-gradient text-white">Dashboard</span></h1>
    <p class="fs-5 opacity-75">Welcome back, <?= htmlspecialchars($user['name']) ?>!</p>
  </div>
</div>

<div class="container pb-5">
  <div class="row g-4">

    <!-- Sidebar -->
    <div class="col-md-3">
      <div class="card p-3">
        <!-- Profile summary -->
        <div class="text-center mb-3 pb-3 border-bottom">
          <div class="alumni-avatar mx-auto mb-2" style="width:64px;height:64px;font-size:1.6rem">
            <?= strtoupper(substr($user['name'], 0, 1)) ?>
          </div>
          <div class="fw-bold"><?= htmlspecialchars($user['name']) ?></div>
          <div class="text-muted small"><?= htmlspecialchars($user['department'] ?? 'No dept set') ?></div>
          <span class="badge-approved mt-1 d-inline-block">Approved</span>
        </div>
        <!-- Nav -->
        <nav class="sidebar-nav">
          <a href="?tab=profile" class="nav-link <?= $activeTab=='profile'?'active':'' ?>">
            <i class="fas fa-user"></i> My Profile
          </a>
          <a href="?tab=edit" class="nav-link <?= $activeTab=='edit'?'active':'' ?>">
            <i class="fas fa-edit"></i> Edit Profile
          </a>
          <a href="?tab=events" class="nav-link <?= $activeTab=='events'?'active':'' ?>">
            <i class="fas fa-calendar"></i> Events
          </a>
          <a href="?tab=jobs" class="nav-link <?= $activeTab=='jobs'?'active':'' ?>">
            <i class="fas fa-briefcase"></i> Jobs
          </a>
          <a href="?tab=network" class="nav-link <?= $activeTab=='network'?'active':'' ?>">
            <i class="fas fa-user-friends"></i> Network Requests
          </a>
          <a href="directory.php" class="nav-link">
            <i class="fas fa-users"></i> Discover
          </a>
          <a href="chat.php" class="nav-link text-white mt-3 rounded-pill text-center shadow-sm" style="background: var(--bs-primary);">
            <i class="fas fa-comments"></i> Open Chat
          </a>
          <hr class="my-2">
          <a href="logout.php" class="nav-link text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </nav>
      </div>
    </div>

    <!-- Main content -->
    <div class="col-md-9">

      <?php if ($success): ?>
        <div class="alert alert-success auto-dismiss"><?= $success ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger auto-dismiss"><?= $error ?></div>
      <?php endif; ?>

      <!-- PROFILE VIEW -->
      <?php if ($activeTab === 'profile'): ?>
      <div class="card">
        <div class="card-header-blue"><i class="fas fa-user me-2"></i>My Profile</div>
        <div class="card-body p-4">
          <div class="row g-3">
            <?php
            $fields = [
              'fas fa-user'          => ['Full Name', $user['name']],
              'fas fa-envelope'      => ['Email', $user['email']],
              'fas fa-phone'         => ['Phone', $user['phone'] ?: '—'],
              'fas fa-university'    => ['Department', $user['department'] ?: '—'],
              'fas fa-graduation-cap'=> ['Passing Year', $user['passing_year'] ?: '—'],
              'fas fa-building'      => ['Company', $user['company'] ?: '—'],
              'fas fa-map-marker-alt'=> ['Location', $user['location'] ?: '—'],
            ];
            foreach ($fields as $icon => $f): ?>
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border h-100 hover-glow">
                <div class="text-muted small mb-1 fw-bold text-uppercase tracking-wide"><i class="<?= $icon ?> me-2 text-primary"></i><?= $f[0] ?></div>
                <div class="fw-semibold fs-6 text-dark"><?= htmlspecialchars((string)$f[1]) ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="mt-4">
            <a href="?tab=edit" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Edit Profile</a>
          </div>
        </div>
      </div>

      <!-- EDIT PROFILE -->
      <?php elseif ($activeTab === 'edit'): ?>
      <div class="card">
        <div class="card-header-blue"><i class="fas fa-edit me-2"></i>Edit Profile</div>
        <div class="card-body p-4">
          <form method="POST">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Company</label>
                <input type="text" name="company" class="form-control" value="<?= htmlspecialchars($user['company'] ?? '') ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department</label>
                <select name="department" class="form-select">
                  <?php foreach ($departments as $d): ?>
                    <option <?= ($user['department'] ?? '') === $d ? 'selected' : '' ?>><?= $d ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Passing Year</label>
                <select name="passing_year" class="form-select">
                  <?php for ($y = $currentYear; $y >= 1990; $y--): ?>
                    <option <?= ($user['passing_year'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                  <?php endfor; ?>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($user['location'] ?? '') ?>">
              </div>
              <div class="col-12">
                <button type="submit" name="update_profile" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="?tab=profile" class="btn btn-outline-secondary ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- EVENTS -->
      <?php elseif ($activeTab === 'events'): ?>
      <div class="card">
        <div class="card-header-blue"><i class="fas fa-calendar me-2"></i>Upcoming Events</div>
        <div class="card-body p-4">
          <?php if (mysqli_num_rows($events) > 0): ?>
            <div class="row g-3">
            <?php while ($ev = mysqli_fetch_assoc($events)): ?>
              <div class="col-md-6">
                <div class="event-card">
                  <span class="event-date"><i class="fas fa-calendar me-1"></i><?= date('d M Y', strtotime($ev['event_date'])) ?></span>
                  <h6 class="fw-bold mb-2"><?= htmlspecialchars($ev['title']) ?></h6>
                  <p class="text-muted small mb-0"><?= htmlspecialchars($ev['description']) ?></p>
                </div>
              </div>
            <?php endwhile; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-calendar-times d-block mb-3" style="font-size:2.5rem;color:#dee2e6"></i>
              <p class="text-muted">No events scheduled yet.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- JOBS -->
      <?php elseif ($activeTab === 'jobs'): ?>
      <div class="row g-4">
        <!-- Post a job form -->
        <div class="col-md-5">
          <div class="card">
            <div class="card-header-blue"><i class="fas fa-paper-plane me-2"></i>Post a Job</div>
            <div class="card-body p-4">
              <form method="POST" action="?tab=jobs">
                <div class="mb-3">
                  <label class="form-label fw-bold">Company <span class="text-danger">*</span></label>
                  <input type="text" name="job_company" class="form-control" placeholder="e.g. Google" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Position <span class="text-danger">*</span></label>
                  <input type="text" name="job_position" class="form-control" placeholder="e.g. Software Engineer" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Description</label>
                  <textarea name="job_desc" class="form-control" rows="3" placeholder="Job details, requirements..."></textarea>
                </div>
                <button type="submit" name="post_job" class="btn btn-primary w-100">
                  <i class="fas fa-paper-plane me-2"></i>Post Job
                </button>
              </form>
            </div>
          </div>
        </div>
        <!-- Job listings -->
        <div class="col-md-7">
          <div class="card">
            <div class="card-header-blue"><i class="fas fa-briefcase me-2"></i>Job Opportunities</div>
            <div class="card-body p-3">
              <?php if (mysqli_num_rows($jobs) > 0): ?>
                <div class="row g-3">
                <?php while ($job = mysqli_fetch_assoc($jobs)): ?>
                  <div class="col-12">
                    <div class="job-card">
                      <div class="d-flex justify-content-between align-items-start">
                        <span class="job-company"><?= htmlspecialchars($job['company']) ?></span>
                        <small class="text-muted"><i class="fas fa-clock me-1"></i><?= date('d M Y', strtotime($job['posted_date'])) ?></small>
                      </div>
                      <h6 class="fw-bold mb-1 mt-1"><?= htmlspecialchars($job['position']) ?></h6>
                      <p class="text-muted small mb-0"><?= htmlspecialchars($job['description'] ?? '') ?></p>
                    </div>
                  </div>
                <?php endwhile; ?>
                </div>
              <?php else: ?>
                <div class="empty-state">
                  <i class="fas fa-briefcase d-block mb-3" style="font-size:2.5rem;color:#ddd6fe"></i>
                  <p class="text-muted">No job postings yet. Be the first to post one!</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      
      <!-- NETWORK REQUESTS -->
      <?php elseif ($activeTab === 'network'): 
             $reqQ = mysqli_query($conn, "SELECT c.id as conn_id, u.id, u.name, p.department, p.company 
                                          FROM connections c 
                                          JOIN users u ON c.sender_id = u.id 
                                          LEFT JOIN alumni_profiles p ON p.user_id = u.id
                                          WHERE c.receiver_id = $uid AND c.status = 'pending'
                                          ORDER BY c.created_at DESC");
      ?>
      <div class="card">
        <div class="card-header-blue"><i class="fas fa-user-friends me-2"></i>Incoming Network Requests</div>
        <div class="card-body p-4">
          <?php if (mysqli_num_rows($reqQ) > 0): ?>
            <div class="row g-3">
            <?php while ($r = mysqli_fetch_assoc($reqQ)): ?>
              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light hover-glow">
                  <div class="d-flex align-items-center mb-3">
                    <div class="alumni-avatar d-flex justify-content-center align-items-center mb-0 me-3 bg-white border" style="width:48px;height:48px;font-size:1.2rem; flex-shrink:0;">
                      <?= strtoupper(substr($r['name'], 0, 1)) ?>
                    </div>
                    <div>
                      <h6 class="fw-bold mb-0"><?= htmlspecialchars($r['name']) ?></h6>
                      <div class="small text-muted"><?= htmlspecialchars($r['department']) ?></div>
                    </div>
                  </div>
                  <div class="d-flex gap-2">
                    <button class="btn btn-success btn-sm flex-grow-1 connection-btn" data-action="accept" data-id="<?= $r['id'] ?>"><i class="fas fa-check me-1"></i> Accept</button>
                    <button class="btn btn-outline-danger btn-sm flex-grow-1 connection-btn" data-action="reject" data-id="<?= $r['id'] ?>"><i class="fas fa-times me-1"></i> Ignore</button>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-user-check d-block mb-3" style="font-size:2.5rem;color:#dee2e6"></i>
              <p class="text-muted">You have no pending connection requests.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <script>
      document.querySelectorAll('.connection-btn').forEach(btn => {
          btn.addEventListener('click', function(e) {
              e.preventDefault();
              const action = this.dataset.action;
              const targetId = this.dataset.id;
              const formData = new FormData();
              formData.append('action', action);
              formData.append('target_id', targetId);
              
              const originalText = this.innerHTML;
              this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
              this.disabled = true;

              fetch('api_connection.php', {
                  method: 'POST',
                  body: formData
              })
              .then(res => res.json())
              .then(data => {
                  if(data.success) {
                      location.reload();
                  } else {
                      alert(data.error || 'Something went wrong');
                      this.innerHTML = originalText;
                      this.disabled = false;
                  }
              }).catch(err => {
                  alert('Something went wrong');
                  this.innerHTML = originalText;
                  this.disabled = false;
              });
          });
      });
      </script>

      <?php endif; ?>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
