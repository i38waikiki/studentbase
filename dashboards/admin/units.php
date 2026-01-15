<?php

/*
Units are managed by the admin and linked to courses.
The relationship is handled via the course_units table.
Bootstrap modals are used for a modern UI.
Prepared statements ensure database security.
*/ 

session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

/*
Fetch units with their related course name
*/
$sql = "
    SELECT 
        u.unit_id,
        u.unit_name,
        c.course_name
    FROM units u
    LEFT JOIN course_unit cu ON u.unit_id = cu.unit_id
    LEFT JOIN courses c ON cu.course_id = c.course_id
    ORDER BY u.unit_name
";
$units = mysqli_query($conn, $sql);

// Fetch courses for dropdown
$courses = mysqli_query($conn, "SELECT course_id, course_name FROM courses ORDER BY course_name");
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>
<?php include '../../includes/sidebar-admin.php'; ?>

<main class="flex-fill mt-3">
    <div class="container-fluid p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Units</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                + Add Unit
            </button>
        </div>

        <div class="card shadow-sm rounded-3">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Unit Name</th>
                            <th>Course</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($unit = mysqli_fetch_assoc($units)): ?>
                        <tr>
                            <td><?= $unit['unit_id']; ?></td>
                            <td><?= htmlspecialchars($unit['unit_name']); ?></td>
                            <td><?= htmlspecialchars($unit['course_name'] ?? '—'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning">Edit</button>
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>

                        <?php if (mysqli_num_rows($units) === 0): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No units found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<?php include '../../includes/footer.php'; ?>
<?php include 'modals/add-unit-modal.php'; ?>

</body>
