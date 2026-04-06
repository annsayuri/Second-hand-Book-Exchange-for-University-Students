<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Only logged in users
require_once 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Handle add to wishlist from book detail page
if (isset($_GET['add'])) {
    $book_id = (int)$_GET['add'];

    // Get book details
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if ($book) {
        $stmt = $pdo->prepare("INSERT INTO wishlist 
                               (user_id, book_title, author, category_id) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $book['title'],
            $book['author'],
            $book['category_id']
        ]);
        $success = "✅ Book added to wishlist!";
    }
}

// Handle delete from wishlist
if (isset($_GET['delete'])) {
    $wishlist_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM wishlist 
                            WHERE wishlist_id = ? AND user_id = ?");
    $stmt->execute([$wishlist_id, $user_id]);
    $success = "✅ Removed from wishlist!";
}

// Handle add manually
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_title  = trim($_POST['book_title']);
    $author      = trim($_POST['author']);
    $category_id = trim($_POST['category_id']);

    if (empty($book_title)) {
        $error = "⚠️ Please enter a book title!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO wishlist 
                               (user_id, book_title, author, category_id) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $book_title,
            $author ?: null,
            $category_id ?: null
        ]);
        $success = "✅ Added to wishlist successfully!";
    }
}

// Get wishlist items
$stmt = $pdo->prepare("SELECT w.*, c.name as category_name 
                        FROM wishlist w
                        LEFT JOIN categories c ON w.category_id = c.category_id
                        WHERE w.user_id = ?
                        ORDER BY w.created_at DESC");
$stmt->execute([$user_id]);
$wishlist = $stmt->fetchAll();

// Get categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <h2 class="text-white fw-bold mb-0">
            <i class="fas fa-heart me-2"></i>My Wishlist
        </h2>
        <p class="text-white-50 mb-0">
            Books you're looking for! 📚
        </p>
    </div>
</div>

<div class="container mt-4 mb-5">
    <div class="row">

        <!-- Add to Wishlist Form -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-plus me-2"></i>Add Book to Wishlist
                </div>
                <div class="card-body">

                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                📚 Book Title *
                            </label>
                            <input type="text" name="book_title"
                                   class="form-control"
                                   placeholder="e.g. Data Structures"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                ✍️ Author
                            </label>
                            <input type="text" name="author"
                                   class="form-control"
                                   placeholder="e.g. Robert Sedgewick">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                🗂️ Category
                            </label>
                            <select name="category_id" class="form-control">
                                <option value="">Select Category</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-heart me-2"></i>Add to Wishlist
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Wishlist Items -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-heart me-2"></i>
                    My Wishlist
                    <span class="badge bg-warning text-dark ms-2">
                        <?php echo count($wishlist); ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if(count($wishlist) > 0): ?>
                        <?php foreach($wishlist as $item): ?>
                        <div class="d-flex justify-content-between 
                                    align-items-center border-bottom pb-3 mb-3">
                            <div>
                                <h6 class="fw-bold mb-1">
                                    📚 <?php echo htmlspecialchars($item['book_title']); ?>
                                </h6>
                                <?php if($item['author']): ?>
                                <small class="text-muted">
                                    ✍️ <?php echo htmlspecialchars($item['author']); ?>
                                </small><br>
                                <?php endif; ?>
                                <?php if($item['category_name']): ?>
                                <span class="badge bg-primary small">
                                    <?php echo htmlspecialchars($item['category_name']); ?>
                                </span>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted">
                                    Added: <?php echo date('d M Y', 
                                        strtotime($item['created_at'])); ?>
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <!-- Search for this book -->
                                <a href="/bookbridge/listings.php?search=<?php echo urlencode($item['book_title']); ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-search me-1"></i>Find
                                </a>
                                <!-- Delete -->
                                <a href="?delete=<?php echo $item['wishlist_id']; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Remove from wishlist?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Your wishlist is empty!</h5>
                        <p class="text-muted">
                            Add books you're looking for 😊
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
