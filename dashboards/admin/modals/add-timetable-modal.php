<div class="modal fade" id="addTimetableModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
    <h5 class="modal-title">Add Lesson</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="timetable-create.php" method="POST">
<div class="modal-body">
<div class="row g-3">

<div class="col-md-6">
<label class="form-label">Unit</label>
<select name="unit_id" class="form-select" required>
<?php while($u = mysqli_fetch_assoc($units)): ?>
<option value="<?= $u['unit_id']; ?>"><?= htmlspecialchars($u['unit_name']); ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-6">
<label class="form-label">Lecturer</label>
<select name="lecturer_id" class="form-select" required>
<?php while($l = mysqli_fetch_assoc($lecturers)): ?>
<option value="<?= $l['user_id']; ?>"><?= htmlspecialchars($l['name']); ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-6">
<label class="form-label">Class</label>
<select name="class_id" class="form-select" required>
<?php while($c = mysqli_fetch_assoc($classes)): ?>
<option value="<?= $c['class_id']; ?>">
Year <?= $c['year']; ?> - <?= $c['group_name']; ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-6">
<label class="form-label">Day</label>
<select name="day_of_week" class="form-select" required>
<option>Monday</option>
<option>Tuesday</option>
<option>Wednesday</option>
<option>Thursday</option>
<option>Friday</option>
</select>
</div>

<div class="col-md-6">
<label class="form-label">Start Time</label>
<input type="time" name="start_time" class="form-control" required>
</div>

<div class="col-md-6">
<label class="form-label">End Time</label>
<input type="time" name="end_time" class="form-control" required>
</div>

<div class="col-md-6">
<label class="form-label">Room</label>
<input type="text" name="room" class="form-control">
</div>

</div>
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-primary">Save</button>
</div>
</form>

</div>
</div>
</div>
