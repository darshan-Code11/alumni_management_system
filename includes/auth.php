<?php
// Auth helper - include at top of protected pages
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function requireCollege() {
    if (!isset($_SESSION['current_college'])) {
        header("Location: select_college.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php");
        exit;
    }
}

function requireAlumni() {
    requireLogin();
    if ($_SESSION['role'] !== 'alumni') {
        header("Location: admin_dashboard.php");
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>
