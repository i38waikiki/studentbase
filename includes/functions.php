<?php


// Get user by email
function getUserByEmail($conn, $email) {
    $email = mysqli_real_escape_string($conn, $email); // prevent basic SQL injection
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        return mysqli_fetch_assoc($result);
    }

    return null;
}

//This function fetches all users with their roles and classes.
function getAllUsers($conn) {
    $sql = "
        SELECT users.user_id, users.name, users.email,
               roles.role_name,
               classes.class_name,
               courses.course_code
        FROM users
        JOIN roles ON users.role_id = roles.role_id
        LEFT JOIN classes ON users.class_id = classes.class_id
        LEFT JOIN courses ON users.course_id = courses.course_id
        WHERE users.deleted = 0
        ORDER BY users.user_id DESC
    ";
    return mysqli_query($conn, $sql);
}


// Get users by role (used for Users tabs)
function getUsersByRole($conn, $role_id) {
    $sql = "
        SELECT users.user_id, users.name, users.email,
               roles.role_name,
               classes.class_name,
               courses.course_code
        FROM users
        JOIN roles ON users.role_id = roles.role_id
        LEFT JOIN classes ON users.class_id = classes.class_id
        LEFT JOIN courses ON users.course_id = courses.course_id
        WHERE users.deleted = 0 AND users.role_id = ?
        ORDER BY users.user_id DESC
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $role_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}


// Get total number of users
function getTotalUsers($conn) {
    $sql = "SELECT COUNT(*) as total FROM users WHERE deleted = 0";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)['total'];
}



// Get total users by role
function getTotalByRole($conn, $role_id) {
    $sql = "SELECT COUNT(*) as total FROM users WHERE role_id = ? AND deleted = 0";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $role_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    return $row['total'];
}


// Total courses
function getTotalCourses($conn) {
    $sql = "SELECT COUNT(*) as total FROM courses";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)['total'];
}

// Total units
function getTotalUnits($conn) {
    $sql = "SELECT COUNT(*) as total FROM units";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)['total'];
}



