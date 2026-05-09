<?php
include("../connections/connection_Admin.php");

if (isset($_GET['id'])) {
    $course_id = intval($_GET['id']);

    sqlsrv_begin_transaction($conn);

    try {
        $deleteEnrollments = "DELETE FROM Enrollments WHERE course_id = ?";
        $stmt1 = sqlsrv_query($conn, $deleteEnrollments, array($course_id));
        if ($stmt1 === false) throw new Exception("Error deleting enrollments: " . print_r(sqlsrv_errors(), true));

        $save1 = sqlsrv_query($conn, "SAVE TRANSACTION BeforeDeletingSchedules");
        if ($save1 === false) throw new Exception("Error saving transaction point: " . print_r(sqlsrv_errors(), true));

        $deleteSchedules = "DELETE FROM CourseSchedules WHERE course_id = ?";
        $stmt2 = sqlsrv_query($conn, $deleteSchedules, array($course_id));
        if ($stmt2 === false) throw new Exception("Error deleting schedule: " . print_r(sqlsrv_errors(), true));

        $deleteInstructorCourses = "DELETE FROM InstructorCourses WHERE course_id = ?";
        $stmtX = sqlsrv_query($conn, $deleteInstructorCourses, array($course_id));
        if ($stmtX === false) throw new Exception("Error deleting instructor courses: " . print_r(sqlsrv_errors(), true));

        $save2 = sqlsrv_query($conn, "SAVE TRANSACTION BeforeDeletingCourse");
        if ($save2 === false) throw new Exception("Error saving transaction point: " . print_r(sqlsrv_errors(), true));

        $deleteCourse = "DELETE FROM Courses WHERE course_id = ?";
        $stmt3 = sqlsrv_query($conn, $deleteCourse, array($course_id));
        if ($stmt3 === false) throw new Exception("Error deleting course: " . print_r(sqlsrv_errors(), true));

        sqlsrv_commit($conn);
        header("Location: ../HomePages/admin.php");
        exit();

    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Error during course deletion transaction: " . $e->getMessage();
    }
} else {
    echo "No course ID provided.";
}
?>
