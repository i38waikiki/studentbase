<?php
require_once '../../includes/auth.php';
requireRole(3);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$student_id = (int)$_SESSION['user_id'];

$myUnits = studentGetMyUnits($conn, $student_id);
$assignments = studentGetAssignments($conn, $student_id);
$tt = studentGetTimetable($conn, $student_id);
?>
<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
  <?php include '../../includes/sidebar-student.php'; ?>

  <main class="flex-fill page-wrap">
    <div class="container-fluid p-4">
      <h3 class="mb-1">Student Dashboard</h3>
      <div class="text-muted mb-4">Overview of your learning</div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card shadow-sm p-3">
            <div class="text-muted">My Units</div>
            <div class="display-6 fw-semibold"><?= mysqli_num_rows($myUnits); ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm p-3">
            <div class="text-muted">Assignments</div>
            <div class="display-6 fw-semibold"><?= mysqli_num_rows($assignments); ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm p-3">
            <div class="text-muted">Lessons</div>
            <div class="display-6 fw-semibold"><?= mysqli_num_rows($tt); ?></div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm mt-4">
        <div class="card-body">
          <h6 class="mb-2">Announcements</h6>
          <div class="text-muted">Announcements will appear here (next step).</div>
        </div>
      </div>
    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
