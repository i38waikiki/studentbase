<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
if ($class_id <= 0) {
    header("Location: class-register.php");
    exit();
}

/* Get class + course info */
$stmt = mysqli_prepare($conn, "
   SELECT cl.class_id, cl.course_id, cl.year, cl.group_name,
       c.course_code, c.course_name
    FROM classes cl
    JOIN courses c ON cl.course_id = c.course_id
    WHERE cl.class_id = ?

");
mysqli_stmt_bind_param($stmt, "i", $class_id);
mysqli_stmt_execute($stmt);
$class = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$class) {
    header("Location: class-register.php");
    exit();
}

/* Students in class (exclude deleted) */
$stmt = mysqli_prepare($conn, "
    SELECT user_id, name, email
    FROM users
    WHERE class_id = ? AND role_id = 3 AND deleted = 0
    ORDER BY name
");
mysqli_stmt_bind_param($stmt, "i", $class_id);
mysqli_stmt_execute($stmt);
$students = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
    <?php include '../../includes/sidebar-admin.php'; ?>

    <main class="flex-fill page-wrap">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">
                        <?= htmlspecialchars($class['course_code']); ?> —
                        Year <?= (int)$class['year']; ?> (<?= htmlspecialchars($class['group_name']); ?>)
                    </h3>
                    <div class="text-muted"><?= htmlspecialchars($class['course_name']); ?></div>
                </div>

               <a href="class-register.php?course_id=<?= (int)$class['course_id']; ?>" class="btn btn-outline-secondary">
                     <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Enrolled Students</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th class="text-end" style="width:160px;">Profile</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($s = mysqli_fetch_assoc($students)): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($s['name']); ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($s['email']); ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-primary" href="user-profile.php?id=<?= $s['user_id']; ?>">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($students) === 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No students assigned to this class yet</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
