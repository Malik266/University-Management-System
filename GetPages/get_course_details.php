<?php
// ============================================================
// GET COURSE DETAILS - Returns course info with schedules
// Used by edit course form via AJAX
// ============================================================

include('../connections/connection_Admin.php');

// Check if course_id is provided
if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch course details using MySQLi
    $courseQuery = "SELECT course_id, name, credits FROM Courses WHERE course_id = ?";
    $courseStmt = mysqli_prepare($conn, $courseQuery);
    mysqli_stmt_bind_param($courseStmt, "i", $course_id);
    mysqli_stmt_execute($courseStmt);
    $courseResult = mysqli_stmt_get_result($courseStmt);

    if ($course = mysqli_fetch_assoc($courseResult)) {
        
        // Fetch schedules for this course
        $scheduleQuery = "SELECT schedule_id, day_of_week, start_time, end_time FROM CourseSchedules WHERE course_id = ?";
        $scheduleStmt = mysqli_prepare($conn, $scheduleQuery);
        mysqli_stmt_bind_param($scheduleStmt, "i", $course_id);
        mysqli_stmt_execute($scheduleStmt);
        $scheduleResult = mysqli_stmt_get_result($scheduleStmt);

        $schedules = [];
        while ($row = mysqli_fetch_assoc($scheduleResult)) {
            // Format time to H:i (hours:minutes)
            $start_time = date('H:i', strtotime($row['start_time']));
            $end_time = date('H:i', strtotime($row['end_time']));
            
            $schedules[] = [
                'day_of_week' => $row['day_of_week'],
                'start_time' => $start_time,
                'end_time' => $end_time,
            ];
        }

        $course['schedules'] = $schedules;
        
        // Return JSON response
        echo json_encode($course);
    } else {
        echo json_encode(["error" => "Course not found."]);
    }
} else {
    echo json_encode(["error" => "Course ID not provided."]);
}
?>