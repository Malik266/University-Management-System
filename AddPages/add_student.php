<?php
// ADD STUDENT - Inserts new student into database
include ('../connections/connection_Admin.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Check if email already exists
    $checkEmailQuery = "SELECT COUNT(*) as count FROM Students WHERE email = ?";
    $stmt = mysqli_prepare($conn, $checkEmailQuery);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] > 0) {
        echo "This Email already exists. Try another.";
    } else {
        // Insert new student (code_seq will be auto incremented by trigger)
        $insertQuery = "INSERT INTO Students (name, email) VALUES (?, ?)";
        $stmtInsert = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmtInsert, "ss", $name, $email);
        
        if (mysqli_stmt_execute($stmtInsert)) {
            header("Location: ../HomePages/admin.php");
            exit();
        } else {
            echo "Error adding student: " . mysqli_error($conn);
        }
    }
}
?>