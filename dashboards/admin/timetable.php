<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

// Fetch data for dropdowns
$units = mysqli_query($conn, "SELECT * FROM units ORDER BY unit_name");
$lecturers = mysqli_query($conn, "
    SELECT user_id, name 
    FROM users 
    WHERE role_id = 2
    ORDER BY name
");
$classes = mysqli_query($conn, "
    SELECT class_id, year, group_name 
    FROM classes 
    ORDER BY year, group_name
");

// Existing timetable entries
$timetable = mysqli_query($conn, "
    SELECT 
        timetable.*,
        units.unit_name,
        users.name AS lecturer_name,
        classes.year,
        classes.group_name
    FROM timetable
    JOIN units ON timetable.unit_id = units.unit_id
    JOIN users ON timetable.lecturer_id = users.user_id
    JOIN classes ON timetable.class_id = classes.class_id
    ORDER BY day_of_week, start_time
");
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>
<?php include '../../includes/sidebar-admin.php'; ?>

<main class="flex-fill">
<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Timetable</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimetableModal">
            + Add Lesson
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Unit</th>
                        <th>Lecturer</th>
                        <th>Class</th>
                        <th>Room</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($timetable)): ?>
                    <tr>
                        <td><?= $row['day_of_week']; ?></td>
                        <td><?= substr($row['start_time'],0,5); ?> - <?= substr($row['end_time'],0,5); ?></td>
                        <td><?= htmlspecialchars($row['unit_name']); ?></td>
                        <td><?= htmlspecialchars($row['lecturer_name']); ?></td>
                        <td>Year <?= $row['year']; ?> - <?= $row['group_name']; ?></td>
                        <td><?= htmlspecialchars($row['room'] ?? '-'); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</main>

<?php include '../../includes/footer.php'; ?>
<?php include 'modals/add-timetable-modal.php'; ?>
</body>
