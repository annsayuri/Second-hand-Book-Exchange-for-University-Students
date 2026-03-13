<?php require_once 'includes/header.php'; ?>
<?php require_once 'includes/db.php'; ?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <h1>📚 BookBridge Sri Lanka</h1>
        <p>Buy, Sell & Exchange Second-Hand University Textbooks</p>
        <p class="opacity-75">Save money. Help fellow students. Go green! 🌱</p>
        <a href="/bookbridge/listings.php" class="btn btn-warning btn-lg me-3">
            <i class="fas fa-search me-2"></i>Browse Books
        </a>
        <a href="/bookbridge/register.php" class="btn btn-outline-light btn-lg">
            <i class="fas fa-user-plus me-2"></i>Join Free
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-5">

    <!-- Stats Row -->
    <div class="row text-center mb-5">
        <div class="col-md-4 mb-3">
            <div class="card p-4">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h3 class="fw-bold text-primary">500+</h3>
                <p class="text-muted">Books Available</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card p-4">
                <i class="fas fa-users fa-3x text-success mb-3"></i>
                <h3 class="fw-bold text-success">1000+</h3>
                <p class="text-muted">Students Registered</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card p-4">
                <i class="fas fa-university fa-3x text-warning mb-3"></i>
                <h3 class="fw-bold text-warning">20+</h3>
                <p class="text-muted">Universities</p>
            </div>
        </div>
    </div>

    <!-- Latest Books Section -->
    <div class="text-center mb-4">
        <h2 class="section-title">📖 Latest Books</h2>
        <p class="section-subtitle">Recently posted books by students</p>
    </div>

    <div class="row">
        <?php
        try {
            $stmt = $pdo->query("SELECT b.*, c.name as category_name 
                                FROM books b 
                                LEFT JOIN categories c ON b.category_id = c.category_id 
                                ORDER BY b.created_at DESC 
                                LIMIT 6");
            $books = $stmt->fetchAll();

            if(count($books) > 0) {
                foreach($books as $book) { ?>
                    <div class="col-md-4 mb-4">
                        <div class="book-card card h-100">
                            <img src="<?php echo $book['image'] ? '/bookbridge/uploads/'.$book['image'] : '/bookbridge/assets/images/no-book.png'; ?>" 
                                 alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">
                                    <?php echo htmlspecialchars($book['category_name'] ?? 'General'); ?>
                                </span>
                                <h5 class="fw-bold"><?php echo htmlspecialchars($book['title']); ?></h5>
                                <p class="text-muted small"><?php echo htmlspecialchars($book['author']); ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="book-price">LKR <?php echo number_format($book['price'], 2); ?></span>
                                    <span class="badge book-condition bg-success">
                                        <?php echo $book['book_condition']; ?>
                                    </span>
                                </div>
                                <a href="/bookbridge/book-detail.php?id=<?php echo $book['book_id']; ?>" 
                                   class="btn btn-primary w-100 mt-3">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No books yet!</h4>
                    <p class="text-muted">Be the first to post a book 😊</p>
                    <a href="/bookbridge/post-book.php" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Post First Book
                    </a>
                </div>
            <?php } ?>
        <?php } catch(Exception $e) { ?>
            <div class="col-12">
                <div class="alert alert-danger">Something went wrong! 😅</div>
            </div>
        <?php } ?>
    </div>

    <!-- How It Works Section -->
    <div class="text-center mt-5 mb-4">
        <h2 class="section-title">🤔 How It Works</h2>
        <p class="section-subtitle">Super simple — 3 easy steps!</p>
    </div>

    <div class="row text-center mb-5">
        <div class="col-md-4 mb-3">
            <div class="card p-4 h-100">
                <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                <h5 class="fw-bold">1. Register Free</h5>
                <p class="text-muted">Create your free account using your university email</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card p-4 h-100">
                <i class="fas fa-upload fa-3x text-success mb-3"></i>
                <h5 class="fw-bold">2. Post Your Book</h5>
                <p class="text-muted">List your used textbooks with photos and set your price</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card p-4 h-100">
                <i class="fas fa-handshake fa-3x text-warning mb-3"></i>
                <h5 class="fw-bold">3. Connect & Sell</h5>
                <p class="text-muted">Chat with buyers, meet up and complete the exchange!</p>
            </div>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>