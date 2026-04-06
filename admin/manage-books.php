<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /bookbridge/index.php');
    exit();
}

$success = "";
$error   = "";

// Handle delete book
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->execute([$delete_id]);
    $success = "✅ Book deleted successfully!";
}

// Handle status change
if (isset($_GET['status']) && isset($_GET['id'])) {
    $book_id    = (int)$_GET['id'];
    $new_status = $_GET['status'];
    if (in_array($new_status, ['available', 'sold', 'reserved'])) {
        $stmt = $pdo->prepare("UPDATE books SET status = ? WHERE book_id = ?");
        $stmt->execute([$new_status, $book_id]);
        $success = "✅ Book status updated!";
    }
}

// Get all books
$books = $pdo->query("SELECT b.*, c.name as category_name,
                       u.name as seller_name
                       FROM books b
                       LEFT JOIN categories c ON b.category_id = c.category_id
                       LEFT JOIN users u ON b.seller_id = u.user_id
                       ORDER BY b.created_at DESC")->fetchAll();
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white fw-bold mb-0">
                    <i class="fas fa-book me-2"></i>Manage Books
                </h2>
                <p class="text-white-50 mb-0">
                    Total: <?php echo count($books); ?> books listed
                </p>
            </div>
            <a href="/bookbridge/admin/dashboard.php"
               class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
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

    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #1F4E79; color: white;">
                        <tr>
                            <th>#</th>
                            <th>Book</th>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Condition</th>
                            <th>Status</th>
                            <th>Posted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($books as $book): ?>
                        <tr>
                            <td><?php echo $book['book_id']; ?></td>
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
                                <small>
                                    <?php echo htmlspecialchars($book['seller_name']); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-primary small">
                                    <?php echo htmlspecialchars($book['category_name'] ?? 'General'); ?>
                                </span>
                            </td>
                            <td>
                                <small>
                                    LKR <?php echo number_format($book['price'], 2); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-info small">
                                    <?php echo $book['book_condition']; ?>
                                </span>
                            </td>
                            <td>
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
                                    <?php echo date('d M Y', strtotime($book['created_at'])); ?>
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
                                   onclick="return confirm('Delete this book? Cannot be undone!')">
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
</div>

<?php require_once '../includes/footer.php'; ?>