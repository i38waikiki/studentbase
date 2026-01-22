<?php
session_start();
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_POST['user_id'];
    $soft = $_POST['soft'] ?? 0; // soft delete flag

    // Prevent deleting yourself
    if ($user_id == $_SESSION['user_id']) {
        echo "cannot_delete_self";
        exit();
    }

    if ($soft) {
        // Mark as deleted instead of removing
        $sql = "UPDATE users SET deleted = 1 WHERE user_id = ?";
    } else {
        // Hard delete (optional)
        $sql = "DELETE FROM users WHERE user_id = ?";
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    echo "success";
}
