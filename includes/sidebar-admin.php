<?php
$current_page = basename($_SERVER['PHP_SELF']);


?>

<?php

ob_start();
?>
    <ul class="nav flex-column p-2">

        <li class="nav-item mb-1">
            <a class="nav-link <?php if($current_page == 'dashboard.php') echo 'active'; ?>" href="dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link <?php if($current_page == 'users.php') echo 'active'; ?>" href="users.php">
                <i class="bi bi-people me-2"></i> Users
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link <?php if($current_page == 'courses.php') echo 'active'; ?>" href="courses.php">
                <i class="bi bi-book me-2"></i> Courses
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link <?php if($current_page == 'units.php') echo 'active'; ?>" href="units.php">
                <i class="bi bi-layers me-2"></i> Units
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link <?php if($current_page == 'class-register.php') echo 'active'; ?>" href="class-register.php">
                <i class="bi bi-diagram-3 me-2"></i> Classes
            </a>
        </li>

        <li class="nav-item mb-1">
            <a class="nav-link <?php if($current_page == 'timetable.php') echo 'active'; ?>" href="timetable.php">
                <i class="bi bi-calendar-week me-2"></i> Timetable
            </a>
        </li>

        <hr class="my-2">

        <li class="nav-item mb-1">
            <a class="nav-link <?php if($current_page == 'profile.php') echo 'active'; ?>"  href="profile.php">
                <i class="bi bi-person me-2"></i> Profile / Settings
            </a>
        </li>


    </ul>
<?php
$menuHtml = ob_get_clean();
?>

<!-- Desktop sidebar  -->

<aside class="admin-sidebar d-none d-lg-block bg-light border-end flex-shrink-0">
    <div class="sidebar-inner">
        <div class="px-3 py-3 border-bottom">
            <div class="fw-bold">Studen Base</div>
            <small class="text-muted">Admin Panel</small>
        </div>

        <?= $menuHtml; ?>
    </div>
</aside>

<!-- Mobile offcanvas  -->

<div class="offcanvas offcanvas-start d-lg-none bg-light text-dark"
     tabindex="-1"
     id="sidebarAdminMobile"
     aria-labelledby="sidebarAdminMobileLabel">
    <div class="offcanvas-header">
        <div>
            <h5 class="offcanvas-title mb-0" id="sidebarAdminMobileLabel">Student Base</h5>
            <small class="text-muted">Admin Panel</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <?= $menuHtml; ?>
    </div>
</div>

<style>


.admin-sidebar{
    width: 280px;
    min-height: calc(100vh - var(--nav-h));
    margin-top: var(--nav-h);
    position: sticky;
    top: var(--nav-h);
}

.admin-sidebar .sidebar-inner{
    height: calc(100vh - var(--nav-h));
    overflow-y: auto;
}

/* Mobile offcanvas */
.offcanvas-start{
    top: var(--nav-h);
    height: calc(100vh - var(--nav-h));
}

/* ---- styling ---- */
.admin-sidebar .nav-link,
.offcanvas .nav-link{
    color: #111827;
    font-weight: 600;
    border-radius: 12px;
    padding: 10px 12px;
    transition: background .15s ease, color .15s ease;
}

.admin-sidebar .nav-link:hover,
.offcanvas .nav-link:hover{
    background: rgba(13,110,253,.10);
    color: #0d6efd;
}

.admin-sidebar .nav-link.active,
.offcanvas .nav-link.active{
    background: #0d6efd;
    color: #fff;
}
</style>
