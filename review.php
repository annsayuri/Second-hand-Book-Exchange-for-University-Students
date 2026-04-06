<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Only logged in users
require_once 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Get seller_id from URL
$seller_id = isset($_GET['seller']) ? (int)$_GET['seller'] : 0;
$book_id   = isset($_GET['book']) ? (int)$_GET['book'] : 0;

// Can't review yourself
if ($seller_id == $user_id) {
    header('Location: /bookbridge/index.php');
    exit();
}

// Get seller info
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$seller_id]);
$seller = $stmt->fetch();

if (!$seller) {
    header('Location: /bookbridge/index.php');
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating  = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5) {
        $error = "⚠️ Please select a rating!";
    } else {
        // Check if already reviewed
        $stmt = $pdo->prepare("SELECT review_id FROM reviews 
                                WHERE reviewer_id = ? AND seller_id = ? 
                                AND book_id = ?");
        $stmt->execute([$user_id, $seller_id, $book_id]);

        if ($stmt->rowCount() > 0) {
            $error = "⚠️ You have already reviewed this seller for this book!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO reviews 
                                   (reviewer_id, seller_id, book_id, rating, comment) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id,
                $seller_id,
                $book_id ?: null,
                $rating,
                $comment
            ]);
            $success = "✅ Review submitted successfully! Thank you! 🌟";
        }
    }
}

// Get existing reviews for this seller
$stmt = $pdo->prepare("SELECT r.*, u.name as reviewer_name 
                        FROM reviews r
                        LEFT JOIN users u ON r.reviewer_id = u.user_id
                        WHERE r.seller_id = ?
                        ORDER BY r.created_at DESC");
$stmt->execute([$seller_id]);
$reviews = $stmt->fetchAll();

$avg_rating = count($reviews) > 0
    ? array_sum(array_column($reviews, 'rating')) / count($reviews)
    : 0;
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <h2 class="text-white fw-bold mb-0">
            <i class="fas fa-star me-2"></i>Write a Review
        </h2>
        <p class="text-white-50 mb-0">
            Share your experience with this seller!
        </p>
    </div>
</div>

<div class="container mt-4 mb-5">
    <div class="row">

        <!-- Seller Info + Review Form -->
        <div class="col-md-5 mb-4">

            <!-- Seller Card -->
            <div class="card shadow mb-4">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-user me-2"></i>Seller
                </div>
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary d-flex align-items-center
                                justify-content-center text-white fw-bold mx-auto mb-3"
                         style="width:70px; height:70px; font-size:1.8rem;">
                        <?php echo strtoupper(substr($seller['name'], 0, 1)); ?>
                    </div>
                    <h5 class="fw-bold">
                        <?php echo htmlspecialchars($seller['name']); ?>
                    </h5>
                    <p class="text-muted small">
                        🏛️ <?php echo htmlspecialchars($seller['university'] ?? 'N/A'); ?>
                    </p>
                    <!-- Current Rating -->
                    <div class="text-warning fs-4">
                        <?php
                        $avg = round($avg_rating);
                        for($i = 1; $i <= 5; $i++) {
                            echo $i <= $avg ? '★' : '☆';
                        }
                        ?>
                    </div>
                    <small class="text-muted">
                        <?php echo number_format($avg_rating, 1); ?>/5
                        (<?php echo count($reviews); ?> reviews)
                    </small>
                </div>
            </div>

            <!-- Review Form -->
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-pen me-2"></i>Your Review
                </div>
                <div class="card-body p-4">

                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">

                        <!-- Star Rating -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                ⭐ Rating *
                            </label>
                            <div class="d-flex gap-2">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="rating"
                                           value="<?php echo $i; ?>"
                                           id="star<?php echo $i; ?>"
                                           required>
                                    <label class="form-check-label text-warning fw-bold"
                                           for="star<?php echo $i; ?>">
                                        <?php echo $i; ?>★
                                    </label>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Comment -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                💬 Comment
                            </label>
                            <textarea name="comment"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Describe your experience with this seller..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-star me-2"></i>Submit Review
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- All Reviews -->
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-star me-2"></i>
                    All Reviews
                    <span class="badge bg-warning text-dark ms-2">
                        <?php echo count($reviews); ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if(count($reviews) > 0): ?>
                        <?php foreach($reviews as $review): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-secondary d-flex
                                                align-items-center justify-content-center
                                                text-white fw-bold"
                                         style="width:38px; height:38px;">
                                        <?php echo strtoupper(substr($review['reviewer_name'], 0, 1)); ?>
                                    </div>
                                    <strong>
                                        <?php echo htmlspecialchars($review['reviewer_name']); ?>
                                    </strong>
                                </div>
                                <div class="text-warning">
                                    <?php for($i=1; $i<=5; $i++)
                                        echo $i <= $review['rating'] ? '★' : '☆'; ?>
                                </div>
                            </div>
                            <?php if($review['comment']): ?>
                            <p class="text-muted mt-2 mb-1 small">
                                <?php echo htmlspecialchars($review['comment']); ?>
                            </p>
                            <?php endif; ?>
                            <small class="text-muted">
                                📅 <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                            </small>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No reviews yet!</h5>
                        <p class="text-muted">Be the first to review! 😊</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
