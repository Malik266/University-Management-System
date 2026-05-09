// تأكد من أن الدوال معرفة بشكل صحيح في النطاق العام
if (typeof window.showManageUsers === "undefined") {
  window.showManageUsers = function () {
    var sections = [
      "dashboard",
      "manage-users",
      "manage-courses",
      "reports-section",
    ];
    sections.forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.style.display = id === "manage-users" ? "block" : "none";
    });
  };
}

if (typeof window.showManageCourses === "undefined") {
  window.showManageCourses = function () {
    var sections = [
      "dashboard",
      "manage-users",
      "manage-courses",
      "reports-section",
    ];
    sections.forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.style.display = id === "manage-courses" ? "block" : "none";
    });
  };
}

if (typeof window.showReports === "undefined") {
  window.showReports = function () {
    var sections = [
      "dashboard",
      "manage-users",
      "manage-courses",
      "reports-section",
    ];
    sections.forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.style.display = id === "reports-section" ? "block" : "none";
    });
  };
}

// ============================================================
//  SECTION NAVIGATION
// ============================================================
function switchSection(sectionId) {
  const sections = [
    "dashboard",
    "manage-users",
    "manage-courses",
    "reports-section",
  ];

  sections.forEach((id) => {
    const el = document.getElementById(id);
    if (el) {
      el.style.display = id === sectionId ? "block" : "none";
    }
  });

  // Close sidebar on mobile after clicking
  const sideBar = document.getElementById("side");
  if (sideBar) sideBar.classList.remove("visible");
}

function showManageUsers() {
  switchSection("manage-users");
}

function showManageCourses() {
  switchSection("manage-courses");
}

function showReports() {
  switchSection("reports-section");
}

function displayDashboard() {
  switchSection("dashboard");
}

// Student / Instructor pages (used in student_home & instructor pages)
function displayCoursesTable() {
  switchSection("main-c");
}

function displayStudentProfile() {
  switchSection("main-p");
}

// ============================================================
//  SIDEBAR TOGGLE (mobile)
// ============================================================
function toggleMenu() {
  const sideBar = document.getElementById("side");
  if (sideBar) sideBar.classList.toggle("visible");
}

// ============================================================
//  SHOW / HIDE FORMS
// ============================================================
function toggleAddStudentForm() {
  const form = document.getElementById("add-student-form");
  if (form)
    form.style.display = form.style.display === "none" ? "block" : "none";
}

function toggleAddInstructorForm() {
  const form = document.getElementById("add-instructor-form");
  if (form)
    form.style.display = form.style.display === "none" ? "block" : "none";
}

// ============================================================
//  MANAGE USERS — show students or instructors sub-section
// ============================================================
function showSection(section) {
  const studentsEl = document.getElementById("students-section");
  const instructorsEl = document.getElementById("instructors-section");
  if (studentsEl)
    studentsEl.style.display = section === "students" ? "block" : "none";
  if (instructorsEl)
    instructorsEl.style.display = section === "instructors" ? "block" : "none";
}

// ============================================================
//  REPORTS — load report via AJAX
// ============================================================
function loadReport() {
  const type = document.getElementById("report-type").value;
  const resultDiv = document.getElementById("report-result");

  if (!type) {
    resultDiv.innerHTML = "";
    return;
  }

  resultDiv.innerHTML = "<p>Loading...</p>";

  const xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "../GetPages/get_report.php?type=" + encodeURIComponent(type),
    true,
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        resultDiv.innerHTML = xhr.responseText;
      } else {
        resultDiv.innerHTML =
          "<p style='color:red;'>Failed to load report.</p>";
      }
    }
  };
  xhr.send();
}

// ============================================================
//  COURSES — load schedules dynamically (student side)
// ============================================================
function loadSchedules(courseId, studentId) {
  const container = document.getElementById("schedules-container-" + studentId);
  if (!courseId) {
    if (container) container.innerHTML = "";
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../GetPages/get_schedules.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      if (container) container.innerHTML = xhr.responseText;
    }
  };
  xhr.send("course_id=" + encodeURIComponent(courseId));
}

// ============================================================
//  COURSES — load schedules dynamically (instructor side)
// ============================================================
function loadSchedulesForInstructor(
  courseId,
  instructorId,
  mode = "instructor",
) {
  const containerId =
    mode === "instructor"
      ? "instructor-schedules-container-" + instructorId
      : "student-schedules-container";

  const container = document.getElementById(containerId);

  if (!courseId) {
    if (container) container.innerHTML = "";
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "../GetPages/get_schedules.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      if (container) container.innerHTML = xhr.responseText;
    }
  };
  xhr.send(
    "course_id=" +
      encodeURIComponent(courseId) +
      "&mode=" +
      encodeURIComponent(mode),
  );
}

// ============================================================
//  EDIT COURSE — fetch details and populate form
// ============================================================
function editCourse(courseId) {
  const xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "../GetPages/get_course_details.php?course_id=" + courseId,
    true,
  );
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      try {
        const course = JSON.parse(xhr.responseText);

        document.getElementById("edit-course-id").value = course.course_id;
        document.getElementById("edit-course-name").value = course.name;

        // Fill schedules
        const container = document.getElementById("add-schedule-container");
        container.innerHTML = "";
        if (course.schedules) {
          course.schedules.forEach((schedule) => {
            const days = [
              "Saturday",
              "Sunday",
              "Monday",
              "Tuesday",
              "Wednesday",
              "Thursday",
            ];
            const options = days
              .map(
                (day) =>
                  `<option value="${day}" ${schedule.day_of_week === day ? "selected" : ""}>${day}</option>`,
              )
              .join("");
            container.innerHTML += `
              <div style="margin-bottom:10px;">
                <label>Day:</label>
                <select name="days[]" required>
                  <option value="">Select</option>${options}
                </select>
                <label>Start:</label>
                <input type="time" name="starts[]" value="${schedule.start_time}" required>
                <label>End:</label>
                <input type="time" name="ends[]" value="${schedule.end_time}" required>
                <button type="button" onclick="this.parentElement.remove()">Remove</button>
              </div>`;
          });
        }

        // Fill instructors dropdown
        fetch("../GetPages/get_instructors.php")
          .then((r) => r.json())
          .then((instructors) => {
            const sel = document.getElementById("edit-course-instructor");
            sel.innerHTML = "";
            instructors.forEach((inst) => {
              const opt = document.createElement("option");
              opt.value = inst.instructor_id;
              opt.textContent = inst.name;
              if (inst.instructor_id == course.instructor_id)
                opt.selected = true;
              sel.appendChild(opt);
            });
          });

        document.getElementById("edit-course-form").style.display = "block";
      } catch (e) {
        console.error("Error parsing course JSON:", e);
      }
    }
  };
  xhr.send();
}

// ============================================================
//  ADD SCHEDULE FIELD (edit course form)
// ============================================================
function addScheduleField() {
  const container = document.getElementById("add-schedule-container");
  const days = [
    "Saturday",
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
  ];
  const options = days
    .map((d) => `<option value="${d}">${d}</option>`)
    .join("");
  container.innerHTML += `
    <div style="margin-bottom:10px;">
      <label>Day:</label>
      <select name="days[]" required>
        <option value="">Select</option>${options}
      </select>
      <label>Start:</label>
      <input type="time" name="starts[]" required>
      <label>End:</label>
      <input type="time" name="ends[]" required>
      <button type="button" onclick="this.parentElement.remove()">Remove</button>
    </div>`;
}

// ============================================================
//  DELETE HELPERS
// ============================================================
function deleteEnrollment(enrollmentId) {
  if (!confirm("Are you sure you want to delete this course for the student?"))
    return;

  fetch("../Delete/delete_enrollment.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "enrollment_id=" + encodeURIComponent(enrollmentId),
  })
    .then((r) => {
      if (r.ok) {
        alert("Enrollment deleted successfully");
        location.reload();
      } else alert("Failed to delete enrollment");
    })
    .catch((err) => {
      console.error(err);
      alert("An error occurred");
    });
}

function deleteInstructorCourse(instructorId, courseId) {
  if (
    !confirm("Are you sure you want to remove this course from the instructor?")
  )
    return;

  fetch("../Delete/delete_instructor_course.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `instructor_id=${instructorId}&course_id=${courseId}`,
  })
    .then((r) => {
      if (r.ok) {
        alert("Course removed from instructor successfully");
        location.reload();
      } else alert("Failed to remove course");
    })
    .catch((err) => {
      console.error(err);
      alert("An error occurred");
    });
}

function deleteInstructor(instructorId) {
  if (!confirm("Are you sure you want to delete this instructor?")) return;

  fetch("../Delete/delete_instructor.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `instructor_id=${instructorId}`,
  })
    .then((r) => {
      if (r.ok) {
        alert("Instructor deleted successfully");
        location.reload();
      } else alert("Failed to delete instructor");
    })
    .catch((err) => {
      console.error(err);
      alert("An error occurred");
    });
}
