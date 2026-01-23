<?php
require_once '../../includes/auth.php';
requireRole(1); 

require_once '../../includes/dbh.php';


/*
    Update timetable lesson with clash checking.
    We exclude the current timetable_id so it doesn't clash with itself.
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $timetable_id = (int)($_POST['timetable_id'] ?? 0);
    $class_id     = (int)($_POST['class_id'] ?? 0);

    $unit_id      = (int)($_POST['unit_id'] ?? 0);
    $lecturer_id  = (int)($_POST['lecturer_id'] ?? 0);
    $day          = trim($_POST['day_of_week'] ?? '');
    $start        = trim($_POST['start_time'] ?? '');
    $end          = trim($_POST['end_time'] ?? '');
    $room         = trim($_POST['room'] ?? '');

    if ($timetable_id <= 0 || $class_id <= 0 || $unit_id <= 0 || $lecturer_id <= 0 || $day === '' || $start === '' || $end === '') {
        header("Location: timetable-class.php?class_id=".$class_id."&error=empty");
        exit();
    }

    if (strtotime($start) >= strtotime($end)) {
        header("Location: timetable-class.php?class_id=".$class_id."&error=time");
        exit();
    }

    // Class clash (exclude this timetable_id)
    $sqlClassClash = "
        SELECT COUNT(*) AS total
        FROM timetable
        WHERE class_id = ?
          AND day_of_week = ?
          AND timetable_id <> ?
          AND (start_time < ? AND ? < end_time)
    ";
    $stmt = mysqli_prepare($conn, $sqlClassClash);
    mysqli_stmt_bind_param($stmt, "isiss", $class_id, $day, $timetable_id, $end, $start);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (($row['total'] ?? 0) > 0) {
        header("Location: timetable-class.php?class_id=".$class_id."&error=class_clash");
        exit();
    }

    // Lecturer clash (exclude this timetable_id)
    $sqlLecturerClash = "
        SELECT COUNT(*) AS total
        FROM timetable
        WHERE lecturer_id = ?
          AND day_of_week = ?
          AND timetable_id <> ?
          AND (start_time < ? AND ? < end_time)
    ";
    $stmt = mysqli_prepare($conn, $sqlLecturerClash);
    mysqli_stmt_bind_param($stmt, "isiss", $lecturer_id, $day, $timetable_id, $end, $start);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (($row['total'] ?? 0) > 0) {
        header("Location: timetable-class.php?class_id=".$class_id."&error=lecturer_clash");
        exit();
    }

    // Update
    $sql = "
        UPDATE timetable
        SET unit_id=?, lecturer_id=?, room=?, day_of_week=?, start_time=?, end_time=?
        WHERE timetable_id=?
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iissssi", $unit_id, $lecturer_id, $room, $day, $start, $end, $timetable_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: timetable-class.php?class_id=".$class_id."&success=updated");
    exit();
}
