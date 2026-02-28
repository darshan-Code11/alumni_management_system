<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth.php';
$pageTitle = "Alumni Discover - AlumniConnect";

// Filters
$college    = $_SESSION['current_college'] ?? '';
$search     = trim($_GET['search'] ?? '');
$filterDept = $_GET['dept'] ?? '';
$filterYear = $_GET['year'] ?? '';

$where = ["u.role='alumni'", "u.status='approved'"];
if ($college)    { $where[] = "u.college_name='".mysqli_real_escape_string($conn, $college)."'"; }
if ($search)     { $s = "%".mysqli_real_escape_string($conn, $search)."%"; $where[] = "(u.name LIKE '$s' OR p.company LIKE '$s')"; }
if ($filterDept) { $where[] = "p.department='".mysqli_real_escape_string($conn, $filterDept)."'"; }
if ($filterYear) { $where[] = "p.passing_year=".intval($filterYear); }

$whereStr = implode(' AND ', $where);
$alumni = mysqli_query($conn, "SELECT u.id, u.name, p.department, p.passing_year, p.company, p.location FROM users u LEFT JOIN alumni_profiles p ON u.id=p.user_id WHERE $whereStr ORDER BY p.passing_year DESC, u.name ASC");

$departments = ['Computer Science', 'Electronics', 'Mechanical', 'Civil', 'Business Administration', 'Arts & Humanities', 'Science', 'Law', 'Medicine', 'Architecture'];
$currentYear = date('Y');
$totalCount = mysqli_num_rows($alumni);

$uid = $_SESSION['user_id'] ?? 0;
// Fetch connections for logged-in user
$connections = [];
if ($uid) {
    $connQ = mysqli_query($conn, "SELECT id, sender_id, receiver_id, status FROM connections WHERE sender_id=$uid OR receiver_id=$uid");
    while ($c = mysqli_fetch_assoc($connQ)) {
        $other_id = ($c['sender_id'] == $uid) ? $c['receiver_id'] : $c['sender_id'];
        $connections[$other_id] = [
            'status' => $c['status'],
            'is_sender' => ($c['sender_id'] == $uid)
        ];
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="page-header position-relative overflow-hidden">
  <div class="blob-shape bg-info" style="width:280px; height:280px; top:-50px; right:-50px; animation-delay:1s;"></div>
  <div class="blob-shape bg-primary" style="width:200px; height:200px; bottom:-30px; left:-30px; animation-delay:2.5s;"></div>
  <div class="container position-relative z-1">
    <div class="badge bg-white text-dark mb-2 px-3 py-1 rounded-pill shadow-sm fw-bold"><i class="fas fa-university me-1 text-primary"></i> <?= htmlspecialchars($college) ?></div>
    <h1 class="fw-bold"><i class="fas fa-users me-2"></i>Alumni <span class="text-gradient text-white">Discover</span></h1>
    <p class="fs-5 opacity-75">Browse our network of approved alumni</p>
  </div>
</div>

<div class="container pb-5">

  <!-- Search bar -->
  <form method="GET" class="search-bar mb-4">
    <div class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label mb-1 small fw-semibold">Search Alumni</label>
        <input type="text" name="search" class="form-control" placeholder="Name or company…" value="<?= htmlspecialchars($search) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label mb-1 small fw-semibold">Department</label>
        <select name="dept" class="form-select">
          <option value="">All Departments</option>
          <?php foreach ($departments as $d): ?>
            <option <?= $filterDept===$d?'selected':'' ?>><?= $d ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label mb-1 small fw-semibold">Batch Year</label>
        <select name="year" class="form-select">
          <option value="">All Years</option>
          <?php for ($y = $currentYear; $y >= 1990; $y--): ?>
            <option <?= $filterYear==$y?'selected':'' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100">
          <i class="fas fa-search me-1"></i>Search
        </button>
        <a href="directory.php" class="btn btn-outline-secondary w-100">
          <i class="fas fa-times"></i>
        </a>
      </div>
    </div>
  </form>

  <!-- Result count -->
  <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm border border-light">
    <p class="mb-0 text-muted w-100 text-center fw-medium">
      Showing <strong class="text-gradient fs-5 px-1"><?= $totalCount ?></strong> alumni
      <?= $search || $filterDept || $filterYear ? '<span class="badge bg-light text-primary border ms-2">Filtered</span>' : '' ?>
    </p>
  </div>

  <!-- Alumni cards -->
  <?php if ($totalCount > 0): ?>
  <div class="row g-3">
    <?php while ($a = mysqli_fetch_assoc($alumni)):
      $initial = strtoupper(substr($a['name'], 0, 1));
      $colors = ['#0d6efd', '#6610f2', '#0dcaf0', '#198754', '#dc3545', '#fd7e14'];
      $color  = $colors[crc32($a['name']) % count($colors)];
    ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="alumni-card">
        <div class="alumni-avatar" style="background:<?= $color ?>">
          <?= $initial ?>
        </div>
        <div class="alumni-name"><?= htmlspecialchars($a['name']) ?></div>
        <?php if ($a['department']): ?>
          <span class="alumni-dept"><?= htmlspecialchars($a['department']) ?></span>
        <?php endif; ?>
        <div class="alumni-meta mt-2">
          <?php if ($a['passing_year']): ?>
            <div class="mb-1"><i class="fas fa-graduation-cap"></i> Class of <?= $a['passing_year'] ?></div>
          <?php endif; ?>
          <?php if ($a['company']): ?>
            <div class="mb-1"><i class="fas fa-building"></i> <?= htmlspecialchars($a['company']) ?></div>
          <?php endif; ?>
          <?php if ($a['location']): ?>
            <div><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($a['location']) ?></div>
          <?php endif; ?>
        </div>
        
        <?php if ($uid && $uid != $a['id']): 
            $conn_status = $connections[$a['id']]['status'] ?? '';
            $is_sender = $connections[$a['id']]['is_sender'] ?? false;
        ?>
          <div class="mt-3 text-center border-top pt-3">
            <?php if ($conn_status === 'accepted'): ?>
              <a href="chat.php?user_id=<?= $a['id'] ?>" class="btn btn-outline-primary w-100 rounded-pill"><i class="fas fa-comment me-1"></i> Message</a>
            <?php elseif ($conn_status === 'pending'): ?>
              <?php if ($is_sender): ?>
                <button class="btn btn-light w-100 rounded-pill text-muted border" disabled><i class="fas fa-clock me-1"></i> Requested</button>
              <?php else: ?>
                <button class="btn btn-success w-100 rounded-pill connection-btn" data-action="accept" data-id="<?= $a['id'] ?>"><i class="fas fa-check me-1"></i> Accept</button>
              <?php endif; ?>
            <?php else: ?>
              <button class="btn btn-primary w-100 rounded-pill connection-btn" data-action="connect" data-id="<?= $a['id'] ?>"><i class="fas fa-user-plus me-1"></i> Connect</button>
            <?php endif; ?>
          </div>
        <?php elseif (!$uid): ?>
           <div class="mt-3 text-center border-top pt-3">
              <a href="login.php" class="btn btn-outline-primary w-100 rounded-pill"><i class="fas fa-sign-in-alt me-1"></i> Login to Connect</a>
           </div>
        <?php endif; ?>
        
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <?php else: ?>
  <div class="empty-state">
    <i class="fas fa-search d-block mb-3"></i>
    <h5 class="fw-bold text-muted">No alumni found</h5>
    <p class="text-muted small">Try different search terms or clear filters.</p>
    <a href="directory.php" class="btn btn-outline-primary btn-sm mt-2">Clear Filters</a>
  </div>
  <?php endif; ?>

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

<?php include 'includes/footer.php'; ?>
