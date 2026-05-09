<?php
include("../connections/connection_Admin.php");

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval(trim($_GET['id']));

    try {
        $setIsolation = sqlsrv_query($conn, "SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
        if ($setIsolation === false) throw new Exception(print_r(sqlsrv_errors(), true));

        if (!sqlsrv_begin_transaction($conn)) {
            throw new Exception("Failed to begin transaction");
        }

        $savePointName = "BeforeDelete";
        $savePointQuery = "SAVE TRANSACTION $savePointName";
        $savePointStmt = sqlsrv_query($conn, $savePointQuery);
        if ($savePointStmt === false) {
            throw new Exception("Failed to create savepoint: " . print_r(sqlsrv_errors(), true));
        }

        if ($type === 'student') {
            $deleteEnrollments = "DELETE FROM Enrollments WHERE student_id = ?";
            $stmt1 = sqlsrv_query($conn, $deleteEnrollments, array($id));
            if ($stmt1 === false) throw new Exception(print_r(sqlsrv_errors(), true));

            $sql = "DELETE FROM Students WHERE student_id = ?";
            $stmt2 = sqlsrv_query($conn, $sql, array($id));
            if ($stmt2 === false) throw new Exception(print_r(sqlsrv_errors(), true));

        } elseif ($type === 'instructor') {
            $deleteInstructorCourses = "DELETE FROM InstructorCourses WHERE instructor_id = ?";
            $stmtDelInstructorCourses = sqlsrv_query($conn, $deleteInstructorCourses, array($id));
            if ($stmtDelInstructorCourses === false) throw new Exception(print_r(sqlsrv_errors(), true));

            $deleteInstructor = "DELETE FROM Instructors WHERE instructor_id = ?";
            $stmtDelInstructor = sqlsrv_query($conn, $deleteInstructor, array($id));
            if ($stmtDelInstructor === false) throw new Exception(print_r(sqlsrv_errors(), true));

        } else {
            throw new Exception("Invalid type.");
        }

        if (!sqlsrv_commit($conn)) {
            throw new Exception("Commit failed: " . print_r(sqlsrv_errors(), true));
        }

        header("Location: ../HomePages/admin.php");
        exit();

    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Error: " . $e->getMessage();
    }

} else {
    echo "Missing parameters.";
}
?>
