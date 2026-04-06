<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Get book ID from URL
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($book_id == 0) {
    header('Location: /bookbridge/listings.php');
    exit();
}

// Get book details
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name, 
                        u.name as seller_name, u.email as seller_email,
                        u.university as seller_university,
                        u.phone as seller_phone, u.user_id as seller_id
                        FROM books b
                        LEFT JOIN categories c ON b.category_id = c.category_id
                        LEFT JOIN users u ON b.seller_id = u.user_id
                        WHERE b.book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

// If book not found
if (!$book) {
    header('Location: /bookbridge/listings.php');
    exit();
}

// Get seller's average rating
$rating_stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, 
                               COUNT(*) as total_reviews 
                               FROM reviews WHERE seller_id = ?");
$rating_stmt->execute([$book['seller_id']]);
$rating = $rating_stmt->fetch();
?>

<div class="container mt-4 mb-5">

    <!-- Back Button -->
    <a href="/bookbridge/listings.php" class="btn btn-outline-secondary mb-4">
        <i class="fas fa-arrow-left me-2"></i>Back to Listings
    </a>

    <div class="row">

        <!-- Book Image -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <img src="<?php echo $book['image'] 
                    ? '/bookbridge/uploads/'.$book['image'] 
                    : '/bookbridge/assets/images/no-book.png'; ?>"
                     class="card-img-top"
                     style="height: 350px; object-fit: cover;"
                     alt="<?php echo htmlspecialchars($book['title']); ?>">
                <div class="card-body text-center">
                    <h3 class="book-price">
                        LKR <?php echo number_format($book['price'], 2); ?>
                    </h3>
                    <span class="badge bg-success p-2 mb-3">
                        <?php echo htmlspecialchars($book['book_condition']); ?>
                    </span>

                    <?php if(isset($_SESSION['user_id']) && 
                              $_SESSION['user_id'] != $book['seller_id']): ?>
                    <div class="d-grid gap-2">
                        <a href="/bookbridge/messages.php?to=<?php echo $book['seller_id']; ?>&book=<?php echo $book['book_id']; ?>"
                           class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i>Contact Seller
                        </a>
                        <a href="/bookbridge/wishlist.php?add=<?php echo $book['book_id']; ?>"
                           class="btn btn-outline-danger">
                            <i class="fas fa-heart me-2"></i>Add to Wishlist
                        </a>
                        <a href="/bookbridge/review.php?seller=<?php echo $book['seller_id']; ?>&book=<?php echo $book['book_id']; ?>"
                           class="btn btn-outline-warning mt-2 w-100">
                            <i class="fas fa-star me-2"></i>Write a Review
                        </a>
                    </div>
                    <?php elseif(!isset($_SESSION['user_id'])): ?>
                    <div class="d-grid">
                        <a href="/bookbridge/login.php"
                           class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Contact
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Book Details -->
        <div class="col-md-8 mb-4">
            <div class="card shadow">
                <div class="card-body p-4">

                    <!-- Category Badge -->
                    <span class="badge bg-primary mb-2">
                        <?php echo htmlspecialchars($book['category_name'] ?? 'General'); ?>
                    </span>

                    <!-- Title -->
                    <h2 class="fw-bold" style="color: #1F4E79;">
                        <?php echo htmlspecialchars($book['title']); ?>
                    </h2>

                    <!-- Details Table -->
                    <table class="table table-bordered mt-3">
                        <tr>
                            <td class="fw-bold" style="width:35%; background:#f8f9fa;">
                                ✍️ Author
                            </td>
                            <td><?php echo htmlspecialchars($book['author'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="background:#f8f9fa;">
                                🔖 Edition
                            </td>
                            <td><?php echo htmlspecialchars($book['edition'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="background:#f8f9fa;">
                                🎓 Subject
                            </td>
                            <td><?php echo htmlspecialchars($book['subject'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="background:#f8f9fa;">
                                🏛️ University
                            </td>
                            <td><?php echo htmlspecialchars($book['university'] ?? 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="background:#f8f9fa;">
                                📅 Posted On
                            </td>
                            <td><?php echo date('d M Y', strtotime($book['created_at'])); ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="background:#f8f9fa;">
                                📦 Status
                            </td>
                            <td>
                                <span class="badge <?php echo $book['status'] == 'available' 
                                    ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo ucfirst($book['status']); ?>
                                </span>
                            </td>
                        </tr>
                    </table>

                    <!-- Description -->
                    <?php if($book['description']): ?>
                    <div class="mt-3">
                        <h6 class="fw-bold" style="color: #1F4E79;">
                            📝 Description
                        </h6>
                        <p class="text-muted">
                            <?php echo nl2br(htmlspecialchars($book['description'])); ?>
                        </p>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Seller Info -->
            <div class="card shadow mt-4">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-user me-2"></i>Seller Information
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center 
                                    justify-content-center text-white fw-bold"
                             style="width:55px; height:55px; font-size:1.3rem;">
                            <?php echo strtoupper(substr($book['seller_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">
                                <?php echo htmlspecialchars($book['seller_name']); ?>
                            </h6>
                            <small class="text-muted">
                                🏛️ <?php echo htmlspecialchars($book['seller_university'] ?? ''); ?>
                            </small><br>
                            <!-- Star Rating -->
                            <small class="text-warning">
                                <?php
                                $avg = round($rating['avg_rating'] ?? 0);
                                for($i = 1; $i <= 5; $i++) {
                                    echo $i <= $avg ? '★' : '☆';
                                }
                                ?>
                                <span class="text-muted">
                                    (<?php echo $rating['total_reviews']; ?> reviews)
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
