<?php
require_once '../../includes/dbh.php';

/*
Create Course
- Uses prepared statements
- Prevents SQL injection
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_code = trim($_POST['course_code']);
    $course_name = trim($_POST['course_name']);
    $description = trim($_POST['description']);

    if (empty($course_name) || empty($course_code)) {
    header("Location: courses.php?error=empty");
    exit;
    }

    $sql = "INSERT INTO courses (course_code, course_name, description)
    VALUES (?, ?, ?)";
    mysqli_stmt_bind_param($stmt, "sss", $course_code, $course_name, $description);

    mysqli_stmt_execute($stmt);

    header("Location: courses.php?success=created");
    exit;
}
