<?php
session_start();
require_once __DIR__ . '/config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$uid = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$target_id = intval($_POST['target_id'] ?? 0);

if (!$target_id || !$action) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

if ($action === 'connect') {
    // Check if connection already exists
    $chk = mysqli_query($conn, "SELECT id FROM connections WHERE (sender_id=$uid AND receiver_id=$target_id) OR (sender_id=$target_id AND receiver_id=$uid)");
    if(mysqli_num_rows($chk) == 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO connections (sender_id, receiver_id, status) VALUES (?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, 'ii', $uid, $target_id);
        mysqli_stmt_execute($stmt);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Connection already requested']);
    }
} elseif ($action === 'accept') {
    // accept the connection requested by target_id
    mysqli_query($conn, "UPDATE connections SET status='accepted' WHERE sender_id=$target_id AND receiver_id=$uid");
    echo json_encode(['success' => true]);
} elseif ($action === 'reject') {
    // reject
    mysqli_query($conn, "UPDATE connections SET status='rejected' WHERE sender_id=$target_id AND receiver_id=$uid");
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
}
?>
