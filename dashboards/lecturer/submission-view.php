<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';

$lecturer_id = (int)$_SESSION['user_id'];

// Accept either ?submission_id= or ?id=
$submission_id = 0;
if (isset($_GET['submission_id'])) $submission_id = (int)$_GET['submission_id'];
if ($submission_id <= 0 && isset($_GET['id'])) $submission_id = (int)$_GET['id'];

if ($submission_id <= 0) {
    echo "<div class='alert alert-danger'>Invalid submission ID received.</div>";
    exit();
}

/* Fetch submission + assignment + unit + student */
$stmt = mysqli_prepare($conn, "
    SELECT
        s.submission_id,
        s.submission_date,
        s.assignment_id,
        s.student_id,
        a.title,
        a.description,
        u.unit_id,
        u.unit_name,
        stu.name AS student_name,
        stu.email AS student_email,
        g.grade,
        g.feedback
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.assignment_id
    JOIN units u ON a.unit_id = u.unit_id
    JOIN users stu ON s.student_id = stu.user_id
    LEFT JOIN grades g ON g.submission_id = s.submission_id
    WHERE s.submission_id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $submission_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
    echo "<div class='alert alert-danger'>Submission not found in database.</div>";
    exit();
}

/* Security check: lecturer must be assigned to that unit */
$stmt2 = mysqli_prepare($conn, "
    SELECT 1
    FROM unit_lecturers
    WHERE unit_id = ? AND lecturer_id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt2, "ii", $row['unit_id'], $lecturer_id);
mysqli_stmt_execute($stmt2);
$ok = mysqli_stmt_get_result($stmt2);
mysqli_stmt_close($stmt2);

if (!$ok || mysqli_num_rows($ok) === 0) {
    echo "<div class='alert alert-danger'>You are not assigned to this unit, so you cannot view this submission.</div>";
    exit();
}

/* Fetch files */
$files = [];
$stmt3 = mysqli_prepare($conn, "SELECT file_name, file_path FROM files WHERE submission_id = ?");
mysqli_stmt_bind_param($stmt3, "i", $submission_id);
mysqli_stmt_execute($stmt3);
$res3 = mysqli_stmt_get_result($stmt3);
while ($f = mysqli_fetch_assoc($res3)) $files[] = $f;
mysqli_stmt_close($stmt3);
?>

<div>
  <div class="mb-2">
    <div class="text-muted small"><?= htmlspecialchars($row['unit_name']); ?></div>
    <h5 class="mb-1"><?= htmlspecialchars($row['title']); ?></h5>
    <div class="text-muted small">Submitted: <?= htmlspecialchars($row['submission_date']); ?></div>
  </div>

  <div class="mb-3">
    <div class="fw-semibold mb-1">Student</div>
    <div><?= htmlspecialchars($row['student_name']); ?> <span class="text-muted">(<?= htmlspecialchars($row['student_email']); ?>)</span></div>
  </div>

  <div class="mb-3">
    <div class="fw-semibold mb-1">Files</div>

    <?php if (count($files)): ?>
      <ul class="list-group">
        <?php foreach ($files as $f): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><?= htmlspecialchars($f['file_name']); ?></span>
            <a class="btn btn-sm btn-outline-primary" href="../../<?= htmlspecialchars($f['file_path']); ?>" target="_blank">Open</a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="text-muted small">No files found.</div>
    <?php endif; ?>
  </div>

  <hr>

  <form action="grade-save.php" method="POST">
    <input type="hidden" name="submission_id" value="<?= (int)$submission_id; ?>">

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Grade (0-100)</label>
        <input type="number" step="0.01" min="0" max="100"
               name="grade" class="form-control"
               value="<?= htmlspecialchars($row['grade'] ?? ''); ?>" required>
      </div>

      <div class="col-md-8">
        <label class="form-label">Feedback</label>
        <input type="text" name="feedback" class="form-control"
               value="<?= htmlspecialchars($row['feedback'] ?? ''); ?>"
               placeholder="Optional feedback">
      </div>
    </div>

    <button class="btn btn-primary mt-3">Save Grade</button>
  </form>
</div>
