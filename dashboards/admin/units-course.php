<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
if ($course_id <= 0) {
    header("Location: units.php");
    exit();
}

/* Course info */
$stmt = mysqli_prepare($conn, "SELECT course_id, course_code, course_name FROM courses WHERE course_id = ?");
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$course = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$course) {
    header("Location: units.php");
    exit();
}

/* Units for this course */
$stmt = mysqli_prepare($conn, "
    SELECT 
        u.unit_id,
        u.unit_name,
        GROUP_CONCAT(DISTINCT l.name SEPARATOR ', ') AS lecturers
    FROM course_unit cu
    JOIN units u ON cu.unit_id = u.unit_id
    LEFT JOIN unit_lecturers ul ON u.unit_id = ul.unit_id
    LEFT JOIN users l ON ul.lecturer_id = l.user_id
    WHERE cu.course_id = ?
    GROUP BY u.unit_id, u.unit_name
    ORDER BY u.unit_name
");
mysqli_stmt_bind_param($stmt, "i", $course_id);
mysqli_stmt_execute($stmt);
$units = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

/* Lecturers for dropdown */
$lecturers = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role_id = 2 AND deleted = 0 ORDER BY name");
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
                    <h3 class="mb-1">Units — <?= htmlspecialchars($course['course_code']); ?></h3>
                    <div class="text-muted"><?= htmlspecialchars($course['course_name']); ?></div>
                </div>

                <div class="d-flex gap-2">
                    <a href="units.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Courses
                    </a>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Unit
                    </button>
                </div>
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
                                    <th>Unit</th>
                                    <th>Lecturers</th>
                                    <th class="text-end" style="width: 180px;">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php while ($u = mysqli_fetch_assoc($units)): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($u['unit_name']); ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($u['lecturers'] ?? '-'); ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUnitModal"
                                                data-id="<?= (int)$u['unit_id']; ?>"
                                                data-name="<?= htmlspecialchars($u['unit_name']); ?>">
                                            Edit
                                        </button>

                                        <button class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteUnitModal"
                                                data-id="<?= (int)$u['unit_id']; ?>"
                                                data-name="<?= htmlspecialchars($u['unit_name']); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($units) === 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No units for this course yet</td>
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

<!-- ===================== -->
<!-- Add Unit Modal -->
<!-- ===================== -->
<div class="modal fade" id="addUnitModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Unit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="unit-create.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="course_id" value="<?= (int)$course_id; ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Unit Name</label>
              <input type="text" class="form-control" name="unit_name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Assign Lecturer(s)</label>
              <select class="form-select" name="lecturers[]" multiple>
                <?php while($l = mysqli_fetch_assoc($lecturers)): ?>
                  <option value="<?= (int)$l['user_id']; ?>"><?= htmlspecialchars($l['name']); ?></option>
                <?php endwhile; ?>
              </select>
              <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit">Create Unit</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- ===================== -->
<!-- Edit Unit Modal -->
<!-- ===================== -->
<div class="modal fade" id="editUnitModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Unit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="unit-update.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="unit_id" id="edit_unit_id">
          <input type="hidden" name="course_id" value="<?= (int)$course_id; ?>">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Unit Name</label>
              <input type="text" class="form-control" name="unit_name" id="edit_unit_name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Assign Lecturer(s)</label>
              <select class="form-select" name="lecturers[]" id="edit_lecturers" multiple>
                <?php
                $lecturers2 = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role_id = 2 AND deleted = 0 ORDER BY name");
                while($l = mysqli_fetch_assoc($lecturers2)):
                ?>
                  <option value="<?= (int)$l['user_id']; ?>"><?= htmlspecialchars($l['name']); ?></option>
                <?php endwhile; ?>
              </select>
              <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
            </div>
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
<!-- Delete Unit Modal -->
<!-- ===================== -->
<div class="modal fade" id="deleteUnitModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Delete Unit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="mb-0">
          Delete <strong id="delete_unit_name"></strong>? This cannot be undone.
        </p>
        <input type="hidden" id="delete_unit_id">
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

        <form action="unit-delete.php" method="POST">
          <input type="hidden" name="unit_id" id="delete_unit_id_input">
          <input type="hidden" name="course_id" value="<?= (int)$course_id; ?>">
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
/* Edit modal fill + select lecturers */
document.getElementById('editUnitModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const unitId = btn.getAttribute('data-id');
    const unitName = btn.getAttribute('data-name');

    document.getElementById('edit_unit_id').value = unitId;
    document.getElementById('edit_unit_name').value = unitName;

    // Load selected lecturers for this unit
    fetch('unit-lecturers-get.php?unit_id=' + encodeURIComponent(unitId))
      .then(res => res.json())
      .then(ids => {
        const select = document.getElementById('edit_lecturers');
        [...select.options].forEach(opt => {
          opt.selected = ids.includes(parseInt(opt.value));
        });
      })
      .catch(()=>{});
});

/* Delete modal fill */
document.getElementById('deleteUnitModal').addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const unitId = btn.getAttribute('data-id');
    const unitName = btn.getAttribute('data-name');

    document.getElementById('delete_unit_name').textContent = unitName;
    document.getElementById('delete_unit_id_input').value = unitId;
});
</script>

</body>
