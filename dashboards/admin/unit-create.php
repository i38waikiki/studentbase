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
    $lecturers = $_POST['lecturers'] ?? [];

    if (empty($unit_name) || empty($course_id)) {
        header("Location: units.php?error=empty");
        exit;
    }

    // 1. Insert into units
    $sql = "INSERT INTO units (unit_name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $unit_name);
    mysqli_stmt_execute($stmt);
    $unit_id = mysqli_insert_id($conn);

    // 2. Insert into course_units
    $sql_course = "INSERT INTO course_unit (course_id, unit_id) VALUES (?, ?)";
    $stmt_course = mysqli_prepare($conn, $sql_course);
    mysqli_stmt_bind_param($stmt_course, "ii", $course_id, $unit_id);
    mysqli_stmt_execute($stmt_course);

    // 3. Insert into unit_lecturers
    foreach ($lecturers as $lecturer_id) {
    $sql_lect = "INSERT INTO unit_lecturers (unit_id, lecturer_id) VALUES (?, ?)";
    $stmt_lect = mysqli_prepare($conn, $sql_lect);
    mysqli_stmt_bind_param($stmt_lect, "ii", $unit_id, $lecturer_id);
    mysqli_stmt_execute($stmt_lect);
}


    header("Location: units-course.php?course_id=".$course_id."&success=created");
    exit;

    
}
