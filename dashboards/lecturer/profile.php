<?php
require_once '../../includes/auth.php';
requireRole(2);

require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';

include '../../includes/header.php';
?>
<body class="d-flex flex-column min-vh-100">
<?php include '../../includes/navbar-dashboard.php'; ?>

<div class="d-flex flex-grow-1">
    <?php include '../../includes/sidebar-lecturer.php'; ?>

    <main class="flex-fill page-wrap">
        <?php include '../../includes/profile-page.php'; ?>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>
</body>
