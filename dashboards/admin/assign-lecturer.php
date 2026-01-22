<?php
require_once '../../includes/dbh.php';

/*
Assign lecturer to unit
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $unit_id = (int) $_POST['unit_id'];
    $lecturer_id = (int) $_POST['lecturer_id'];

    if (!$unit_id || !$lecturer_id) {
        header("Location: unit-lecturers.php?error=empty");
        exit;
    }

    // Prevent duplicate assignments
    $check = mysqli_prepare(
        $conn,
        "SELECT 1 FROM unit_lecturers WHERE unit_id = ? AND lecturer_id = ?"
    );
    mysqli_stmt_bind_param($check, "ii", $unit_id, $lecturer_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        header("Location: unit-lecturers.php?error=exists");
        exit;
    }

    // Insert assignment
    $sql = "INSERT INTO unit_lecturers (unit_id, lecturer_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $unit_id, $lecturer_id);
    mysqli_stmt_execute($stmt);

    header("Location: unit-lecturers.php?success=assigned");
    exit;
}
