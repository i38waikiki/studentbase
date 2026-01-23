<?php
require_once '../../includes/auth.php';
requireRole(3);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

/*
  timetable.php (Student)
  - Same grid layout as admin timetable-class.php
  - Read only
  - Shows full schedule for student's class_id
*/

$student_id = (int)$_SESSION['user_id'];

/* Get student's class_id */
$stmt = mysqli_prepare($conn, "SELECT class_id FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$userRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$class_id = (int)($userRow['class_id'] ?? 0);

if ($class_id <= 0) {
    // Student not assigned to a class yet
    $entries = [];
    $classInfo = null;
} else {
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
    $classInfo = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    /* Fetch timetable entries for this class */
    $stmt = mysqli_prepare($conn, "
        SELECT t.*, u.unit_name, l.name AS lecturer_name
        FROM timetable t
        JOIN units u ON t.unit_id = u.unit_id
        JOIN users l ON t.lecturer_id = l.user_id
        WHERE t.class_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $entries = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $entries[] = $row;
    }
    mysqli_stmt_close($stmt);
}

/* Helpers */
function timeToMinutes($t) {
    $parts = explode(':', $t);
    return ((int)$parts[0]) * 60 + (int)$parts[1];
}

$dayMap = [
    "Monday" => 0,
    "Tuesday" => 1,
    "Wednesday" => 2,
    "Thursday" => 3,
    "Friday" => 4
];

/* Grid hours */
$startDay = 8 * 60;   // 08:00
$endDay   = 18 * 60;  // 18:00
$pxPerMin = 1;        // 1 minute = 1px
$gridHeight = ($endDay - $startDay) * $pxPerMin;
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">

<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
    <?php include '../../includes/sidebar-student.php'; ?>

    <main class="flex-fill page-wrap">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">
                        Timetable
                        <?php if ($classInfo): ?>
                            — <?= htmlspecialchars($classInfo['course_code']); ?> Year <?= (int)$classInfo['year']; ?> (<?= htmlspecialchars($classInfo['group_name']); ?>)
                        <?php endif; ?>
                    </h3>
                    <div class="text-muted">
                        <?php if ($classInfo): ?>
                            <?= htmlspecialchars($classInfo['course_name']); ?>
                        <?php else: ?>
                            You are not assigned to a class yet.
                        <?php endif; ?>
                    </div>
                </div>

                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <!-- Week Grid -->
            <div class="card">
                <div class="card-body">

                    <div class="timetable-grid">
                        <!-- Time Column -->
                        <div class="time-col">
                            <?php for ($m = $startDay; $m <= $endDay; $m += 60): ?>
                                <div class="time-slot" style="height: 60px;">
                                    <?= sprintf("%02d:00", floor($m/60)); ?>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <!-- Day Columns -->
                        <?php foreach ($dayMap as $dayName => $dayIndex): ?>
                            <div class="day-col">
                                <div class="day-header"><?= $dayName; ?></div>

                                <div class="day-body" style="height: <?= $gridHeight; ?>px;">
                                    <?php foreach ($entries as $e): ?>
                                        <?php
                                            if (!isset($dayMap[$e['day_of_week']])) continue;
                                            if ($dayMap[$e['day_of_week']] !== $dayIndex) continue;

                                            $top = (timeToMinutes($e['start_time']) - $startDay) * $pxPerMin;
                                            $height = (timeToMinutes($e['end_time']) - timeToMinutes($e['start_time'])) * $pxPerMin;

                                            if ($top < 0) $top = 0;
                                            if ($height < 30) $height = 30;
                                        ?>

                                        <div class="lesson-block"
                                             style="top: <?= $top; ?>px; height: <?= $height; ?>px;">

                                            <div class="fw-semibold"><?= htmlspecialchars($e['unit_name']); ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($e['lecturer_name']); ?></div>
                                            <div class="small">
                                                <?= substr($e['start_time'],0,5); ?> - <?= substr($e['end_time'],0,5); ?>
                                                <?= $e['room'] ? " · ".htmlspecialchars($e['room']) : ""; ?>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>

        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>

<style>
.timetable-grid{
  display: grid;
  grid-template-columns: 90px repeat(5, 1fr);
  gap: 12px;
  align-items: start;
}
.time-col .time-slot{
  border-top: 1px solid #e9ecef;
  font-size: 12px;
  color: #6c757d;
  padding-top: 6px;
}
.day-col{
  border: 1px solid #e9ecef;
  border-radius: 10px;
  overflow: hidden;
  background: #fff;
}
.day-header{
  background: #f8f9fa;
  padding: 10px 12px;
  font-weight: 600;
  border-bottom: 1px solid #e9ecef;
}
.day-body{
  position: relative;
  background: repeating-linear-gradient(
    to bottom,
    #ffffff,
    #ffffff 29px,
    #f6f7f9 30px
  );
}
.lesson-block{
  position: absolute;
  left: 10px;
  right: 10px;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #cfe2ff;
  background: #e7f1ff;
  overflow: hidden;
}
</style>

</body>
