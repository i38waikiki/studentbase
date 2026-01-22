<?php
session_start();
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id = (int)($_POST['course_id'] ?? 0);

    if ($course_id <= 0) {
        header("Location: courses.php?error=invalid");
        exit();
    }

    // NOTE:
    // Because course_units links courses -> units, we delete those links first.
    // This prevents "Cannot delete or update a parent row" foreign key errors.

    $stmt1 = mysqli_prepare($conn, "DELETE FROM course_unit WHERE course_id = ?");
    mysqli_stmt_bind_param($stmt1, "i", $course_id);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    // Now delete the course itself
    $stmt2 = mysqli_prepare($conn, "DELETE FROM courses WHERE course_id = ?");
    mysqli_stmt_bind_param($stmt2, "i", $course_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    header("Location: courses.php?success=deleted");
    exit();
}
