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

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="mb-1">Assignments</h3>
          <div class="text-muted">View and submit your work</div>
        </div>
      </div>

      <?php if (isset($_GET['success']) && $_GET['success'] === 'submitted'): ?>
        <div class="alert alert-success">Submission uploaded successfully.</div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
          <?php
            if ($_GET['error'] === 'upload_failed') echo "Upload failed. Please try again.";
            else if ($_GET['error'] === 'nofiles') echo "Please choose at least one file.";
            else echo "Something went wrong.";
          ?>
        </div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Assignment</th>
                <th>Unit</th>
                <th>Due</th>
                <th>Status</th>
                <th class="text-end" style="width:140px;">Open</th>
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

                  <td class="text-end">
                    <button class="btn btn-outline-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#assignmentModal"
                            data-assignment-id="<?= (int)$a['assignment_id']; ?>">
                      View
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>

              <?php if (mysqli_num_rows($assignments) === 0): ?>
                <tr>
                  <td colspan="5" class="text-muted text-center py-4">No assignments yet</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>


<div class="modal fade" id="assignmentModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Assignment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="assignmentModalBody">
        <div class="text-center text-muted py-5">Loading...</div>
      </div>

    </div>
  </div>
</div>

<script>
const assignmentModal = document.getElementById('assignmentModal');
const assignmentModalBody = document.getElementById('assignmentModalBody');

assignmentModal.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  const id = button.getAttribute('data-assignment-id');

  assignmentModalBody.innerHTML = `<div class="text-center text-muted py-5">Loading...</div>`;

  fetch('assignment-view.php?assignment_id=' + encodeURIComponent(id))
    .then(res => res.text())
    .then(html => assignmentModalBody.innerHTML = html)
    .catch(() => assignmentModalBody.innerHTML = `<div class="alert alert-danger">Failed to load assignment.</div>`);
});
</script>

</body>
