<?php
session_start();
require_once '../includes/dbh.php';

if (isset($_SESSION['login_id'])) {
    $login_id = (int)$_SESSION['login_id'];

    $stmt = mysqli_prepare($conn, "UPDATE login_history SET logout_time = NOW() WHERE login_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $login_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}


session_unset();
session_destroy();

header("Location: login.php?success=loggedout");
exit();
