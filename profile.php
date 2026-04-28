<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireCollege();

$pageTitle = "Alumni Profile - AlumniConnect";
$college = $_SESSION['current_college'];
$uid = intval($_GET['id'] ?? 0);

if (!$uid) {
    header("Location: directory.php");
    exit;
}

// Fetch profile
$stmt = mysqli_prepare($conn, "SELECT u.id, u.name, u.email, u.created_at, p.phone, p.department, p.passing_year, p.company, p.location FROM users u LEFT JOIN alumni_profiles p ON u.id=p.user_id WHERE u.id=? AND u.college_name=?");
mysqli_stmt_bind_param($stmt, 'is', $uid, $college);
mysqli_stmt_execute($stmt);
$alumni = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$alumni) {
    header("Location: directory.php");
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<div class="page-header position-relative overflow-hidden">
  <div class="blob-shape bg-warning" style="width:280px;height:280px;top:-50px;right:-50px;"></div>
  <div class="container position-relative z-1">
    <a href="directory.php" class="btn btn-light btn-sm mb-3"><i class="fas fa-arrow-left me-2"></i>Back to Directory</a>
    <h1 class="fw-bold"><i class="fas fa-user-graduate me-2"></i>Alumni <span class="text-gradient text-white">Profile</span></h1>
  </div>
</div>

<div class="container py-5">
  <div class="row g-4">

    <!-- Profile Card -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-center p-4" style="border-radius:20px;">
        <div class="alumni-avatar mx-auto mb-3" style="width:90px;height:90px;font-size:2.5rem;">
          <?= strtoupper(substr($alumni['name'], 0, 1)) ?>
        </div>
        <h4 class="fw-bold mb-1"><?= htmlspecialchars($alumni['name']) ?></h4>
        <p class="text-muted mb-2"><?= htmlspecialchars($alumni['department'] ?? 'Department not set') ?></p>
        <span class="badge-approved mb-3 d-inline-block">Verified Alumni</span>

        <?php if ($alumni['company']): ?>
          <div class="py-2 px-3 rounded-3 mb-2" style="background:#7c3aed15;">
            <i class="fas fa-building me-2" style="color:#7c3aed;"></i>
            <span class="fw-semibold"><?= htmlspecialchars($alumni['company']) ?></span>
          </div>
        <?php endif; ?>

        <?php if ($alumni['location']): ?>
          <div class="py-2 px-3 rounded-3 mb-2" style="background:#10b98115;">
            <i class="fas fa-map-marker-alt me-2" style="color:#10b981;"></i>
            <span class="fw-semibold"><?= htmlspecialchars($alumni['location']) ?></span>
          </div>
        <?php endif; ?>

        <?php if (isLoggedIn() && $_SESSION['user_id'] != $uid): ?>
          <button class="btn btn-primary w-100 mt-3" id="connectBtn" data-id="<?= $alumni['id'] ?>">
            <i class="fas fa-user-plus me-2"></i>Connect
          </button>
        <?php endif; ?>

        <?php if (isLoggedIn()): ?>
          <a href="chat.php" class="btn btn-outline-primary w-100 mt-2">
            <i class="fas fa-comment me-2"></i>Open Chat
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Details -->
    <div class="col-md-8">
      <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:20px;">
        <h5 class="fw-bold mb-4"><i class="fas fa-info-circle me-2 text-primary"></i>Profile Details</h5>
        <div class="row g-3">
          <?php
          $fields = [
            ['fas fa-user',         'Full Name',     $alumni['name']],
            ['fas fa-university',   'Department',    $alumni['department'] ?? '—'],
            ['fas fa-graduation-cap','Passing Year', $alumni['passing_year'] ?? '—'],
            ['fas fa-building',     'Company',       $alumni['company'] ?? '—'],
            ['fas fa-map-marker-alt','Location',     $alumni['location'] ?? '—'],
            ['fas fa-calendar',     'Member Since',  date('M Y', strtotime($alumni['created_at']))],
          ];
          foreach ($fields as $f): ?>
          <div class="col-md-6">
            <div class="p-3 bg-light rounded-3 border h-100 hover-glow">
              <div class="text-muted small mb-1 fw-bold text-uppercase">
                <i class="<?= $f[0] ?> me-2 text-primary"></i><?= $f[1] ?>
              </div>
              <div class="fw-semibold"><?= htmlspecialchars((string)$f[2]) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- About Section -->
      <div class="card border-0 shadow-sm p-4" style="border-radius:20px;background:linear-gradient(135deg,#7c3aed08,#10b98108);">
        <h5 class="fw-bold mb-3"><i class="fas fa-graduation-cap me-2 text-primary"></i>About</h5>
        <p class="text-muted mb-0">
          <?= htmlspecialchars($alumni['name']) ?> is a proud alumnus of <strong><?= htmlspecialchars($college) ?></strong>,
          graduating from the <strong><?= htmlspecialchars($alumni['department'] ?? 'N/A') ?></strong> department
          <?= $alumni['passing_year'] ? 'in <strong>'.$alumni['passing_year'].'</strong>' : '' ?>.
          <?= $alumni['company'] ? 'Currently working at <strong>'.htmlspecialchars($alumni['company']).'</strong>' : '' ?>
          <?= $alumni['location'] ? ', based in <strong>'.htmlspecialchars($alumni['location']).'</strong>.' : '.' ?>
        </p>
      </div>
    </div>

  </div>
</div>

<script>
const btn = document.getElementById('connectBtn');
if (btn) {
  btn.addEventListener('click', function() {
    const targetId = this.dataset.id;
    const fd = new FormData();
    fd.append('action', 'send');
    fd.append('target_id', targetId);
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    this.disabled = true;
    fetch('api_connection.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          this.innerHTML = '<i class="fas fa-check me-2"></i>Request Sent!';
          this.classList.replace('btn-primary', 'btn-success');
        } else {
          this.innerHTML = '<i class="fas fa-user-plus me-2"></i>Connect';
          this.disabled = false;
          alert(d.error || 'Something went wrong');
        }
      });
  });
}
</script>

<div class="py-4"></div>
<?php include 'includes/footer.php'; ?>
