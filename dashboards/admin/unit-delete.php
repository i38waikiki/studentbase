<?php
session_start();
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $unit_id = (int)($_POST['unit_id'] ?? 0);

    if ($unit_id <= 0) {
        header("Location: units.php?error=invalid");
        exit();
    }

    // Remove links first to avoid errors
    $stmt = mysqli_prepare($conn, "DELETE FROM unit_lecturers WHERE unit_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $unit_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM course_unit WHERE unit_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $unit_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    //  delete unit
    $stmt = mysqli_prepare($conn, "DELETE FROM units WHERE unit_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $unit_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: units-course.php?course_id=".$course_id."&success=deleted");
    exit();
}
