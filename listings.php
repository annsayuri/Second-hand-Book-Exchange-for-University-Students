<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Search and filter
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$category   = isset($_GET['category']) ? trim($_GET['category']) : '';
$condition  = isset($_GET['condition']) ? trim($_GET['condition']) : '';
$sort       = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';

// Build query
$query  = "SELECT b.*, c.name as category_name, u.name as seller_name, u.university 
           FROM books b 
           LEFT JOIN categories c ON b.category_id = c.category_id
           LEFT JOIN users u ON b.seller_id = u.user_id
           WHERE b.status = 'available'";

$params = [];

if (!empty($search)) {
    $query .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.subject LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND b.category_id = ?";
    $params[] = $category;
}

if (!empty($condition)) {
    $query .= " AND b.book_condition = ?";
    $params[] = $condition;
}

// Sorting
if ($sort == 'price_low')       $query .= " ORDER BY b.price ASC";
elseif ($sort == 'price_high')  $query .= " ORDER BY b.price DESC";
else                            $query .= " ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Get categories for filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <h2 class="text-white fw-bold mb-0">
            <i class="fas fa-book me-2"></i>Browse Books
        </h2>
        <p class="text-white-50 mb-0">Find your next textbook at the best price!</p>
    </div>
</div>

<div class="container mt-4 mb-5">
    <div class="row">

        <!-- Filters Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold" style="background-color: #1F4E79; color:white;">
                    <i class="fas fa-filter me-2"></i>Filter Books
                </div>
                <div class="card-body">
                    <form method="GET" action="">

                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">🔍 Search</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Title, author..."
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">🗂️ Category</label>
                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>"
                                        <?php echo $category == $cat['category_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Condition -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">📖 Condition</label>
                            <select name="condition" class="form-control">
                                <option value="">All Conditions</option>
                                <option value="New" <?php echo $condition=='New'?'selected':''; ?>>New</option>
                                <option value="Like New" <?php echo $condition=='Like New'?'selected':''; ?>>Like New</option>
                                <option value="Good" <?php echo $condition=='Good'?'selected':''; ?>>Good</option>
                                <option value="Fair" <?php echo $condition=='Fair'?'selected':''; ?>>Fair</option>
                                <option value="Poor" <?php echo $condition=='Poor'?'selected':''; ?>>Poor</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">↕️ Sort By</label>
                            <select name="sort" class="form-control">
                                <option value="newest" <?php echo $sort=='newest'?'selected':''; ?>>Newest First</option>
                                <option value="price_low" <?php echo $sort=='price_low'?'selected':''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sort=='price_high'?'selected':''; ?>>Price: High to Low</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                            <a href="/bookbridge/listings.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Books Grid -->
        <div class="col-md-9">

            <!-- Results count -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0">
                    <strong><?php echo count($books); ?></strong> books found
                </p>
                <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/bookbridge/post-book.php" class="btn btn-warning btn-sm fw-bold">
                    <i class="fas fa-plus me-1"></i>Post a Book
                </a>
                <?php endif; ?>
            </div>

            <?php if(count($books) > 0): ?>
            <div class="row">
                <?php foreach($books as $book): ?>
                <div class="col-md-4 mb-4">
                    <div class="book-card card h-100">
                        <img src="<?php echo $book['image'] 
                            ? '/bookbridge/uploads/'.$book['image'] 
                            : '/bookbridge/assets/images/no-book.png'; ?>"
                             alt="<?php echo htmlspecialchars($book['title']); ?>">
                        <div class="card-body">
                            <span class="badge bg-primary mb-2 small">
                                <?php echo htmlspecialchars($book['category_name'] ?? 'General'); ?>
                            </span>
                            <h6 class="fw-bold"><?php echo htmlspecialchars($book['title']); ?></h6>
                            <p class="text-muted small mb-1">
                                ✍️ <?php echo htmlspecialchars($book['author'] ?? 'Unknown'); ?>
                            </p>
                            <p class="text-muted small mb-2">
                                🏛️ <?php echo htmlspecialchars($book['university'] ?? ''); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="book-price">
                                    LKR <?php echo number_format($book['price'], 2); ?>
                                </span>
                                <span class="badge bg-success small">
                                    <?php echo htmlspecialchars($book['book_condition']); ?>
                                </span>
                            </div>
                            <a href="/bookbridge/book-detail.php?id=<?php echo $book['book_id']; ?>"
                               class="btn btn-primary w-100 mt-3 btn-sm">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No books found!</h4>
                <p class="text-muted">Try different search terms or filters 😊</p>
                <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/bookbridge/post-book.php" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-1"></i>Post First Book
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
