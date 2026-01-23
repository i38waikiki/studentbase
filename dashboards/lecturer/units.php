<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$lecturer_id = (int)$_SESSION['user_id'];
$units = lecturerGetMyUnits($conn, $lecturer_id);
?>
<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
  <?php include '../../includes/sidebar-lecturer.php'; ?>

  <main class="flex-fill page-wrap">
    <div class="container-fluid p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="mb-1">My Units</h3>
          <div class="text-muted">Units assigned to you</div>
        </div>
      </div>

      <div class="row g-4">
        <?php while($u = mysqli_fetch_assoc($units)): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <div class="fw-semibold"><?= htmlspecialchars($u['unit_name']); ?></div>
                <div class="text-muted small mt-1">Courses: <?= htmlspecialchars($u['courses'] ?? '-'); ?></div>
              </div>
             <div class="card-footer bg-white border-0 d-flex gap-2">
              <a class="btn btn-outline-primary btn-sm" href="unit-view.php?unit_id=<?= (int)$u['unit_id']; ?>">
                View Courses
              </a>

              <a class="btn btn-outline-secondary btn-sm" href="assignments.php?unit_id=<?= (int)$u['unit_id']; ?>">
                View Assignments
              </a>
            </div>

            <form action="unit-brief-upload.php" method="POST" enctype="multipart/form-data" class="mt-2">
              <input type="hidden" name="unit_id" value="<?= (int)$u['unit_id']; ?>">

              <input type="file" name="brief" class="form-control form-control-sm" required>
              <button class="btn btn-outline-secondary btn-sm mt-2 w-100" type="submit">
                Upload / Replace Brief
              </button>
            </form>
            </div>
          </div>
        <?php endwhile; ?>

        <?php if (mysqli_num_rows($units) === 0): ?>
          <div class="col-12">
            <div class="alert alert-secondary">No units assigned yet.</div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
