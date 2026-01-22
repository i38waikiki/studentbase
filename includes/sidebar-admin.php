<?php
// Get current page filename to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="offcanvas offcanvas-start offcanvas-lg bg-light text-dark" tabindex="-1" id="sidebarAdmin">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Admin Panel</h5>
        <!-- Close button -->
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="nav flex-column">

            <!-- Dashboard -->
            <li class="nav-item mb-2">
                <a class="nav-link <?php if($current_page == 'dashboard.php') echo 'active'; ?>" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <!-- Users -->
            <li class="nav-item mb-2">
                <a class="nav-link <?php if($current_page == 'users.php') echo 'active'; ?>" href="users.php">
                    <i class="bi bi-people me-2"></i> Users
                </a>
            </li>

            <!-- Courses -->
            <li class="nav-item mb-2">
                <a class="nav-link <?php if($current_page == 'courses.php') echo 'active'; ?>" href="courses.php">
                    <i class="bi bi-book me-2"></i> Courses
                </a>
            </li>

            <!-- Units -->
            <li class="nav-item mb-2">
                <a class="nav-link <?php if($current_page == 'units.php') echo 'active'; ?>" href="units.php">
                    <i class="bi bi-layers me-2"></i> Units
                </a>
            </li>

            <!-- Class Register -->
            <li class="nav-item mb-2">
                <a class="nav-link <?php if($current_page == 'class-register.php') echo 'active'; ?>" href="class-register.php">
                    <i class="bi bi-diagram-3 me-2"></i> Classes
                </a>
            </li>

            <!-- Timetable -->
            <li class="nav-item mb-2">
                <a class="nav-link <?php if($current_page == 'timetable.php') echo 'active'; ?>" href="timetable.php">
                    <i class="bi bi-calendar-week me-2"></i> Timetable
                </a>
            </li>



            <!-- Announcements -->
            <li class="nav-item mb-2">
                <a class="nav-link <?php if($current_page == 'announcements.php') echo 'active'; ?>" href="announcements.php">
                    <i class="bi bi-bell me-2"></i> Announcements
                </a>
            </li>

            <!-- Profile / Settings -->
            <li class="nav-item mt-4">
                <a class="nav-link <?php if($current_page == 'profile.php') echo 'active'; ?>" href="profile.php">
                    <i class="bi bi-person me-2"></i> Profile / Settings
                </a>
            </li>

        </ul>
    </div>
</div>

<!-- Sidebar CSS -->
<style>
    /* Sidebar link styles */
    .offcanvas-start .nav-link {
        color: #393939ff;                   /* Dark gray text */
        font-weight: 500;
        font-family: "Segoe UI", sans-serif;
        transition: background 0.15s ease;
    }

    /* Hover and active highlight */
    .offcanvas-start .nav-link:hover,
    .offcanvas-start .nav-link.active {
        background-color: #0d6efd;     /* Bootstrap primary blue */
        color: white;                  /* White text */
        border-radius: 0.25rem;        /* Rounded effect */
    }
</style>
