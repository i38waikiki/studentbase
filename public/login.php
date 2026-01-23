<?php
session_start();
require_once '../includes/dbh.php';       // include database connection
require_once '../includes/functions.php'; // include functions

$error = ""; // will hold login error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use function to get user
    $user = getUserByEmail($conn, $email);

    if ($user) {
        // Check password (later we hash passwords)
        if ($password === $user['password']) {
            // Store session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];

            // Role-based redirect
            if ($user['role_id'] == 1) {
                header("Location: ../dashboards/admin/dashboard.php");
            } elseif ($user['role_id'] == 2) {
                header("Location: ../dashboards/lecturer/dashboard.php");
            } else {
                header("Location: ../dashboards/student/dashboard.php");
            }
            exit;
        }
    }

    $error = "Invalid email or password";
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<!-- Login Card -->
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm rounded-3" style="width: 100%; max-width: 420px;">
        <div class="card-body p-4">

            <!-- Logo -->
            <div class="text-center mb-3">
                <img src="/assets/logo.png" width="50" alt="School Logo">
            </div>

            <h4 class="text-center mb-4">Login</h4>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="login-handler.php">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary">Login</button>
                </div>
            </form>

            <div class="text-center mt-3">
                <a href="forgot-password.php" class="text-decoration-none">Forgot your password?</a>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

