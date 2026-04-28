<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
$pageTitle = "Login - AlumniConnect";

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    if (empty($email) || empty($pass)) {
        $error = "Please enter your email and password.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, password, role, status, college_name FROM users WHERE email=?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($pass, $user['password'])) {
            if ($user['status'] === 'pending') {
                $error = "Your account is pending admin approval. Please wait.";
            } elseif ($user['status'] === 'rejected') {
                $error = "Your registration was rejected. Please contact admin.";
            } else {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = $user['role'];
                if (!empty($user['college_name'])) {
                    $_SESSION['current_college'] = $user['college_name'];
                }

                header("Location: index.php");
                exit;
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="page-header">
  <div class="container">
    <h1><i class="fas fa-sign-in-alt me-2"></i>Login</h1>
    <p>Sign in to your AlumniConnect account</p>
  </div>
</div>

<div class="container pb-5">
  <div class="form-card">

    <?php if ($error): ?>
      <div class="alert alert-danger auto-dismiss"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="you@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Your password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="fas fa-sign-in-alt me-2"></i>Login
      </button>
    </form>

    <hr class="my-4">
    <div class="text-center small text-muted">
      <p class="mb-1">Don't have an account? <a href="register.php" class="text-primary fw-semibold">Register here</a></p>
      <p class="mb-0">
        <span class="badge bg-primary me-2">Demo Admin</span>admin@alumni.com / password
      </p>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
