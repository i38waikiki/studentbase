<!-- Assign Lecturer Modal -->
<div class="modal fade" id="assignLecturerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Assign Lecturer to Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="assign-lecturer.php" method="POST">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <select name="unit_id" class="form-select" required>
                            <option value="" disabled selected>Select unit</option>
                            <?php while ($unit = mysqli_fetch_assoc($units)): ?>
                                <option value="<?= $unit['unit_id']; ?>">
                                    <?= htmlspecialchars($unit['unit_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lecturer</label>
                        <select name="lecturer_id" class="form-select" required>
                            <option value="" disabled selected>Select lecturer</option>
                            <?php while ($lecturer = mysqli_fetch_assoc($lecturers)): ?>
                                <option value="<?= $lecturer['user_id']; ?>">
                                    <?= htmlspecialchars($lecturer['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>

        </div>
    </div>
</div>
