<?php
session_start();
include('../connections/connection_Admin.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Query to find instructor
    $query = "SELECT instructor_id, name, email FROM Instructors WHERE email = ? AND password = MD5(?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($instructor = mysqli_fetch_assoc($result)) {
        // Set session variables
        $_SESSION['instructor_id'] = $instructor['instructor_id'];
        $_SESSION['instructor_name'] = $instructor['name'];
        $_SESSION['email'] = $instructor['email'];
        $_SESSION['role'] = 'instructor';
        
        // Return success for AJAX
        echo "success";
        exit();
    } else {
        echo "Invalid instructor credentials";
    }
}
?>