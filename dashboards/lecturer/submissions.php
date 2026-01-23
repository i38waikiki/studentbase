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
      <h3 class="mb-1">Review / Grade</h3>
      <div class="text-muted mb-4">Submissions for your units</div>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Student</th>
                <th>Unit</th>
                <th>Assignment</th>
                <th>Submitted</th>
                <th>File</th>
                <th style="width:220px;">Grade</th>
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
                    <?php if (!empty($r['file_url'])): ?>
                      <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($r['file_url']); ?>" target="_blank">
                        View
                      </a>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td>
                    <form class="d-flex gap-2" action="grade-save.php" method="POST">
                      <input type="hidden" name="submission_id" value="<?= (int)$r['submission_id']; ?>">
                      <input type="number" step="0.01" min="0" max="100" name="grade"
                             class="form-control form-control-sm"
                             value="<?= htmlspecialchars($r['grade'] ?? ''); ?>" placeholder="0-100">
                      <button class="btn btn-sm btn-primary">Save</button>
                    </form>
                    <form class="mt-2" action="grade-save.php" method="POST">
                      <input type="hidden" name="submission_id" value="<?= (int)$r['submission_id']; ?>">
                      <input type="hidden" name="grade" value="<?= htmlspecialchars($r['grade'] ?? ''); ?>">
                      <input type="text" name="feedback" class="form-control form-control-sm"
                             value="<?= htmlspecialchars($r['feedback'] ?? ''); ?>" placeholder="Feedback (optional)">
                      <button class="btn btn-sm btn-outline-primary mt-2">Save Feedback</button>
                    </form>
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
</body>
