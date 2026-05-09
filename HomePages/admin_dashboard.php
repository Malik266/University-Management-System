<?php
  session_start();
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ini_set('log_errors', 1);
  ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
  include("../connections/connection_Admin.php");
  if (!isset($_SESSION['admin_user'])) {
      header("Location: ../LogIN/login.html");
      exit();
  }
  $admin_name = $_SESSION['admin_user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - UniPortal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/dashboard-style.css?v=2.0">
</head>
<body>

<header class="header">
    <div class="icon-container">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1><i class="fas fa-graduation-cap"></i> UniPortal - Admin</h1>
    </div>
    <a href="../LogOut/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
</header>

<div class="dashboard-container">
    <aside class="sidebar admin" id="sidebar">
        <nav class="sidebar-nav">
            <a class="nav-item active" data-section="dashboard">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-item" data-section="users">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a class="nav-item" data-section="courses">
                <i class="fas fa-book-open"></i> Manage Courses
            </a>
            <a class="nav-item" data-section="reports">
                <i class="fas fa-square-poll-vertical"></i> Reports
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <!-- ================================================ -->
        <!-- DASHBOARD SECTION -->
        <!-- ================================================ -->
        <div class="section active" id="dashboard-section">
            <div class="section-header">
                <h2>Admin Dashboard</h2>
            </div>
            <div class="stats-grid">
                <?php
                $totalStudents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM Students"))['count'];
                $totalInstructors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM Instructors"))['count'];
                $totalCourses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM Courses"))['count'];
                ?>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value"><?php echo $totalStudents; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
                    <div class="stat-value"><?php echo $totalInstructors; ?></div>
                    <div class="stat-label">Total Instructors</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <div class="stat-value"><?php echo $totalCourses; ?></div>
                    <div class="stat-label">Total Courses</div>
                </div>
            </div>
            <div class="card">
                <p>Welcome to the admin control panel, <strong><?php echo htmlspecialchars($admin_name); ?></strong>.</p>
                <p>You can manage users, courses, view reports, and monitor the entire university system from here.</p>
            </div>
        </div>

        <!-- ================================================ -->
        <!-- MANAGE USERS SECTION -->
        <!-- ================================================ -->
        <div class="section" id="users-section">
            <div class="section-header">
                <h2>Manage Users</h2>
            </div>
            <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                <button class="btn btn-primary" onclick="showUserSection('students')">Show Students</button>
                <button class="btn btn-primary" onclick="showUserSection('instructors')">Show Instructors</button>
            </div>

            <!-- Students Section -->
            <div id="students-section" style="display: block;">
                <div class="card">
                    <h3><i class="fas fa-user-graduate"></i> Students</h3>
                    <button class="btn btn-success" onclick="toggleAddStudentForm()" style="margin-bottom: 15px;">
                        <i class="fas fa-plus"></i> Add New Student
                    </button>
                    
                    <div id="add-student-form" style="display: none; margin-bottom: 20px; padding: 20px; background: var(--light); border-radius: var(--radius);">
                        <form method="post" action="../AddPages/add_student.php">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-success">Add Student</button>
                            <button type="button" class="btn" onclick="toggleAddStudentForm()">Cancel</button>
                        </form>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th><th>Name</th><th>Email</th><th>Code</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $studentQuery = "SELECT student_id, name, email, code FROM Students";
                                $studentResult = mysqli_query($conn, $studentQuery);
                                while ($row = mysqli_fetch_assoc($studentResult)) {
                                    echo "<tr>
                                        <td>{$row['student_id']}</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['email']}</td>
                                        <td><span class='badge'>{$row['code']}</span></td>
                                        <td>
                                            <a href='../UpdatePages/update_student.php?id={$row['student_id']}' class='btn btn-primary' style='padding: 5px 10px;'><i class='fas fa-edit'></i> Edit</a>
                                            <a href='../Delete/delete_user.php?type=student&id={$row['student_id']}' onclick='return confirm(\"Are you sure?\")' class='btn btn-danger' style='padding: 5px 10px;'><i class='fas fa-trash'></i> Delete</a>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Enrolled Courses Section (Keep original complex structure) -->
                <?php
                $studentQuery2 = "SELECT student_id, name, email FROM Students";
                $studentResult2 = mysqli_query($conn, $studentQuery2);
                while ($row = mysqli_fetch_assoc($studentResult2)) {
                    $studentId = $row['student_id'];
                    $name = $row['name'];
                    $email = $row['email'];
                ?>
                <div class="card">
                    <h4>Enrolled Courses for: <?php echo htmlspecialchars($name); ?></h4>
                    <form method='post' action='../UpdatePages/update_student.php'>
                        <input type='hidden' name='student_id' value='<?php echo $studentId; ?>'>
                        <div class="form-group">
                            <label>Name</label>
                            <input type='text' name='name' value='<?php echo htmlspecialchars($name); ?>'>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type='email' name='email' value='<?php echo htmlspecialchars($email); ?>'>
                        </div>
                        <button type='submit' class='btn btn-primary'>Save Student</button>
                    </form>

                    <form method='post' action='../AddPages/add_enrollment.php'>
                        <strong>Enrolled Courses:</strong>
                        <div class="table-container">
                            <table class="data-table">
                                <thead><tr><th>Course Name</th><th>Schedule</th><th>Action</th></tr></thead>
                                <tbody>
                                <?php
                                $coursesQuery = "SELECT E.enrollment_id, C.course_id, C.name AS course_name, CS.schedule_id, CS.day_of_week, CS.start_time, CS.end_time 
                                                FROM Enrollments E 
                                                JOIN Courses C ON E.course_id = C.course_id 
                                                LEFT JOIN CourseSchedules CS ON C.course_id = CS.course_id 
                                                WHERE E.student_id = ?";
                                $stmt = mysqli_prepare($conn, $coursesQuery);
                                mysqli_stmt_bind_param($stmt, "i", $studentId);
                                mysqli_stmt_execute($stmt);
                                $coursesResult = mysqli_stmt_get_result($stmt);
                                $i = 0;
                                while ($course = mysqli_fetch_assoc($coursesResult)) {
                                    echo "<tr>
                                            <td><input type='text' value='{$course['course_name']}' readonly style='background:#f0f0f0; width:100%;'></td>
                                            <td>
                                                <select name='courses[$i][schedule_id]' class='form-control'>";
                                    $schedulesQuery = "SELECT schedule_id, day_of_week, start_time, end_time FROM CourseSchedules WHERE course_id = {$course['course_id']}";
                                    $schedulesResult = mysqli_query($conn, $schedulesQuery);
                                    while ($schedule = mysqli_fetch_assoc($schedulesResult)) {
                                        $selected = ($schedule['schedule_id'] == $course['schedule_id']) ? "selected" : "";
                                        echo "<option value='{$schedule['schedule_id']}' $selected>{$schedule['day_of_week']} (" . substr($schedule['start_time'],0,5) . " - " . substr($schedule['end_time'],0,5) . ")</option>";
                                    }
                                    echo "</select>
                                            <input type='hidden' name='courses[$i][enrollment_id]' value='{$course['enrollment_id']}'>
                                            </td>
                                            <td><button type='button' class='btn btn-danger' onclick='deleteEnrollment({$course['enrollment_id']})'>Delete</button></td>
                                          </tr>";
                                    $i++;
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <strong>Add New Course:</strong>
                        <input type='hidden' name='student_id' value='<?php echo $studentId; ?>'>
                        <select name='course_id' class='form-control' style='margin-top:10px;' onchange='loadSchedules(this.value, <?php echo $studentId; ?>)'>
                            <option value=''>-- Select Course --</option>
                            <?php
                            $allCoursesResult = mysqli_query($conn, "SELECT course_id, name FROM Courses");
                            while ($c = mysqli_fetch_assoc($allCoursesResult)) {
                                echo "<option value='{$c['course_id']}'>{$c['name']}</option>";
                            }
                            ?>
                        </select>
                        <div id='schedules-container-<?php echo $studentId; ?>' style='margin-top:10px;'></div>
                        <div style='margin-top:15px;'>
                            <button type='submit' class='btn btn-success'>Save</button>
                            <button type='reset' class='btn'>Cancel</button>
                        </div>
                    </form>
                </div>
                <?php } ?>
            </div>

            <!-- Instructors Section -->
            <div id="instructors-section" style="display: none;">
                <div class="card">
                    <h3><i class="fas fa-chalkboard-user"></i> Instructors</h3>
                    <button class="btn btn-success" onclick="toggleAddInstructorForm()" style="margin-bottom: 15px;">
                        <i class="fas fa-plus"></i> Add New Instructor
                    </button>
                    
                    <div id="add-instructor-form" style="display: none; margin-bottom: 20px; padding: 20px; background: var(--light); border-radius: var(--radius);">
                        <form method="POST" action="../AddPages/add_instructor.php">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <select name="department_name" required>
                                    <option value="">-- Select Department --</option>
                                    <?php
                                    $deptQuery = "SELECT department_id, name FROM Departments";
                                    $deptResult = mysqli_query($conn, $deptQuery);
                                    while ($dept = mysqli_fetch_assoc($deptResult)) {
                                        echo "<option value='{$dept['department_id']}'>{$dept['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Add Instructor</button>
                            <button type="button" class="btn" onclick="toggleAddInstructorForm()">Cancel</button>
                        </form>
                    </div>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $instructorQuery = "SELECT instructor_id, name, email FROM Instructors";
                                $instructorResult = mysqli_query($conn, $instructorQuery);
                                while ($row = mysqli_fetch_assoc($instructorResult)) {
                                    echo "<tr>
                                        <td>{$row['instructor_id']}</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>
                                            <a href='../UpdatePages/update_instructor.php?id={$row['instructor_id']}' class='btn btn-primary'><i class='fas fa-edit'></i> Edit</a>
                                            <a href='../Delete/delete_user.php?type=instructor&id={$row['instructor_id']}' onclick='return confirm(\"Are you sure?\")' class='btn btn-danger'><i class='fas fa-trash'></i> Delete</a>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================ -->
        <!-- MANAGE COURSES SECTION -->
        <!-- ================================================ -->
        <div class="section" id="courses-section">
            <div class="section-header">
                <h2>Manage Courses</h2>
            </div>
            <div class="card">
                <h3>Courses List</h3>
                <button class="btn btn-success" onclick="document.getElementById('add-course-form').style.display='block'" style="margin-bottom: 15px;">
                    <i class="fas fa-plus"></i> Add New Course
                </button>

                <div id="add-course-form" style="display: none; margin-bottom: 20px; padding: 20px; background: var(--light); border-radius: var(--radius);">
                    <form method="post" action="../AddPages/add_course.php">
                        <div class="form-group">
                            <label>Course Name</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Credits</label>
                            <input type="number" name="credits" required>
                        </div>
                        <button type="submit" class="btn btn-success">Add Course</button>
                        <button type="button" class="btn" onclick="document.getElementById('add-course-form').style.display='none'">Cancel</button>
                    </form>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr><th>ID</th><th>Course Name</th><th>Credits</th><th>Instructor</th><th>Schedules</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $courseQuery = "SELECT c.course_id, c.name, c.credits, i.name as instructor_name, cs.day_of_week, cs.start_time, cs.end_time
                                            FROM Courses c
                                            LEFT JOIN InstructorCourses ic ON c.course_id = ic.course_id
                                            LEFT JOIN Instructors i ON ic.instructor_id = i.instructor_id
                                            LEFT JOIN CourseSchedules cs ON c.course_id = cs.course_id
                                            ORDER BY c.course_id, cs.day_of_week";
                            $courseResult = mysqli_query($conn, $courseQuery);
                            $courses = [];
                            while ($row = mysqli_fetch_assoc($courseResult)) {
                                $cid = $row['course_id'];
                                if (!isset($courses[$cid])) {
                                    $courses[$cid] = [
                                        'name' => $row['name'],
                                        'credits' => $row['credits'],
                                        'instructor' => $row['instructor_name'] ?? 'Not Assigned',
                                        'schedules' => []
                                    ];
                                }
                                if ($row['day_of_week']) {
                                    $start = date('H:i', strtotime($row['start_time']));
                                    $end = date('H:i', strtotime($row['end_time']));
                                    $courses[$cid]['schedules'][] = "{$row['day_of_week']} ($start - $end)";
                                }
                            }
                            foreach ($courses as $id => $course) {
                                echo "<tr>
                                        <td>{$id}</td>
                                        <td>" . htmlspecialchars($course['name']) . "</td>
                                        <td>{$course['credits']}</td>
                                        <td>" . htmlspecialchars($course['instructor']) . "</td>
                                        <td>" . (empty($course['schedules']) ? 'No schedule' : implode('<br>', $course['schedules'])) . "</td>
                                        <td>
                                            <button class='btn btn-primary' onclick='editCourse({$id})'>Edit</button>
                                            <a href='../Delete/delete_course.php?id={$id}' onclick='return confirm(\"Are you sure?\")' class='btn btn-danger'>Delete</a>
                                        </td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edit Course Form -->
            <div id="edit-course-form" style="display: none;">
                <div class="card">
                    <h3>Edit Course</h3>
                    <form method="post" action="../UpdatePages/update_course.php">
                        <input type="hidden" name="course_id" id="edit-course-id">
                        <div class="form-group">
                            <label>Course Name</label>
                            <input type="text" name="name" id="edit-course-name" required>
                        </div>
                        <div class="form-group">
                            <label>Credits</label>
                            <input type="number" name="credits" id="edit-course-credits" required>
                        </div>
                        <div class="form-group">
                            <label>Instructor</label>
                            <select name="instructor_id" id="edit-course-instructor">
                                <option value="">Select Instructor</option>
                                <?php
                                $instResult = mysqli_query($conn, "SELECT instructor_id, name FROM Instructors");
                                while ($inst = mysqli_fetch_assoc($instResult)) {
                                    echo "<option value='{$inst['instructor_id']}'>{$inst['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <h4>Schedules</h4>
                        <div id="add-schedule-container"></div>
                        <button type="button" class="btn" onclick="addScheduleField()">+ Add Schedule</button>
                        <div style="margin-top: 15px;">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn" onclick="document.getElementById('edit-course-form').style.display='none'">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================================================ -->
        <!-- REPORTS SECTION -->
        <!-- ================================================ -->
        <div class="section" id="reports-section">
            <div class="section-header">
                <h2>Reports</h2>
            </div>
            <div class="card">
                <div class="form-group">
                    <label>Select Report Type</label>
                    <select id="report-type" onchange="loadReport()">
                        <option value="">-- Choose a report --</option>
                        <option value="students_multiple_courses">Students with Multiple Courses</option>
                        <option value="course_avg_grades">Average Grades per Course</option>
                        <option value="students_without_courses">Students Not Enrolled</option>
                        <option value="students_per_department">Number of Students per Department</option>
                        <option value="cs_department_courses">Courses by CS Instructors</option>
                        <option value="top_students_per_course">Top Students per Course</option>
                        <option value="most_active_instructor">Most Active Instructor</option>
                        <option value="student_name_change_log">Student Name Change Log</option>
                    </select>
                </div>
                <div id="report-result" style="margin-top: 20px;"></div>
            </div>
        </div>
    </main>
</div>

<footer class="footer">
    &copy; 2025 University Portal. All rights reserved.
</footer>
<style>
  @media (max-width: 768px) {
      .menu-toggle {
          display: flex !important;
          position: fixed;
          top: 12px;
          left: 12px;
          z-index: 1001;
          background: linear-gradient(135deg, #2563eb, #1d4ed8);
          border: none;
          color: white;
          width: 42px;
          height: 42px;
          border-radius: 10px;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      }
      
      .menu-toggle i {
          font-size: 1.2rem;
      }
      
      .header {
          padding: 12px 16px;
          justify-content: center;
      }
      
      .header h1 {
          font-size: 1rem;
          text-align: center;
          margin-left: 0;
      }
      
      .icon-container {
          width: 100%;
          justify-content: center;
      }
      
      .sidebar {
          top: 0;
          z-index: 1000;
      }
  }
</style>

<script>
    // Navigation between sections
    const navItems = document.querySelectorAll('.nav-item');
    const sections = {
        dashboard: document.getElementById('dashboard-section'),
        users: document.getElementById('users-section'),
        courses: document.getElementById('courses-section'),
        reports: document.getElementById('reports-section')
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
    
    // Toggle between students and instructors
    function showUserSection(type) {
        const studentsSection = document.getElementById('students-section');
        const instructorsSection = document.getElementById('instructors-section');
        if (type === 'students') {
            studentsSection.style.display = 'block';
            instructorsSection.style.display = 'none';
        } else {
            studentsSection.style.display = 'none';
            instructorsSection.style.display = 'block';
        }
    }
    
    // Toggle forms
    function toggleAddStudentForm() {
        const form = document.getElementById('add-student-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    
    function toggleAddInstructorForm() {
        const form = document.getElementById('add-instructor-form');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
    
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
    
    // Load schedules
    function loadSchedules(courseId, studentId) {
        if (!courseId) {
            document.getElementById('schedules-container-' + studentId).innerHTML = '';
            return;
        }
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../GetPages/get_schedules.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('schedules-container-' + studentId).innerHTML = xhr.responseText;
            }
        };
        xhr.send('course_id=' + encodeURIComponent(courseId));
    }
    
    function loadSchedulesForInstructor(courseId, instructorId) {
        if (!courseId) {
            document.getElementById('instructor-schedules-container-' + instructorId).innerHTML = '';
            return;
        }
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../GetPages/get_schedules.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('instructor-schedules-container-' + instructorId).innerHTML = xhr.responseText;
            }
        };
        xhr.send('course_id=' + encodeURIComponent(courseId));
    }
    
    // Load report
    function loadReport() {
        var type = document.getElementById('report-type').value;
        var resultDiv = document.getElementById('report-result');
        if (!type) {
            resultDiv.innerHTML = '';
            return;
        }
        resultDiv.innerHTML = '<p><i class="fas fa-spinner fa-spin"></i> Loading...</p>';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../GetPages/get_report.php?type=' + encodeURIComponent(type), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    resultDiv.innerHTML = xhr.responseText;
                } else {
                    resultDiv.innerHTML = '<p style="color:var(--danger);">Failed to load report.</p>';
                }
            }
        };
        xhr.send();
    }
    
    // Edit course
    function editCourse(courseId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '../GetPages/get_course_details.php?course_id=' + courseId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var course = JSON.parse(xhr.responseText);
                    document.getElementById('edit-course-id').value = course.course_id;
                    document.getElementById('edit-course-name').value = course.name;
                    document.getElementById('edit-course-credits').value = course.credits;
                    var container = document.getElementById('add-schedule-container');
                    container.innerHTML = '';
                    if (course.schedules && course.schedules.length > 0) {
                        course.schedules.forEach(function(schedule) {
                            addScheduleField(schedule);
                        });
                    }
                    document.getElementById('edit-course-form').style.display = 'block';
                } catch(e) {
                    console.error('Error:', e);
                }
            }
        };
        xhr.send();
    }
    
    function addScheduleField(scheduleData) {
        var container = document.getElementById('add-schedule-container');
        var dayValue = scheduleData ? scheduleData.day_of_week : '';
        var startValue = scheduleData ? scheduleData.start_time : '';
        var endValue = scheduleData ? scheduleData.end_time : '';
        container.innerHTML += '<div style="margin-bottom:10px;">' +
            '<label>Day:</label>' +
            '<select name="days[]" required>' +
            '<option value="">Select</option>' +
            '<option value="Saturday"' + (dayValue === 'Saturday' ? ' selected' : '') + '>Saturday</option>' +
            '<option value="Sunday"' + (dayValue === 'Sunday' ? ' selected' : '') + '>Sunday</option>' +
            '<option value="Monday"' + (dayValue === 'Monday' ? ' selected' : '') + '>Monday</option>' +
            '<option value="Tuesday"' + (dayValue === 'Tuesday' ? ' selected' : '') + '>Tuesday</option>' +
            '<option value="Wednesday"' + (dayValue === 'Wednesday' ? ' selected' : '') + '>Wednesday</option>' +
            '<option value="Thursday"' + (dayValue === 'Thursday' ? ' selected' : '') + '>Thursday</option>' +
            '</select>' +
            '<label>Start:</label>' +
            '<input type="time" name="starts[]" value="' + startValue + '" required>' +
            '<label>End:</label>' +
            '<input type="time" name="ends[]" value="' + endValue + '" required>' +
            '<button type="button" class="btn btn-danger" style="padding:2px 8px;" onclick="this.parentElement.remove()">Remove</button>' +
            '</div>';
    }
    
    // Delete functions
    function deleteEnrollment(enrollmentId) {
        if (confirm('Are you sure you want to delete this enrollment?')) {
            fetch('../Delete/delete_enrollment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'enrollment_id=' + encodeURIComponent(enrollmentId)
            })
            .then(function(r) { 
                if (r.ok) { alert('Deleted successfully'); location.reload(); } 
                else { alert('Failed to delete'); }
            })
            .catch(function(err) { console.error(err); alert('Error occurred'); });
        }
    }
    
    function deleteInstructorCourse(instructorId, courseId) {
        if (confirm('Remove this course from instructor?')) {
            fetch('../Delete/delete_instructor_course.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'instructor_id=' + instructorId + '&course_id=' + courseId
            })
            .then(function(r) { if (r.ok) { alert('Removed'); location.reload(); } else { alert('Failed'); } })
            .catch(function(err) { console.error(err); alert('Error'); });
        }
    }
</script>
</body>
</html>