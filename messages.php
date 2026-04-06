<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Only logged in users
require_once 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Handle send message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_id  = (int)$_POST['receiver_id'];
    $book_id      = (int)$_POST['book_id'];
    $message_text = trim($_POST['message_text']);

    if (empty($message_text)) {
        $error = "⚠️ Message cannot be empty!";
    } elseif ($receiver_id == $user_id) {
        $error = "⚠️ You cannot message yourself!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages 
                               (sender_id, receiver_id, book_id, message_text) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $receiver_id,
            $book_id ?: null,
            $message_text
        ]);
        $success = "✅ Message sent successfully!";
    }
}

// Get selected conversation
$chat_with = isset($_GET['to']) ? (int)$_GET['to'] : 0;
$book_id   = isset($_GET['book']) ? (int)$_GET['book'] : 0;

// Get all conversations for this user
$conversations = $pdo->prepare("SELECT DISTINCT
    CASE WHEN m.sender_id = ? THEN m.receiver_id 
         ELSE m.sender_id END as other_user_id,
    u.name as other_user_name,
    MAX(m.sent_at) as last_message_time,
    SUM(CASE WHEN m.receiver_id = ? AND m.is_read = 0 THEN 1 ELSE 0 END) as unread
    FROM messages m
    LEFT JOIN users u ON u.user_id = 
        CASE WHEN m.sender_id = ? THEN m.receiver_id 
             ELSE m.sender_id END
    WHERE m.sender_id = ? OR m.receiver_id = ?
    GROUP BY other_user_id, other_user_name
    ORDER BY last_message_time DESC");
$conversations->execute([$user_id, $user_id, $user_id, $user_id, $user_id]);
$conversations = $conversations->fetchAll();

// Get messages for selected conversation
$messages = [];
$chat_user = null;
$chat_book = null;

if ($chat_with > 0) {
    // Get chat user info
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$chat_with]);
    $chat_user = $stmt->fetch();

    // Get book info if provided
    if ($book_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
        $stmt->execute([$book_id]);
        $chat_book = $stmt->fetch();
    }

    // Get messages
    $stmt = $pdo->prepare("SELECT m.*, 
                            u.name as sender_name
                            FROM messages m
                            LEFT JOIN users u ON m.sender_id = u.user_id
                            WHERE (m.sender_id = ? AND m.receiver_id = ?)
                            OR (m.sender_id = ? AND m.receiver_id = ?)
                            ORDER BY m.sent_at ASC");
    $stmt->execute([$user_id, $chat_with, $chat_with, $user_id]);
    $messages = $stmt->fetchAll();

    // Mark messages as read
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 
                            WHERE receiver_id = ? AND sender_id = ?");
    $stmt->execute([$user_id, $chat_with]);
}
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <h2 class="text-white fw-bold mb-0">
            <i class="fas fa-envelope me-2"></i>Messages
        </h2>
        <p class="text-white-50 mb-0">Chat with buyers and sellers!</p>
    </div>
</div>

<div class="container mt-4 mb-5">
    <div class="row">

        <!-- Conversations List -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-comments me-2"></i>Conversations
                </div>
                <div class="card-body p-0">
                    <?php if(count($conversations) > 0): ?>
                        <?php foreach($conversations as $conv): ?>
                        <a href="/bookbridge/messages.php?to=<?php echo $conv['other_user_id']; ?>"
                           class="d-flex align-items-center gap-3 p-3 text-decoration-none
                                  border-bottom <?php echo $chat_with == $conv['other_user_id'] 
                                  ? 'bg-light' : ''; ?>">
                            <!-- Avatar -->
                            <div class="rounded-circle bg-primary d-flex align-items-center
                                        justify-content-center text-white fw-bold flex-shrink-0"
                                 style="width:45px; height:45px;">
                                <?php echo strtoupper(substr($conv['other_user_name'], 0, 1)); ?>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="text-dark">
                                    <?php echo htmlspecialchars($conv['other_user_name']); ?>
                                </strong>
                                <?php if($conv['unread'] > 0): ?>
                                <span class="badge bg-danger ms-2">
                                    <?php echo $conv['unread']; ?>
                                </span>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted">
                                    <?php echo date('d M, h:i A', 
                                        strtotime($conv['last_message_time'])); ?>
                                </small>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <p class="text-muted small">No conversations yet!</p>
                        <a href="/bookbridge/listings.php"
                           class="btn btn-primary btn-sm">
                            Browse Books
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="col-md-8">
            <div class="card shadow">
                <?php if($chat_with > 0 && $chat_user): ?>

                <!-- Chat Header -->
                <div class="card-header d-flex align-items-center gap-3"
                     style="background-color: #1F4E79;">
                    <div class="rounded-circle bg-warning d-flex align-items-center
                                justify-content-center fw-bold"
                         style="width:40px; height:40px;">
                        <?php echo strtoupper(substr($chat_user['name'], 0, 1)); ?>
                    </div>
                    <div>
                        <strong class="text-white">
                            <?php echo htmlspecialchars($chat_user['name']); ?>
                        </strong>
                        <br>
                        <small class="text-white-50">
                            <?php echo htmlspecialchars($chat_user['university'] ?? ''); ?>
                        </small>
                    </div>
                </div>

                <!-- Book Reference -->
                <?php if($chat_book): ?>
                <div class="p-3 border-bottom" style="background: #f8f9fa;">
                    <small class="text-muted">
                        📚 About: <strong>
                            <?php echo htmlspecialchars($chat_book['title']); ?>
                        </strong>
                        — LKR <?php echo number_format($chat_book['price'], 2); ?>
                    </small>
                </div>
                <?php endif; ?>

                <!-- Messages -->
                <div class="card-body p-3"
                     style="height: 350px; overflow-y: auto;"
                     id="messageBox">

                    <?php if($success): ?>
                        <div class="alert alert-success py-2 small">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if(count($messages) > 0): ?>
                        <?php foreach($messages as $msg): ?>
                        <div class="d-flex <?php echo $msg['sender_id'] == $user_id 
                            ? 'justify-content-end' : 'justify-content-start'; ?> mb-3">
                            <div class="p-3 rounded-3 <?php echo $msg['sender_id'] == $user_id 
                                ? 'text-white' : 'bg-light text-dark'; ?>"
                                 style="max-width: 70%; <?php echo $msg['sender_id'] == $user_id 
                                 ? 'background-color: #1F4E79;' : ''; ?>">
                                <p class="mb-1 small">
                                    <?php echo nl2br(htmlspecialchars($msg['message_text'])); ?>
                                </p>
                                <small class="opacity-75" style="font-size: 0.75rem;">
                                    <?php echo date('h:i A', strtotime($msg['sent_at'])); ?>
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comment fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No messages yet!<br>
                            Say hello! 👋</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Send Message Form -->
                <div class="card-footer p-3">
                    <?php if($error): ?>
                        <div class="alert alert-danger py-2 small mb-2">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <input type="hidden" name="receiver_id"
                               value="<?php echo $chat_with; ?>">
                        <input type="hidden" name="book_id"
                               value="<?php echo $book_id; ?>">
                        <div class="d-flex gap-2">
                            <textarea name="message_text"
                                      class="form-control"
                                      rows="2"
                                      placeholder="Type your message..."
                                      required></textarea>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <?php else: ?>
                <!-- No chat selected -->
                <div class="card-body text-center py-5">
                    <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Select a conversation</h5>
                    <p class="text-muted small">
                        Or go to a book listing and click
                        "Contact Seller" to start chatting! 😊
                    </p>
                    <a href="/bookbridge/listings.php"
                       class="btn btn-primary mt-2">
                        <i class="fas fa-book me-2"></i>Browse Books
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Auto scroll to bottom of messages -->
<script>
    var messageBox = document.getElementById('messageBox');
    if (messageBox) {
        messageBox.scrollTop = messageBox.scrollHeight;
    }
</script>

<?php require_once 'includes/footer.php'; ?>
