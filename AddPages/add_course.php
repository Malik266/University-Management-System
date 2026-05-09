<?php
// ADD COURSE - Inserts new course and its schedules into database
include("../connections/connection_Admin.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $credit = $_POST['credits'];

    // Insert new course
    $query = "INSERT INTO Courses (name, credits) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $name, $credit);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Get the inserted course ID
        $course_id = mysqli_insert_id($conn);

        // Insert schedules if provided
        if (isset($_POST['days'], $_POST['starts'], $_POST['ends'])) {
            $days = $_POST['days'];
            $starts = $_POST['starts'];
            $ends = $_POST['ends'];

            $scheduleQuery = "INSERT INTO CourseSchedules (course_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
            $scheduleStmt = mysqli_prepare($conn, $scheduleQuery);

            for ($i = 0; $i < count($days); $i++) {
                $day = $days[$i];
                $start = $starts[$i];
                $end = $ends[$i];

                mysqli_stmt_bind_param($scheduleStmt, "isss", $course_id, $day, $start, $end);
                mysqli_stmt_execute($scheduleStmt);

                if (mysqli_stmt_affected_rows($scheduleStmt) == 0) {
                    echo "Error adding schedule for day: $day";
                }
            }
        }

        // Redirect back to admin page
        header("Location: ../HomePages/admin.php");
        exit();
    } else {
        echo "Error adding course: " . mysqli_error($conn);
    }
}
?>