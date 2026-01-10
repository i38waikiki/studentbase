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

