<?php
session_start();
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id   = (int)($_POST['course_id'] ?? 0);
    $course_code = trim($_POST['course_code'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($course_id <= 0 || $course_code === '' || $course_name === '') {
        header("Location: courses.php?error=invalid");
        exit();
    }

    $sql = "UPDATE courses SET course_code = ?, course_name = ?, description = ? WHERE course_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        header("Location: courses.php?error=stmt");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sssi", $course_code, $course_name, $description, $course_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: courses.php?success=updated");
    exit();
}
