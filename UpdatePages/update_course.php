<?php
require '../connections/connection_Admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $name = $_POST['name'];
    $instructor_id = $_POST['instructor_id'];
    $days = $_POST['days'] ?? [];
    $starts = $_POST['starts'] ?? [];
    $ends = $_POST['ends'] ?? [];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // 1. Update course name
        $updateCourseQuery = "UPDATE Courses SET name = ? WHERE course_id = ?";
        $stmt = mysqli_prepare($conn, $updateCourseQuery);
        mysqli_stmt_bind_param($stmt, "si", $name, $course_id);
        mysqli_stmt_execute($stmt);

        // 2. Delete old instructor link
        $deleteInstructorLink = "DELETE FROM InstructorCourses WHERE course_id = ?";
        $stmt = mysqli_prepare($conn, $deleteInstructorLink);
        mysqli_stmt_bind_param($stmt, "i", $course_id);
        mysqli_stmt_execute($stmt);

        // 3. Insert new instructor link (if instructor selected)
        if ($instructor_id > 0) {
            $insertInstructorLink = "INSERT INTO InstructorCourses (instructor_id, course_id) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insertInstructorLink);
            mysqli_stmt_bind_param($stmt, "ii", $instructor_id, $course_id);
            mysqli_stmt_execute($stmt);
        }

        // 4. Delete old schedules
        $deleteScheduleQuery = "DELETE FROM CourseSchedules WHERE course_id = ?";
        $stmt = mysqli_prepare($conn, $deleteScheduleQuery);
        mysqli_stmt_bind_param($stmt, "i", $course_id);
        mysqli_stmt_execute($stmt);

        // 5. Insert new schedules
        $insertScheduleQuery = "INSERT INTO CourseSchedules (course_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertScheduleQuery);

        for ($i = 0; $i < count($days); $i++) {
            $day = $days[$i];
            $start = $starts[$i];
            $end = $ends[$i];
            mysqli_stmt_bind_param($stmt, "isss", $course_id, $day, $start, $end);
            mysqli_stmt_execute($stmt);
        }

        // Commit transaction
        mysqli_commit($conn);

        // Redirect back to admin page
        header("Location: ../HomePages/admin.php");
        exit();

    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        echo "Error updating course: " . $e->getMessage();
    }
}
?>