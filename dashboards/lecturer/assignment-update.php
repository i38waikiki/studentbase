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
$unit_id       = (int)($_POST['unit_id'] ?? 0);
$title         = trim($_POST['title'] ?? '');
$description   = trim($_POST['description'] ?? '');
$due_date      = trim($_POST['due_date'] ?? '');

if ($assignment_id <= 0 || $unit_id <= 0 || $title === '' || $due_date === '') {
  header("Location: assignments.php?error=empty");
  exit();
}

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

/* Update assignment */
$stmt = mysqli_prepare($conn, "
  UPDATE assignments
  SET unit_id = ?, title = ?, description = ?, due_date = ?
  WHERE assignment_id = ?
");
mysqli_stmt_bind_param($stmt, "isssi", $unit_id, $title, $description, $due_date, $assignment_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: assignments.php?success=updated");
exit();
