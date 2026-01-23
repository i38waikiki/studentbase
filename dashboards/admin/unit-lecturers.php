<?php
require_once '../../includes/auth.php';
requireRole(1); 

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

/*
Fetch units
*/
$units = mysqli_query($conn, "SELECT unit_id, unit_name FROM units ORDER BY unit_name");

/*
Fetch lecturers (role_id = 2)
*/
$lecturers = mysqli_query(
    $conn,
    "SELECT user_id, name FROM users WHERE role_id = 2 ORDER BY name"
);

/*
Fetch existing assignments
*/
$assignments = mysqli_query(
    $conn,
    "SELECT ul.unit_id, u.unit_name, us.name AS lecturer_name
     FROM unit_lecturers ul
     JOIN units u ON ul.unit_id = u.unit_id
     JOIN users us ON ul.lecturer_id = us.user_id"
);
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>
<?php include '../../includes/sidebar-admin.php'; ?>

<main class="flex-fill mt-3">
    <div class="container-fluid p-4">

        <h2 class="mb-4">Assign Lecturers to Units</h2>

        <div class="card shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Lecturer(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($assignments)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['unit_name']); ?></td>
                            <td><?= htmlspecialchars($row['lecturer_name']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($assignments) === 0): ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                No lecturers assigned yet
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignLecturerModal">
            Assign Lecturer
        </button>

    </div>
</main>

<?php include '../../includes/footer.php'; ?>
<?php include 'modals/assign-lecturer-modal.php'; ?>

</body>
