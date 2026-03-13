<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookBridge Sri Lanka</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Our Custom CSS -->
    <link href="/bookbridge/assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1F4E79;">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold fs-4" href="/bookbridge/index.php">
            <i class="fas fa-book-open me-2"></i>BookBridge
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/bookbridge/index.php">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/bookbridge/listings.php">
                        <i class="fas fa-book me-1"></i>Browse Books
                    </a>
                </li>
                <?php if(isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/bookbridge/post-book.php">
                        <i class="fas fa-plus-circle me-1"></i>Post a Book
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/bookbridge/messages.php">
                        <i class="fas fa-envelope me-1"></i>Messages
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <!-- Right Side -->
            <ul class="navbar-nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/bookbridge/profile.php">
                            <i class="fas fa-user me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="/bookbridge/my-books.php">
                            <i class="fas fa-book me-2"></i>My Books</a></li>
                        <li><a class="dropdown-item" href="/bookbridge/wishlist.php">
                            <i class="fas fa-heart me-2"></i>Wishlist</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/bookbridge/logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="/bookbridge/login.php">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-warning ms-2 fw-bold" href="/bookbridge/register.php">
                        <i class="fas fa-user-plus me-1"></i>Register
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>