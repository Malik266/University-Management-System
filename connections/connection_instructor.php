<?php
// ============================================================
// CONNECTION INSTRUCTOR - Database connection for Instructor User
// ============================================================

// Database configuration for MySQL
$servername = "localhost";
$username = "InstructorUser";  // Instructor database user
$password = "StrongPassword123!";  // Instructor user password
$dbname = "UniversityDB";

// Create MySQLi connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");

?>