<?php
require_once '../../includes/auth.php';
requireRole(3);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$student_id = (int)$_SESSION['user_id'];
$units = studentGetMyUnits($conn, $student_id);
?>
<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
  <?php include '../../includes/sidebar-student.php'; ?>

  <main class="flex-fill page-wrap">
    <div class="container-fluid p-4">
      <h3 class="mb-1">My Units</h3>
      <div class="text-muted mb-4">Units for your course</div>

      <div class="row g-4">
        <?php while($u = mysqli_fetch_assoc($units)): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <div class="fw-semibold"><?= htmlspecialchars($u['unit_name']); ?></div>
              </div>
              <div class="card-footer bg-white border-0">
                <a class="btn btn-outline-primary btn-sm" href="assignments.php">
                  View Assignments
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($units) === 0): ?>
          <div class="col-12">
            <div class="alert alert-secondary">No units found for your course.</div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
