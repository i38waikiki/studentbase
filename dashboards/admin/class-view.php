<?php
require_once '../../includes/auth.php';
requireRole(1);

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
        <div class="container-fluid p-4">

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

            <div class="card shadow-sm">
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
                                        
                                        <button class="btn btn-sm btn-primary"
                                                type="button"
                                                onclick="openUserProfile(<?= (int)$s['user_id']; ?>)">
                                            View
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($students) === 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No students assigned to this class yet
                                    </td>
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


<!-- Offcanvas: User Profile Panel -->

<div class="offcanvas offcanvas-end" tabindex="-1" id="userProfileCanvas" aria-labelledby="userProfileCanvasLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="userProfileCanvasLabel">User Profile</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body" id="userProfileContent">
    <div class="text-center text-muted py-5">Select a user…</div>
  </div>
</div>


<!-- Delete User Modal -->

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

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="user-update.php" method="POST" id="editUserForm">
        <div class="modal-body">
          <input type="hidden" name="user_id" id="edit_user_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="name" id="edit_name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="edit_email" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Role</label>
              <select class="form-select" name="role_id" id="edit_role_id" required>
                <option value="1">Admin</option>
                <option value="2">Lecturer</option>
                <option value="3">Student</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Course</label>
              <select class="form-select" name="course_id" id="edit_course_id">
                <option value="">Select course</option>
                <?php
                $courses2 = mysqli_query($conn, "SELECT course_id, course_code FROM courses ORDER BY course_name");
                while ($c = mysqli_fetch_assoc($courses2)):
                ?>
                  <option value="<?= (int)$c['course_id']; ?>"><?= htmlspecialchars($c['course_code']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Class</label>
              <select class="form-select" name="class_id" id="edit_class_id">
                <option value="">Select class</option>
              </select>
              <div class="form-text">Classes load after choosing a course.</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Password (leave blank to keep)</label>
              <input type="password" class="form-control" name="password">
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Save Changes</button>
        </div>
      </form>

    </div>
  </div>
</div>


<script>

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
/* ===== Student validation ===== */
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

<script>

function openUserProfile(userId) {
  const canvasEl = document.getElementById('userProfileCanvas');
  const canvas = bootstrap.Offcanvas.getOrCreateInstance(canvasEl);
  const content = document.getElementById('userProfileContent');

  content.innerHTML = `<div class="text-center text-muted py-5">Loading profile...</div>`;

  fetch('user-profile-fragment.php?id=' + encodeURIComponent(userId))
    .then(res => res.text())
    .then(html => {
      content.innerHTML = html;
      canvas.show();
    })
    .catch(() => {
      content.innerHTML = `<div class="alert alert-danger">Failed to load profile.</div>`;
      canvas.show();
    });
}
</script>

<script>
const profileContent = document.getElementById('userProfileContent');

/* Helper: load classes for course */
function loadClassesForCourse(courseId, selectedClassId = null) {
    const classSelect = document.getElementById('edit_class_id');
    classSelect.innerHTML = '<option value="">Loading...</option>';

    if (!courseId) {
        classSelect.innerHTML = '<option value="">Select class</option>';
        return;
    }

    fetch('get-classes.php?course_id=' + encodeURIComponent(courseId))
        .then(res => res.json())
        .then(data => {
            classSelect.innerHTML = '<option value="">Select class</option>';
            data.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.class_id;
                opt.textContent = `Year ${c.year} - Group ${c.group_name}`;
                if (selectedClassId && parseInt(selectedClassId) === parseInt(c.class_id)) {
                    opt.selected = true;
                }
                classSelect.appendChild(opt);
            });
        })
        .catch(() => {
            classSelect.innerHTML = '<option value="">Error loading classes</option>';
        });
}

/* Click handlers inside offcanvas */
profileContent.addEventListener('click', function(e) {

    // EDIT
    const editBtn = e.target.closest('.js-edit-user');
    if (editBtn) {
        const userId = editBtn.getAttribute('data-userid');

        fetch('user-get.php?id=' + encodeURIComponent(userId))
            .then(res => res.json())
            .then(data => {
                if (!data.ok) {
                    alert(data.error || 'Could not load user');
                    return;
                }

                const u = data.user;

                document.getElementById('edit_user_id').value = u.user_id;
                document.getElementById('edit_name').value = u.name;
                document.getElementById('edit_email').value = u.email;
                document.getElementById('edit_role_id').value = u.role_id;

                document.getElementById('edit_course_id').value = u.course_id ?? '';
                loadClassesForCourse(u.course_id, u.class_id);

                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal'));
                modal.show();
            })
            .catch(() => alert('Error loading user'));
    }


});

/* When course changes in edit modal -> reload classes */
document.getElementById('edit_course_id').addEventListener('change', function () {
    loadClassesForCourse(this.value);
});


</script>


</body>
</html>
