<?php
require_once '../../includes/auth.php';
requireRole(1); 

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

/*
    Adding lesson to timetable with clash checks.

    Clash Rules:
    1) A class cannot have two lessons overlapping on the same day.
    2) A lecturer cannot teach two lessons overlapping on the same day.
*/

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

    // Ensure start < end
    if (strtotime($start) >= strtotime($end)) {
        header("Location: timetable-class.php?class_id=".$class_id."&error=time");
        exit();
    }



    // 1) Check class clash
    $sqlClassClash = "
        SELECT COUNT(*) AS total
        FROM timetable
        WHERE class_id = ?
          AND day_of_week = ?
          AND (start_time < ? AND ? < end_time)
    ";
    $stmt = mysqli_prepare($conn, $sqlClassClash);
    mysqli_stmt_bind_param($stmt, "isss", $class_id, $day, $end, $start);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (($row['total'] ?? 0) > 0) {
        // Class clash found
        header("Location: timetable-class.php?class_id=".$class_id."&error=class_clash");
        exit();
    }

    // 2) Check lecturer clash
    $sqlLecturerClash = "
        SELECT COUNT(*) AS total
        FROM timetable
        WHERE lecturer_id = ?
          AND day_of_week = ?
          AND (start_time < ? AND ? < end_time)
    ";
    $stmt = mysqli_prepare($conn, $sqlLecturerClash);
    mysqli_stmt_bind_param($stmt, "isss", $lecturer_id, $day, $end, $start);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (($row['total'] ?? 0) > 0) {
        // Lecturer clash found
        header("Location: timetable-class.php?class_id=".$class_id."&error=lecturer_clash");
        exit();
    }

    

    // Insert lesson 
    $sqlInsert = "
        INSERT INTO timetable (class_id, unit_id, lecturer_id, room, day_of_week, start_time, end_time)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = mysqli_prepare($conn, $sqlInsert);
    mysqli_stmt_bind_param($stmt, "iiissss", $class_id, $unit_id, $lecturer_id, $room, $day, $start, $end);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: timetable-class.php?class_id=".$class_id."&success=1");
    exit();
}
