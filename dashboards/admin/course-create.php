<?php
session_start();
require_once '../../includes/dbh.php';

/*
    Create Course
    - Uses prepared statements
    - Prevents SQL injection
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Always trim input
    $course_code = trim($_POST['course_code'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Basic validation
    if (empty($course_code) || empty($course_name)) {
        header("Location: courses.php?error=empty");
        exit();
    }

    // SQL with placeholders
    $sql = "INSERT INTO courses (course_code, course_name, description)
            VALUES (?, ?, ?)";

    // Prepare statement
    $stmt = mysqli_prepare($conn, $sql);

    // Safety check
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    // Bind parameters
    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $course_code,
        $course_name,
        $description
    );

    // Execute
    mysqli_stmt_execute($stmt);

    // Close statement
    mysqli_stmt_close($stmt);

    // Redirect back
    header("Location: courses.php?success=created");
    exit();
}
