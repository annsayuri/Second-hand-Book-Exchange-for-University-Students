<?php require_once 'includes/header.php'; ?> 
<!-- Includes the header file (usually contains HTML <head>, navigation bar, CSS links etc.)
require_once ensures the file is included only once -->

<?php require_once 'includes/db.php'; ?> 
<!-- Includes the database connection file so this page can access the database using $pdo -->

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <!-- Main title of the website -->
        <h1>📚 BookBridge Sri Lanka</h1>

        <!-- Short description explaining the platform purpose -->
        <p>Buy, Sell & Exchange Second-Hand University Textbooks</p>

        <!-- Additional message encouraging sustainability -->
        <p class="opacity-75">Save money. Help fellow students. Go green! 🌱</p>

        <!-- Button that links users to the page where all books are listed -->
        <a href="/bookbridge/listings.php" class="btn btn-warning btn-lg me-3">
            <!-- FontAwesome search icon -->
            <i class="fas fa-search me-2"></i>Browse Books
        </a>

        <!-- Button that links to the user registration page -->
        <a href="/bookbridge/register.php" class="btn btn-outline-light btn-lg">
            <!-- FontAwesome user icon -->
            <i class="fas fa-user-plus me-2"></i>Join Free
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-5">

    <!-- Stats Row -->
    <!-- Shows statistics about the platform -->
    <div class="row text-center mb-5">

        <!-- Column for number of books -->
        <div class="col-md-4 mb-3">
            <div class="card p-4">
                <!-- Book icon -->
                <i class="fas fa-book fa-3x text-primary mb-3"></i>

                <!-- Number of books available -->
                <h3 class="fw-bold text-primary">500+</h3>

                <!-- Description text -->
                <p class="text-muted">Books Available</p>
            </div>
        </div>

        <!-- Column for number of registered students -->
        <div class="col-md-4 mb-3">
            <div class="card p-4">
                <!-- Users icon -->
                <i class="fas fa-users fa-3x text-success mb-3"></i>

                <!-- Number of registered users -->
                <h3 class="fw-bold text-success">1000+</h3>

                <!-- Description text -->
                <p class="text-muted">Students Registered</p>
            </div>
        </div>

        <!-- Column for number of universities -->
        <div class="col-md-4 mb-3">
            <div class="card p-4">
                <!-- University icon -->
                <i class="fas fa-university fa-3x text-warning mb-3"></i>

                <!-- Number of universities -->
                <h3 class="fw-bold text-warning">20+</h3>

                <!-- Description -->
                <p class="text-muted">Universities</p>
            </div>
        </div>

    </div>

    <!-- Latest Books Section -->
    <div class="text-center mb-4">
        <!-- Section title -->
        <h2 class="section-title">📖 Latest Books</h2>

        <!-- Subtitle -->
        <p class="section-subtitle">Recently posted books by students</p>
    </div>

    <div class="row">
        <?php
        try {
            // SQL query to get latest 6 books from database
            // It joins books table with categories table
            $stmt = $pdo->query("SELECT b.*, c.name as category_name 
                                FROM books b 
                                LEFT JOIN categories c ON b.category_id = c.category_id 
                                ORDER BY b.created_at DESC 
                                LIMIT 6");

            // Fetch all results as an array
            $books = $stmt->fetchAll();

            // Check if books exist
            if(count($books) > 0) {

                // Loop through each book
                foreach($books as $book) { ?>

                    <div class="col-md-4 mb-4">
                        <div class="book-card card h-100">

                            <!-- Display book image -->
                            <!-- If image exists, load it from uploads folder -->
                            <!-- Otherwise show default 'no book image' -->
                            <img src="<?php echo $book['image'] ? '/bookbridge/uploads/'.$book['image'] : '/bookbridge/assets/images/no-book.png'; ?>" 
                                 alt="<?php echo htmlspecialchars($book['title']); ?>">

                            <div class="card-body">

                                <!-- Display category name -->
                                <span class="badge bg-primary mb-2">
                                    <?php echo htmlspecialchars($book['category_name'] ?? 'General'); ?>
                                </span>

                                <!-- Book title -->
                                <h5 class="fw-bold"><?php echo htmlspecialchars($book['title']); ?></h5>

                                <!-- Book author -->
                                <p class="text-muted small"><?php echo htmlspecialchars($book['author']); ?></p>

                                <!-- Price and condition row -->
                                <div class="d-flex justify-content-between align-items-center mt-3">

                                    <!-- Display book price formatted to 2 decimal places -->
                                    <span class="book-price">LKR <?php echo number_format($book['price'], 2); ?></span>

                                    <!-- Book condition badge -->
                                    <span class="badge book-condition bg-success">
                                        <?php echo $book['book_condition']; ?>
                                    </span>

                                </div>

                                <!-- Button to view book details -->
                                <a href="/bookbridge/book-detail.php?id=<?php echo $book['book_id']; ?>" 
                                   class="btn btn-primary w-100 mt-3">

                                    <!-- Eye icon -->
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>

                            </div>
                        </div>
                    </div>

                <?php }

            } else { ?>

                <!-- If no books exist in database -->
                <div class="col-12 text-center py-5">

                    <!-- Icon -->
                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>

                    <!-- Message -->
                    <h4 class="text-muted">No books yet!</h4>

                    <p class="text-muted">Be the first to post a book 😊</p>

                    <!-- Button to add first book -->
                    <a href="/bookbridge/post-book.php" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Post First Book
                    </a>

                </div>

            <?php }

        } catch(Exception $e) { ?>

            <!-- If database error occurs -->
            <div class="col-12">
                <div class="alert alert-danger">Something went wrong! 😅</div>
            </div>

        <?php } ?>
    </div>

    <!-- How It Works Section -->
    <div class="text-center mt-5 mb-4">

        <!-- Section title -->
        <h2 class="section-title">🤔 How It Works</h2>

        <!-- Subtitle -->
        <p class="section-subtitle">Super simple — 3 easy steps!</p>
    </div>

    <div class="row text-center mb-5">

        <!-- Step 1 -->
        <div class="col-md-4 mb-3">
            <div class="card p-4 h-100">

                <!-- Register icon -->
                <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>

                <!-- Step title -->
                <h5 class="fw-bold">1. Register Free</h5>

                <!-- Explanation -->
                <p class="text-muted">Create your free account using your university email</p>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="col-md-4 mb-3">
            <div class="card p-4 h-100">

                <!-- Upload icon -->
                <i class="fas fa-upload fa-3x text-success mb-3"></i>

                <!-- Step title -->
                <h5 class="fw-bold">2. Post Your Book</h5>

                <!-- Explanation -->
                <p class="text-muted">List your used textbooks with photos and set your price</p>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="col-md-4 mb-3">
            <div class="card p-4 h-100">

                <!-- Handshake icon -->
                <i class="fas fa-handshake fa-3x text-warning mb-3"></i>

                <!-- Step title -->
                <h5 class="fw-bold">3. Connect & Sell</h5>

                <!-- Explanation -->
                <p class="text-muted">Chat with buyers, meet up and complete the exchange!</p>
            </div>
        </div>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?> 
<!-- Includes the footer file (usually contains closing HTML tags, footer section, scripts etc.) -->