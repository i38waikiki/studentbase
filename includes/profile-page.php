<?php
/*
    PROFILE / SETTINGS (shared include)

    NOTES:
    - This file is included inside admin/lecturer/student profile.php pages.
    - It reads the currently logged-in user via $_SESSION['user_id'].
*/

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

/* Fetch user + related info */
$sql = "
    SELECT 
        u.user_id, u.name, u.email, u.role_id,
        r.role_name,
        c.course_name, c.course_code,
        cl.year, cl.group_name
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
    LEFT JOIN courses c ON u.course_id = c.course_id
    LEFT JOIN classes cl ON u.class_id = cl.class_id
    WHERE u.user_id = ?
    LIMIT 1
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$user) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    return;
}

$initial = strtoupper(substr($user['name'], 0, 1));
$courseText = $user['course_name']
    ? htmlspecialchars($user['course_name']) . " (" . htmlspecialchars($user['course_code']) . ")"
    : "-";
$classText = ($user['year'] && $user['group_name'])
    ? "Year " . (int)$user['year'] . " - Group " . htmlspecialchars($user['group_name'])
    : "-";
?>

<div class="container-fluid p-4">

    <!-- Page header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h3 class="mb-1">Profile & Settings</h3>
            <div class="text-muted">Manage your personal info and account security</div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Left: Profile card -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center p-4">

                    <!-- Avatar circle -->
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                         style="width:96px;height:96px;font-size:36px;">
                        <?= $initial; ?>
                    </div>

                    <h5 class="mt-3 mb-0"><?= htmlspecialchars($user['name']); ?></h5>
                    <div class="text-muted"><?= htmlspecialchars($user['role_name']); ?></div>

                    <hr class="my-4">

                    <!-- Quick info -->
                    <div class="text-start">
                        <div class="mb-3">
                            <div class="text-muted small">Email</div>
                            <div class="fw-semibold"><?= htmlspecialchars($user['email']); ?></div>
                        </div>

                        <?php if ($user['role_id'] == 3): // Student ?>
                            <div class="mb-3">
                                <div class="text-muted small">Course</div>
                                <div class="fw-semibold"><?= $courseText; ?></div>
                            </div>

                            <div class="mb-1">
                                <div class="text-muted small">Class</div>
                                <div class="fw-semibold"><?= $classText; ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- Small help card -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="fw-semibold mb-1">Tip</div>
                    <div class="text-muted small">
                        Keep your password private and update it regularly.
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Tabs -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">

                    <!-- Tabs header -->
                    <ul class="nav nav-tabs px-3 pt-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-account" type="button" role="tab">
                                <i class="bi bi-person me-1"></i> Account
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-security" type="button" role="tab">
                                <i class="bi bi-shield-lock me-1"></i> Security
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs content -->
                    <div class="tab-content p-4">

                        <!-- ACCOUNT TAB -->
                        <div class="tab-pane fade show active" id="tab-account" role="tabpanel">

                            <!-- 
                                 Submit goes to one handler that updates the logged-in user -->
                            <form action="profile-update.php" method="POST">
                                <input type="hidden" name="type" value="account">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="name"
                                               value="<?= htmlspecialchars($user['name']); ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email"
                                               value="<?= htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>

                        </div>

                        <!-- SECURITY TAB -->
                        <div class="tab-pane fade" id="tab-security" role="tabpanel">

                            <!--
                                 Leave password blank = keep the same password -->
                            <form action="profile-update.php" method="POST">
                                <input type="hidden" name="type" value="password">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="new_password"
                                               placeholder="Enter new password">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="confirm_password"
                                               placeholder="Re-enter new password">
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-3 mb-0">
                                    <small>
                                        Use at least 8 characters. Avoid using your name or email in the password.
                                    </small>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button class="btn btn-primary">
                                        <i class="bi bi-key me-1"></i> Update Password
                                    </button>
                                </div>
                            </form>

                        </div>

                    </div>

                </div>
            </div>

            
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <div class="fw-semibold mb-1">Recent Activity</div>
                    <div class="text-muted small">
                        This section can later show login history / last password update.
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
