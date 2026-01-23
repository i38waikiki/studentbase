<?php
require_once '../../includes/auth.php';
requireRole(3);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$student_id = (int)$_SESSION['user_id'];
$assignments = studentGetAssignments($conn, $student_id);
?>
<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
  <?php include '../../includes/sidebar-student.php'; ?>

  <main class="flex-fill page-wrap">
    <div class="container-fluid p-4">
      <h3 class="mb-1">Assignments</h3>
      <div class="text-muted mb-4">View and submit your work</div>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Assignment</th>
                <th>Unit</th>
                <th>Due</th>
                <th>Status</th>
                <th style="width:260px;">Submit</th>
              </tr>
            </thead>
            <tbody>
              <?php while($a = mysqli_fetch_assoc($assignments)): ?>
                <?php $submitted = studentHasSubmitted($conn, (int)$a['assignment_id'], $student_id); ?>
                <tr>
                  <td class="fw-semibold"><?= htmlspecialchars($a['title']); ?></td>
                  <td><?= htmlspecialchars($a['unit_name']); ?></td>
                  <td><?= htmlspecialchars($a['due_date']); ?></td>
                  <td>
                    <?php if ($submitted): ?>
                      <span class="badge bg-success">Submitted</span>
                    <?php else: ?>
                      <span class="badge bg-warning text-dark">Not submitted</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <form action="submission-upload.php" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                      <input type="hidden" name="assignment_id" value="<?= (int)$a['assignment_id']; ?>">
                      <input type="file" name="file" class="form-control form-control-sm" required>
                      <button class="btn btn-sm btn-primary">Upload</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>

              <?php if (mysqli_num_rows($assignments) === 0): ?>
                <tr><td colspan="5" class="text-muted text-center py-4">No assignments yet</td></tr>
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
