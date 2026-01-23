<?php
session_start();
$link = $_SESSION['reset_link'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset link • Student Base</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-2">Reset link</h4>
      <p class="text-muted">Instead of sending the reset link by email for now .</p>

      <?php if ($link): ?>
        <div class="alert alert-info">
          <a href="<?= htmlspecialchars($link); ?>"><?= htmlspecialchars($link); ?></a>
        </div>
        <?php unset($_SESSION['reset_link']); ?>
      <?php else: ?>
        <div class="alert alert-warning">No link found. Go back and request again.</div>
      <?php endif; ?>

      <a class="btn btn-primary" href="login.php">Back to sign in</a>
    </div>
  </div>
</div>
</body>
</html>
