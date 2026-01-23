<?php
session_start();
require_once '../includes/dbh.php';
require_once '../includes/functions.php';

/*
    This file handles login logic.
    It checks user credentials and redirects based on role.
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=empty");
        exit();
    }

    $sql = "SELECT * FROM users WHERE email = ? AND deleted = 0";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: login.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {

        if (password_verify($password, $user['password'])) {

            // Store session data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role_id'] = $user['role_id'];

            // Log login activity (Recent Activity)
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

            $logSql = "INSERT INTO login_history (user_id, login_time, ip_address)
                       VALUES (?, NOW(), ?)";

            $logStmt = mysqli_prepare($conn, $logSql);
            if ($logStmt) {
                mysqli_stmt_bind_param($logStmt, "is", $_SESSION['user_id'], $ip);
                mysqli_stmt_execute($logStmt);
                mysqli_stmt_close($logStmt);
            }

            $_SESSION['login_id'] = mysqli_insert_id($conn);

            // Role-based redirect
            switch ($user['role_id']) {
                case 1:
                    header("Location: ../dashboards/admin/dashboard.php");
                    break;
                case 2:
                    header("Location: ../dashboards/lecturer/dashboard.php");
                    break;
                case 3:
                    header("Location: ../dashboards/student/dashboard.php");
                    break;
                default:
                    header("Location: login.php?error=invalidrole");
            }
            exit();
        } else {
            header("Location: login.php?error=wrongpassword");
            exit();
        }

    } else {
        header("Location: login.php?error=nouser");
        exit();
    }
}


