<?php
require_once '../../includes/auth.php';
require_once '../../includes/dbh.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
$nid = (int)($_POST['notification_id'] ?? 0);

if ($user_id <= 0 || $nid <= 0) {
  http_response_code(400);
  echo "bad_request";
  exit();
}

$sql = "UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $nid, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "ok";
