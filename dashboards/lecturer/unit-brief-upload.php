<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';

$lecturer_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: units.php");
  exit();
}

$unit_id = (int)($_POST['unit_id'] ?? 0);

if ($unit_id <= 0 || !isset($_FILES['brief'])) {
  header("Location: units.php?error=empty");
  exit();
}

$allowed = ['pdf','doc','docx','png','jpg','jpeg'];
$original = $_FILES['brief']['name'];
$ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
  header("Location: units.php?error=filetype");
  exit();
}

$uploadDir = "../../uploads/unit-briefs/";
if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

$safeName = time() . "_unit{$unit_id}_" . preg_replace("/[^a-zA-Z0-9\._-]/", "_", $original);
$targetPath = $uploadDir . $safeName;

if (!move_uploaded_file($_FILES['brief']['tmp_name'], $targetPath)) {
  header("Location: units.php?error=upload");
  exit();
}

$dbPath = "uploads/unit-briefs/" . $safeName;

$sql = "
  INSERT INTO unit_briefs (unit_id, lecturer_id, file_name, file_path)
  VALUES (?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    lecturer_id = VALUES(lecturer_id),
    file_name = VALUES(file_name),
    file_path = VALUES(file_path),
    uploaded_at = CURRENT_TIMESTAMP
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiss", $unit_id, $lecturer_id, $original, $dbPath);
mysqli_stmt_execute($stmt);

header("Location: units.php?success=brief");
exit();
