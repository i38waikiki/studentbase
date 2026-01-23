<?php
require_once '../../includes/auth.php';
requireRole(3);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$student_id = (int)$_SESSION['user_id'];
$grades = studentGetGrades($conn, $student_id);
?>
<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
  <?php include '../../includes/sidebar-student.php'; ?>

  <main class="flex-fill page-wrap">
    <div class="container-fluid p-4">
      <h3 class="mb-1">Grades</h3>
      <div class="text-muted mb-4">Your results and feedback</div>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Unit</th>
                <th>Assignment</th>
                <th>Submitted</th>
                <th>Grade</th>
                <th>Feedback</th>
              </tr>
            </thead>
            <tbody>
              <?php while($r = mysqli_fetch_assoc($grades)): ?>
                <tr>
                  <td><?= htmlspecialchars($r['unit_name']); ?></td>
                  <td class="fw-semibold"><?= htmlspecialchars($r['title']); ?></td>
                  <td class="text-muted small"><?= htmlspecialchars($r['submission_date']); ?></td>
                  <td>
                    <?= $r['grade'] !== null ? htmlspecialchars($r['grade']) : '<span class="text-muted">Pending</span>'; ?>
                  </td>
                  <td><?= htmlspecialchars($r['feedback'] ?? ''); ?></td>
                </tr>
              <?php endwhile; ?>

              <?php if (mysqli_num_rows($grades) === 0): ?>
                <tr><td colspan="5" class="text-muted text-center py-4">No grades yet</td></tr>
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
