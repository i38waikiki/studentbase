<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Course</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($user = mysqli_fetch_assoc($users)): ?>
                <tr style="cursor:pointer;" onclick="window.location='user-profile.php?id=<?= $user['user_id']; ?>'">
                    <td><?= htmlspecialchars($user['name']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                    <td>
                        <?php
                        switch ($user['role_name']) {
                            case 'Admin':    $badgeClass = 'bg-success'; break;
                            case 'Lecturer': $badgeClass = 'bg-primary'; break;
                            case 'Student':  $badgeClass = 'bg-info'; break;
                            default:         $badgeClass = 'bg-secondary';
                        }
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($user['role_name']); ?>
                        </span>
                    </td>

                    <td><?= $user['course_code'] ?? '-'; ?></td>

                    <td class="text-end">
                        <button class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteUserModal"
                                data-userid="<?= $user['user_id']; ?>"
                                onclick="event.stopPropagation();">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
