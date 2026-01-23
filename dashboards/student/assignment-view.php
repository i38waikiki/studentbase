<?php
require_once '../../includes/auth.php';
requireRole(3);

require_once '../../includes/dbh.php';

$student_id = (int)$_SESSION['user_id'];
$assignment_id = (int)($_GET['assignment_id'] ?? 0);

if ($assignment_id <= 0) {
  echo "<div class='alert alert-danger'>Invalid assignment.</div>";
  exit();
}

/* Assignment details */
$stmt = mysqli_prepare($conn, "
  SELECT a.assignment_id, a.unit_id, a.title, a.description, a.files_url, a.due_date,
         u.unit_name
  FROM assignments a
  JOIN units u ON a.unit_id = u.unit_id
  WHERE a.assignment_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $assignment_id);
mysqli_stmt_execute($stmt);
$a = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$a) {
  echo "<div class='alert alert-danger'>Assignment not found.</div>";
  exit();
}

/* Latest submission */
$stmt = mysqli_prepare($conn, "
  SELECT submission_id, submission_date
  FROM submissions
  WHERE assignment_id = ? AND student_id = ?
  ORDER BY submission_date DESC
  LIMIT 1
");
mysqli_stmt_bind_param($stmt, "ii", $assignment_id, $student_id);
mysqli_stmt_execute($stmt);
$sub = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$files = [];
$gradeRow = null;

if ($sub) {
  $submission_id = (int)$sub['submission_id'];

  $stmt = mysqli_prepare($conn, "SELECT file_name, file_path FROM files WHERE submission_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $submission_id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  while ($f = mysqli_fetch_assoc($res)) $files[] = $f;
  mysqli_stmt_close($stmt);

  $stmt = mysqli_prepare($conn, "SELECT grade, feedback FROM grades WHERE submission_id = ? LIMIT 1");
  mysqli_stmt_bind_param($stmt, "i", $submission_id);
  mysqli_stmt_execute($stmt);
  $gradeRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
  mysqli_stmt_close($stmt);
}
?>

<div>
  <div class="mb-2">
    <div class="text-muted small"><?= htmlspecialchars($a['unit_name']); ?></div>
    <h5 class="mb-1"><?= htmlspecialchars($a['title']); ?></h5>
    <div class="text-muted small">Due: <?= htmlspecialchars($a['due_date']); ?></div>
  </div>

  <div class="mb-3">
    <div class="fw-semibold mb-1">Description</div>
    <div class="text-muted"><?= nl2br(htmlspecialchars($a['description'])); ?></div>
  </div>

  <?php if (!empty($a['file_url'])): ?>
    <div class="mb-3">
      <div class="fw-semibold mb-1">Attachment</div>
      <a class="btn btn-outline-secondary btn-sm" href="../../<?= htmlspecialchars($a['file_url']); ?>" target="_blank">
        View attachment
      </a>
    </div>
  <?php endif; ?>

  <hr>

  <div class="mb-2 fw-semibold">Your submission</div>

  <?php if ($sub): ?>
    <div class="text-muted small mb-2">
      Submitted on <?= htmlspecialchars($sub['submission_date']); ?>
    </div>

    <?php if (count($files)): ?>
      <ul class="list-group mb-3">
        <?php foreach ($files as $f): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><?= htmlspecialchars($f['file_name']); ?></span>
            <a class="btn btn-sm btn-outline-primary" href="../../<?= htmlspecialchars($f['file_path']); ?>" target="_blank">
              Open
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="text-muted small mb-3">No files found for this submission.</div>
    <?php endif; ?>

    <?php if ($gradeRow): ?>
      <div class="alert alert-success">
        <div><strong>Grade:</strong> <?= htmlspecialchars($gradeRow['grade']); ?></div>
        <div><strong>Feedback:</strong> <?= nl2br(htmlspecialchars($gradeRow['feedback'] ?? '')); ?></div>
      </div>
    <?php else: ?>
      <div class="alert alert-warning">Not graded yet.</div>
    <?php endif; ?>

  <?php else: ?>
    <form action="submission-create.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="assignment_id" value="<?= (int)$a['assignment_id']; ?>">

      <label class="form-label">Upload file(s)</label>
      <input type="file" name="files[]" class="form-control" multiple required>

      <button class="btn btn-primary mt-3">Submit</button>
    </form>
  <?php endif; ?>
</div>
