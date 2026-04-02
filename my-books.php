<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Only logged in users
if (!isset($_SESSION['user_id'])) {
    header('Location: /bookbridge/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Handle delete book
if (isset($_GET['delete'])) {
    $book_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM books 
                            WHERE book_id = ? AND seller_id = ?");
    $stmt->execute([$book_id, $user_id]);
    $success = "✅ Book deleted successfully!";
}

// Handle status change
if (isset($_GET['status']) && isset($_GET['id'])) {
    $book_id    = (int)$_GET['id'];
    $new_status = $_GET['status'];
    if (in_array($new_status, ['available', 'sold', 'reserved'])) {
        $stmt = $pdo->prepare("UPDATE books SET status = ? 
                                WHERE book_id = ? AND seller_id = ?");
        $stmt->execute([$new_status, $book_id, $user_id]);
        $success = "✅ Book status updated!";
    }
}

// Get my books
$stmt = $pdo->prepare("SELECT b.*, c.name as category_name
                        FROM books b
                        LEFT JOIN categories c ON b.category_id = c.category_id
                        WHERE b.seller_id = ?
                        ORDER BY b.created_at DESC");
$stmt->execute([$user_id]);
$my_books = $stmt->fetchAll();

// Stats
$total     = count($my_books);
$available = 0;
$sold      = 0;
$reserved  = 0;
foreach($my_books as $b) {
    if($b['status'] == 'available') $available++;
    if($b['status'] == 'sold')      $sold++;
    if($b['status'] == 'reserved')  $reserved++;
}
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white fw-bold mb-0">
                    <i class="fas fa-book me-2"></i>My Books
                </h2>
                <p class="text-white-50 mb-0">
                    Manage your book listings!
                </p>
            </div>
            <a href="/bookbridge/post-book.php"
               class="btn btn-warning fw-bold">
                <i class="fas fa-plus me-2"></i>Post New Book
            </a>
        </div>
    </div>
</div>

<div class="container mt-4 mb-5">

    <!-- Messages -->
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-book fa-2x text-primary mb-2"></i>
                <h4 class="fw-bold text-primary"><?php echo $total; ?></h4>
                <small class="text-muted">Total Books</small>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h4 class="fw-bold text-success"><?php echo $available; ?></h4>
                <small class="text-muted">Available</small>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-shopping-cart fa-2x text-danger mb-2"></i>
                <h4 class="fw-bold text-danger"><?php echo $sold; ?></h4>
                <small class="text-muted">Sold</small>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-lock fa-2x text-warning mb-2"></i>
                <h4 class="fw-bold text-warning"><?php echo $reserved; ?></h4>
                <small class="text-muted">Reserved</small>
            </div>
        </div>
    </div>

    <!-- Books Table -->
    <?php if(count($my_books) > 0): ?>
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #1F4E79; color: white;">
                        <tr>
                            <th>Book</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Condition</th>
                            <th>Status</th>
                            <th>Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($my_books as $book): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($book['title']); ?>
                                </strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($book['author'] ?? ''); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-primary small">
                                    <?php echo htmlspecialchars($book['category_name'] ?? 'General'); ?>
                                </span>
                            </td>
                            <td>
                                <strong>
                                    LKR <?php echo number_format($book['price'], 2); ?>
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $book['book_condition']; ?>
                                </span>
                            </td>
                            <td>
                                <!-- Status Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-sm <?php echo $book['status'] == 'available' 
                                        ? 'btn-success' : ($book['status'] == 'sold' 
                                        ? 'btn-danger' : 'btn-warning'); ?> dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                        <?php echo ucfirst($book['status']); ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item"
                                               href="?id=<?php echo $book['book_id']; ?>&status=available">
                                                ✅ Available
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                               href="?id=<?php echo $book['book_id']; ?>&status=sold">
                                                💰 Sold
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                               href="?id=<?php echo $book['book_id']; ?>&status=reserved">
                                                🔒 Reserved
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('d M Y', 
                                        strtotime($book['created_at'])); ?>
                                </small>
                            </td>
                            <td>
                                <!-- View -->
                                <a href="/bookbridge/book-detail.php?id=<?php echo $book['book_id']; ?>"
                                   class="btn btn-sm btn-outline-primary mb-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <!-- Edit -->
                                <a href="/bookbridge/edit-book.php?id=<?php echo $book['book_id']; ?>"
                                   class="btn btn-sm btn-outline-success mb-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Delete -->
                                <a href="?delete=<?php echo $book['book_id']; ?>"
                                   class="btn btn-sm btn-danger mb-1"
                                   onclick="return confirm('Delete this book?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">No books posted yet!</h4>
        <p class="text-muted">Start selling your textbooks today! 😊</p>
        <a href="/bookbridge/post-book.php" class="btn btn-primary mt-2">
            <i class="fas fa-plus me-2"></i>Post Your First Book
        </a>
    </div>
    <?php endif; ?>

</div>

<?php require_once 'includes/footer.php'; ?>
