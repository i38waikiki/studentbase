<?php
session_start();
require_once '../../includes/auth.php';
requireRole(1);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

/*
    Units (Admin)
*/

$courses = mysqli_query($conn, "
    SELECT course_id, course_code, course_name
    FROM courses
    ORDER BY course_code
");
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
    <?php include '../../includes/sidebar-admin.php'; ?>

    <main class="flex-fill page-wrap">
        <div class="container-fluid">

            <div class="mb-4">
                <h3 class="mb-1">Units</h3>
                <div class="text-muted">Select a course to manage its units</div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label">Search course</label>
                    <input type="text" id="courseSearch" class="form-control" placeholder="Type course code or name...">
                </div>
            </div>

            <div class="row g-3" id="courseGrid">
                <?php while ($c = mysqli_fetch_assoc($courses)): ?>
                    <div class="col-md-4 course-card"
                         data-search="<?= strtolower($c['course_code'].' '.$c['course_name']); ?>">
                        <a class="text-decoration-none text-dark"
                           href="units-course.php?course_id=<?= (int)$c['course_id']; ?>">
                            <div class="card p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="text-muted small mb-1">Course</div>
                                        <div class="fw-bold fs-5"><?= htmlspecialchars($c['course_code']); ?></div>
                                        <div class="text-muted"><?= htmlspecialchars($c['course_name']); ?></div>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>

        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>

<script>
document.getElementById('courseSearch').addEventListener('input', function(){
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.course-card').forEach(card => {
        card.style.display = card.getAttribute('data-search').includes(q) ? '' : 'none';
    });
});
</script>

</body>
