<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';

$lecturer_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: submissions.php");
  exit();
}

$submission_id = (int)($_POST['submission_id'] ?? 0);
$grade = ($_POST['grade'] ?? null);
$feedback = trim($_POST['feedback'] ?? '');

if ($submission_id <= 0) {
  header("Location: submissions.php?error=invalid");
  exit();
}

// Check if grade row exists
$stmt = mysqli_prepare($conn, "SELECT grade_id FROM grades WHERE submission_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $submission_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($row) {
  // Update
  $sql = "UPDATE grades SET grade=?, feedback=? WHERE submission_id=?";
  $stmt = mysqli_prepare($conn, $sql);
  $g = ($grade === '' || $grade === null) ? null : (float)$grade;
  mysqli_stmt_bind_param($stmt, "dsi", $g, $feedback, $submission_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
} else {
  // Insert
  $sql = "INSERT INTO grades (submission_id, lecturer_id, grade, feedback) VALUES (?,?,?,?)";
  $stmt = mysqli_prepare($conn, $sql);
  $g = ($grade === '' || $grade === null) ? null : (float)$grade;
  mysqli_stmt_bind_param($stmt, "iids", $submission_id, $lecturer_id, $g, $feedback);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

header("Location: submissions.php?success=saved");

$msg = "Your submission has been graded.";
$stmtN = mysqli_prepare($conn, "
  INSERT INTO notifications (user_id, message, is_read, created_at)
  SELECT s.student_id, ?, 0, NOW()
  FROM submissions s
  WHERE s.submission_id = ?
  LIMIT 1
");
mysqli_stmt_bind_param($stmtN, "si", $msg, $submission_id);
mysqli_stmt_execute($stmtN);
mysqli_stmt_close($stmtN);
exit();

