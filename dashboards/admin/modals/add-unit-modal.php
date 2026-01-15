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

                    <div class="mb-3">
                        <label for="unit_name" class="form-label">Unit Name</label>
                        <input type="text" name="unit_name" id="unit_name"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="" disabled selected>Select course</option>
                            <?php while ($course = mysqli_fetch_assoc($courses)): ?>
                                <option value="<?= $course['course_id']; ?>">
                                    <?= htmlspecialchars($course['course_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
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
