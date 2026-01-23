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
