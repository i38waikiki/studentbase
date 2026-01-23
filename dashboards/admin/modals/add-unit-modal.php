<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="unit-create.php" method="POST">
                <div class="modal-body">

                    <!-- Existing Unit OR New Unit -->
                    <div class="mb-3">
                        <label class="form-label">Unit</label>

                        <!-- Select existing unit -->
                        <select name="existing_unit_id" id="existing_unit_id" class="form-select mb-2">
                            <option value="">+ Create a new unit</option>
                            <?php
                            // This loads all existing units so you don't create duplicates
                            $existingUnits = mysqli_query($conn, "SELECT unit_id, unit_name FROM units ORDER BY unit_name");
                            while ($u = mysqli_fetch_assoc($existingUnits)):
                            ?>
                                <option value="<?= (int)$u['unit_id']; ?>">
                                    <?= htmlspecialchars($u['unit_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                        <!-- Type a new unit name -->
                        <input type="text"
                               name="new_unit_name"
                               id="new_unit_name"
                               class="form-control"
                               placeholder="Or type a new unit name...">

                        <div class="form-text">
                            Choose an existing unit to avoid duplicates. If it doesn't exist, type a new name.
                        </div>
                    </div>

                    <!-- Course -->
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="" disabled selected>Select course</option>
                            <?php while ($course = mysqli_fetch_assoc($courses)): ?>
                                <option value="<?= (int)$course['course_id']; ?>">
                                    <?= htmlspecialchars($course['course_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Lecturers -->
                    <div class="mb-3">
                        <label for="lecturers" class="form-label">Assign Lecturer(s)</label>
                        <select name="lecturers[]" id="lecturers" class="form-select" multiple>
                            <?php
                            $lecturers = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role_id = 2 ORDER BY name");
                            while ($lecturer = mysqli_fetch_assoc($lecturers)):
                            ?>
                                <option value="<?= (int)$lecturer['user_id']; ?>">
                                    <?= htmlspecialchars($lecturer['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                        <div class="form-text">
                            Hold CTRL (Windows) / CMD (Mac) to select multiple.
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Unit</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>

const existingUnitSelect = document.getElementById('existing_unit_id');
const newUnitInput = document.getElementById('new_unit_name');

existingUnitSelect.addEventListener('change', function () {
    if (this.value) {
        newUnitInput.value = '';
        newUnitInput.disabled = true;
    } else {
        newUnitInput.disabled = false;
    }
});
</script>
