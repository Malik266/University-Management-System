<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Admin credentials
    if ($email == 'admin@uni.edu' && $password == 'admin123') {
        $_SESSION['admin_user'] = 'Administrator';
        $_SESSION['user_id'] = 1;
        $_SESSION['name'] = 'Administrator';
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';
        
        echo "success";
        exit();
    } else {
        echo "Invalid admin credentials.";
    }
}
?>