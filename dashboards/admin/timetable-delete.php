<?php
require_once '../../includes/auth.php';
requireRole(1); 

require_once '../../includes/dbh.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $timetable_id = (int)($_POST['timetable_id'] ?? 0);
    $class_id     = (int)($_POST['class_id'] ?? 0);

    if ($timetable_id <= 0 || $class_id <= 0) {
        header("Location: timetable.php?error=invalid");
        exit();
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM timetable WHERE timetable_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $timetable_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: timetable-class.php?class_id=".$class_id."&success=deleted");
    exit();
}
