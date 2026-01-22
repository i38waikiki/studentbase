<?php
require_once '../../includes/dbh.php';

header('Content-Type: application/json');

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
if ($course_id <= 0) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT u.unit_id, u.unit_name
    FROM course_unit cu
    JOIN units u ON cu.unit_id = u.unit_id
    WHERE cu.course_id = ?
    ORDER BY u.unit_name
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}
mysqli_stmt_close($stmt);

echo json_encode($data);
