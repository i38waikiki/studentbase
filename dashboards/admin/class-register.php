<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$classes = mysqli_query($conn, "
    SELECT classes.*, courses.course_code
    FROM classes
    JOIN courses ON classes.course_id = courses.course_id
    ORDER BY courses.course_code, classes.year, classes.group_name
");
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>
<?php include '../../includes/sidebar-admin.php'; ?>

<main class="flex-fill">
    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Class Register</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                + Add Class
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Group</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($class = mysqli_fetch_assoc($classes)): ?>
                        <tr>
                            <td><?= $class['class_id']; ?></td>
                            <td><?= htmlspecialchars($class['course_code']); ?></td>
                            <td>Year <?= $class['year']; ?></td>
                            <td><?= htmlspecialchars($class['group_name']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteClassModal"
                                        data-id="<?= $class['class_id']; ?>">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>


<div class="modal fade" id="deleteClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Delete Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>This will permanently delete the class. Students assigned will be affected.</p>
                <input type="hidden" id="delete_class_id">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteClass">Delete</button>
            </div>

        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
<?php include 'modals/add-class-modal.php'; ?>



<script>
const deleteClassModal = document.getElementById('deleteClassModal');
const classIdInput = document.getElementById('delete_class_id');

deleteClassModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    classIdInput.value = button.getAttribute('data-id');
});

document.getElementById('confirmDeleteClass').addEventListener('click', function () {
    fetch('class-delete.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'class_id=' + classIdInput.value
    }).then(() => location.reload());
});
</script>

</body>
