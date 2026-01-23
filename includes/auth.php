<?php
/*
  AUTHENTICATION

  Notes:
  - Blocks any dashboard page unless user is logged in
  - Can enforce role restrictions per dashboard
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'], $_SESSION['role_id'])) {
    header("Location: /studentbase/public/login.php?error=loginrequired");
    exit();
}

function requireRole(int $role_id): void {
    if ((int)$_SESSION['role_id'] !== $role_id) {
        header("Location: /studentbase/public/login.php?error=unauthorized");
        exit();
    }
}
