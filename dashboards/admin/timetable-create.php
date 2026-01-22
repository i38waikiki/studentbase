<?php
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "INSERT INTO timetable 
    (unit_id, lecturer_id, class_id, room, day_of_week, start_time, end_time)
    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "iiissss",
        $_POST['unit_id'],
        $_POST['lecturer_id'],
        $_POST['class_id'],
        $_POST['room'],
        $_POST['day_of_week'],
        $_POST['start_time'],
        $_POST['end_time']
    );

    mysqli_stmt_execute($stmt);
    header("Location: timetable.php?success=1");
    exit();
}
