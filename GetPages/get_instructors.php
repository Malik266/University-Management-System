<?php
// GET INSTRUCTORS - Returns list of all instructors as JSON for dropdown
include('../connections/connection_Admin.php');

// Query to get all instructors
$query = "SELECT instructor_id, name FROM Instructors ORDER BY name";
$result = mysqli_query($conn, $query);

// Build array of instructors
$instructors = [];
while ($row = mysqli_fetch_assoc($result)) {
    $instructors[] = [
        'instructor_id' => $row['instructor_id'],
        'name' => $row['name']
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($instructors);
?>