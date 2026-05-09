<?php
session_start();
include('../connections/connection_Admin.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $query = "SELECT student_id, name, email FROM Students WHERE email = ? AND password = MD5(?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($student = mysqli_fetch_assoc($result)) {
        $_SESSION['user_id'] = $student['student_id'];
        $_SESSION['name'] = $student['name'];
        $_SESSION['email'] = $student['email'];
        $_SESSION['role'] = 'student';
        
        echo "success";
        exit();
    } else {
        echo "Invalid student credentials";
    }
}
?>