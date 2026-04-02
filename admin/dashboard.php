<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: /bookbridge/index.php');
    exit();
}

// Get statistics
$total_users  = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_books  = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$available    = $pdo->query("SELECT COUNT(*) FROM books WHERE status='available'")->fetchColumn();
$sold         = $pdo->query("SELECT COUNT(*) FROM books WHERE status='sold'")->fetchColumn();
$total_msgs   = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$total_reviews= $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

// Recent users
$recent_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Recent books
$recent_books = $pdo->query("SELECT b.*, u.name as seller_name 
                              FROM books b 
                              LEFT JOIN users u ON b.seller_id = u.user_id 
                              ORDER BY b.created_at DESC LIMIT 5")->fetchAll();
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <h2 class="text-white fw-bold mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
        </h2>
        <p class="text-white-50 mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>! 👋</p>
    </div>
</div>

<div class="container mt-4 mb-5">

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                <h4 class="fw-bold text-primary"><?php echo $total_users; ?></h4>
                <small class="text-muted">Total Users</small>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-book fa-2x text-success mb-2"></i>
                <h4 class="fw-bold text-success"><?php echo $total_books; ?></h4>
                <small class="text-muted">Total Books</small>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-check-circle fa-2x text-info mb-2"></i>
                <h4 class="fw-bold text-info"><?php echo $available; ?></h4>
                <small class="text-muted">Available</small>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-shopping-cart fa-2x text-warning mb-2"></i>
                <h4 class="fw-bold text-warning"><?php echo $sold; ?></h4>
                <small class="text-muted">Sold</small>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-envelope fa-2x text-danger mb-2"></i>
                <h4 class="fw-bold text-danger"><?php echo $total_msgs; ?></h4>
                <small class="text-muted">Messages</small>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card shadow text-center p-3">
                <i class="fas fa-star fa-2x text-warning mb-2"></i>
                <h4 class="fw-bold text-warning"><?php echo $total_reviews; ?></h4>
                <small class="text-muted">Reviews</small>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-tools me-2"></i>Quick Actions
                </div>
                <div class="card-body">
                    <a href="/bookbridge/admin/manage-users.php"
                       class="btn btn-primary me-2 mb-2">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                    <a href="/bookbridge/admin/manage-books.php"
                       class="btn btn-success me-2 mb-2">
                        <i class="fas fa-book me-2"></i>Manage Books
                    </a>
                    <a href="/bookbridge/listings.php"
                       class="btn btn-info me-2 mb-2">
                        <i class="fas fa-eye me-2"></i>View Listings
                    </a>
                    <a href="/bookbridge/index.php"
                       class="btn btn-secondary me-2 mb-2">
                        <i class="fas fa-home me-2"></i>View Website
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Recent Users -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-users me-2"></i>Recent Users
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Name</th>
                                <th>University</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_users as $u): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($u['name']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($u['email']); ?>
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <?php echo htmlspecialchars($u['university'] ?? 'N/A'); ?>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Books -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-book me-2"></i>Recent Books
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Title</th>
                                <th>Seller</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_books as $book): ?>
                            <tr>
                                <td>
                                    <small>
                                        <strong>
                                            <?php echo htmlspecialchars($book['title']); ?>
                                        </strong>
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <?php echo htmlspecialchars($book['seller_name']); ?>
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        LKR <?php echo number_format($book['price'], 2); ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $book['status'] == 'available' 
                                        ? 'bg-success' : 'bg-danger'; ?> small">
                                        <?php echo ucfirst($book['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>