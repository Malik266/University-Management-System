<?php
session_start();
include('../connections/connection_instructor.php');

if (!isset($_SESSION['instructor_id'])) {
    header("Location: ../LogIN/login.html");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];
$username = $_SESSION['instructor_name'];

// Get instructor info - MySQL version
$sql_info = "
SELECT 
    i.name AS instructor_name,
    i.email,
    d.name AS department_name
FROM Instructors i
LEFT JOIN Departments d ON i.department_id = d.department_id
WHERE i.instructor_id = ?";

$stmt_info = mysqli_prepare($conn, $sql_info);
mysqli_stmt_bind_param($stmt_info, "i", $instructor_id);
mysqli_stmt_execute($stmt_info);
$info_result = mysqli_stmt_get_result($stmt_info);
$info = mysqli_fetch_assoc($info_result);

// Get courses taught by instructor
$sql_courses = "
SELECT 
    c.name AS course_name,
    cs.day_of_week,
    DATE_FORMAT(cs.start_time, '%H:%i') AS start_time,
    DATE_FORMAT(cs.end_time, '%H:%i') AS end_time
FROM InstructorCourses ic
JOIN Courses c ON ic.course_id = c.course_id
JOIN CourseSchedules cs ON c.course_id = cs.course_id
WHERE ic.instructor_id = ?
ORDER BY cs.day_of_week, cs.start_time";

$stmt_courses = mysqli_prepare($conn, $sql_courses);
mysqli_stmt_bind_param($stmt_courses, "i", $instructor_id);
mysqli_stmt_execute($stmt_courses);
$courses_result = mysqli_stmt_get_result($stmt_courses);

// Get enrollments for instructor's courses
$sql_enrollments = "
SELECT 
    e.enrollment_id,
    c.name AS course_name,
    s.name AS student_name, 
    s.student_id,
    e.grade,
    e.status
FROM Enrollments e
JOIN Courses c ON e.course_id = c.course_id
JOIN InstructorCourses ic ON c.course_id = ic.course_id
JOIN Students s ON e.student_id = s.student_id    
WHERE ic.instructor_id = ?";

$stmt_enrollments = mysqli_prepare($conn, $sql_enrollments);
mysqli_stmt_bind_param($stmt_enrollments, "i", $instructor_id);
mysqli_stmt_execute($stmt_enrollments);
$enrollments_result = mysqli_stmt_get_result($stmt_enrollments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - UniPortal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/dashboard-style.css?v=2.0">
    <style>
        /* Additional styles for instructor */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-completed { background: #dbeafe; color: #1e40af; }
        .badge-withdrawn { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        
        .form-inline {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .form-inline input, .form-inline select {
            margin: 0;
            width: auto;
            min-width: 100px;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="icon-container">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1><i class="fas fa-chalkboard-user"></i> UniPortal - Instructor</h1>
    </div>
    <a href="../LogOut/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</header>

<div class="dashboard-container">
    <aside class="sidebar instructor" id="sidebar">
        <nav class="sidebar-nav">
            <a class="nav-item active" data-section="dashboard">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-item" data-section="schedule">
                <i class="fas fa-calendar-alt"></i> My Schedule
            </a>
            <a class="nav-item" data-section="enrollments">
                <i class="fas fa-users"></i> Manage Enrollments
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
                    <div class="stat-value">
                        <?php 
                            $courseCount = mysqli_num_rows($courses_result);
                            mysqli_data_seek($courses_result, 0);
                            echo $courseCount;
                        ?>
                    </div>
                    <div class="stat-label">Courses Taught</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value">
                        <?php 
                            $studentCount = mysqli_num_rows($enrollments_result);
                            mysqli_data_seek($enrollments_result, 0);
                            echo $studentCount;
                        ?>
                    </div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?php echo $courseCount * 3; ?></div>
                    <div class="stat-label">Weekly Hours</div>
                </div>
            </div>
            <div class="card">
                <p>Welcome back, <strong><?php echo htmlspecialchars($username); ?></strong>!</p>
                <p>You can manage your courses, view your schedule, and update student grades from this dashboard.</p>
            </div>
        </div>

        <!-- Schedule Section -->
        <div class="section" id="schedule-section">
            <div class="section-header">
                <h2>My Teaching Schedule</h2>
            </div>
            <div class="card">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Day</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($courses_result) > 0): ?>
                                <?php 
                                mysqli_data_seek($courses_result, 0);
                                while ($row = mysqli_fetch_assoc($courses_result)): 
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['day_of_week']); ?></td>
                                    <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                                    <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No courses assigned yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manage Enrollments Section -->
        <div class="section" id="enrollments-section">
            <div class="section-header">
                <h2>Manage Student Enrollments</h2>
            </div>
            <div class="card">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            mysqli_data_seek($enrollments_result, 0);
                            while ($row = mysqli_fetch_assoc($enrollments_result)): 
                                $statusClass = '';
                                if ($row['status'] == 'active') $statusClass = 'badge-active';
                                elseif ($row['status'] == 'completed') $statusClass = 'badge-completed';
                                elseif ($row['status'] == 'withdrawn') $statusClass = 'badge-withdrawn';
                                else $statusClass = 'badge-pending';
                            ?>
                            <tr>
                                <form method="post" action="../UpdatePages/update_enrollment.php" class="form-inline">
                                    <input type="hidden" name="enrollment_id" value="<?php echo $row['enrollment_id']; ?>">
                                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><input type="text" name="grade" value="<?php echo htmlspecialchars($row['grade']); ?>" style="width: 60px;"></td>
                                    <td>
                                        <select name="status">
                                            <option value="active" <?php echo ($row['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="withdrawn" <?php echo ($row['status'] == 'withdrawn') ? 'selected' : ''; ?>>Withdrawn</option>
                                            <option value="completed" <?php echo ($row['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                            <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        </select>
                                    </td>
                                    <td><button type="submit" class="btn btn-primary" style="padding: 5px 12px;">Update</button></td>
                                </form>
                            </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($enrollments_result) == 0): ?>
                                <tr><td colspan="6" class="text-center">No students enrolled in your courses.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="section" id="profile-section">
            <div class="section-header">
                <h2>My Profile</h2>
            </div>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar"><i class="fas fa-chalkboard-user"></i></div>
                    <h3><?php echo htmlspecialchars($info['instructor_name'] ?? $username); ?></h3>
                    <p><?php echo htmlspecialchars($info['department_name'] ?? 'Instructor'); ?></p>
                </div>
                <div class="profile-body">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-envelope"></i> Email</span>
                        <span class="info-value"><?php echo htmlspecialchars($info['email'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-id-card"></i> Instructor ID</span>
                        <span class="info-value"><?php echo htmlspecialchars($instructor_id); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-building"></i> Department</span>
                        <span class="info-value"><?php echo htmlspecialchars($info['department_name'] ?? 'Not assigned'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<footer class="footer">
    &copy; 2025 University Portal. All rights reserved.
</footer>

<script>
    // Navigation between sections
    const navItems = document.querySelectorAll('.nav-item');
    const sections = {
        dashboard: document.getElementById('dashboard-section'),
        schedule: document.getElementById('schedule-section'),
        enrollments: document.getElementById('enrollments-section'),
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
    
    // Sidebar toggle for mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }
    
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