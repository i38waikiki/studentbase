<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$lecturer_id = (int)$_SESSION['user_id'];
$subs = lecturerGetSubmissions($conn, $lecturer_id);
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
          <h3 class="mb-1">Review / Grade</h3>
          <div class="text-muted">Submissions for your units</div>
        </div>
      </div>

      <?php if (isset($_GET['success']) && $_GET['success'] === 'graded'): ?>
        <div class="alert alert-success">Grade saved.</div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Student</th>
                <th>Unit</th>
                <th>Assignment</th>
                <th>Submitted</th>
                <th>Files</th>
                <th style="width:120px;" class="text-end">Grade</th>
              </tr>
            </thead>

            <tbody>
              <?php while($r = mysqli_fetch_assoc($subs)): ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= htmlspecialchars($r['student_name']); ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($r['student_email']); ?></div>
                  </td>

                  <td><?= htmlspecialchars($r['unit_name']); ?></td>
                  <td><?= htmlspecialchars($r['title']); ?></td>
                  <td class="text-muted small"><?= htmlspecialchars($r['submission_date']); ?></td>

                  <td>
                    <button type="button"
                     class="btn btn-sm btn-outline-secondary"
                     data-bs-toggle="modal"
                     data-bs-target="#submissionModal"
                     data-submission-id="<?= (int)$r['submission_id']; ?>">
                     View
                    </button>
                  </td>

                  <td class="text-end">
                    <?php if ($r['grade'] !== null && $r['grade'] !== ''): ?>
                      <span class="badge bg-success"><?= htmlspecialchars($r['grade']); ?></span>
                    <?php else: ?>
                      <span class="badge bg-warning text-dark">Pending</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>

              <?php if (mysqli_num_rows($subs) === 0): ?>
                <tr><td colspan="6" class="text-muted text-center py-4">No submissions yet</td></tr>
              <?php endif; ?>
            </tbody>

          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>

<!-- Modal -->
<div class="modal fade" id="submissionModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Submission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="submissionModalBody">
        <div class="text-center text-muted py-5">Loading...</div>
      </div>

    </div>
  </div>
</div>

<script>
const subModal = document.getElementById('submissionModal');
const subBody  = document.getElementById('submissionModalBody');

subModal.addEventListener('show.bs.modal', function (event) {
  const trigger = event.relatedTarget;

  
  const id = trigger?.dataset?.submissionId || trigger?.getAttribute('data-submission-id');

  if (!id) {
    subBody.innerHTML = `<div class="alert alert-danger">No submission ID found on the button.</div>`;
    return;
  }

  subBody.innerHTML = `<div class="text-center text-muted py-5">Loading submission #${id}...</div>`;

  fetch('./submission-view.php?submission_id=' + encodeURIComponent(id))
    .then(res => res.text())
    .then(html => subBody.innerHTML = html)
    .catch(() => subBody.innerHTML = `<div class="alert alert-danger">Failed to load submission.</div>`);
});
</script>



</body>
