<?php
require_once '../../includes/auth.php';
require_once '../../includes/dbh.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);
if ($user_id <= 0) {
  header('Content-Type: application/json');
  echo json_encode(['ok' => false, 'error' => 'Not logged in']);
  exit();
}

$sql = "
  SELECT notification_id, message, is_read, created_at
  FROM notifications
  WHERE user_id = ?
  ORDER BY created_at DESC
  LIMIT 10
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$list = [];
while ($row = mysqli_fetch_assoc($res)) {
  $list[] = $row;
}
mysqli_stmt_close($stmt);

$sql2 = "SELECT COUNT(*) AS total FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt2 = mysqli_prepare($conn, $sql2);
mysqli_stmt_bind_param($stmt2, "i", $user_id);
mysqli_stmt_execute($stmt2);
$row2 = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
mysqli_stmt_close($stmt2);

header('Content-Type: application/json');
echo json_encode([
  'ok' => true,
  'unread' => (int)$row2['total'],
  'items' => $list
]);
