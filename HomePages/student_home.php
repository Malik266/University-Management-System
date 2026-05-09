<?php
session_start();
include('../connections/connection_Admin.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../LogIN/login.html");
    exit();
}

$student_name = $_SESSION['name'];
$student_id = $_SESSION['user_id'];

// Get enrolled courses count
$countQuery = "SELECT COUNT(*) as total FROM Enrollments WHERE student_id = ?";
$countStmt = mysqli_prepare($conn, $countQuery);
mysqli_stmt_bind_param($countStmt, "i", $student_id);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalCourses = mysqli_fetch_assoc($countResult)['total'];

// Get courses for dashboard
$sql = "SELECT c.name AS course_name, e.grade FROM Enrollments e 
        JOIN Courses c ON e.course_id = c.course_id WHERE e.student_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get detailed courses
$query_courses = "SELECT c.name AS course_name, c.credits, i.name AS instructor_name,
                  cs.day_of_week, DATE_FORMAT(cs.start_time, '%H:%i') AS start_time,
                  DATE_FORMAT(cs.end_time, '%H:%i') AS end_time, e.grade
                  FROM Enrollments e
                  JOIN Courses c ON e.course_id = c.course_id
                  LEFT JOIN InstructorCourses ic ON c.course_id = ic.course_id
                  LEFT JOIN Instructors i ON ic.instructor_id = i.instructor_id
                  LEFT JOIN CourseSchedules cs ON c.course_id = cs.course_id
                  WHERE e.student_id = ? ORDER BY cs.day_of_week, cs.start_time";
$courses_stmt = mysqli_prepare($conn, $query_courses);
mysqli_stmt_bind_param($courses_stmt, "i", $student_id);
mysqli_stmt_execute($courses_stmt);
$courses_result = mysqli_stmt_get_result($courses_stmt);

// Get total credits
$creditsQuery = "SELECT SUM(c.credits) as total_credits FROM Enrollments e 
                 JOIN Courses c ON e.course_id = c.course_id WHERE e.student_id = ?";
$creditsStmt = mysqli_prepare($conn, $creditsQuery);
mysqli_stmt_bind_param($creditsStmt, "i", $student_id);
mysqli_stmt_execute($creditsStmt);
$creditsResult = mysqli_stmt_get_result($creditsStmt);
$totalCredits = mysqli_fetch_assoc($creditsResult)['total_credits'] ?? 0;

// Get profile
$sql_profile = "SELECT s.name AS student_name, s.email, s.code, d.name AS department_name
                FROM Students s LEFT JOIN Departments d ON s.major_id = d.department_id
                WHERE s.student_id = ?";
$profile_stmt = mysqli_prepare($conn, $sql_profile);
mysqli_stmt_bind_param($profile_stmt, "i", $student_id);
mysqli_stmt_execute($profile_stmt);
$profile_result = mysqli_stmt_get_result($profile_stmt);
$row_profile = mysqli_fetch_assoc($profile_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - UniPortal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="../CSS/student-style.css?v=2.0"> -->
    <link rel="stylesheet" href="../CSS/dashboard-style.css?v=2.0">
</head>

<body>

<header class="header">
    <div class="icon-container">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1><i class="fas fa-graduation-cap"></i> UniPortal - Student</h1>
    </div>
    <a href="../LogOut/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</header>

<div class="dashboard-container">
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a class="nav-item active" data-section="dashboard">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-item" data-section="courses">
                <i class="fas fa-book-open"></i> My Courses
            </a>
            <a class="nav-item" data-section="profile">
                <i class="fas fa-user-circle"></i> Profile
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <!-- Dashboard Section -->
        <div class="section active" id="dashboard-section">
            <div class="section-header">
                <h2>Dashboard</h2>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <div class="stat-value"><?php echo $totalCourses; ?></div>
                    <div class="stat-label">Enrolled Courses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?php echo $totalCredits; ?></div>
                    <div class="stat-label">Total Credits</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-chart-simple"></i></div>
                    <div class="stat-value">3.5</div>
                    <div class="stat-label">Current GPA</div>
                </div>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead><tr><th>Course Name</th><th>Grade</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): 
                            $grade = $row['grade'] ?? 'N/A';
                            $gradeClass = ($grade == 'A') ? 'grade-a' : 'grade-na';
                            $status = ($grade == 'N/A' || $grade == null) ? 'In Progress' : 'Completed';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><span class="grade-badge <?php echo $gradeClass; ?>"><?php echo $grade; ?></span></td>
                            <td><?php echo $status; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Courses Section -->
        <div class="section" id="courses-section">
            <div class="section-header">
                <h2>My Courses</h2>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead><tr><th>Course Name</th><th>Credits</th><th>Instructor</th><th>Schedule</th><th>Grade</th></tr></thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($courses_result, 0);
                        while ($row = mysqli_fetch_assoc($courses_result)): 
                            $grade = $row['grade'] ?? 'N/A';
                            $gradeClass = ($grade == 'A') ? 'grade-a' : 'grade-na';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['credits']); ?></td>
                            <td><?php echo htmlspecialchars($row['instructor_name'] ?? 'Not Assigned'); ?></td>
                            <td>
                                <?php if ($row['day_of_week']): ?>
                                <span class="schedule-badge">
                                    <i class="far fa-calendar"></i> 
                                    <?php echo htmlspecialchars($row['day_of_week'] . ' ' . $row['start_time'] . ' - ' . $row['end_time']); ?>
                                </span>
                                <?php else: ?>
                                <span class="schedule-badge">No schedule</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="grade-badge <?php echo $gradeClass; ?>"><?php echo $grade; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="section" id="profile-section">
            <div class="section-header">
                <h2>My Profile</h2>
            </div>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar"><i class="fas fa-user-graduate"></i></div>
                    <h3><?php echo htmlspecialchars($row_profile['student_name'] ?? $student_name); ?></h3>
                    <p><?php echo htmlspecialchars($row_profile['department_name'] ?? 'Student'); ?></p>
                </div>
                <div class="profile-body">
                    <div class="info-row"><span class="info-label"><i class="fas fa-envelope"></i> Email</span><span class="info-value"><?php echo htmlspecialchars($row_profile['email'] ?? 'N/A'); ?></span></div>
                    <div class="info-row"><span class="info-label"><i class="fas fa-id-card"></i> Student ID</span><span class="info-value"><?php echo htmlspecialchars($student_id); ?></span></div>
                    <div class="info-row"><span class="info-label"><i class="fas fa-code"></i> Student Code</span><span class="info-value"><?php echo htmlspecialchars($row_profile['code'] ?? 'N/A'); ?></span></div>
                    <div class="info-row"><span class="info-label"><i class="fas fa-building"></i> Department</span><span class="info-value"><?php echo htmlspecialchars($row_profile['department_name'] ?? 'Not assigned'); ?></span></div>
                </div>
            </div>
        </div>
    </main>
</div>

<footer class="footer">
    &copy; 2025 University Portal. All rights reserved.
</footer>

<script>
    // Navigation
    const navItems = document.querySelectorAll('.nav-item');
    const sections = {
        dashboard: document.getElementById('dashboard-section'),
        courses: document.getElementById('courses-section'),
        profile: document.getElementById('profile-section')
    };
    
    function hideAllSections() {
        Object.values(sections).forEach(s => { if(s) s.classList.remove('active'); });
    }
    
    function showSection(sectionId) {
        hideAllSections();
        if(sections[sectionId]) sections[sectionId].classList.add('active');
    }
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
            showSection(this.dataset.section);
            if(window.innerWidth <= 768) document.getElementById('sidebar').classList.remove('open');
        });
    });
    
    // Sidebar toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    menuToggle?.addEventListener('click', () => sidebar.classList.toggle('open'));
    
    document.addEventListener('click', (event) => {
        if(window.innerWidth <= 768 && sidebar?.classList.contains('open')) {
            if(!sidebar.contains(event.target) && !menuToggle?.contains(event.target)) {
                sidebar.classList.remove('open');
            }
        }
    });
</script>
</body>
</html>