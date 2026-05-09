<?php
// ADD ENROLLMENT - Enrolls a student in a course
include("../connections/connection_Admin.php");

// Debug: log received data
file_put_contents('debug.txt', print_r($_POST, true));

// Get form data
$studentId = $_POST['student_id'];
$courseId = $_POST['course_id'];

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert enrollment record
    $insertQuery = "INSERT INTO Enrollments (student_id, course_id, enrollment_date, status) VALUES (?, ?, CURDATE(), 'active')";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "ii", $studentId, $courseId);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        throw new Exception('Enrollment insert failed: ' . mysqli_error($conn));
    }

    // Get the new enrollment ID
    $enrollmentId = mysqli_insert_id($conn);

    // Commit transaction
    mysqli_commit($conn);
    
    echo "<script>alert('Course added successfully'); window.history.back();</script>";
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    file_put_contents('debug.txt', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo "<script>alert('Failed to add course: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
}
?>