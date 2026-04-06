<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Only logged in users
require_once 'includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Get book ID from URL
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($book_id == 0) {
    header('Location: /bookbridge/my-books.php');
    exit();
}

// Get book details — make sure it belongs to this user
$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ? AND seller_id = ?");
$stmt->execute([$book_id, $user_id]);
$book = $stmt->fetch();

// If book not found or doesn't belong to user
if (!$book) {
    header('Location: /bookbridge/my-books.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = trim($_POST['title']);
    $author      = trim($_POST['author']);
    $edition     = trim($_POST['edition']);
    $subject     = trim($_POST['subject']);
    $category_id = trim($_POST['category_id']);
    $price       = trim($_POST['price']);
    $condition   = trim($_POST['book_condition']);
    $description = trim($_POST['description']);
    $university  = trim($_POST['university']);
    $status      = trim($_POST['status']);

    // Validation
    if (empty($title) || empty($price) || empty($condition)) {
        $error = "⚠️ Please fill in all required fields!";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "⚠️ Please enter a valid price!";
    } else {
        // Handle image upload
        $image = $book['image']; // Keep existing image by default

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed  = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $maxsize  = 5 * 1024 * 1024; // 5MB

            if (!in_array($ext, $allowed)) {
                $error = "⚠️ Only JPG, PNG, GIF images allowed!";
            } elseif ($_FILES['image']['size'] > $maxsize) {
                $error = "⚠️ Image size must be less than 5MB!";
            } else {
                // Delete old image if exists
                if ($book['image'] && file_exists('uploads/' . $book['image'])) {
                    unlink('uploads/' . $book['image']);
                }
                $newname = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $newname);
                $image = $newname;
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("UPDATE books SET 
                title = ?, author = ?, edition = ?,
                subject = ?, category_id = ?, price = ?,
                book_condition = ?, description = ?,
                university = ?, status = ?, image = ?
                WHERE book_id = ? AND seller_id = ?");

            $stmt->execute([
                $title, $author, $edition,
                $subject, $category_id ?: null, $price,
                $condition, $description,
                $university, $status, $image,
                $book_id, $user_id
            ]);

            $success = "✅ Book updated successfully!";

            // Refresh book data
            $stmt = $pdo->prepare("SELECT * FROM books 
                                    WHERE book_id = ? AND seller_id = ?");
            $stmt->execute([$book_id, $user_id]);
            $book = $stmt->fetch();
        }
    }
}

// Get categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white fw-bold mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Book
                </h2>
                <p class="text-white-50 mb-0">
                    Update your book listing details!
                </p>
            </div>
            <a href="/bookbridge/my-books.php"
               class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>Back to My Books
            </a>
        </div>
    </div>
</div>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">

                    <!-- Error -->
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- Success -->
                    <?php if($success): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                            <a href="/bookbridge/my-books.php" class="fw-bold">
                                View My Books!
                            </a>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">

                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-book me-1"></i>Book Title *
                                </label>
                                <input type="text" name="title"
                                       class="form-control" required
                                       value="<?php echo htmlspecialchars($book['title']); ?>">
                            </div>

                            <!-- Author -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-1"></i>Author
                                </label>
                                <input type="text" name="author"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($book['author'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <!-- Edition -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-bookmark me-1"></i>Edition
                                </label>
                                <input type="text" name="edition"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($book['edition'] ?? ''); ?>">
                            </div>

                            <!-- Subject -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-graduation-cap me-1"></i>Subject
                                </label>
                                <input type="text" name="subject"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($book['subject'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-1"></i>Category
                                </label>
                                <select name="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>"
                                            <?php echo $book['category_id'] == $cat['category_id'] 
                                                ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Condition -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-star me-1"></i>Condition *
                                </label>
                                <select name="book_condition" class="form-control" required>
                                    <option value="">Select Condition</option>
                                    <?php foreach(['New','Like New','Good','Fair','Poor'] as $c): ?>
                                    <option value="<?php echo $c; ?>"
                                        <?php echo $book['book_condition'] == $c ? 'selected' : ''; ?>>
                                        <?php echo $c; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Price -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-1"></i>Price (LKR) *
                                </label>
                                <input type="number" name="price"
                                       class="form-control" min="1" required
                                       value="<?php echo $book['price']; ?>">
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-info-circle me-1"></i>Status
                                </label>
                                <select name="status" class="form-control">
                                    <option value="available" <?php echo $book['status'] == 'available' ? 'selected' : ''; ?>>✅ Available</option>
                                    <option value="sold" <?php echo $book['status'] == 'sold' ? 'selected' : ''; ?>>💰 Sold</option>
                                    <option value="reserved" <?php echo $book['status'] == 'reserved' ? 'selected' : ''; ?>>🔒 Reserved</option>
                                </select>
                            </div>
                        </div>

                        <!-- University -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-university me-1"></i>University
                            </label>
                            <input type="text" name="university"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($book['university'] ?? ''); ?>">
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left me-1"></i>Description
                            </label>
                            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($book['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Current Image -->
                        <?php if($book['image']): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                📸 Current Image:
                            </label>
                            <br>
                            <img src="/bookbridge/uploads/<?php echo $book['image']; ?>"
                                 style="height: 120px; border-radius: 8px; border: 2px solid #ddd;"
                                 alt="Current book image">
                        </div>
                        <?php endif; ?>

                        <!-- New Image Upload -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-image me-1"></i>
                                Change Cover Image
                            </label>
                            <input type="file" name="image"
                                   class="form-control" accept="image/*">
                            <small class="text-muted">
                                Leave empty to keep current image.
                                Max 5MB. JPG, PNG, GIF allowed.
                            </small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="/bookbridge/my-books.php"
                               class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
