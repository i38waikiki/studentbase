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
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Class</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?= $user['user_id']; ?></td>
                            <td><?= htmlspecialchars($user['name']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td>
                                <?php
                          // Assign badge color based on role
                         switch ($user['role_name']) {
                        case 'Admin':
                         $badgeClass = 'bg-danger'; 
                        break;
                                 
                        case 'Lecturer':
                        $badgeClass = 'bg-primary'; 
                         break;
                                    
                         case 'Student':
                        $badgeClass = 'bg-danger';
                         break;

                        default:
                         $badgeClass = 'bg-success'; // grey
                                     }
                         ?>
                       <span class="badge <?= $badgeClass ?>">
                       <?= htmlspecialchars($user['role_name']); ?>
                     </span>
                      </td>
                     <td><?= $user['class_name'] ?? '-'; ?></td>
                     <td>
                     <button class="btn btn-sm btn-warning">Edit</button>
                      <button class="btn btn-sm btn-danger">Delete</button>
                      </td>
                     </tr>
                    <?php endwhile; ?>
             </tbody>

                </table>

                <!-- NOTE FOR LECTURER:
                     Table data will later be populated dynamically from the database -->
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

                        <!-- Class -->
                        <div class="col-md-6">
                            <label for="class_id" class="form-label">Class</label>
                            <select name="class_id" id="class_id" class="form-select">
                                <option value="">No class</option>
                                <option value="1">Class A</option>
                                <option value="2">Class B</option>
                            </select>
                            <!-- Class only required for students -->
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

</body>
