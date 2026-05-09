<?php
include '../connections/connection_Admin.php';

$instructorId = $_POST['instructor_id'];
$courseId = $_POST['course_id'];

$deleteQuery = "DELETE FROM InstructorCourses WHERE instructor_id = ? AND course_id = ?";
$params = array($instructorId, $courseId);
$result = sqlsrv_query($conn, $deleteQuery, $params);

if ($result) {
    echo "success";
} else {
    http_response_code(500);
    echo "error";
}
?>
