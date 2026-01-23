<?php
session_start();
$status = $_GET['status'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot password • Student Base</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>

<div class="auth-shell">
  <div class="row g-0 min-vh-100">
    <div class="col-lg-5 auth-left">
      <div class="auth-card">

        <div class="auth-title">Forgot password</div>
        <div class="auth-sub">Enter your email and we’ll generate a reset link.</div>

        <?php if ($status === 'sent'): ?>
          <div class="alert alert-success">
            If the email exists, a reset link has been created.
            <div class="small text-muted mt-1">
              (For dev, we will show the link on the next page after submit.)
            </div>
          </div>
        <?php elseif ($status === 'error'): ?>
          <div class="alert alert-danger">Something went wrong. Try again.</div>
        <?php endif; ?>

        <form action="forgot-password-handler.php" method="POST">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required>
          </div>

          <button class="btn btn-primary w-100 py-2" type="submit">Send reset link</button>

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
