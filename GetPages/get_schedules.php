<?php
// GET SCHEDULES - Returns schedule options for a selected course (used by AJAX)
include("../connections/connection_Admin.php");

if (isset($_POST['course_id'])) {
    $courseId = $_POST['course_id'];
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'student';

    // Query to get schedules for the selected course
    $schedulesQuery = "SELECT schedule_id, day_of_week, start_time, end_time FROM CourseSchedules WHERE course_id = ?";
    $stmt = mysqli_prepare($conn, $schedulesQuery);
    mysqli_stmt_bind_param($stmt, "i", $courseId);
    mysqli_stmt_execute($stmt);
    $schedulesResult = mysqli_stmt_get_result($stmt);

    // Set select box name based on mode
    $selectName = ($mode === 'instructor') ? 'new_schedule_id' : 'schedule_id';

    echo "<label>Select Schedule:</label>";
    echo "<select name='$selectName' required style='width: 100%; margin-top: 5px;'>";
    echo "<option value=''>-- Select Schedule --</option>";
    
    while ($schedule = mysqli_fetch_assoc($schedulesResult)) {
        $scheduleId = $schedule['schedule_id'];
        $day = $schedule['day_of_week'];
        // Format time from string to H:i
        $start = date('H:i', strtotime($schedule['start_time']));
        $end = date('H:i', strtotime($schedule['end_time']));
        echo "<option value='$scheduleId'>$day ($start - $end)</option>";
    }
    
    echo "</select>";
}
?>