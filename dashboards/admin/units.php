<?php
/*
    Units are managed by the admin and linked to courses.
    Relationship is handled via the course_units table.
    Unit lecturers are handled via unit_lecturers.
*/

session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

/*
  Fetch units with:
  - course name
  - lecturers list
*/
$sql = "
SELECT 
    u.unit_id,
    u.unit_name,
    c.course_id,
    c.course_name,
    GROUP_CONCAT(DISTINCT l.name SEPARATOR ', ') AS lecturers
FROM units u
JOIN course_unit cu ON u.unit_id = cu.unit_id
JOIN courses c ON cu.course_id = c.course_id
LEFT JOIN unit_lecturers ul ON u.unit_id = ul.unit_id
LEFT JOIN users l ON ul.lecturer_id = l.user_id
GROUP BY u.unit_id, u.unit_name, c.course_id, c.course_name
ORDER BY u.unit_id DESC
";
$units = mysqli_query($conn, $sql);

/* Courses dropdown */
$courses = mysqli_query($conn, "SELECT course_id, course_name, course_code FROM courses ORDER BY course_name");

/* Lecturers dropdown */
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
                    <h3 class="mb-1">Units</h3>
                    <div class="text-muted">Manage units and assign lecturers</div>
                </div>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Unit
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
                                    <th>Unit Name</th>
                                    <th style="width: 260px;">Course</th>
                                    <th>Lecturers</th>
                                    <th class="text-end" style="width: 180px;">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php while ($unit = mysqli_fetch_assoc($units)): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($unit['unit_name']); ?></td>
                                    <td><?= htmlspecialchars($unit['course_name'] ?? '—'); ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($unit['lecturers'] ?? '-'); ?></td>

                                    <td class="text-end">
                                        <button
                                            class="btn btn-sm btn-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUnitModal"
                                            data-id="<?= $unit['unit_id']; ?>"
                                            data-name="<?= htmlspecialchars($unit['unit_name']); ?>"
                                            data-courseid="<?= $unit['course_id']; ?>">
                                            Edit
                                        </button>

                                        <button
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteUnitModal"
                                            data-id="<?= $unit['unit_id']; ?>"
                                            data-name="<?= htmlspecialchars($unit['unit_name']); ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($units) === 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No units found</td>
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

<?php include 'modals/add-unit-modal.php'; ?>

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

      <form action="unit-update.php" method="POST" id="editUnitForm">
        <div class="modal-body">
          <input type="hidden" name="unit_id" id="edit_unit_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Unit Name</label>
              <input type="text" class="form-control" name="unit_name" id="edit_unit_name" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Course</label>
              <select class="form-select" name="course_id" id="edit_course_id" required>
                <option value="">Select course</option>
                <?php
                
                $courses2 = mysqli_query($conn, "SELECT course_id, course_name, course_code FROM courses ORDER BY course_name");
                while ($c = mysqli_fetch_assoc($courses2)):
                ?>
                  <option value="<?= $c['course_id']; ?>">
                    <?= htmlspecialchars($c['course_code'] . " - " . $c['course_name']); ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Assign Lecturer(s)</label>
              <select class="form-select" name="lecturers[]" id="edit_lecturers" multiple>
                <?php
                $lecturers2 = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role_id = 2 AND deleted = 0 ORDER BY name");
                while ($l = mysqli_fetch_assoc($lecturers2)):
                ?>
                  <option value="<?= $l['user_id']; ?>"><?= htmlspecialchars($l['name']); ?></option>
                <?php endwhile; ?>
              </select>
              <div class="form-text">
                Hold Ctrl (Windows) / Cmd (Mac) to select multiple.
              </div>
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
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Unit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="unit-delete.php" method="POST">
        <div class="modal-body">
          <input type="hidden" name="unit_id" id="delete_unit_id">
          <p class="mb-0">
            Are you sure you want to delete <strong id="delete_unit_name"></strong>?
            This will remove the unit permanently.
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
/* Fill Edit Unit Modal */
document.getElementById('editUnitModal').addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  document.getElementById('edit_unit_id').value = btn.getAttribute('data-id');
  document.getElementById('edit_unit_name').value = btn.getAttribute('data-name');
  document.getElementById('edit_course_id').value = btn.getAttribute('data-courseid');

  
  // We do it dynamically so the modal selects the correct lecturers.
  const unitId = btn.getAttribute('data-id');
  fetch('unit-lecturers-get.php?unit_id=' + encodeURIComponent(unitId))
    .then(res => res.json())
    .then(ids => {
      const select = document.getElementById('edit_lecturers');
      [...select.options].forEach(opt => {
        opt.selected = ids.includes(parseInt(opt.value));
      });
    })
    .catch(() => {});
});

/* Fill Delete Unit Modal */
document.getElementById('deleteUnitModal').addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  document.getElementById('delete_unit_id').value = btn.getAttribute('data-id');
  document.getElementById('delete_unit_name').textContent = btn.getAttribute('data-name');
});
</script>

</body>
