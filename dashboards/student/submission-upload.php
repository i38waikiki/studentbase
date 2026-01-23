<?php
require_once '../../includes/auth.php';
requireRole(3);

require_once '../../includes/dbh.php';

$student_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: assignments.php");
  exit();
}

$assignment_id = (int)($_POST['assignment_id'] ?? 0);
if ($assignment_id <= 0) {
  header("Location: assignments.php?error=invalid");
  exit();
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
  header("Location: assignments.php?error=nofile");
  exit();
}

// Save file
$uploadDir = "../../uploads/submissions/$student_id/";
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

$original = basename($_FILES['file']['name']);
$ext = pathinfo($original, PATHINFO_EXTENSION);
$filename = "A{$assignment_id}_" . time() . "." . $ext;
$targetPath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
  header("Location: assignments.php?error=uploadfail");
  exit();
}

// Store URL (web path)
$file_url = "/studentbase/uploads/submissions/$student_id/$filename";

// Insert submission
$stmt = mysqli_prepare($conn, "
  INSERT INTO submissions (assignment_id, student_id, file_url, submission_date)
  VALUES (?, ?, ?, NOW())
");
mysqli_stmt_bind_param($stmt, "iis", $assignment_id, $student_id, $file_url);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: assignments.php?success=submitted");
exit();
