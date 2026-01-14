
<?php
require_once '../../includes/dbh.php';

/*
    This setup page creates the first Admin account.
    It can only be used once for security reasons.

    user: admin@studentbase.edu.mt
    password: admin1234
*/

// Check if an admin already exists
$checkAdmin = "SELECT user_id FROM users WHERE role_id = 1 LIMIT 1";
$result = mysqli_query($conn, $checkAdmin);

if (mysqli_num_rows($result) > 0) {
    die("Admin already exists. Setup is disabled.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password, role_id, class_id) VALUES (?, ?, ?, 1, NULL)";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);
            mysqli_stmt_execute($stmt);
            $success = "Admin account created successfully.";
        } else {
            $error = "Something went wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">Initial Admin Setup</h4>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <button class="btn btn-primary w-100">Create Admin</button>
                    </form>

                    <small class="text-muted d-block mt-3">
                        This page will disable itself after the admin is created.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

