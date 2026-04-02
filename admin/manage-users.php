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

// Handle delete user
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    // Prevent admin from deleting themselves
    if ($delete_id == $_SESSION['user_id']) {
        $error = "⚠️ You cannot delete your own account!";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$delete_id]);
        $success = "✅ User deleted successfully!";
    }
}

// Handle role change
if (isset($_GET['make_admin'])) {
    $user_id = (int)$_GET['make_admin'];
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $success = "✅ User role updated to Admin!";
}

if (isset($_GET['make_user'])) {
    $user_id = (int)$_GET['make_user'];
    $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $success = "✅ User role updated to User!";
}

// Get all users
$users = $pdo->query("SELECT u.*, 
                       COUNT(b.book_id) as total_books
                       FROM users u
                       LEFT JOIN books b ON u.user_id = b.seller_id
                       GROUP BY u.user_id
                       ORDER BY u.created_at DESC")->fetchAll();
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white fw-bold mb-0">
                    <i class="fas fa-users me-2"></i>Manage Users
                </h2>
                <p class="text-white-50 mb-0">
                    Total: <?php echo count($users); ?> users registered
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>University</th>
                            <th>Books</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?php echo $u['user_id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary d-flex 
                                                align-items-center justify-content-center 
                                                text-white fw-bold"
                                         style="width:35px; height:35px; font-size:0.9rem;">
                                        <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                    </div>
                                    <strong><?php echo htmlspecialchars($u['name']); ?></strong>
                                </div>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($u['email']); ?></small>
                            </td>
                            <td>
                                <small>
                                    <?php echo htmlspecialchars($u['university'] ?? 'N/A'); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $u['total_books']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $u['role'] == 'admin' 
                                    ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo ucfirst($u['role']); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                                </small>
                            </td>
                            <td>
                                <!-- Role Toggle -->
                                <?php if($u['role'] == 'user'): ?>
                                <a href="?make_admin=<?php echo $u['user_id']; ?>"
                                   class="btn btn-sm btn-warning mb-1"
                                   onclick="return confirm('Make this user an Admin?')">
                                    <i class="fas fa-crown"></i>
                                </a>
                                <?php else: ?>
                                <a href="?make_user=<?php echo $u['user_id']; ?>"
                                   class="btn btn-sm btn-secondary mb-1"
                                   onclick="return confirm('Remove admin role?')">
                                    <i class="fas fa-user"></i>
                                </a>
                                <?php endif; ?>

                                <!-- Delete -->
                                <?php if($u['user_id'] != $_SESSION['user_id']): ?>
                                <a href="?delete=<?php echo $u['user_id']; ?>"
                                   class="btn btn-sm btn-danger mb-1"
                                   onclick="return confirm('Delete this user? This cannot be undone!')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
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
