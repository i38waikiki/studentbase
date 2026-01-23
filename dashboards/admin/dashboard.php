<?php
require_once '../../includes/auth.php';
requireRole(1); 

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';


// Fetch numbers dynamically
$totalStudents  = getTotalByRole($conn, 3);
$totalLecturers = getTotalByRole($conn, 2);
$totalAdmins    = getTotalByRole($conn, 1);
$totalCourses   = getTotalCourses($conn);
$totalUnits     = getTotalUnits($conn);
?>

<?php include '../../includes/header.php'; ?>

<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>

<!-- Shell: Sidebar + Content -->
<div class="d-flex flex-grow-1">

    <!-- Sidebar -->
    <?php include '../../includes/sidebar-admin.php'; ?>

    <!-- Main content -->
    <main class="flex-fill page-wrap">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">Admin Dashboard</h3>
                    <div class="text-muted">Overview of the system</div>
                </div>
            </div>

            <!-- Stats cards -->
            <div class="row g-4">

                <div class="col-md-3">
                    <div class="card p-3">
                        <div class="text-muted mb-1">Students</div>
                        <div class="fs-2 fw-bold"><?= $totalStudents ?></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3">
                        <div class="text-muted mb-1">Lecturers</div>
                        <div class="fs-2 fw-bold"><?= $totalLecturers ?></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3">
                        <div class="text-muted mb-1">Courses</div>
                        <div class="fs-2 fw-bold"><?= $totalCourses ?></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3">
                        <div class="text-muted mb-1">Units</div>
                        <div class="fs-2 fw-bold"><?= $totalUnits ?></div>
                    </div>
                </div>

            </div>

            <!-- Recent activity / announcements -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card p-3">
                        <h5 class="mb-2">Recent Activity / Announcements</h5>
                        <p class="text-muted mb-0">Content will appear here in future iterations.</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

</div>

<?php include '../../includes/footer.php'; ?>

</body>
