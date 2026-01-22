<?php
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id = $_POST['course_id'];
    $year = $_POST['year'];
    $group = trim($_POST['group_name']);

    if (empty($course_id) || empty($year) || empty($group)) {
        header("Location: class-register.php?error=empty");
        exit;
    }

    $sql = "INSERT INTO classes (course_id, year, group_name)
            VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $course_id, $year, $group);
    mysqli_stmt_execute($stmt);

    header("Location: class-register.php?success=created");
    exit;
}
