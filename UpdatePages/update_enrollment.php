<?php
session_start();
require '../connections/connection_Admin.php';

if (!isset($_SESSION['instructor_id']) && !isset($_SESSION['role'])) {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = $_POST['enrollment_id'];
    $grade = $_POST['grade'];
    $status = $_POST['status'];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Update enrollment
        $sql = "UPDATE Enrollments SET grade = ?, status = ? WHERE enrollment_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $grade, $status, $enrollment_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Commit transaction
            mysqli_commit($conn);
            
            // Redirect based on role
            if (isset($_SESSION['instructor_id'])) {
                header("Location: ../HomePages/instructor_home.php");
            } else {
                header("Location: ../HomePages/admin.php");
            }
            exit();
        } else {
            throw new Exception("Update failed: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        die("Error: " . $e->getMessage());
    }
}
?>