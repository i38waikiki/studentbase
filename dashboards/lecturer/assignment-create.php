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

// =============================
// NOTIFICATIONS: students in the SAME COURSE as this unit
// =============================

// NOTE FOR LECTURER:
// We notify all students enrolled in the course that this unit belongs to.
// This uses course_unit to find the course_id for the unit.

$msg = "New assignment posted: " . $title;

// Find the course_id for this unit
$stmtC = mysqli_prepare($conn, "SELECT course_id FROM course_unit WHERE unit_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmtC, "i", $unit_id);
mysqli_stmt_execute($stmtC);
$resC = mysqli_stmt_get_result($stmtC);
$rowC = mysqli_fetch_assoc($resC);
mysqli_stmt_close($stmtC);

if ($rowC) {
    $course_id = (int)$rowC['course_id'];

    // Insert notifications for all students in that course
    $stmtN = mysqli_prepare($conn, "
        INSERT INTO notifications (user_id, message, is_read, created_at)
        SELECT user_id, ?, 0, NOW()
        FROM users
        WHERE role_id = 3
          AND course_id = ?
          AND deleted = 0
    ");
    mysqli_stmt_bind_param($stmtN, "si", $msg, $course_id);
    mysqli_stmt_execute($stmtN);
    mysqli_stmt_close($stmtN);
}
exit();

