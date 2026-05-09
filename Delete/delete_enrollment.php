<?php
include('../connections/connection_Admin.php');

if (isset($_POST['enrollment_id'])) {
    $enrollment_id = $_POST['enrollment_id'];

    $deleteQuery = "DELETE FROM Enrollments WHERE enrollment_id = ?";
    $stmt = sqlsrv_query($conn, $deleteQuery, array($enrollment_id));

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
}

header("Location: ../HomePages/admin.php");
exit;
?>
