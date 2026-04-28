<?php
session_start();
require_once 'config/db.php';
require_once 'includes/auth.php';

requireAlumni();
requireCollege();

$uid = $_SESSION['user_id'];
$college = $_SESSION['current_college'];

// Get all accepted connections
$friendsQ = mysqli_query($conn, "
    SELECT u.id, u.name, p.department 
    FROM connections c
    JOIN users u ON (u.id = c.sender_id OR u.id = c.receiver_id) AND u.id != $uid
    LEFT JOIN alumni_profiles p ON p.user_id = u.id
    WHERE (c.sender_id = $uid OR c.receiver_id = $uid) 
      AND c.status = 'accepted'
    ORDER BY u.name ASC
");

$friends = [];
while ($f = mysqli_fetch_assoc($friendsQ)) {
    $friends[] = $f;
}

$active_friend_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : ($friends[0]['id'] ?? 0);
$active_friend = null;
foreach ($friends as $f) {
    if ($f['id'] == $active_friend_id) {
        $active_friend = $f;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message']) && $active_friend_id) {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iis', $uid, $active_friend_id, $msg);
        mysqli_stmt_execute($stmt);
        header("Location: chat.php?user_id=$active_friend_id");
        exit;
    }
}

// Get messages for active friend
$messages = [];
if ($active_friend_id) {
    // Mark as read
    mysqli_query($conn, "UPDATE messages SET is_read=1 WHERE sender_id=$active_friend_id AND receiver_id=$uid");
    
    $msgQ = mysqli_query($conn, "
        SELECT * FROM messages 
        WHERE (sender_id=$uid AND receiver_id=$active_friend_id) OR (sender_id=$active_friend_id AND receiver_id=$uid)
        ORDER BY sent_at ASC
    ");
    while ($m = mysqli_fetch_assoc($msgQ)) {
        $messages[] = $m;
    }
}

$pageTitle = "Messages - AlumniConnect";
?>
<?php include 'includes/header.php'; ?>
<style>
.chat-container { height: 600px; display: flex; border: 1px solid #e2e8f0; border-radius: 1rem; overflow: hidden; background: #fff;}
.chat-sidebar { width: 300px; border-right: 1px solid #e2e8f0; display: flex; flex-direction: column; background: #f8fafc;}
.chat-sidebar-header { padding: 1rem; border-bottom: 1px solid #e2e8f0; font-weight: bold; font-size: 1.1rem; flex-shrink: 0;}
.chat-list { flex-grow: 1; overflow-y: auto; overflow-x: hidden;}
.chat-friend-item { padding: 1rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 0.75rem; cursor: pointer; text-decoration: none; color: inherit; transition: background 0.2s;}
.chat-friend-item:hover { background: #f1f5f9; }
.chat-friend-item.active { background: var(--bs-primary); color: #fff;}
.chat-friend-item.active .text-muted { color: #e2e8f0 !important; }
.chat-main { flex-grow: 1; display: flex; flex-direction: column; }
.chat-header { padding: 1rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1rem; flex-shrink: 0; }
.chat-messages { flex-grow: 1; padding: 1rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; background: #f8fafc; }
.chat-bubble { max-width: 70%; padding: 0.75rem 1rem; border-radius: 1rem; position: relative;}
.chat-bubble.sent { background: var(--bs-primary); color: #fff; align-self: flex-end; border-bottom-right-radius: 0;}
.chat-bubble.received { background: #e2e8f0; color: #1e293b; align-self: flex-start; border-bottom-left-radius: 0;}
.chat-input { padding: 1rem; border-top: 1px solid #e2e8f0; background: #fff; flex-shrink: 0;}
</style>

<div class="page-header position-relative overflow-hidden py-4">
  <div class="blob-shape bg-warning" style="width:200px; height:200px; top:-50px; left:-50px; animation-delay:1s;"></div>
  <div class="container position-relative z-1">
    <h2 class="fw-bold"><i class="fas fa-comments me-2"></i>My <span class="text-gradient text-white">Messages</span></h2>
  </div>
</div>

<div class="container pb-5 pt-3">
    <div class="chat-container shadow-sm">
        
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                My Connections
            </div>
            <div class="chat-list">
                <?php if (empty($friends)): ?>
                    <div class="p-4 text-center text-muted small">You don't have any connections yet. Go to Discover to find alumni.</div>
                <?php else: ?>
                    <?php foreach ($friends as $f): ?>
                        <a href="?user_id=<?= $f['id'] ?>" class="chat-friend-item <?= $active_friend_id == $f['id'] ? 'active' : '' ?>">
                            <div class="alumni-avatar d-flex justify-content-center align-items-center mb-0" style="width:40px; height:40px; font-size:1rem; flex-shrink:0;">
                                <?= strtoupper(substr($f['name'], 0, 1)) ?>
                            </div>
                            <div style="flex-grow:1; min-width:0;">
                                <div class="fw-bold text-truncate"><?= htmlspecialchars($f['name']) ?></div>
                                <div class="text-muted small text-truncate" style="font-size: 0.75rem;"><?= htmlspecialchars($f['department']) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Main Chat -->
        <div class="chat-main">
            <?php if ($active_friend_id && $active_friend): ?>
                <div class="chat-header bg-white">
                    <div class="alumni-avatar d-flex justify-content-center align-items-center mb-0" style="width:40px; height:40px; font-size:1rem;">
                        <?= strtoupper(substr($active_friend['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($active_friend['name']) ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($active_friend['department']) ?></small>
                    </div>
                </div>
                
                <div class="chat-messages" id="chatMessages">
                    <?php if (empty($messages)): ?>
                        <div class="text-center text-muted my-auto">
                            No messages yet. Send a message to start the conversation!
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $m): 
                            $is_sent = ($m['sender_id'] == $uid);
                        ?>
                            <div class="chat-bubble <?= $is_sent ? 'sent' : 'received' ?>">
                                <?= nl2br(htmlspecialchars($m['message'])) ?>
                                <div style="font-size: 0.65rem; text-align: <?= $is_sent ? 'right' : 'right' ?>; opacity: 0.7; margin-top: 4px;">
                                    <?= date('M d, H:i', strtotime($m['sent_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="chat-input">
                    <form method="POST" class="d-flex gap-2">
                        <input type="text" name="message" class="form-control rounded-pill" placeholder="Type a message..." required autofocus autocomplete="off">
                        <button type="submit" name="send_message" class="btn btn-primary rounded-pill px-4" style="background: var(--bs-primary); border-color: var(--bs-primary);"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
                <script>
                    const msgContainer = document.getElementById('chatMessages');
                    msgContainer.scrollTop = msgContainer.scrollHeight;
                </script>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center flex-column h-100 bg-light text-muted">
                    <i class="fas fa-comments fa-4x mb-3 text-secondary" style="opacity: 0.3;"></i>
                    <h5>Select a conversation to start chatting</h5>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>
