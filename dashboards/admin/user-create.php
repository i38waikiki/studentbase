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
    $password = $_POST['password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($role_id) || empty($password)) {
        header("Location: users.php?error=empty");
        exit();
    }

    // If role is NOT student, class must be NULL
    if ($role_id != 3) {
        $class_id = NULL;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role_id, class_id)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: users.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sssii",
        $name,
        $email,
        $hashedPassword,
        $role_id,
        $class_id
    );

    mysqli_stmt_execute($stmt);

    header("Location: users.php?success=created");
    exit();
}
