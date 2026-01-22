<?php
require_once '../../includes/dbh.php';

header('Content-Type: application/json');

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id <= 0) {
    echo json_encode(['ok' => false, 'error' => 'Invalid user id']);
    exit;
}

$stmt = mysqli_prepare($conn, "
    SELECT user_id, name, email, role_id, course_id, class_id
    FROM users
    WHERE user_id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$user) {
    echo json_encode(['ok' => false, 'error' => 'User not found']);
    exit;
}

echo json_encode(['ok' => true, 'user' => $user]);
exit;
