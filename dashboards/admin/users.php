<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$students  = getUsersByRole($conn, 3);
$lecturers = getUsersByRole($conn, 2);
$admins    = getUsersByRole($conn, 1);

/*
    Admin - Users page
    Shows users table + add user modal + soft delete modal
*/
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>

<!-- Shell: Sidebar + Content -->
<div class="d-flex flex-grow-1">

    <?php include '../../includes/sidebar-admin.php'; ?>

    <main class="flex-fill page-wrap">
        <div class="container-fluid">

            <!-- Page title + button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">Users</h3>
                    <div class="text-muted">Manage students, lecturers, and admins</div>
                </div>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle me-1"></i> Add User
                </button>
            </div>

            
            <div class="card">
                <div class="card-body">
                    <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="usersTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="students-tab" data-bs-toggle="tab" data-bs-target="#students"
                                type="button" role="tab">
                            Students
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="lecturers-tab" data-bs-toggle="tab" data-bs-target="#lecturers"
                                type="button" role="tab">
                            Lecturers
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins"
                                type="button" role="tab">
                            Staff / Admins
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="usersTabsContent">

                    <!-- Students -->
                    <div class="tab-pane fade show active" id="students" role="tabpanel">
                        <?php $users = $students; include 'partials/users-table.php'; ?>
                    </div>

                    <!-- Lecturers -->
                    <div class="tab-pane fade" id="lecturers" role="tabpanel">
                        <?php $users = $lecturers; include 'partials/users-table.php'; ?>
                    </div>

                    <!-- Admins -->
                    <div class="tab-pane fade" id="admins" role="tabpanel">
                        <?php $users = $admins; include 'partials/users-table.php'; ?>
                    </div>

                </div>

                </div>
            </div>

        </div>
    </main>

</div>

<?php include '../../includes/footer.php'; ?>

<!-- ===================== -->
<!-- Add User Modal -->
<!-- ===================== -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="user-create.php" method="POST" id="addUserForm">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role</label>
                            <select name="role_id" id="role_id" class="form-select" required>
                                <option value="" selected disabled>Select role</option>
                                <option value="1">Admin</option>
                                <option value="2">Lecturer</option>
                                <option value="3">Student</option>
                            </select>
                        </div>

                        <!-- Course (student only) -->
                        <div class="col-md-6 student-only">
                            <label for="course_id" class="form-label">Course</label>
                            <select name="course_id" id="course_id" class="form-select">
                                <option value="">Select course</option>
                                <?php
                                $courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY course_name");
                                while ($course = mysqli_fetch_assoc($courses)):
                                ?>
                                    <option value="<?= $course['course_id'] ?>">
                                        <?= htmlspecialchars($course['course_code']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Class (student only) -->
                        <div class="col-md-6 student-only">
                            <label class="form-label">Class</label>
                            <select name="class_id" id="class_id" class="form-select">
                                <option value="">Select class</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <!-- NOTE:
                             In JS we enforce that students must choose course + class.
                             Backend (user-create.php) should also validate this (safety). -->

                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">Create User</button>
            </div>

        </div>
    </div>
</div>

<!-- ===================== -->
<!-- Delete User Modal -->
<!-- ===================== -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to delete this user?
                    They will not be removed from the database, only marked as deleted.
                </p>
                <input type="hidden" id="delete_user_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
/* ===== Delete modal ===== */
var deleteModal = document.getElementById('deleteUserModal');
var userIdInput = deleteModal.querySelector('#delete_user_id');

deleteModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var userId = button.getAttribute('data-userid');
    userIdInput.value = userId;
});

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    var userId = userIdInput.value;

    fetch('user-delete.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'user_id=' + encodeURIComponent(userId) + '&soft=1'
    })
    .then(() => {
        var modal = bootstrap.Modal.getInstance(deleteModal);
        modal.hide();
        location.reload();
    })
    .catch(error => console.error('Error:', error));
});
</script>

<script>
/* ===== Student validation (front-end) ===== */
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    var role = document.getElementById('role_id').value;
    var course = document.getElementById('course_id').value;
    var classSel = document.getElementById('class_id').value;

    if (role === "3") { // Student
        if (course === "" || classSel === "") {
            e.preventDefault();
            alert("Students must have a Course and Class selected.");
        }
    }
});
</script>

<script>
/* ===== Load classes by course ===== */
document.getElementById('course_id').addEventListener('change', function () {
    const courseId = this.value;
    const classSelect = document.getElementById('class_id');

    classSelect.innerHTML = '<option value="">Loading...</option>';

    if (courseId === "") {
        classSelect.innerHTML = '<option value="">Select class</option>';
        return;
    }

    fetch('get-classes.php?course_id=' + encodeURIComponent(courseId))
        .then(res => res.json())
        .then(data => {
            classSelect.innerHTML = '<option value="">Select class</option>';

            data.forEach(c => {
                classSelect.innerHTML += `
                    <option value="${c.class_id}">
                        Year ${c.year} - Group ${c.group_name}
                    </option>`;
            });
        })
        .catch(() => {
            classSelect.innerHTML = '<option value="">Error loading classes</option>';
        });
});
</script>

</body>
