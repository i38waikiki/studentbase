<?php

/*
Courses are managed using a modal-based CRUD system.
Bootstrap 5 modals are used for a modern UI.
Prepared statements are used for security.
Courses act as the parent entity for units.
*/

session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

// Fetch all courses
$sql = "SELECT * FROM courses ORDER BY course_name";
$courses = mysqli_query($conn, $sql);
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>
<?php include '../../includes/sidebar-admin.php'; ?>

<main class="flex-fill mt-3">
    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Courses</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                + Add Course
            </button>
        </div>

        <div class="card shadow-sm rounded-3">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Name</th>
                            <th>Description</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($course = mysqli_fetch_assoc($courses)): ?>
                        <tr>
                            <td><?= $course['course_id']; ?></td>
                            <td><?= htmlspecialchars($course['course_name']); ?></td>
                            <td><?= htmlspecialchars($course['description']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning">Edit</button>
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($courses) === 0): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No courses found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    
</main>


<?php include '../../includes/footer.php'; ?>

<?php include 'modals/add-course-modal.php'; ?>


</body>

