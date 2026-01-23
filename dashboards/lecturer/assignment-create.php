<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: assignments.php");
  exit();
}

$unit_id = (int)($_POST['unit_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$due_date = $_POST['due_date'] ?? '';

if ($unit_id <= 0 || $title === '' || $due_date === '') {
  header("Location: assignments.php?error=empty");
  exit();
}

// NOTE: file_url not used yet, keep empty for now
$file_url = '';

$stmt = mysqli_prepare($conn, "
  INSERT INTO assignments (unit_id, title, description, files_url, due_date)
  VALUES (?, ?, ?, ?, ?)
");
mysqli_stmt_bind_param($stmt, "issss", $unit_id, $title, $description, $file_url, $due_date);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: assignments.php?success=created");
exit();

// NOTE: notify all students in the course linked to this unit
$msg = "New assignment posted: $title";
$stmtN = mysqli_prepare($conn, "
  INSERT INTO notifications (user_id, message, is_read, created_at)
  SELECT u.user_id, ?, 0, NOW()
  FROM users u
  WHERE u.role_id = 3
    AND u.course_id = (
      SELECT cu.course_id FROM course_unit cu WHERE cu.unit_id = ? LIMIT 1
    )
    AND u.deleted = 0
");
mysqli_stmt_bind_param($stmtN, "si", $msg, $unit_id);
mysqli_stmt_execute($stmtN);
mysqli_stmt_close($stmtN);
