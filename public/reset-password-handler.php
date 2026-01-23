<?php
session_start();
require_once '../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$rawToken = $_POST['token'] ?? '';
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password2'] ?? '';

if ($rawToken === '' || $pass1 === '' || $pass2 === '') {
    header("Location: reset-password.php?token=" . urlencode($rawToken) . "&status=invalid");
    exit();
}

if ($pass1 !== $pass2) {
    header("Location: reset-password.php?token=" . urlencode($rawToken) . "&status=mismatch");
    exit();
}

$tokenHash = hash('sha256', $rawToken);

/* Find valid token row */
$stmt = mysqli_prepare($conn, "
    SELECT reset_id, user_id, expiry, used
    FROM password_reset_tokens
    WHERE token = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "s", $tokenHash);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$row) {
    header("Location: reset-password.php?token=" . urlencode($rawToken) . "&status=invalid");
    exit();
}

if ((int)$row['used'] === 1) {
    header("Location: reset-password.php?token=" . urlencode($rawToken) . "&status=invalid");
    exit();
}

if (strtotime($row['expiry']) < time()) {
    header("Location: reset-password.php?token=" . urlencode($rawToken) . "&status=invalid");
    exit();
}

$user_id = (int)$row['user_id'];
$reset_id = (int)$row['reset_id'];

$hashed = password_hash($pass1, PASSWORD_DEFAULT);

/* Update password */
$stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "si", $hashed, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

/* Mark token used */
$stmt = mysqli_prepare($conn, "UPDATE password_reset_tokens SET used = 1 WHERE reset_id = ?");
mysqli_stmt_bind_param($stmt, "i", $reset_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: login.php?reset=success");
exit();
