<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requireCollege();

$pageTitle = "Register - AlumniConnect";
$college = $_SESSION['current_college'];

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $pass    = $_POST['password'];
    $pass2   = $_POST['password2'];
    $phone   = trim($_POST['phone']);
    $dept    = trim($_POST['department']);
    $year    = intval($_POST['passing_year']);
    $company = trim($_POST['company']);
    $loc     = trim($_POST['location']);

    // Validation
    if (empty($name) || empty($email) || empty($pass) || empty($dept) || empty($year)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($pass !== $pass2) {
        $error = "Passwords do not match.";
    } elseif ($year < 1990 || $year > date('Y')) {
        $error = "Invalid passing year.";
    } else {
        // Check email exists
        $chk = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
        mysqli_stmt_bind_param($chk, 's', $email);
        mysqli_stmt_execute($chk);
        mysqli_stmt_store_result($chk);

        if (mysqli_stmt_num_rows($chk) > 0) {
            $error = "This email is already registered. <a href='login.php'>Login instead</a>";
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);

            // Insert user — auto-approved so they can login immediately
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role, status, college_name) VALUES (?, ?, ?, 'alumni', 'approved', ?)");
            mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $hashed, $college);

            if (mysqli_stmt_execute($stmt)) {
                $uid = mysqli_insert_id($conn);
                // Insert profile
                $prof = mysqli_prepare($conn, "INSERT INTO alumni_profiles (user_id, phone, department, passing_year, company, location) VALUES (?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($prof, 'ississ', $uid, $phone, $dept, $year, $company, $loc);
                mysqli_stmt_execute($prof);

                // Auto-login: start session immediately after registration
                $_SESSION['user_id']   = $uid;
                $_SESSION['user_name'] = $name;
                $_SESSION['role']      = 'alumni';

                // Redirect straight to index
                header("Location: index.php");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

$departments = ['Computer Science', 'Electronics', 'Mechanical', 'Civil', 'Business Administration', 'Arts & Humanities', 'Science', 'Law', 'Medicine', 'Architecture'];
$currentYear = date('Y');
?>
<?php include 'includes/header.php'; ?>

<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-user-plus me-2"></i>Alumni Registration</h1>
    <p>Create your account to join the alumni network</p>
  </div>
</div>

<div class="container pb-5">
  <div class="form-card">

    <?php if ($error): ?>
      <div class="alert alert-danger auto-dismiss"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" novalidate>

      <div class="form-section-title"><i class="fas fa-user me-2"></i>Account Details</div>
      <div class="row g-3 mb-4">
        <div class="col-12">
          <label class="form-label">Full Name <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control" placeholder="Your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="col-12">
          <label class="form-label">Email Address <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="you@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Password <span class="text-danger">*</span></label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Min 6 characters" oninput="checkPasswordMatch()" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
          <input type="password" name="password2" id="password2" class="form-control" placeholder="Repeat password" oninput="checkPasswordMatch()" required>
          <div id="pw-match-hint" class="form-text"></div>
        </div>
      </div>

      <div class="form-section-title"><i class="fas fa-graduation-cap me-2"></i>Academic & Professional Info</div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Department <span class="text-danger">*</span></label>
          <select name="department" class="form-select" required>
            <option value="">Select Department</option>
            <?php foreach ($departments as $d): ?>
              <option <?= (($_POST['department'] ?? '') === $d) ? 'selected' : '' ?>><?= $d ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Passing Year <span class="text-danger">*</span></label>
          <select name="passing_year" class="form-select" required>
            <option value="">Select Year</option>
            <?php for ($y = $currentYear; $y >= 1990; $y--): ?>
              <option <?= (($_POST['passing_year'] ?? '') == $y) ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" placeholder="e.g. 9876543210" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Current Company</label>
          <input type="text" name="company" class="form-control" placeholder="Where do you work?" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Location / City</label>
          <input type="text" name="location" class="form-control" placeholder="e.g. Mumbai, India" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
        </div>
        <div class="col-12 mt-2">
          <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="fas fa-user-plus me-2"></i>Create Account
          </button>
        </div>
        <div class="col-12 text-center">
          <small class="text-muted">Already registered? <a href="login.php" class="text-primary fw-semibold">Login here</a></small>
        </div>
      </div>

    </form>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
