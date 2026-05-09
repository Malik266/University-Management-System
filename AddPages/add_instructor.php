<?php
// ADD INSTRUCTOR - Inserts new instructor into database
include '../connections/connection_Admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department_id = $_POST['department_name'];

    // Insert new instructor using MySQLi
    $sql = "INSERT INTO Instructors (name, email, department_id) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $department_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../HomePages/admin.php");
        exit;
    } else {
        echo "Error adding instructor: " . mysqli_error($conn);
    }
}
?>