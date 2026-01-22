<?php
session_start();
require_once '../../includes/dbh.php';

/*
    This file handles creation of users by the Admin.
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role_id = $_POST['role_id'];
    $class_id = $_POST['class_id'] ?? NULL;
    $course_id = $_POST['course_id'] ?? NULL;
    $password = $_POST['password'];

    // STUDENT VALIDATION
    if ($role_id == 3) {
        if (empty($course_id) || empty($class_id)) {
            die("Students must have a Course and a Class.");
        }
    } else {
        $course_id = NULL;
        $class_id = NULL;
    }


    // Basic validation
    if (empty($name) || empty($email) || empty($role_id) || empty($password)) {
        header("Location: users.php?error=empty");
        exit();
    }


    // If role is student, course is required
    if ($role_id == 3 && empty($course_id)) {
    header("Location: users.php?error=nocourse");
    exit();
    }

    // If role is NOT student, course and class must be NULL
    if ($role_id != 3) {
    $class_id  = NULL;
    $course_id = NULL;
    }


    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role_id, class_id, course_id)
        VALUES (?, ?, ?, ?, ?, ?)";


    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: users.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param( $stmt,"sssiii",
    $name,
    $email,
    $hashedPassword,
    $role_id,
    $class_id,
    $course_id
);

// If role is NOT student, class must be NULL
if ($role_id != 3) {
    $class_id = NULL;
}

if ($class_id === '' || $class_id === null) {
    $class_id = NULL;
}

if ($role_id == 3) { // Student
    if (empty($course_id) || empty($class_id)) {
        header("Location: users.php?error=student_missing_course_or_class");
        exit();
    }
} else {
    $class_id = NULL;
    $course_id = NULL; // optional if you store course_id in users
}


    mysqli_stmt_execute($stmt);

    header("Location: users.php?success=created");
    exit();


    
}

