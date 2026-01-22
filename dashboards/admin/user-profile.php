
<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = (int) $_GET['id'];

$sql = "
    SELECT 
        users.user_id,
        users.name,
        users.email,
        users.role_id,
        users.course_id,
        users.class_id,
        roles.role_name,
        courses.course_name,
        courses.course_code,
        classes.year,
        classes.group_name
    FROM users
    JOIN roles ON users.role_id = roles.role_id
    LEFT JOIN courses ON users.course_id = courses.course_id
    LEFT JOIN classes ON users.class_id = classes.class_id
    WHERE users.user_id = ?
";


$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

if (!$user) {
    header("Location: users.php");
    exit();
}
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>
<?php include '../../includes/sidebar-admin.php'; ?>

<main class="flex-fill">
    <div class="container-fluid p-4">

        <a href="users.php" class="btn btn-danger mb-3">← Back to Users</a>
        <a href="#" class="btn btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#editUserModal">
    <i class="bi bi-pencil-square me-1"></i> Edit User
</a>


        <div class="card shadow-sm rounded-3 p-4">
            <h3 class="mb-3"><?= htmlspecialchars($user['name']); ?></h3>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($user['role_name']); ?></p>
                </div>

                <div class="col-md-6">
                    <p>
                        <strong>Course:</strong>
                        <?= $user['course_name']
                            ? htmlspecialchars($user['course_name']) . " (" . htmlspecialchars($user['course_code']) . ")"
                            : '-'; ?>
                    </p>

                    <p>
                        <strong>Class:</strong>
                        <?= $user['class_name'] ?? '-'; ?>
                    </p>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include '../../includes/footer.php'; ?>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User: <?= htmlspecialchars($user['name']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="user-update.php" method="POST">
                    <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">

                    <div class="row g-3">

                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control" 
                                   value="<?= htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   value="<?= htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role</label>
                            <select name="role_id" id="role_id" class="form-select" required>
                                <option value="" selected disabled>Select role</option>
                                <option value="1" <?= $user['role_name'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="2" <?= $user['role_name'] === 'Lecturer' ? 'selected' : ''; ?>>Lecturer</option>
                                <option value="3" <?= $user['role_name'] === 'Student' ? 'selected' : ''; ?>>Student</option>
                            </select>
                        </div>

                        <div class="col-md-6 student-only">
                            <label class="form-label">Course</label>
                            <select name="course_id" id="edit_course_id" class="form-select">
                                <option value="">Select course</option>
                                <?php
                                $courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY course_name");
                                while ($course = mysqli_fetch_assoc($courses)):
                                ?>
                                    <option value="<?= $course['course_id']; ?>"
                                        <?= $course['course_id'] == $user['course_id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($course['course_code']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>


                        <!-- Class -->
                       <div class="col-md-6 student-only">
                           <label class="form-label">Class</label>
                         <select name="class_id" id="edit_class_id" class="form-select">
                            <option value="">Select class</option>
                          </select>
                        </div>


                        <!-- Optional: Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password (leave blank to keep)</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>

                    </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>

                </form>
        </div>
    </div>
</div>

<script>
const courseSelect = document.getElementById('edit_course_id');
const classSelect = document.getElementById('edit_class_id');

const currentClassId = <?= (int) ($user['class_id'] ?? 0); ?>;

function loadClasses(courseId, selectedClassId = null) {
    classSelect.innerHTML = '<option value="">Loading...</option>';

    fetch('get-classes.php?course_id=' + courseId)
        .then(res => res.json())
        .then(data => {
            classSelect.innerHTML = '<option value="">Select class</option>';

            data.forEach(c => {
                const option = document.createElement('option');
                option.value = c.class_id;
                option.textContent = `Year ${c.year} - Group ${c.group_name}`;

                if (selectedClassId && c.class_id == selectedClassId) {
                    option.selected = true;
                }

                classSelect.appendChild(option);
            });
        });
}

// Load classes when course changes
courseSelect.addEventListener('change', function () {
    loadClasses(this.value);
});

// Load classes immediately on modal open
document.getElementById('editUserModal').addEventListener('shown.bs.modal', function () {
    if (courseSelect.value) {
        loadClasses(courseSelect.value, currentClassId);
    }
});
</script>

</body>
