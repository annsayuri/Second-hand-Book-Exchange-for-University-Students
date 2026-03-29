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

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name       = trim($_POST['name']);
    $university = trim($_POST['university']);
    $phone      = trim($_POST['phone']);

    if (empty($name)) {
        $error = "⚠️ Name cannot be empty!";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, 
                                university = ?, phone = ? 
                                WHERE user_id = ?");
        $stmt->execute([$name, $university, $phone, $user_id]);

        // Update session
        $_SESSION['name']       = $name;
        $_SESSION['university'] = $university;

        $success = "✅ Profile updated successfully!";
    }
}

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get user's books
$stmt = $pdo->prepare("SELECT * FROM books WHERE seller_id = ? 
                        ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$my_books = $stmt->fetchAll();

// Get user's stats
$total_books    = count($my_books);
$available      = 0;
$sold           = 0;
foreach($my_books as $b) {
    if($b['status'] == 'available') $available++;
    if($b['status'] == 'sold')      $sold++;
}

// Get reviews about this user
$stmt = $pdo->prepare("SELECT r.*, u.name as reviewer_name 
                        FROM reviews r
                        LEFT JOIN users u ON r.reviewer_id = u.user_id
                        WHERE r.seller_id = ?
                        ORDER BY r.created_at DESC");
$stmt->execute([$user_id]);
$reviews = $stmt->fetchAll();

$avg_rating = count($reviews) > 0 
    ? array_sum(array_column($reviews, 'rating')) / count($reviews) 
    : 0;
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <h2 class="text-white fw-bold mb-0">
            <i class="fas fa-user me-2"></i>My Profile
        </h2>
        <p class="text-white-50 mb-0">Manage your account and listings</p>
    </div>
</div>

<div class="container mt-4 mb-5">
    <div class="row">

        <!-- Left Side - Profile Card -->
        <div class="col-md-4 mb-4">

            <!-- Profile Info -->
            <div class="card shadow text-center mb-4">
                <div class="card-body p-4">
                    <!-- Avatar -->
                    <div class="rounded-circle bg-primary d-flex align-items-center 
                                justify-content-center text-white fw-bold mx-auto mb-3"
                         style="width:80px; height:80px; font-size:2rem;">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <h5 class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="text-muted small">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    <p class="text-muted small">
                        🏛️ <?php echo htmlspecialchars($user['university'] ?? 'N/A'); ?>
                    </p>
                    <!-- Star Rating -->
                    <div class="text-warning fs-5">
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

            <!-- Stats -->
            <div class="card shadow mb-4">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    📊 My Statistics
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>📚 Total Books</span>
                        <strong><?php echo $total_books; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>✅ Available</span>
                        <strong class="text-success"><?php echo $available; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>💰 Sold</span>
                        <strong class="text-danger"><?php echo $sold; ?></strong>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    ✏️ Edit Profile
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
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?php echo htmlspecialchars($user['name']); ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">University</label>
                            <input type="text" name="university" class="form-control"
                                   value="<?php echo htmlspecialchars($user['university'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Right Side - My Books & Reviews -->
        <div class="col-md-8">

            <!-- My Books -->
            <div class="card shadow mb-4">
                <div class="card-header fw-bold text-white d-flex 
                            justify-content-between align-items-center"
                     style="background-color: #1F4E79;">
                    <span><i class="fas fa-book me-2"></i>My Books</span>
                    <a href="/bookbridge/post-book.php"
                       class="btn btn-warning btn-sm fw-bold">
                        <i class="fas fa-plus me-1"></i>Post New
                    </a>
                </div>
                <div class="card-body">
                    <?php if(count($my_books) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Condition</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
                                    <td>LKR <?php echo number_format($book['price'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo $book['book_condition']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $book['status'] == 'available' 
                                            ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ucfirst($book['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/bookbridge/book-detail.php?id=<?php echo $book['book_id']; ?>"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No books posted yet!</p>
                        <a href="/bookbridge/post-book.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Post Your First Book
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews -->
            <div class="card shadow">
                <div class="card-header fw-bold text-white"
                     style="background-color: #1F4E79;">
                    <i class="fas fa-star me-2"></i>Reviews About Me
                </div>
                <div class="card-body">
                    <?php if(count($reviews) > 0): ?>
                        <?php foreach($reviews as $review): ?>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>
                                    <?php echo htmlspecialchars($review['reviewer_name']); ?>
                                </strong>
                                <span class="text-warning">
                                    <?php for($i=1;$i<=5;$i++) 
                                        echo $i<=$review['rating']?'★':'☆'; ?>
                                </span>
                            </div>
                            <p class="text-muted small mb-0">
                                <?php echo htmlspecialchars($review['comment'] ?? ''); ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No reviews yet! 😊</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>