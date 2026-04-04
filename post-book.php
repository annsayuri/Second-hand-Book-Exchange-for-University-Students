<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Only logged in users can post books
require_once 'includes/auth_check.php';

$error   = "";
$success = "";

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

    // Validation
    if (empty($title) || empty($price) || empty($condition)) {
        $error = "⚠️ Please fill in all required fields!";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "⚠️ Please enter a valid price!";
    } else {
        // Handle image upload
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed     = ['jpg', 'jpeg', 'png', 'gif'];
            $filename    = $_FILES['image']['name'];
            $ext         = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $maxsize     = 5 * 1024 * 1024; // 5MB

            if (!in_array($ext, $allowed)) {
                $error = "⚠️ Only JPG, PNG, GIF images allowed!";
            } elseif ($_FILES['image']['size'] > $maxsize) {
                $error = "⚠️ Image size must be less than 5MB!";
            } else {
                $newname = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'],
                    'uploads/' . $newname);
                $image = $newname;
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO books 
                (seller_id, category_id, title, author, edition, 
                subject, price, book_condition, description, 
                image, university) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $_SESSION['user_id'],
                $category_id ?: null,
                $title, $author, $edition,
                $subject, $price, $condition,
                $description, $image, $university
            ]);

            $success = "✅ Book posted successfully!";
        }
    }
}

// Get categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<!-- Page Header -->
<div class="py-4" style="background-color: #1F4E79;">
    <div class="container">
        <h2 class="text-white fw-bold mb-0">
            <i class="fas fa-plus-circle me-2"></i>Post a Book
        </h2>
        <p class="text-white-50 mb-0">List your textbook and help fellow students!</p>
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
                            <a href="/bookbridge/listings.php" class="fw-bold">
                                View Listings!</a>
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
                                       class="form-control"
                                       placeholder="e.g. Introduction to Programming"
                                       required>
                            </div>

                            <!-- Author -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-1"></i>Author
                                </label>
                                <input type="text" name="author"
                                       class="form-control"
                                       placeholder="e.g. John Smith">
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
                                       placeholder="e.g. 3rd Edition">
                            </div>

                            <!-- Subject -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-graduation-cap me-1"></i>Subject
                                </label>
                                <input type="text" name="subject"
                                       class="form-control"
                                       placeholder="e.g. Computer Science">
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
                                        <option value="<?php echo $cat['category_id']; ?>">
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
                                    <option value="New">New</option>
                                    <option value="Like New">Like New</option>
                                    <option value="Good">Good</option>
                                    <option value="Fair">Fair</option>
                                    <option value="Poor">Poor</option>
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
                                       class="form-control"
                                       placeholder="e.g. 1500"
                                       min="1" required>
                            </div>

                            <!-- University -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-university me-1"></i>University
                                </label>
                                <input type="text" name="university"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($_SESSION['university'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-align-left me-1"></i>Description
                            </label>
                            <textarea name="description" class="form-control"
                                      rows="4"
                                      placeholder="Describe the book condition, any missing pages, highlights, etc."></textarea>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-image me-1"></i>Book Cover Image
                            </label>
                            <input type="file" name="image"
                                   class="form-control"
                                   accept="image/*">
                            <small class="text-muted">
                                Max 5MB. JPG, PNG, GIF allowed.
                            </small>
                        </div>

                        <!-- Submit -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload me-2"></i>Post Book
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
