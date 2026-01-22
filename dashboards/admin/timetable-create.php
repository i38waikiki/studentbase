<?php
session_start();
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $class_id    = (int)($_POST['class_id'] ?? 0);
    $unit_id     = (int)($_POST['unit_id'] ?? 0);
    $lecturer_id = (int)($_POST['lecturer_id'] ?? 0);
    $day         = trim($_POST['day_of_week'] ?? '');
    $start       = trim($_POST['start_time'] ?? '');
    $end         = trim($_POST['end_time'] ?? '');
    $room        = trim($_POST['room'] ?? '');

    if ($class_id <= 0 || $unit_id <= 0 || $lecturer_id <= 0 || $day === '' || $start === '' || $end === '') {
        header("Location: timetable-class.php?class_id=".$class_id."&error=empty");
        exit();
    }

    // Basic time check
    if (strtotime($start) >= strtotime($end)) {
        header("Location: timetable-class.php?class_id=".$class_id."&error=time");
        exit();
    }

    $sql = "INSERT INTO timetable (class_id, unit_id, lecturer_id, room, day_of_week, start_time, end_time)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiissss", $class_id, $unit_id, $lecturer_id, $room, $day, $start, $end);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: timetable-class.php?class_id=".$class_id."&success=1");
    exit();
}
