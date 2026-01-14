<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

// Fetch numbers dynamically
$totalStudents = getTotalByRole($conn, 3);
$totalLecturers = getTotalByRole($conn, 2);
$totalAdmins = getTotalByRole($conn, 1);
$totalCourses = getTotalCourses($conn);
$totalUnits = getTotalUnits($conn);
?>

<?php include '../../includes/header.php'; ?>

<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <?php include '../../includes/navbar-dashboard.php'; ?>

    <!-- Sidebar -->
    <?php include '../../includes/sidebar-admin.php'; ?>

    <!-- Main content -->
    <main class="flex-fill mt-3">
        <div class="container-fluid p-4">
            <div class="row g-4">
                <!-- total students -->
                <div class="col-md-4">
                    <div class="card shadow-sm rounded-3 p-3">
                        <h5>Total Students</h5>
                        <h2><?= $totalStudents ?></h2>
                    </div>
                </div>

                <!-- total lecturers -->
                <div class="col-md-4">
                    <div class="card shadow-sm rounded-3 p-3">
                        <h5>Total Lecturers</h5>
                        <h2><?= $totalLecturers ?></h2>
                    </div>
                </div>

                <!-- total courses -->
                <div class="col-md-4">
                    <div class="card shadow-sm rounded-3 p-3">
                        <h5>Total Courses</h5>
                        <h2><?= $totalCourses ?></h2>
                    </div>
                </div>

                 <!-- Total Units -->
                <div class="col-md-4">
                    <div class="card shadow-sm rounded-3 p-3 mt-3">
                        <h5>Total Units</h5>
                        <h2><?= $totalUnits ?></h2>
                    </div>
                </div>
            </div>

            <!-- Recent activity / announcements -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm rounded-3 p-3">
                        <h5>Recent Activity / Announcements</h5>
                        <p>Content will appear here in future iterations.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../../includes/footer.php'; ?>

</body>
