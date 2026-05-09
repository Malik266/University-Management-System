<?php
// GET REPORT - Returns report data based on selected type
include("../connections/connection_Admin.php");

if (!isset($_GET['type'])) {
    echo "Invalid report type.";
    exit;
}

$type = $_GET['type'];
$query = "";
$title = "";

switch ($type) {
    case "students_multiple_courses":
        $title = "Students Enrolled in More Than One Course";
        $query = "
            SELECT s.name AS StudentName, 
                   COUNT(e.course_id) AS NumberOfCourses
            FROM Students s
            JOIN Enrollments e ON s.student_id = e.student_id
            GROUP BY s.name
            HAVING COUNT(e.course_id) > 1";
        break;

    case "course_avg_grades":
        $title = "Average Grade per Course";
        $query = "
            SELECT c.name AS CourseName, 
                   AVG(CASE 
                       WHEN e.grade = 'A' THEN 4
                       WHEN e.grade = 'B' THEN 3
                       WHEN e.grade = 'C' THEN 2
                       WHEN e.grade = 'D' THEN 1
                       ELSE 0
                   END) AS AverageGrade
            FROM Courses c
            JOIN Enrollments e ON c.course_id = e.course_id
            GROUP BY c.name";
        break;

    case "students_without_courses":
        $title = "Students Not Enrolled in Any Course";
        $query = "
            SELECT s.name AS StudentName
            FROM Students s
            LEFT JOIN Enrollments e ON s.student_id = e.student_id
            WHERE e.enrollment_id IS NULL";
        break;

    case "students_per_department":
        $title = "Number of Students per Department";
        $query = "
            SELECT d.name AS DepartmentName, 
                   COUNT(s.student_id) AS NumberOfStudents
            FROM Departments d
            LEFT JOIN Students s ON d.department_id = s.major_id
            GROUP BY d.name";
        break;

    case "cs_department_courses":
        $title = "Courses Taught by Computer Science Instructors";
        $query = "
            SELECT DISTINCT c.name AS CourseName
            FROM Courses c
            JOIN InstructorCourses ic ON c.course_id = ic.course_id
            JOIN Instructors i ON ic.instructor_id = i.instructor_id
            JOIN Departments d ON i.department_id = d.department_id
            WHERE d.name = 'Computer Science'";
        break;

    case "top_students_per_course":
        $title = "Top Students per Course";
        $query = "
            SELECT c.name AS CourseName,
                   s.name AS StudentName,
                   e.grade
            FROM Enrollments e
            JOIN Courses c ON e.course_id = c.course_id
            JOIN Students s ON e.student_id = s.student_id
            WHERE e.grade = 'A'";
        break;

    case "most_active_instructor":
        $title = "Most Active Instructor";
        $query = "
            SELECT i.name AS InstructorName, 
                   COUNT(ic.course_id) AS NumberOfCourses
            FROM Instructors i
            JOIN InstructorCourses ic ON i.instructor_id = ic.instructor_id
            GROUP BY i.name
            ORDER BY NumberOfCourses DESC";
        break;

    case "student_name_change_log":
        $title = "Student Name Change Log";
        $query = "
            SELECT StudentID, OldName, NewName, ChangeDate
            FROM StudentNameChangeLog
            ORDER BY ChangeDate DESC";
        break;

    default:
        echo "Unknown report type.";
        exit;
}

// Execute query using MySQLi
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Display results
echo "<h3>$title</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";

$firstRow = true;
while ($row = mysqli_fetch_assoc($result)) {
    // Print headers
    if ($firstRow) {
        echo "<tr style='background-color: #e3f2fd;'>";
        foreach ($row as $colName => $value) {
            echo "<th style='padding: 10px;'>" . htmlspecialchars($colName) . "</th>";
        }
        echo "</tr>";
        $firstRow = false;
    }
    
    // Print row data
    echo "<tr>";
    foreach ($row as $colValue) {
        $displayValue = is_null($colValue) ? 'N/A' : htmlspecialchars($colValue);
        echo "<td style='padding: 8px;'>" . $displayValue . "</td>";
    }
    echo "</tr>";
}

echo "</table>";
?>