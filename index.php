<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requireCollege();

$pageTitle = "AlumniConnect - Home";
$college = $_SESSION['current_college'];

// Quick stats
$total = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status='approved' AND college_name='$college'"))[0] ?? 0;
$depts = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(DISTINCT p.department) FROM alumni_profiles p JOIN users u ON p.user_id=u.id WHERE u.college_name='$college'"))[0] ?? 0;
$events = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM events WHERE college_name='$college'"))[0] ?? 0;
$jobs   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM jobs WHERE college_name='$college'"))[0] ?? 0;

// Fetch upcoming events
$upcomingEvents = mysqli_query($conn, "SELECT title, event_date, description FROM events WHERE college_name='$college' ORDER BY event_date ASC LIMIT 3");

// Fetch recent alumni
$recentAlumni = mysqli_query($conn, "SELECT u.name, p.department, p.company FROM users u JOIN alumni_profiles p ON u.id = p.user_id WHERE u.role='alumni' AND u.status='approved' AND u.college_name='$college' ORDER BY u.id DESC LIMIT 3");
?>
<?php include 'includes/header.php'; ?>

<!-- Hero -->
<section class="hero position-relative overflow-hidden">
  <div class="blob-shape bg-warning" style="width:400px; height:400px; top:-100px; left:-100px; animation-delay:0s;"></div>
  <div class="blob-shape bg-info" style="width:300px; height:300px; bottom:-50px; right:10%; animation-delay:2s;"></div>
  <div class="container position-relative z-1 animate-fade-up">
    <div class="badge bg-white text-dark mb-3 px-3 py-2 rounded-pill shadow-sm fw-bold"><i class="fas fa-university me-2 text-primary"></i> <?= htmlspecialchars($college) ?></div>
    <h1 class="display-4 fw-bold mb-3"><i class="fas fa-graduation-cap me-3 float-icon d-inline-block text-warning"></i><span class="text-white">AlumniConnect</span></h1>
    <p class="lead delay-100 fs-5 mb-4">Stay connected with your fellow graduates. Find jobs, attend events, and grow your professional network.</p>
    <div class="mt-4 animate-fade-up delay-200">
      <?php if (!isLoggedIn()): ?>
        <a href="register.php" class="btn btn-warning btn-animated btn-lg me-3 px-4">
          <i class="fas fa-user-plus me-2"></i>Register Now
        </a>
        <a href="login.php" class="btn btn-outline-light btn-outline-animated btn-lg px-4">
          <i class="fas fa-sign-in-alt me-2"></i>Login
        </a>
      <?php else: ?>
        <a href="<?= $_SESSION['role']==='admin' ? 'admin_dashboard.php' : 'dashboard.php' ?>" class="btn btn-warning btn-animated btn-lg px-4">
          <i class="fas fa-th-large me-2"></i>Go to Dashboard
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Stats -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row g-4">
      <div class="col-6 col-md-3">
        <div class="stat-card hover-glow animate-fade-up delay-100">
          <div class="icon blue"><i class="fas fa-users"></i></div>
          <div class="number"><?= $total ?></div>
          <div class="label">Alumni Members</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card hover-glow animate-fade-up delay-200">
          <div class="icon yellow"><i class="fas fa-building"></i></div>
          <div class="number"><?= $depts ?></div>
          <div class="label">Departments</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card hover-glow animate-fade-up delay-300">
          <div class="icon green"><i class="fas fa-calendar-check"></i></div>
          <div class="number"><?= $events ?></div>
          <div class="label">Events</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card hover-glow animate-fade-up delay-400">
          <div class="icon red"><i class="fas fa-briefcase"></i></div>
          <div class="number"><?= $jobs ?></div>
          <div class="label">Job Postings</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Features -->
<section class="py-5">
  <div class="container py-3">
    <div class="text-center mb-5 animate-fade-up">
      <h2 class="fw-bold mb-2">Why AlumniConnect?</h2>
      <p class="text-muted">Everything you need to stay in touch with your alma mater</p>
    </div>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="card p-4 h-100 hover-glow animate-fade-up delay-100">
          <div class="mb-3"><i class="fas fa-search fa-3x text-primary float-icon d-inline-block"></i></div>
          <h5 class="fw-bold">Find Alumni</h5>
          <p class="text-muted small mb-0">Search by department, batch year, or company. Stay in touch with your classmates.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 h-100 hover-glow animate-fade-up delay-200">
          <div class="mb-3"><i class="fas fa-briefcase fa-3x text-warning float-icon d-inline-block" style="animation-delay:0.5s"></i></div>
          <h5 class="fw-bold">Job Opportunities</h5>
          <p class="text-muted small mb-0">Alumni post jobs and opportunities. Get hired through your own network.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 h-100 hover-glow animate-fade-up delay-300">
          <div class="mb-3"><i class="fas fa-calendar-alt fa-3x text-success float-icon d-inline-block" style="animation-delay:1s"></i></div>
          <h5 class="fw-bold">Events & Meetups</h5>
          <p class="text-muted small mb-0">Never miss a reunion, seminar, or networking event from your institution.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Us -->
<section class="py-5 position-relative">
  <div class="blob-shape bg-primary opacity-10" style="width:400px; height:400px; top:10%; right:-100px;"></div>
  <div class="container py-4 position-relative z-1">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 animate-fade-up delay-100">
        <div class="position-relative">
          <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="Alumni Group" class="img-fluid rounded-4 shadow-lg border border-3 border-white">
          <div class="position-absolute bottom-0 end-0 bg-glass p-3 rounded-4 shadow-lg mb-n3 me-n3 border border-white" style="backdrop-filter: blur(12px);">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-primary text-white p-3 rounded-circle d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                <i class="fas fa-users fs-4"></i>
              </div>
              <div>
                <h4 class="fw-bold text-dark mb-0">10k+</h4>
                <div class="text-muted small fw-semibold">Active Alumni</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 animate-fade-up delay-200">
        <div class="text-uppercase text-purple fw-bold tracking-wide mb-2 small"><i class="fas fa-info-circle me-2"></i>About Our Network</div>
        <h2 class="display-6 fw-bold mb-4">Empowering our graduates to achieve more, <span class="text-gradient">together.</span></h2>
        <p class="text-muted mb-4 fs-6">AlumniConnect bridges the gap between past graduates and current opportunities. We believe in lifelong learning, meaningful networking, and giving back to the community.</p>
        <ul class="list-unstyled mb-4">
          <li class="mb-3 d-flex align-items-center bg-white p-3 rounded-3 shadow-sm border"><i class="fas fa-check-circle text-emerald fs-4 me-3"></i> <span class="fw-medium text-dark">Exclusive networking events and webinars</span></li>
          <li class="mb-3 d-flex align-items-center bg-white p-3 rounded-3 shadow-sm border"><i class="fas fa-check-circle text-emerald fs-4 me-3"></i> <span class="fw-medium text-dark">Direct access to job boards and resources</span></li>
          <li class="d-flex align-items-center bg-white p-3 rounded-3 shadow-sm border"><i class="fas fa-check-circle text-emerald fs-4 me-3"></i> <span class="fw-medium text-dark">Mentorship programs for new graduates</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="py-5 bg-white">
  <div class="container py-4">
    <div class="text-center mb-5 animate-fade-up">
      <h2 class="fw-bold mb-2">Success <span class="text-gradient">Stories</span></h2>
      <p class="text-muted">Hear from our distinguished alumni</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4 animate-fade-up delay-100">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light text-center" style="border-radius:20px;">
          <div class="mb-3 text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p class="fst-italic text-muted mb-4">"The alumni network helped me land my dream job at a top tech company. The connections you make here are invaluable!"</p>
          <div class="mt-auto d-flex align-items-center justify-content-center gap-3">
            <img src="https://ui-avatars.com/api/?name=Sarah+Jenkins&background=0D8ABC&color=fff&rounded=true" alt="Sarah" width="48" height="48" class="rounded-circle shadow-sm">
            <div class="text-start">
              <h6 class="fw-bold mb-0">Sarah Jenkins</h6>
              <small class="text-primary">Class of 2018</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 animate-fade-up delay-200">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light text-center" style="border-radius:20px;">
          <div class="mb-3 text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p class="fst-italic text-muted mb-4">"Attending the regional meetups allowed me to find a co-founder for my startup. A fantastic community to be a part of."</p>
          <div class="mt-auto d-flex align-items-center justify-content-center gap-3">
            <img src="https://ui-avatars.com/api/?name=David+Chen&background=10b981&color=fff&rounded=true" alt="David" width="48" height="48" class="rounded-circle shadow-sm">
            <div class="text-start">
              <h6 class="fw-bold mb-0">David Chen</h6>
              <small class="text-primary">Class of 2015</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 animate-fade-up delay-300">
        <div class="card h-100 p-4 border-0 shadow-sm bg-light text-center" style="border-radius:20px;">
          <div class="mb-3 text-warning"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
          <p class="fst-italic text-muted mb-4">"Being able to mentor recent graduates has been incredibly rewarding. AlumniConnect makes giving back so easy."</p>
          <div class="mt-auto d-flex align-items-center justify-content-center gap-3">
            <img src="https://ui-avatars.com/api/?name=Emily+Davis&background=f59e0b&color=fff&rounded=true" alt="Emily" width="48" height="48" class="rounded-circle shadow-sm">
            <div class="text-start">
              <h6 class="fw-bold mb-0">Emily Davis</h6>
              <small class="text-primary">Class of 2010</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Recent Alumni & Upcoming Events (Dynamic Content) -->
<section class="py-5 bg-white">
  <div class="container py-4">
    <div class="row g-5">
      <!-- Upcoming Events -->
      <div class="col-lg-6 animate-fade-up">
        <h3 class="fw-bold mb-4 border-bottom pb-2 border-2 border-primary d-inline-block">Upcoming Events</h3>
        <?php if(mysqli_num_rows($upcomingEvents) > 0): ?>
          <?php while($ev = mysqli_fetch_assoc($upcomingEvents)): ?>
            <div class="card mb-3 hover-glow border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex w-100 justify-content-between">
                  <h5 class="mb-1 text-primary fw-bold"><?= htmlspecialchars($ev['title']) ?></h5>
                  <small class="badge bg-primary text-white rounded-pill d-flex align-items-center"><i class="fas fa-calendar-day me-1"></i><?= date('M d, Y', strtotime($ev['event_date'])) ?></small>
                </div>
                <p class="mb-1 text-muted small mt-2"><?= htmlspecialchars(substr($ev['description'], 0, 80)) ?>...</p>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="alert alert-light text-center">No upcoming events right now.</div>
        <?php endif; ?>
      </div>

      <!-- Recent Alumni -->
      <div class="col-lg-6 animate-fade-up delay-200">
        <h3 class="fw-bold mb-4 border-bottom pb-2 border-2 text-warning border-warning d-inline-block">New Members</h3>
        <div class="row g-3">
          <?php if(mysqli_num_rows($recentAlumni) > 0): ?>
            <?php while($al = mysqli_fetch_assoc($recentAlumni)): ?>
              <div class="col-sm-6">
                <div class="alumni-card hover-glow p-3 h-100 border text-start">
                  <div class="d-flex align-items-center mb-2">
                    <div class="alumni-avatar mb-0 me-3 bg-light text-primary" style="width:48px; height:48px; font-size:1.2rem; flex-shrink:0;">
                      <?= strtoupper(substr($al['name'], 0, 1)) ?>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-bold"><?= htmlspecialchars($al['name']) ?></h6>
                      <small class="text-muted"><i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($al['department']) ?></small>
                    </div>
                  </div>
                  <?php if($al['company']): ?>
                    <div class="small mt-2 px-2 py-1 bg-light rounded text-dark"><i class="fas fa-building me-1 text-warning"></i> <?= htmlspecialchars($al['company']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="col-12"><div class="alert alert-light text-center">No alumni found.</div></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<?php if (!isLoggedIn()): ?>
<section class="py-5" style="background: linear-gradient(135deg, var(--yellow) 0%, #ffca2c 100%);">
  <div class="container py-4 text-center animate-fade-up">
    <h2 class="fw-bold mb-3 text-dark">Ready to Reconnect?</h2>
    <p class="lead mb-4 text-dark opacity-75" style="max-width: 600px; margin: 0 auto;">Join hundreds of alumni who are already sharing opportunities, attending events, and expanding their professional networks.</p>
    <a href="register.php" class="btn btn-dark btn-lg px-5 py-3 btn-animated shadow-sm rounded-pill fw-bold">Create Your Free Profile</a>
  </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
