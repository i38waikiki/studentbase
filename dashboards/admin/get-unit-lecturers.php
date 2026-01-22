<?php
require_once '../../includes/dbh.php';

header('Content-Type: application/json');

$unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
if ($unit_id <= 0) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT u.user_id, u.name
    FROM unit_lecturers ul
    JOIN users u ON ul.lecturer_id = u.user_id
    WHERE ul.unit_id = ?
      AND u.role_id = 2
      AND u.deleted = 0
    ORDER BY u.name
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $unit_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}
mysqli_stmt_close($stmt);

echo json_encode($data);
