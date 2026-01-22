<?php
session_start();
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $unit_id   = (int)($_POST['unit_id'] ?? 0);
    $unit_name = trim($_POST['unit_name'] ?? '');
    $course_id = (int)($_POST['course_id'] ?? 0);
    $lecturers = $_POST['lecturers'] ?? []; // array

    if ($unit_id <= 0 || $unit_name === '' || $course_id <= 0) {
        header("Location: units.php?error=invalid");
        exit();
    }

    // 1) Update unit name
    $stmt = mysqli_prepare($conn, "UPDATE units SET unit_name = ? WHERE unit_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $unit_name, $unit_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 2) Update course link (remove old link, then insert new)
    $stmt = mysqli_prepare($conn, "DELETE FROM course_unit WHERE unit_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $unit_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "INSERT INTO course_unit (course_id, unit_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $course_id, $unit_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 3) Update lecturer links (reset then insert)
    $stmt = mysqli_prepare($conn, "DELETE FROM unit_lecturers WHERE unit_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $unit_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if (!empty($lecturers)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO unit_lecturers (unit_id, lecturer_id) VALUES (?, ?)");
        foreach ($lecturers as $lecturer_id) {
            $lecturer_id = (int)$lecturer_id;
            mysqli_stmt_bind_param($stmt, "ii", $unit_id, $lecturer_id);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    }

    header("Location: units.php?success=updated");
    exit();
}
