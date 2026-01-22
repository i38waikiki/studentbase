<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
if ($class_id <= 0) {
    header("Location: timetable.php");
    exit();
}

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
$class = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$class) {
    header("Location: timetable.php");
    exit();
}

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


/* Dropdown data for modal */
$units = mysqli_query($conn, "SELECT unit_id, unit_name FROM units ORDER BY unit_name");
$lecturers = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role_id = 2 AND deleted = 0 ORDER BY name");

/* Helpers for placing blocks */
function timeToMinutes($t){
    // $t is "HH:MM:SS" or "HH:MM"
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


$startDay = 8 * 60;   // 08:00
$endDay   = 18 * 60;  // 18:00
$pxPerMin = 1;        // 1 minute = 1px (simple)
$gridHeight = ($endDay - $startDay) * $pxPerMin;
?>

<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
    <?php include '../../includes/sidebar-admin.php'; ?>

    <main class="flex-fill page-wrap">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-1">
                        <?= htmlspecialchars($class['course_code']); ?> —
                        Year <?= (int)$class['year']; ?> (<?= htmlspecialchars($class['group_name']); ?>)
                    </h3>
                    <div class="text-muted"><?= htmlspecialchars($class['course_name']); ?></div>
                </div>

                <div class="d-flex gap-2">
                    <a href="timetable-classes.php?course_id=<?= (int)$class['course_id']; ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Lesson
                    </button>
                </div>
            </div>

            <!-- Week Grid -->
            <div class="card">
                <div class="card-body">

                    <div class="timetable-grid">
                        <div class="time-col">
                            <?php for ($m = $startDay; $m <= $endDay; $m += 60): ?>
                                <div class="time-slot" style="height: 60px;">
                                    <?= sprintf("%02d:00", floor($m/60)); ?>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <?php foreach ($dayMap as $dayName => $dayIndex): ?>
                            <div class="day-col">
                                <div class="day-header"><?= $dayName; ?></div>
                                <div class="day-body" style="height: <?= $gridHeight; ?>px;">
                                    <?php foreach ($entries as $e): ?>
                                        <?php if (!isset($dayMap[$e['day_of_week']])) continue; ?>
                                        <?php if ($dayMap[$e['day_of_week']] !== $dayIndex) continue; ?>

                                        <?php
                                            $top = (timeToMinutes($e['start_time']) - $startDay) * $pxPerMin;
                                            $height = (timeToMinutes($e['end_time']) - timeToMinutes($e['start_time'])) * $pxPerMin;
                                            if ($top < 0) $top = 0;
                                            if ($height < 30) $height = 30; // minimum for readability
                                        ?>

                                        <div class="lesson-block" style="top: <?= $top; ?>px; height: <?= $height; ?>px;">
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

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Lesson</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="timetable-create.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="class_id" value="<?= (int)$class_id; ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Unit</label>
              <select class="form-select" name="unit_id" required>
                <option value="" disabled selected>Select unit</option>
                <?php while($u = mysqli_fetch_assoc($units)): ?>
                  <option value="<?= $u['unit_id']; ?>"><?= htmlspecialchars($u['unit_name']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Lecturer</label>
              <select class="form-select" name="lecturer_id" required>
                <option value="" disabled selected>Select lecturer</option>
                <?php while($l = mysqli_fetch_assoc($lecturers)): ?>
                  <option value="<?= $l['user_id']; ?>"><?= htmlspecialchars($l['name']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Day</label>
              <select class="form-select" name="day_of_week" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Start Time</label>
              <input type="time" class="form-control" name="start_time" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">End Time</label>
              <input type="time" class="form-control" name="end_time" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Room (optional)</label>
              <input type="text" class="form-control" name="room">
            </div>
          </div>

          <div class="form-text mt-2">
            NOTE: We can add clash checking later (prevent overlapping lessons).
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Save Lesson</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* timetable layout */
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
