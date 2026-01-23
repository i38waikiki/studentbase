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

/* ===== Student ===== */

function studentGetMyUnits($conn, $student_id) {

    $sql = "
        SELECT DISTINCT
            u.unit_id,
            u.unit_name,
            ub.file_path
        FROM users s
        JOIN classes cl ON s.class_id = cl.class_id
        JOIN course_unit cu ON cl.course_id = cu.course_id
        JOIN units u ON cu.unit_id = u.unit_id
        LEFT JOIN unit_briefs ub ON ub.unit_id = u.unit_id
        WHERE s.user_id = ?
        ORDER BY u.unit_name
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}


function studentGetAssignments(mysqli $conn, int $student_id) {
    $sql = "
      SELECT a.*, u.unit_name
      FROM users s
      JOIN course_unit cu ON cu.course_id = s.course_id
      JOIN units u ON u.unit_id = cu.unit_id
      JOIN assignments a ON a.unit_id = u.unit_id
      WHERE s.user_id = ?
      ORDER BY a.due_date ASC
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function studentHasSubmitted(mysqli $conn, int $assignment_id, int $student_id) {
    $sql = "SELECT submission_id FROM submissions WHERE assignment_id=? AND student_id=? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $assignment_id, $student_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res); // returns row or null
}

function studentGetGrades(mysqli $conn, int $student_id) {
    $sql = "
      SELECT g.grade, g.feedback, g.lecturer_id,
             a.title, a.due_date,
             u.unit_name,
             sub.submission_date
      FROM submissions sub
      JOIN assignments a ON sub.assignment_id = a.assignment_id
      JOIN units u ON a.unit_id = u.unit_id
      LEFT JOIN grades g ON g.submission_id = sub.submission_id
      WHERE sub.student_id = ?
      ORDER BY sub.submission_date DESC
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function studentGetTimetable(mysqli $conn, int $student_id) {
    $sql = "
      SELECT t.*, u.unit_name, lec.name AS lecturer_name, c.year, c.group_name
      FROM users s
      JOIN classes c ON s.class_id = c.class_id
      JOIN timetable t ON t.class_id = c.class_id
      JOIN units u ON t.unit_id = u.unit_id
      JOIN users lec ON t.lecturer_id = lec.user_id
      WHERE s.user_id = ?
      ORDER BY FIELD(t.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
               t.start_time
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

/* ===== Lecturer ===== */

function lecturerGetMyUnits(mysqli $conn, int $lecturer_id) {
    $sql = "
      SELECT DISTINCT u.unit_id, u.unit_name
      FROM unit_lecturers ul
      JOIN units u ON ul.unit_id = u.unit_id
      WHERE ul.lecturer_id = ?
      ORDER BY u.unit_name
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $lecturer_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}


function lecturerGetAssignments(mysqli $conn, int $lecturer_id) {
    // Assignments for lecturer’s units
    $sql = "
      SELECT a.*, u.unit_name
      FROM unit_lecturers ul
      JOIN assignments a ON a.unit_id = ul.unit_id
      JOIN units u ON u.unit_id = a.unit_id
      WHERE ul.lecturer_id = ?
      ORDER BY a.due_date DESC
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $lecturer_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function lecturerGetSubmissions($conn, $lecturer_id) {

    $sql = "
        SELECT
            s.submission_id,
            s.submission_date,
            u.unit_name,
            a.title,
            stu.name AS student_name,
            stu.email AS student_email,
            g.grade,
            g.feedback
        FROM submissions s
        JOIN assignments a ON s.assignment_id = a.assignment_id
        JOIN units u ON a.unit_id = u.unit_id
        JOIN unit_lecturers ul ON ul.unit_id = u.unit_id
        JOIN users stu ON s.student_id = stu.user_id
        LEFT JOIN grades g ON g.submission_id = s.submission_id
        WHERE ul.lecturer_id = ?
        ORDER BY s.submission_date DESC
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $lecturer_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}


function lecturerGetTimetable(mysqli $conn, int $lecturer_id) {
    $sql = "
      SELECT t.*, u.unit_name, c.year, c.group_name
      FROM timetable t
      JOIN units u ON t.unit_id = u.unit_id
      JOIN classes c ON t.class_id = c.class_id
      WHERE t.lecturer_id = ?
      ORDER BY FIELD(t.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
               t.start_time
    ";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $lecturer_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}


