<?php
session_start();
require_once '../../includes/dbh.php';

/*
    PROFILE UPDATE HANDLER (shared logic)

    Notes:
    - Updates ONLY the currently logged-in user (session user_id).
    - Two forms share this handler:
        1) type=account  -> updates name + email
        2) type=password -> updates password (with confirmation)
    - Uses prepared statements for security.
*/

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$type = $_POST['type'] ?? '';

/* Helper: redirect back with query message */
function back($query) {
    header("Location: profile.php?$query");
    exit();
}

/* Update Account Details */
if ($type === 'account') {

    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Basic validation
    if ($name === '' || $email === '') {
        back("error=empty");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        back("error=bademail");
    }

    //prevent duplicate emails 
    $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ? AND user_id != ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
    mysqli_stmt_execute($stmt);
    $dup = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($dup) > 0) {
        back("error=emailtaken");
    }

    // Update name + email
    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ? WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    back("success=account_updated");
}


/* Update Password */
if ($type === 'password') {

    $new_password = $_POST['new_password'] ?? '';
    $confirm      = $_POST['confirm_password'] ?? '';

    // If user submits empty password, we do nothing
    if ($new_password === '' && $confirm === '') {
        back("error=empty_password");
    }

    if ($new_password !== $confirm) {
        back("error=nomatch");
    }

    // Basic strength check (simple)
    if (strlen($new_password) < 8) {
        back("error=weak");
    }

    $hashed = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $hashed, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    back("success=password_updated");
}


/* If type is not recognised */
back("error=invalid");
