<?php
session_start();
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role_id = $_POST['role_id'];
    $class_id = $_POST['class_id'] ?: NULL;
    $password = $_POST['password'];

    // Optional: only hash password if provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET name=?, email=?, role_id=?, class_id=?, password=? WHERE user_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssissi", $name, $email, $role_id, $class_id, $hashedPassword, $user_id);
    } else {
        $sql = "UPDATE users SET name=?, email=?, role_id=?, class_id=? WHERE user_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssiii", $name, $email, $role_id, $class_id, $user_id);
    }

    mysqli_stmt_execute($stmt);
    header("Location: user-profile.php?id=$user_id&success=updated");
    exit();
}
