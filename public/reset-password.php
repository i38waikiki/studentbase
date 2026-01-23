<?php
session_start();
$token = $_GET['token'] ?? '';
$status = $_GET['status'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset password • Student Base</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>

<div class="auth-shell">
  <div class="row g-0 min-vh-100">
    <div class="col-lg-5 auth-left">
      <div class="auth-card">

        <div class="auth-title">Reset password</div>
        <div class="auth-sub">Set a new password for your account.</div>

        <?php if ($status === 'invalid'): ?>
          <div class="alert alert-danger">This reset link is invalid or expired.</div>
        <?php elseif ($status === 'mismatch'): ?>
          <div class="alert alert-danger">Passwords do not match.</div>
        <?php endif; ?>

        <form action="reset-password-handler.php" method="POST">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">

          <div class="mb-3">
            <label class="form-label">New password</label>
            <input class="form-control" type="password" name="password" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <input class="form-control" type="password" name="password2" required>
          </div>

          <button class="btn btn-primary w-100 py-2" type="submit">Update password</button>
          <a class="btn btn-link w-100 mt-2" href="login.php">Back to sign in</a>
        </form>

      </div>
    </div>

    <div class="col-lg-7 auth-right">
      <div class="auth-hero">
        <img src="../assets/login-hero.png" alt="Dashboard preview">
      </div>
    </div>
  </div>
</div>

</body>
</html>
