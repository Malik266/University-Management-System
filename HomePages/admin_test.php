<?php
  session_start();
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

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
  <title>Admin Dashboard - Test</title>
  <link rel="stylesheet" href="../CSS/admin_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f4f7fa; }
    header { background: #2c3e50; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
    header button { background: none; border: none; color: white; font-size: 24px; cursor: pointer; }
    .container { display: flex; min-height: calc(100vh - 70px); }
    .sidebar { width: 250px; background: #34495e; color: white; padding: 20px; }
    .sidebar a { display: block; color: white; text-decoration: none; padding: 12px 15px; margin: 5px 0; border-radius: 5px; }
    .sidebar a:hover { background: #2c3e50; }
    .main { flex: 1; padding: 20px; }
    .card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    button { background: #3498db; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
    button:hover { background: #2980b9; }
    footer { background: #2c3e50; color: white; text-align: center; padding: 15px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    th { background: #e3f2fd; }
    .hidden { display: none; }
  </style>
</head>
<body>
  <header>
    <button onclick="toggleMenu()"><i class="fa fa-bars"></i></button>
    <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?> (Admin)</h1>
    <a href="../LogOut/logout.php" style="color: white;">Logout</a>
  </header>

  <div class="container">
    <div class="sidebar" id="sidebar">
      <br><br>
      <a href="#" onclick="showDashboard()"><i class="fa-solid fa-house-user"></i> Home</a>
      <a href="#" onclick="showManageUsers()"><i class="fa-solid fa-users"></i> Manage Users</a>
      <a href="#" onclick="showManageCourses()"><i class="fa-solid fa-book-open"></i> Manage Courses</a>
      <a href="#" onclick="showReports()"><i class="fa-solid fa-square-poll-vertical"></i> Reports</a>
    </div>

    <!-- Dashboard -->
    <div class="main" id="dashboard">
      <h2>Admin Dashboard</h2>
      <div class="card">
        <p>Welcome to the admin control panel.</p>
        <p>You can manage users, courses, view reports, and monitor the entire university system from here.</p>
      </div>
    </div>

    <!-- Manage Users -->
    <div class="main hidden" id="manage-users">
      <h2>Manage Users</h2>
      <div class="card">
        <button onclick="showStudents()">Show Students</button>
        <button onclick="showInstructors()">Show Instructors</button>
      </div>
      <div id="students-list" class="card">
        <h3>Students</h3>
        <?php
          $result = mysqli_query($conn, "SELECT student_id, name, email FROM Students LIMIT 5");
          echo "<table><tr><th>ID</th><th>Name</th><th>Email</th></tr>";
          while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr><td>{$row['student_id']}</td><td>{$row['name']}</td><td>{$row['email']}</td></tr>";
          }
          echo "</table>";
        ?>
      </div>
      <div id="instructors-list" class="card hidden">
        <h3>Instructors</h3>
        <?php
          $result = mysqli_query($conn, "SELECT instructor_id, name, email FROM Instructors LIMIT 5");
          echo "<table><tr><th>ID</th><th>Name</th><th>Email</th></tr>";
          while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr><td>{$row['instructor_id']}</td><td>{$row['name']}</td><td>{$row['email']}</td></tr>";
          }
          echo "</table>";
        ?>
      </div>
    </div>

    <!-- Manage Courses -->
    <div class="main hidden" id="manage-courses">
      <h2>Manage Courses</h2>
      <div class="card">
        <h3>Courses List</h3>
        <?php
          $result = mysqli_query($conn, "SELECT course_id, name, credits FROM Courses LIMIT 5");
          echo "<table><tr><th>ID</th><th>Name</th><th>Credits</th></tr>";
          while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr><td>{$row['course_id']}</td><td>{$row['name']}</td><td>{$row['credits']}</td></tr>";
          }
          echo "</table>";
        ?>
      </div>
    </div>

    <!-- Reports -->
    <div class="main hidden" id="reports-section">
      <h2>Reports</h2>
      <div class="card">
        <p>Reports section is working!</p>
        <p>Select a report from the dropdown below:</p>
        <select id="report-type" onchange="loadReport()">
          <option value="">-- Select Report --</option>
          <option value="students_multiple_courses">Students with Multiple Courses</option>
          <option value="course_avg_grades">Average Grades per Course</option>
        </select>
        <div id="report-result" style="margin-top: 20px;"></div>
      </div>
    </div>
  </div>

  <footer>
    &copy; 2025 University Portal. All rights reserved.
  </footer>

  <script>
    function showDashboard() {
      document.getElementById('dashboard').classList.remove('hidden');
      document.getElementById('manage-users').classList.add('hidden');
      document.getElementById('manage-courses').classList.add('hidden');
      document.getElementById('reports-section').classList.add('hidden');
    }
    
    function showManageUsers() {
      document.getElementById('dashboard').classList.add('hidden');
      document.getElementById('manage-users').classList.remove('hidden');
      document.getElementById('manage-courses').classList.add('hidden');
      document.getElementById('reports-section').classList.add('hidden');
      showStudents();
    }
    
    function showManageCourses() {
      document.getElementById('dashboard').classList.add('hidden');
      document.getElementById('manage-users').classList.add('hidden');
      document.getElementById('manage-courses').classList.remove('hidden');
      document.getElementById('reports-section').classList.add('hidden');
      console.log("Manage Courses section is now visible");
    }
    
    function showReports() {
      document.getElementById('dashboard').classList.add('hidden');
      document.getElementById('manage-users').classList.add('hidden');
      document.getElementById('manage-courses').classList.add('hidden');
      document.getElementById('reports-section').classList.remove('hidden');
      console.log("Reports section is now visible");
    }
    
    function showStudents() {
      document.getElementById('students-list').classList.remove('hidden');
      document.getElementById('instructors-list').classList.add('hidden');
    }
    
    function showInstructors() {
      document.getElementById('students-list').classList.add('hidden');
      document.getElementById('instructors-list').classList.remove('hidden');
    }
    
    function toggleMenu() {
      document.getElementById('sidebar').classList.toggle('visible');
    }
    
    function loadReport() {
      var type = document.getElementById('report-type').value;
      var resultDiv = document.getElementById('report-result');
      if (!type) {
        resultDiv.innerHTML = '';
        return;
      }
      resultDiv.innerHTML = '<p>Loading...</p>';
      var xhr = new XMLHttpRequest();
      xhr.open('GET', '../GetPages/get_report.php?type=' + encodeURIComponent(type), true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            resultDiv.innerHTML = xhr.responseText;
          } else {
            resultDiv.innerHTML = '<p style="color:red;">Failed to load report. Status: ' + xhr.status + '</p>';
          }
        }
      };
      xhr.send();
    }
    
    // Show dashboard by default
    showDashboard();
  </script>
</body>
</html>