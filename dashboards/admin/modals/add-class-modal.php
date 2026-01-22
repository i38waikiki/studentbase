<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="class-create.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Course -->
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select" required>
                            <option value="">Select course</option>
                            <?php
                            $courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY course_code");
                            while ($course = mysqli_fetch_assoc($courses)):
                            ?>
                                <option value="<?= $course['course_id']; ?>">
                                    <?= htmlspecialchars($course['course_code']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Year -->
                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select" required>
                            <option value="">Select year</option>
                            <option value="1">Year 1</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                        </select>
                    </div>

                    <!-- Group -->
                    <div class="mb-3">
                        <label class="form-label">Group</label>
                        <input type="text" name="group_name" class="form-control" placeholder="A / B / C" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Create Class</button>
                </div>
            </form>

        </div>
    </div>
</div>
