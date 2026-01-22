<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

/*
    Class Register (Admin)
    - First view: list all courses (cards) + search
    - If course_id is selected: show classes for that course
*/

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Fetch courses for the top grid
$courses = mysqli_query($conn, "SELECT course_id, course_code, course_name FROM courses ORDER BY course_code");

// If a course is selected, fetch its classes
$classes = null;
$selectedCourse = null;

if ($course_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT course_id, course_code, course_name FROM courses WHERE course_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $selectedCourse = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

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
}
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
                    <h3 class="mb-1">Class Register</h3>
                    <div class="text-muted">Browse courses → classes → enrolled students</div>
                </div>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Class
                </button>
            </div>

            <!-- Search Courses -->
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label">Search course</label>
                    <input type="text" id="courseSearch" class="form-control" placeholder="Type course code or name...">
                    <div class="form-text">Tip: you can search by course code (e.g. BSC-IM).</div>
                </div>
            </div>

            <!-- Courses Grid -->
            <div class="row g-3" id="courseGrid">
                <?php while ($c = mysqli_fetch_assoc($courses)): ?>
                    <div class="col-md-4 course-card"
                         data-search="<?= strtolower($c['course_code'].' '.$c['course_name']); ?>">
                        <a href="class-register.php?course_id=<?= $c['course_id']; ?>"
                           class="text-decoration-none text-dark">
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

            <!-- Classes Table (only if course selected) -->
            <?php if ($course_id > 0 && $selectedCourse): ?>
                <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
                    <div>
                        <h4 class="mb-0">
                            Classes for <?= htmlspecialchars($selectedCourse['course_code']); ?>
                        </h4>
                        <div class="text-muted"><?= htmlspecialchars($selectedCourse['course_name']); ?></div>
                    </div>
                    <a href="class-register.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Courses
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:120px;">Year</th>
                                        <th style="width:160px;">Group</th>
                                        <th>Students</th>
                                        <th class="text-end" style="width:160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($cl = mysqli_fetch_assoc($classes)): ?>
                                        <?php
                                        // Count students in class (excluding deleted)
                                        $stmt = mysqli_prepare($conn, "
                                            SELECT COUNT(*) AS total
                                            FROM users
                                            WHERE class_id = ? AND role_id = 3 AND deleted = 0
                                        ");
                                        mysqli_stmt_bind_param($stmt, "i", $cl['class_id']);
                                        mysqli_stmt_execute($stmt);
                                        $countRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                                        mysqli_stmt_close($stmt);
                                        $studentCount = $countRow['total'] ?? 0;
                                        ?>

                                        <tr style="cursor:pointer;"
                                            onclick="window.location='class-view.php?class_id=<?= $cl['class_id']; ?>'">
                                            <td>Year <?= (int)$cl['year']; ?></td>
                                            <td class="fw-semibold"><?= htmlspecialchars($cl['group_name']); ?></td>
                                            <td class="text-muted"><?= $studentCount; ?> student(s)</td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteClassModal"
                                                        data-id="<?= $cl['class_id']; ?>"
                                                        data-name="Year <?= (int)$cl['year']; ?> - <?= htmlspecialchars($cl['group_name']); ?>"
                                                        onclick="event.stopPropagation();">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>

                                    <?php if ($classes && mysqli_num_rows($classes) === 0): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">No classes for this course yet</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
<?php include 'modals/add-class-modal.php'; ?>

<!-- Delete Class Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Delete Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    This will permanently delete the class: <strong id="deleteClassName"></strong>.
                </p>
                <div class="text-muted small mt-2">
                    NOTE: You cannot delete a class if students are assigned to it (MySQL foreign key).
                </div>

                <input type="hidden" id="delete_class_id">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteClass">Delete</button>
            </div>

        </div>
    </div>
</div>

<script>
/* Course search (front-end filter) */
document.getElementById('courseSearch').addEventListener('input', function(){
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.course-card').forEach(card => {
        const hay = card.getAttribute('data-search');
        card.style.display = hay.includes(q) ? '' : 'none';
    });
});

/* Delete modal setup */
const deleteClassModal = document.getElementById('deleteClassModal');
const classIdInput = document.getElementById('delete_class_id');
const deleteClassName = document.getElementById('deleteClassName');

deleteClassModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    classIdInput.value = button.getAttribute('data-id');
    deleteClassName.textContent = button.getAttribute('data-name');
});

document.getElementById('confirmDeleteClass').addEventListener('click', function () {
    fetch('class-delete.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'class_id=' + encodeURIComponent(classIdInput.value)
    })
    .then(res => res.text())
    .then(() => location.reload())
    .catch(() => alert('Delete failed. Make sure no students are assigned to this class.'));
});
</script>

</body>
