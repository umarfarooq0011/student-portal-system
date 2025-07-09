<div class="sidebar" id="sidebaradmin">
  <div class="d-flex align-items-center px-4 mb-4" style="height:64px;">
    <div class="logo-wrapper flex-grow-1" style="max-width: 120px; min-width: 80px; display: flex; align-items: center; justify-content: flex-start;">
      <img src="../assets/images/logo-student.png" alt="Admin Portal Logo" style="width: 100%; height: auto; object-fit: contain; display: block;">
    </div>
    <button class="btn d-lg-none ms-auto p-0" id="sidebaradminToggleClose" aria-label="Close sidebar" style="font-size:1.5rem; color:#fff;">
      <i class="bi bi-x"></i>
    </button>
  </div>
  <nav class="nav flex-column">
    <a href="../admin/index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
      <i class="bi bi-grid"></i> Dashboard
    </a>
    <a href="../admin/manage_announcements.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_announcements.php') ? 'active' : ''; ?>">
      <i class="bi bi-megaphone"></i> Manage Announcements
    </a>
    <a href="../admin/manage_assignments.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_assignments.php') ? 'active' : ''; ?>">
      <i class="bi bi-file-earmark-text"></i> Manage Assignments
    </a>
    <!-- <a href="../admin/students.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'students.php') ? 'active' : ''; ?>">
      <i class="bi bi-people"></i> Manage Students
    </a> -->
    <a href="../admin/manage_timetable.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_timetable.php') ? 'active' : ''; ?>">
      <i class="bi bi-calendar3"></i> Manage Timetables
    </a>
    <!-- <a href="../admin/manage_grades.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_grades.php') ? 'active' : ''; ?>">
      <i class="bi bi-mortarboard"></i> Manage Grades
    </a> -->
  </nav>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebaradmin');
    var closeBtn = document.getElementById('sidebaradminToggleClose');
    if (closeBtn && sidebar) {
      closeBtn.addEventListener('click', function() {
        sidebar.classList.remove('active');
        document.getElementById('sidebaradminOverlay').style.display = 'none';
        document.body.style.overflow = '';
      });
    }
  });
</script>