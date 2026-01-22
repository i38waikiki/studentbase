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

/* Dropdown data for modals */
$units = mysqli_query($conn, "SELECT unit_id, unit_name FROM units ORDER BY unit_name");
$lecturers = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role_id = 2 AND deleted = 0 ORDER BY name");

/* Helpers */
function timeToMinutes($t){
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

            <!-- Alerts -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php
                        if ($_GET['error'] === 'class_clash') echo "This class already has a lesson during that time.";
                        else if ($_GET['error'] === 'lecturer_clash') echo "This lecturer is already booked during that time.";
                        else if ($_GET['error'] === 'time') echo "End time must be after start time.";
                        else echo "Something went wrong. Please try again.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                        if ($_GET['success'] === 'updated') echo "Lesson updated successfully.";
                        else if ($_GET['success'] === 'deleted') echo "Lesson deleted successfully.";
                        else echo "Lesson added successfully.";
                    ?>
                </div>
            <?php endif; ?>

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
                                            if ($height < 30) $height = 30; // minimum block height
                                        ?>

                                        <!-- Clickable Lesson Block -->
                                        <div class="lesson-block"
                                             role="button"
                                             data-bs-toggle="modal"
                                             data-bs-target="#editLessonModal"
                                             data-id="<?= (int)$e['timetable_id']; ?>"
                                             data-unit="<?= (int)$e['unit_id']; ?>"
                                             data-lecturer="<?= (int)$e['lecturer_id']; ?>"
                                             data-day="<?= htmlspecialchars($e['day_of_week']); ?>"
                                             data-start="<?= substr($e['start_time'],0,5); ?>"
                                             data-end="<?= substr($e['end_time'],0,5); ?>"
                                             data-room="<?= htmlspecialchars($e['room'] ?? ''); ?>"
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

<!-- ===================== -->
<!-- Add Lesson Modal -->
<!-- ===================== -->
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

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Save Lesson</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ===================== -->
<!-- Edit Lesson Modal -->
<!-- ===================== -->
<div class="modal fade" id="editLessonModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Lesson</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="timetable-update.php" method="POST">
        <div class="modal-body">

          <input type="hidden" name="timetable_id" id="edit_timetable_id">
          <input type="hidden" name="class_id" value="<?= (int)$class_id; ?>">

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Unit</label>
              <select class="form-select" name="unit_id" id="edit_unit_id" required>
                <?php
                $units2 = mysqli_query($conn, "SELECT unit_id, unit_name FROM units ORDER BY unit_name");
                while($u = mysqli_fetch_assoc($units2)):
                ?>
                  <option value="<?= $u['unit_id']; ?>"><?= htmlspecialchars($u['unit_name']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Lecturer</label>
              <select class="form-select" name="lecturer_id" id="edit_lecturer_id" required>
                <?php
                $lecturers2 = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role_id = 2 AND deleted = 0 ORDER BY name");
                while($l = mysqli_fetch_assoc($lecturers2)):
                ?>
                  <option value="<?= $l['user_id']; ?>"><?= htmlspecialchars($l['name']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Day</label>
              <select class="form-select" name="day_of_week" id="edit_day" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Start Time</label>
              <input type="time" class="form-control" name="start_time" id="edit_start" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">End Time</label>
              <input type="time" class="form-control" name="end_time" id="edit_end" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Room (optional)</label>
              <input type="text" class="form-control" name="room" id="edit_room">
            </div>

          </div>

        

        </div>

        <div class="modal-footer d-flex justify-content-between">
          <button type="submit" class="btn btn-primary">Save Changes</button>

         <button type="button"
                class="btn btn-danger"
                data-bs-toggle="modal"
                data-bs-target="#deleteLessonModal">
            Delete Lesson
        </button>

        </div>

      </form>
    </div>
  </div>
</div>

<!-- ================================= -->
<!-- Delete Lesson Confirmation Modal -->
<!-- ================================ -->
<div class="modal fade" id="deleteLessonModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title text-danger">Delete Lesson</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="mb-0">
          Are you sure you want to delete this lesson?<br>
          <strong>This action cannot be undone.</strong>
        </p>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">
          Cancel
        </button>

        <form action="timetable-delete.php" method="POST">
          <input type="hidden" name="timetable_id" id="delete_timetable_id">
          <input type="hidden" name="class_id" value="<?= (int)$class_id; ?>">
          <button type="submit" class="btn btn-danger">
            Delete
          </button>
        </form>
      </div>

    </div>
  </div>
</div>


<style>
/* Simple timetable layout */
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
  cursor: pointer;
  transition: transform 0.1s ease;
}

.lesson-block:hover{
  transform: scale(1.01);
}
</style>

<script>
/* Prefill Edit Lesson Modal */
document.getElementById('editLessonModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;

    document.getElementById('edit_timetable_id').value = btn.getAttribute('data-id');
    document.getElementById('edit_unit_id').value = btn.getAttribute('data-unit');
    document.getElementById('edit_lecturer_id').value = btn.getAttribute('data-lecturer');
    document.getElementById('edit_day').value = btn.getAttribute('data-day');
    document.getElementById('edit_start').value = btn.getAttribute('data-start');
    document.getElementById('edit_end').value = btn.getAttribute('data-end');
    document.getElementById('edit_room').value = btn.getAttribute('data-room') || '';
});
</script>

<script>
/* Pass timetable_id into delete modal */
document.getElementById('deleteLessonModal').addEventListener('show.bs.modal', function () {
    const timetableId = document.getElementById('edit_timetable_id').value;
    document.getElementById('delete_timetable_id').value = timetableId;
});
</script>


</body>
