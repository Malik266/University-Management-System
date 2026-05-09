<?php
require '../connections/connection_Admin.php';

$instructorId = $_POST['instructor_id'];
$name = $_POST['name'];
$email = $_POST['email'];

// Start transaction
mysqli_begin_transaction($conn);

try {
    // 1. Update instructor basic info
    $updateInstructorQuery = "UPDATE Instructors SET name = ?, email = ? WHERE instructor_id = ?";
    $stmt = mysqli_prepare($conn, $updateInstructorQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $instructorId);
    mysqli_stmt_execute($stmt);

    // 2. Update existing courses schedules
    if (!empty($_POST['courses'])) {
        $updateScheduleQuery = "UPDATE InstructorCourses SET schedule_id = ? WHERE instructor_id = ? AND course_id = ?";
        $stmt = mysqli_prepare($conn, $updateScheduleQuery);
        
        foreach ($_POST['courses'] as $courseData) {
            $courseId = $courseData['course_id'];
            $newScheduleId = $courseData['schedule_id'];
            
            mysqli_stmt_bind_param($stmt, "iii", $newScheduleId, $instructorId, $courseId);
            mysqli_stmt_execute($stmt);
        }
    }

    // 3. Add new course to instructor
    if (!empty($_POST['new_course_id']) && !empty($_POST['new_schedule_id'])) {
        $newCourseId = $_POST['new_course_id'];
        $newScheduleId = $_POST['new_schedule_id'];

        // Check if already exists
        $checkQuery = "SELECT COUNT(*) as count FROM InstructorCourses WHERE instructor_id = ? AND course_id = ?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($stmt, "ii", $instructorId, $newCourseId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_fetch_assoc($result)['count'];

        if ($exists == 0) {
            $insertCourseQuery = "INSERT INTO InstructorCourses (instructor_id, course_id, schedule_id) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertCourseQuery);
            mysqli_stmt_bind_param($stmt, "iii", $instructorId, $newCourseId, $newScheduleId);
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
    echo "Error updating instructor: " . $e->getMessage();
}
?>