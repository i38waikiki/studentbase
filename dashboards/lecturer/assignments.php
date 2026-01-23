<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

$lecturer_id = (int)$_SESSION['user_id'];
$assignments = lecturerGetAssignments($conn, $lecturer_id);

// units dropdown
$units = lecturerGetMyUnits($conn, $lecturer_id);
?>
<?php include '../../includes/header.php'; ?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
  <?php include '../../includes/sidebar-lecturer.php'; ?>

  <main class="flex-fill page-wrap">
    <div class="container-fluid p-4">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="mb-1">Assignments</h3>
          <div class="text-muted">Create and manage assignments</div>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
          <i class="bi bi-plus-circle me-1"></i> Add Assignment
        </button>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Title</th>
                <th>Unit</th>
                <th>Due</th>
              </tr>
            </thead>
            <tbody>
              <?php while($a = mysqli_fetch_assoc($assignments)): ?>
                <tr>
                  <td class="fw-semibold"><?= htmlspecialchars($a['title']); ?></td>
                  <td><?= htmlspecialchars($a['unit_name']); ?></td>
                  <td><?= htmlspecialchars($a['due_date']); ?></td>
                </tr>
              <?php endwhile; ?>
              <?php if (mysqli_num_rows($assignments) === 0): ?>
                <tr><td colspan="3" class="text-muted text-center py-4">No assignments yet</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<?php include '../../includes/footer.php'; ?>

<!-- Add Assignment Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add Assignment</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="assignment-create.php" method="POST">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Unit</label>
              <select class="form-select" name="unit_id" required>
                <option value="">Select unit</option>
                <?php
                  mysqli_data_seek($units, 0);
                  while($u = mysqli_fetch_assoc($units)):
                ?>
                  <option value="<?= (int)$u['unit_id']; ?>"><?= htmlspecialchars($u['unit_name']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Due Date</label>
              <input type="date" class="form-control" name="due_date" required>
            </div>

            <div class="col-12">
              <label class="form-label">Title</label>
              <input type="text" class="form-control" name="title" required>
            </div>

            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
          <button class="btn btn-primary" type="submit">Create</button>
        </div>
      </form>

    </div>
  </div>
</div>

</body>
