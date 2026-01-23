<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';

$lecturer_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: assignments.php");
  exit();
}

$assignment_id = (int)($_POST['assignment_id'] ?? 0);
if ($assignment_id <= 0) {
  header("Location: assignments.php?error=invalid");
  exit();
}

/* Find unit_id for this assignment */
$stmt = mysqli_prepare($conn, "SELECT unit_id FROM assignments WHERE assignment_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $assignment_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) {
  header("Location: assignments.php?error=notfound");
  exit();
}

$unit_id = (int)$row['unit_id'];

/* Security: lecturer must be assigned to the unit */
$stmt = mysqli_prepare($conn, "SELECT 1 FROM unit_lecturers WHERE unit_id = ? AND lecturer_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ii", $unit_id, $lecturer_id);
mysqli_stmt_execute($stmt);
$ok = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

if (!$ok || mysqli_num_rows($ok) === 0) {
  header("Location: assignments.php?error=notallowed");
  exit();
}

/* Delete dependents first (FK safe) */
mysqli_begin_transaction($conn);

try {
  // files -> via submissions
  $q = "
    DELETE f FROM files f
    JOIN submissions s ON f.submission_id = s.submission_id
    WHERE s.assignment_id = ?
  ";
  $stmt = mysqli_prepare($conn, $q);
  mysqli_stmt_bind_param($stmt, "i", $assignment_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  // grades -> via submissions
  $q = "
    DELETE g FROM grades g
    JOIN submissions s ON g.submission_id = s.submission_id
    WHERE s.assignment_id = ?
  ";
  $stmt = mysqli_prepare($conn, $q);
  mysqli_stmt_bind_param($stmt, "i", $assignment_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  // submissions
  $stmt = mysqli_prepare($conn, "DELETE FROM submissions WHERE assignment_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $assignment_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  // assignment
  $stmt = mysqli_prepare($conn, "DELETE FROM assignments WHERE assignment_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $assignment_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  mysqli_commit($conn);
  header("Location: assignments.php?success=deleted");
  exit();

} catch (Throwable $e) {
  mysqli_rollback($conn);
  header("Location: assignments.php?error=deletefailed");
  exit();
}
