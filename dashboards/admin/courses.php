<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$sql = "SELECT * FROM courses ORDER BY course_name";
$courses = mysqli_query($conn, $sql);
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
                    <h3 class="mb-1">Courses</h3>
                    <div class="text-muted">Manage course list and details</div>
                </div>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Course
                </button>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Action completed successfully.</div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">Something went wrong. Please try again.</div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 90px;">ID</th>
                                
                                    <th>Course Name</th>
                                    <th>Description</th>
                                    <th class="text-end" style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php while ($course = mysqli_fetch_assoc($courses)): ?>
                                <tr>
                                    <td><?= $course['course_id']; ?></td>
                                    
                                    <td><?= htmlspecialchars($course['course_name']); ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($course['description']); ?></td>

                                    <td class="text-end">
                                        <button
                                            class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCourseModal"
                                            data-id="<?= $course['course_id']; ?>"
                                            data-code="<?= htmlspecialchars($course['course_code']); ?>"
                                            data-name="<?= htmlspecialchars($course['course_name']); ?>"
                                            data-desc="<?= htmlspecialchars($course['description']); ?>">
                                            Edit
                                        </button>

                                        <button
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCourseModal"
                                            data-id="<?= $course['course_id']; ?>"
                                            data-name="<?= htmlspecialchars($course['course_name']); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($courses) === 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No courses found</td>
                                </tr>
                            <?php endif; ?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>

<?php include 'modals/add-course-modal.php'; ?>

<!-- ===================== -->
<!-- Edit Course Modal -->
<!-- ===================== -->
<div class="modal fade" id="editCourseModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Course</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="course-update.php" method="POST" id="editCourseForm">
        <div class="modal-body">
          <input type="hidden" name="course_id" id="edit_course_id">

          <div class="mb-3">
            <label class="form-label">Course Code</label>
            <input type="text" name="course_code" id="edit_course_code" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Course Name</label>
            <input type="text" name="course_name" id="edit_course_name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" id="edit_course_desc" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ===================== -->
<!-- Delete Course Modal -->
<!-- ===================== -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Course</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="course-delete.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="course_id" id="delete_course_id">
          <p class="mb-0">
            Are you sure you want to delete <strong id="delete_course_name"></strong>?
            This will remove the course permanently.
          </p>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" type="submit">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
/* Fill Edit Modal */
document.getElementById('editCourseModal').addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  document.getElementById('edit_course_id').value   = btn.getAttribute('data-id');
  document.getElementById('edit_course_code').value = btn.getAttribute('data-code');
  document.getElementById('edit_course_name').value = btn.getAttribute('data-name');
  document.getElementById('edit_course_desc').value = btn.getAttribute('data-desc');
});

/* Fill Delete Modal */
document.getElementById('deleteCourseModal').addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  document.getElementById('delete_course_id').value = btn.getAttribute('data-id');
  document.getElementById('delete_course_name').textContent = btn.getAttribute('data-name');
});
</script>

</body>
