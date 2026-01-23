<?php
session_start();

/*
  - This page is UI only. The actual login processing is handled in login-handler.php
  - Error messages are shown using query string: ?error=...
*/

function loginErrorMessage($code) {
    switch ($code) {
        case 'empty': return "Please fill in both email and password.";
        case 'stmtfailed': return "Something went wrong. Please try again.";
        case 'wrongpassword': return "Incorrect password.";
        case 'nouser': return "No account found with that email.";
        case 'invalidrole': return "Your account role is not valid.";
        default: return "";
    }
}

$errorMsg = "";
if (isset($_GET['error'])) {
    $errorMsg = loginErrorMessage($_GET['error']);
}
?>

<?php include '../includes/header.php'; ?>

<body class="auth-shell">

  <div class="container-fluid">
    <div class="row g-0 min-vh-100">

      <!-- LEFT: Login -->
      <div class="col-lg-5 auth-left">

        <div class="auth-card">

          <!-- Brand -->
          <div class="auth-brand">
            <!-- NOTE: Use your real logo path -->
            <img src="/studentbase/assets/logoB.png" alt="Logo">
            <div class="fw-semibold">Student Base</div>
          </div>

          <div class="auth-title">Sign in</div>
          <div class="auth-sub">
            Use your school email to access your dashboard.
          </div>

          <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger py-2">
              <?= htmlspecialchars($errorMsg); ?>
            </div>
          <?php endif; ?>

          <!-- Login Form -->
          <form method="POST" action="login-handler.php" class="mt-3">

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input
                type="email"
                name="email"
                class="form-control"
                placeholder="name@example.com"
                required
                autocomplete="email"
              >
            </div>

            <div class="mb-2">
              <label class="form-label">Password</label>
              <input
                type="password"
                name="password"
                class="form-control"
                placeholder="••••••••"
                required
                autocomplete="current-password"
              >
            </div>

            <div class="d-flex justify-content-end mb-3">
              <a href="forgot-password.php" class="text-decoration-none small">
                Forgot password?
              </a>
            </div>

            <button class="btn btn-primary w-100 py-2 fw-semibold">
              Sign in
            </button>

          </form>

          
          <div class="text-muted small mt-4">
            Having trouble? Contact your administrator.
          </div>

        </div>
      </div>

      <!-- RIGHT: Hero panel -->
      <div class="col-lg-7 auth-right">
        <div class="auth-hero">
          <img src="/studentbase/assets/login-hero.png" alt="Dashboard preview">
        </div>
      </div>

    </div>
  </div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
