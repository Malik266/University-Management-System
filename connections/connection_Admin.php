<?php
$servername = "localhost";
$username = "AdminUser"; 
$password = "StrongPassword123!";
$dbname = "UniversityDB"; 

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>