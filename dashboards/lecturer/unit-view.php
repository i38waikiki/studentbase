<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';

$lecturer_id = (int)$_SESSION['user_id'];
$unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;

if ($unit_id <= 0) {
    header("Location: units.php");
    exit();
}

/* lecturer must be assigned to this unit */
$check = mysqli_prepare($conn, "SELECT 1 FROM unit_lecturers WHERE unit_id=? AND lecturer_id=? LIMIT 1");
mysqli_stmt_bind_param($check, "ii", $unit_id, $lecturer_id);
mysqli_stmt_execute($check);
$ok = mysqli_stmt_get_result($check);
if (mysqli_num_rows($ok) === 0) {
    header("Location: units.php?error=unauthorized");
    exit();
}

/* Unit info */
$stmt = mysqli_prepare($conn, "SELECT unit_name FROM units WHERE unit_id=?");
mysqli_stmt_bind_param($stmt, "i", $unit_id);
mysqli_stmt_execute($stmt);
$unit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

/* Courses linked to this unit */
$coursesStmt = mysqli_prepare($conn, "
    SELECT c.course_id, c.course_code, c.course_name
    FROM course_unit cu
    JOIN courses c ON c.course_id = cu.course_id
    WHERE cu.unit_id = ?
    ORDER BY c.course_code
");
mysqli_stmt_bind_param($coursesStmt, "i", $unit_id);
mysqli_stmt_execute($coursesStmt);
$courses = mysqli_stmt_get_result($coursesStmt);

include '../../includes/header.php';
?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>
<div class="d-flex flex-grow-1">
  <?php include '../../includes/sidebar-lecturer.php'; ?>

  <main class="flex-fill page-wrap">
    <div class="container-fluid p-4">
      <a href="units.php" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
      </a>

      <h3 class="mb-1"><?= htmlspecialchars($unit['unit_name'] ?? 'Unit'); ?></h3>
      <div class="text-muted mb-4">Courses that include this unit</div>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Course Code</th>
                <th>Course Name</th>
              </tr>
            </thead>
            <tbody>
              <?php while($c = mysqli_fetch_assoc($courses)): ?>
                <tr>
                  <td class="fw-semibold"><?= htmlspecialchars($c['course_code']); ?></td>
                  <td><?= htmlspecialchars($c['course_name']); ?></td>
                </tr>
              <?php endwhile; ?>
              <?php if (mysqli_num_rows($courses) === 0): ?>
                <tr><td colspan="2" class="text-muted text-center py-4">No courses linked</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>
<?php include '../../includes/footer.php'; ?>
</body>
