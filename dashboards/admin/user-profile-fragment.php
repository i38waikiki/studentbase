<?php
require_once '../../includes/dbh.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id <= 0) {
    echo "<div class='text-danger'>Invalid user.</div>";
    exit;
}

$sql = "
    SELECT 
        u.user_id,
        u.name,
        u.email,
        r.role_name,
        c.course_name,
        c.course_code,
        cl.year,
        cl.group_name
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
    LEFT JOIN courses c ON u.course_id = c.course_id
    LEFT JOIN classes cl ON u.class_id = cl.class_id
    WHERE u.user_id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$user) {
    echo "<div class='text-danger'>User not found.</div>";
    exit;
}

$initial = strtoupper(substr($user['name'], 0, 1));
$classText = ($user['year'] && $user['group_name'])
    ? "Year " . (int)$user['year'] . " - Group " . htmlspecialchars($user['group_name'])
    : "-";
$courseText = ($user['course_name'])
    ? htmlspecialchars($user['course_name']) . " (" . htmlspecialchars($user['course_code']) . ")"
    : "-";
?>

<div class="text-center mb-4">
    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
         style="width:90px;height:90px;font-size:34px;">
        <?= $initial; ?>
    </div>

    <h5 class="mt-3 mb-0"><?= htmlspecialchars($user['name']); ?></h5>
    <div class="text-muted"><?= htmlspecialchars($user['role_name']); ?></div>
</div>

<div class="d-flex justify-content-center gap-2 mb-3">
    <button class="btn btn-outline-secondary btn-sm" type="button" disabled>
        <i class="bi bi-telephone"></i>
    </button>
    <button class="btn btn-outline-secondary btn-sm" type="button" disabled>
        <i class="bi bi-envelope"></i>
    </button>
    <button class="btn btn-outline-secondary btn-sm" type="button" disabled>
        <i class="bi bi-chat"></i>
    </button>
</div>

<hr class="my-3">

<div class="mb-3">
    <div class="text-muted small">Email</div>
    <div class="fw-semibold"><?= htmlspecialchars($user['email']); ?></div>
</div>

<div class="mb-3">
    <div class="text-muted small">Course</div>
    <div class="fw-semibold"><?= $courseText; ?></div>
</div>

<div class="mb-3">
    <div class="text-muted small">Class</div>
    <div class="fw-semibold"><?= $classText; ?></div>
</div>

<hr class="my-3">

<div class="d-grid gap-2">
   
   <div class="d-grid gap-2">
    <button class="btn btn-warning js-edit-user" type="button" data-userid="<?= (int)$user['user_id']; ?>">
     <i class="bi bi-pencil-square me-1"></i> Edit User
</button>


</div>

</div>
