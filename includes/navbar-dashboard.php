<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
    <button class="btn btn-outline-primary d-lg-none"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#sidebarAdmin"
        aria-controls="sidebarAdmin">
    <i class="bi bi-list"></i>
</button>

        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../../assets/logoW.png" alt="Logo" width="35" class="me-2">
            <strong>Student Base</strong>
        </a>

        <!-- Right-side menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="dashboardNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <!-- Notifications -->
                <li class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell" style="font-size: 1.2rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                        <li><a class="dropdown-item" href="#">New announcement</a></li>
                    </ul>
                </li>

               
                <!-- User dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        John Doe
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php">Profile Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../../public/home.php">Logout</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>
