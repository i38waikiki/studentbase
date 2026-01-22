<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';


$users = getAllUsers($conn);
/*
    
    This page displays the User Management interface for Admins.
    Currently UI-only; CRUD functionality will be added later.
*/
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>
<?php include '../../includes/sidebar-admin.php'; ?>

<main class="flex-fill">
    <div class="container-fluid p-4">

        <!-- Page title + button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Users</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-circle me-1"></i> Add User
            </button>
        </div>
        
        <!-- Users table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Course</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                            <tr style="cursor:pointer;" onclick="window.location='user-profile.php?id=<?= $user['user_id']; ?>'">
                              
                               <td><?= htmlspecialchars($user['name']); ?></td>
                              <td><?= htmlspecialchars($user['email']); ?></td>
                                 <td>
                            <?php
                          switch ($user['role_name']) {
                             case 'Admin': $badgeClass = 'bg-success'; break;
                             case 'Lecturer': $badgeClass = 'bg-primary'; break;
                            case 'Student': $badgeClass = 'bg-success'; break;
                         default: $badgeClass = 'bg-secondary';
                         }
                         ?>
                                          <span class="badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($user['role_name']); ?>
                            </span>
                        </td>
                        <td><?= $user['class_name'] ?? '-'; ?></td>
                        <td>
                           

                         <!-- Delete Button triggers modal -->
                            <button class="btn btn-sm btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteUserModal" 
                                    data-userid="<?= $user['user_id']; ?>" 
                                    onclick="event.stopPropagation();">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>

             </tbody>

                </table>

                <!--  Table data will later be populated dynamically from the database -->
            </div>
        </div>

    </div>
</main>

<?php include '../../includes/footer.php'; ?>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Notice: Added action and method -->
                <form action="user-create.php" method="POST">
                    <div class="row g-3">

                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role</label>
                            <select name="role_id" id="role_id" class="form-select" required>
                                <option value="" selected disabled>Select role</option>
                                <option value="1">Admin</option>
                                <option value="2">Lecturer</option>
                                <option value="3">Student</option>
                            </select>
                        </div>
                        
                        <!-- Course -->
                        <div class="col-md-6 student-only">
                            <label for="course_id" class="form-label">Course</label>
                            <select name="course_id" id="course_id" class="form-select">
                                <option value="">Select course</option>
                                <?php
                                $courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY course_name");
                                while ($course = mysqli_fetch_assoc($courses)):
                                ?>
                                <option value="<?= $course['course_id'] ?>"><?= htmlspecialchars($course['course_code']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>


                        <!-- Class -->
                      <div class="col-md-6 student-only">
                          <label class="form-label">Class</label>
                          <select name="class_id" id="class_id" class="form-select" required>
                              <option value="">Select class</option>
                         </select>
                    </div>


                        
                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                    </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>

                </form>
        </div>
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
                <p>Are you sure you want to delete this user? They will not be removed from the database, only marked as deleted.</p>
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
    .then(response => response.text())
    .then(data => {
        // Close modal
        var modal = bootstrap.Modal.getInstance(deleteModal);
        modal.hide();

        // Optionally remove user row from table or reload page
        location.reload(); 
    })
    .catch(error => console.error('Error:', error));
});
</script>

<script>
document.querySelector('#addUserModal form').addEventListener('submit', function(e) {
    var role = document.getElementById('role_id').value;
    var course = document.getElementById('course_id').value;
    var classSel = document.getElementById('class_id').value;

    if(role == "3") { // Student
        if(course === "" || classSel === "") {
            e.preventDefault();
            alert("Students must have a Course and Class selected.");
        }
    }
});
</script>

<script>
document.getElementById('course_id').addEventListener('change', function () {
    const courseId = this.value;
    const classSelect = document.getElementById('class_id');

    classSelect.innerHTML = '<option value="">Loading...</option>';

    if (courseId === "") {
        classSelect.innerHTML = '<option value="">Select class</option>';
        return;
    }

    fetch('get-classes.php?course_id=' + courseId)
        .then(response => response.json())
        .then(data => {
            classSelect.innerHTML = '<option value="">Select class</option>';
            data.forEach(cls => {
                classSelect.innerHTML += 
                    `<option value="${cls.class_id}">${cls.class_name}</option>`;
            });
        })
        .catch(() => {
            classSelect.innerHTML = '<option value="">Error loading classes</option>';
        });
});
</script>

<script>
const courseSelect = document.getElementById('course_id');
const classSelect = document.getElementById('class_id');

courseSelect.addEventListener('change', function () {
    const courseId = this.value;
    classSelect.innerHTML = '<option value="">Loading...</option>';

    fetch('get-classes.php?course_id=' + courseId)
        .then(res => res.json())
        .then(data => {
            classSelect.innerHTML = '<option value="">Select class</option>';

            data.forEach(c => {
                classSelect.innerHTML += `
                    <option value="${c.class_id}">
                        Year ${c.year} - Group ${c.group_name}
                    </option>`;
            });
        });
});
</script>


</body>
