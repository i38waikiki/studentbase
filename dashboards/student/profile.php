<?php
require_once '../../includes/auth.php';
requireRole(3);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

include '../../includes/header.php';
?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
    <?php include '../../includes/sidebar-student.php'; ?>

    <main class="flex-fill page-wrap">
        <div class="container-fluid p-4">
        <?php include '../../includes/profile-page.php'; ?>
        </div>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
