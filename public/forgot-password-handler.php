<?php
session_start();
require_once '../includes/dbh.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: forgot-password.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
if ($email === '') {
    header("Location: forgot-password.php?status=error");
    exit();
}

/* Find user */
$stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

/* Always respond "sent" to avoid leaking which emails exist */
if (!$user) {
    header("Location: forgot-password.php?status=sent");
    exit();
}

$user_id = (int)$user['user_id'];

/* Create token */
$rawToken = bin2hex(random_bytes(32)); // 64 chars
$expiry = date('Y-m-d H:i:s', time() + (60 * 30)); // 30 minutes

// NOTE: store a hash in DB for better security
$tokenHash = hash('sha256', $rawToken);

/* Insert token */
$stmt = mysqli_prepare($conn, "
    INSERT INTO password_reset_tokens (user_id, token, expiry, used)
    VALUES (?, ?, ?, 0)
");
mysqli_stmt_bind_param($stmt, "iss", $user_id, $tokenHash, $expiry);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

/* DEV: show link on screen */
$_SESSION['reset_link'] = "http://localhost/studentbase/public/reset-password.php?token=" . urlencode($rawToken);

header("Location: reset-link.php");
exit();
