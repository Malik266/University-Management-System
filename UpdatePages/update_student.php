<?php
require '../connections/connection_Admin.php';

$student_id = $_POST['student_id'];
$name = $_POST['name'];
$email = $_POST['email'];

// Start transaction
mysqli_begin_transaction($conn);

try {
    // 1. Update student basic info
    $updateStudent = "UPDATE Students SET name = ?, email = ? WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $updateStudent);
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $student_id);
    mysqli_stmt_execute($stmt);

    // 2. Update course schedules for enrolled courses
    if (!empty($_POST['courses'])) {
        $updateSchedule = "
            UPDATE CourseSchedules cs
            JOIN Enrollments e ON cs.course_id = e.course_id
            SET cs.day_of_week = ?, cs.start_time = ?, cs.end_time = ?
            WHERE e.enrollment_id = ?
        ";
        $stmt = mysqli_prepare($conn, $updateSchedule);
        
        foreach ($_POST['courses'] as $course) {
            $day = $course['day'];
            $start = $course['start'];
            $end = $course['end'];
            $enrollment_id = $course['enrollment_id'];
            
            mysqli_stmt_bind_param($stmt, "sssi", $day, $start, $end, $enrollment_id);
            mysqli_stmt_execute($stmt);
        }
    }

    // Commit transaction
    mysqli_commit($conn);
    
    header("Location: ../HomePages/admin.php");
    exit();

} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    echo "Error updating student: " . $e->getMessage();
}
?>