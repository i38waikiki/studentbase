<?php
require_once '../../includes/dbh.php';

/*
Create Unit
- Inserts unit
- Links unit to course via course_units table
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $unit_name = trim($_POST['unit_name']);
    $course_id = (int) $_POST['course_id'];

    if (empty($unit_name) || empty($course_id)) {
        header("Location: units.php?error=empty");
        exit;
    }

    // Insert unit
    $sqlUnit = "INSERT INTO units (unit_name) VALUES (?)";
    $stmtUnit = mysqli_prepare($conn, $sqlUnit);
    mysqli_stmt_bind_param($stmtUnit, "s", $unit_name);
    mysqli_stmt_execute($stmtUnit);

    $unit_id = mysqli_insert_id($conn);

    // Link unit to course
    $sqlLink = "INSERT INTO course_units (course_id, unit_id) VALUES (?, ?)";
    $stmtLink = mysqli_prepare($conn, $sqlLink);
    mysqli_stmt_bind_param($stmtLink, "ii", $course_id, $unit_id);
    mysqli_stmt_execute($stmtLink);

    header("Location: units.php?success=created");
    exit;
}
