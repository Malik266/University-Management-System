<?php
// ============================================================
// CONNECTION STUDENT - Dynamic connection for each student
// ============================================================

session_start();

// Check if student is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['db_username'])) {
    die("Please login first");
}

// Get student's personal MySQL credentials from session
$username = $_SESSION['db_username'];
$password = $_SESSION['db_password'];

$servername = "localhost";
$dbname = "UniversityDB";

// Create MySQLi connection using student's own MySQL user
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8mb4");
?>