<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $password   = trim($_POST['password']);
    $confirm    = trim($_POST['confirm_password']);
    $university = trim($_POST['university']);
    $phone      = trim($_POST['phone']);

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($university)) {
        $error = "⚠️ Please fill in all required fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ Please enter a valid email address!";
    } elseif (strlen($password) < 6) {
        $error = "⚠️ Password must be at least 6 characters!";
    } elseif ($password !== $confirm) {
        $error = "⚠️ Passwords do not match!";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "⚠️ This email is already registered!";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $pdo->prepare("INSERT INTO users 
                (name, email, password_hash, university, phone) 
                VALUES (?, ?, ?, ?, ?)");

            $stmt->execute([$name, $email, $password_hash, $university, $phone]);

            $success = "✅ Registration successful! You can now login!";
        }
    }
}
?>

<!-- Main Content -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-white text-center py-3"
                     style="background-color: #1F4E79;">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </h4>
                    <small class="opacity-75">Join BookBridge Sri Lanka for free!</small>
                </div>

                <div class="card-body p-4">

                    <!-- Error Message -->
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- Success Message -->
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?>
                            <a href="/bookbridge/login.php" class="fw-bold">Login here!</a>
                        </div>
                    <?php endif; ?>

                    <!-- Register Form -->
                    <form method="POST" action="">

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user me-1"></i>Full Name *
                            </label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Enter your full name" required
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address *
                            </label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="Enter your email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <!-- University -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-university me-1"></i>University *
                            </label>
                            <input type="text" name="university" class="form-control"
                                   placeholder="e.g. University of Kelaniya" required
                                   value="<?php echo isset($_POST['university']) ? htmlspecialchars($_POST['university']) : ''; ?>">
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-phone me-1"></i>Phone Number
                            </label>
                            <input type="text" name="phone" class="form-control"
                                   placeholder="e.g. 0771234567"
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock me-1"></i>Password *
                            </label>
                            <input type="password" name="password"
                                   class="form-control"
                                   placeholder="Minimum 6 characters" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-lock me-1"></i>Confirm Password *
                            </label>
                            <input type="password" name="confirm_password"
                                   class="form-control"
                                   placeholder="Re-enter your password" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>

                    </form>

                    <!-- Login Link -->
                    <div class="text-center mt-3">
                        <p class="text-muted">Already have an account?
                            <a href="/bookbridge/login.php" class="fw-bold"
                               style="color: #1F4E79;">Login here!</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
