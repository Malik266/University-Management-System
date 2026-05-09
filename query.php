<?php
include '../SQLProject/connection.php';

$query = "SELECT * FROM Students";
$stmt = sqlsrv_query($conn, $query);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Name</th><th>email</th></tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row['student_id'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
