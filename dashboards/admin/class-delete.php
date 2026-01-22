<?php
require_once '../../includes/dbh.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $class_id = $_POST['class_id'];

    $stmt = mysqli_prepare($conn, "DELETE FROM classes WHERE class_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);

}
