<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
if ($course_id <= 0) {
    header("Location: timetable.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT course_code, course_name FROM courses WHERE course_id = ?");
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$course = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$course) {
    header("Location: timetable.php");
    exit();
}

$stmt = mysqli_prepare($conn, "
    SELECT class_id, year, group_name
    FROM classes
    WHERE course_id = ?
    ORDER BY year, group_name
");
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$classes = mysqli_stmt_get_result($stmt);
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
                    <h3 class="mb-1">Timetable — <?= htmlspecialchars($course['course_code']); ?></h3>
                    <div class="text-muted"><?= htmlspecialchars($course['course_name']); ?></div>
                </div>

                <a href="timetable.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Courses
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Select a class</h5>

                    <div class="row g-3">
                        <?php while ($cl = mysqli_fetch_assoc($classes)): ?>
                            <div class="col-md-4">
                                <a class="text-decoration-none text-dark"
                                   href="timetable-class.php?class_id=<?= $cl['class_id']; ?>">
                                    <div class="card p-3 h-100">
                                        <div class="fw-bold">Year <?= (int)$cl['year']; ?></div>
                                        <div class="text-muted"><?= htmlspecialchars($cl['group_name']); ?></div>
                                        <div class="text-muted small mt-2">Click to view schedule</div>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>

                        <?php if (mysqli_num_rows($classes) === 0): ?>
                            <div class="text-muted">No classes found for this course yet.</div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
