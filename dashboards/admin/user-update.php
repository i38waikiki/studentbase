<?php
session_start();
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: users.php");
    exit();
}

$user_id  = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$role_id  = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;

$course_id = isset($_POST['course_id']) && $_POST['course_id'] !== '' ? (int)$_POST['course_id'] : NULL;
$class_id  = isset($_POST['class_id']) && $_POST['class_id'] !== '' ? (int)$_POST['class_id'] : NULL;

$password = $_POST['password'] ?? '';

/* Basic validation */
if ($user_id <= 0 || $name === '' || $email === '' || $role_id <= 0) {
    header("Location: users.php?error=empty");
    exit();
}

/*  Student rules:
   - Students must have course + class
   - Non-students should not have course/class saved */
if ($role_id === 3) { // Student
    if ($course_id === NULL || $class_id === NULL) {
        header("Location: users.php?error=student_missing_course_class");
        exit();
    }
} else {
    $course_id = NULL;
    $class_id  = NULL;
}


if ($password !== '') {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE users
            SET name = ?, email = ?, role_id = ?, course_id = ?, class_id = ?, password = ?
            WHERE user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssiiisi",
        $name,
        $email,
        $role_id,
        $course_id,
        $class_id,
        $hashedPassword,
        $user_id
    );

} else {

    $sql = "UPDATE users
            SET name = ?, email = ?, role_id = ?, course_id = ?, class_id = ?
            WHERE user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssiiii",
        $name,
        $email,
        $role_id,
        $course_id,
        $class_id,
        $user_id
    );
}

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: users.php?success=updated");
exit();
