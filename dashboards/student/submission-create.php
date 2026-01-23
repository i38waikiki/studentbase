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
  header("Location: assignments.php?error=empty");
  exit();
}

if (!isset($_FILES['files'])) {
  header("Location: assignments.php?error=nofiles");
  exit();
}

/* Create submission row */
$stmt = mysqli_prepare($conn, "INSERT INTO submissions (assignment_id, student_id) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ii", $assignment_id, $student_id);
mysqli_stmt_execute($stmt);
$submission_id = (int)mysqli_insert_id($conn);

/* Save each file into `files` table */
$allowed = ['pdf','doc','docx','png','jpg','jpeg','zip'];
$uploadDir = "../../uploads/submissions/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$count = count($_FILES['files']['name']);

for ($i = 0; $i < $count; $i++) {

  if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) continue;

  $original = $_FILES['files']['name'][$i];
  $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
  if (!in_array($ext, $allowed)) continue;

  $safeName = time() . "_sub{$submission_id}_" . preg_replace("/[^a-zA-Z0-9\._-]/", "_", $original);
  $targetPath = $uploadDir . $safeName;

  if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $targetPath)) {
    $dbPath = "uploads/submissions/" . $safeName;

    $stmt2 = mysqli_prepare($conn, "INSERT INTO files (submission_id, file_name, file_path) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, "iss", $submission_id, $original, $dbPath);
    mysqli_stmt_execute($stmt2);
  }
}

header("Location: assignments.php?success=submitted");
exit();
