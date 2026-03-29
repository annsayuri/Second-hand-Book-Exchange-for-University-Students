<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validation
    if (empty($email) || empty($password)) {
        $error = "⚠️ Please fill in all fields!";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful!
            $_SESSION['user_id']    = $user['user_id'];
            $_SESSION['name']       = $user['name'];
            $_SESSION['email']      = $user['email'];
            $_SESSION['role']       = $user['role'];
            $_SESSION['university'] = $user['university'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header('Location: /bookbridge/admin/dashboard.php');
            } else {
                header('Location: /bookbridge/index.php');
            }
            exit();
        } else {
            $error = "⚠️ Invalid email or password!";
        }
    }
}
?>

<!-- Main Content -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header text-white text-center py-3"
                     style="background-color: #1F4E79;">
                    <h4 class="mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>Welcome Back!
                    </h4>
                    <small class="opacity-75">Login to your BookBridge account</small>
                </div>

                <div class="card-body p-4">

                    <!-- Error Message -->
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form method="POST" action="">

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="Enter your email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock me-1"></i>Password
                            </label>
                            <input type="password" name="password"
                                   class="form-control"
                                   placeholder="Enter your password" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>

                    </form>

                    <!-- Register Link -->
                    <div class="text-center mt-3">
                        <p class="text-muted">Don't have an account?
                            <a href="/bookbridge/register.php" class="fw-bold"
                               style="color: #1F4E79;">Register here!</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
