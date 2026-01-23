<?php
require_once '../../includes/dbh.php';

/*
Create Unit (Improved)
- Admin can select an existing unit OR type a new one
- Prevents duplicate units in the units table
- Links unit to course via course_unit table
- Assigns lecturers via unit_lecturers table
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id = (int)($_POST['course_id'] ?? 0);

    // NEW FIELDS FROM MODAL
    $existing_unit_id = (int)($_POST['existing_unit_id'] ?? 0);
    $new_unit_name = trim($_POST['new_unit_name'] ?? '');

    $lecturers = $_POST['lecturers'] ?? [];

    // Validation
    if ($course_id <= 0) {
        header("Location: units.php?error=empty");
        exit;
    }

    // Must pick existing OR type new
    if ($existing_unit_id <= 0 && $new_unit_name === '') {
        header("Location: units.php?error=unit_required");
        exit;
    }

   
    $unit_id = 0;

    // If admin selected existing unit
    if ($existing_unit_id > 0) {
        $unit_id = $existing_unit_id;

    } else {
        // Admin typed a new unit name:
        // Check if this unit already exists (prevents duplicates)
        $stmt = mysqli_prepare($conn, "SELECT unit_id FROM units WHERE unit_name = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $new_unit_name);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if ($row) {
            $unit_id = (int)$row['unit_id'];
        } else {
            // Insert new unit
            $stmt = mysqli_prepare($conn, "INSERT INTO units (unit_name) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $new_unit_name);
            mysqli_stmt_execute($stmt);
            $unit_id = (int)mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        }
    }

    if ($unit_id <= 0) {
        header("Location: units-course.php?course_id=".$course_id."&error=unit_failed");
        exit;
    }

    /* Link unit to course */
    $stmt = mysqli_prepare($conn, "SELECT 1 FROM course_unit WHERE course_id = ? AND unit_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ii", $course_id, $unit_id);
    mysqli_stmt_execute($stmt);
    $exists = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($exists) === 0) {
        $stmt_course = mysqli_prepare($conn, "INSERT INTO course_unit (course_id, unit_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt_course, "ii", $course_id, $unit_id);
        mysqli_stmt_execute($stmt_course);
        mysqli_stmt_close($stmt_course);
    }

    /*Assign lecturers*/
    if (!empty($lecturers) && is_array($lecturers)) {
        foreach ($lecturers as $lecturer_id) {
            $lecturer_id = (int)$lecturer_id;
            if ($lecturer_id <= 0) continue;

            // Prevent duplicates
            $stmt = mysqli_prepare($conn, "SELECT 1 FROM unit_lecturers WHERE unit_id = ? AND lecturer_id = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "ii", $unit_id, $lecturer_id);
            mysqli_stmt_execute($stmt);
            $r = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);

            if (mysqli_num_rows($r) === 0) {
                $stmt_lect = mysqli_prepare($conn, "INSERT INTO unit_lecturers (unit_id, lecturer_id) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt_lect, "ii", $unit_id, $lecturer_id);
                mysqli_stmt_execute($stmt_lect);
                mysqli_stmt_close($stmt_lect);
            }
        }
    }

    header("Location: units-course.php?course_id=".$course_id."&success=created");
    exit;
}
