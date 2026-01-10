<?php
// dbh.php - handles the database connection
// Note: Using mysqli procedural style

$host = "localhost";
$user = "root";
$password = "";
$database = "studentbase";

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
