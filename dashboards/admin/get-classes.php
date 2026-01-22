<?php
require_once '../../includes/dbh.php';

$course_id = $_GET['course_id'] ?? 0;

$result = mysqli_query($conn, "
    SELECT class_id, year, group_name
    FROM classes
    WHERE course_id = $course_id
    ORDER BY year, group_name
");

$classes = [];

while ($row = mysqli_fetch_assoc($result)) {
    $classes[] = $row;
}

echo json_encode($classes);
