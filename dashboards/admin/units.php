<?php
session_start();
require_once '../../includes/dbh.php';
require_once '../../includes/functions.php';
?>

<?php include '../../includes/header.php'; ?>

<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <?php include '../../includes/navbar-dashboard.php'; ?>

    <!-- Sidebar -->
    <?php include '../../includes/sidebar-admin.php'; ?>


    <!-- Footer -->
    <?php include '../../includes/footer.php'; ?>