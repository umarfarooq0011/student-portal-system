<div class="sidebar" id="sidebar">
  <div class="d-flex align-items-center px-4 mb-4" style="height:64px;">
    <div class="logo-wrapper flex-grow-1" style="max-width: 120px; min-width: 80px; display: flex; align-items: center; justify-content: flex-start;">
      <img src="../assets/images/logo-student.png" alt="Student Portal Logo" style="width: 100%; height: auto; object-fit: contain; display: block;">
    </div>
    <button class="btn d-lg-none ms-auto p-0" id="sidebarToggleClose" aria-label="Close sidebar" style="font-size:1.5rem; color:#fff;">
      <i class="bi bi-x"></i>
    </button>
  </div>
  <nav class="nav flex-column">

    <a href="../dashboard/index.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
      <i class="bi bi-house-door"></i> Dashboard
    </a>
    <a href="../dashboard/annoucements.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'annoucements.php') ? 'active' : ''; ?>">
      <i class="bi bi-megaphone"></i> Announcements
    </a>

    <a href="../dashboard/assignments.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'assignments.php') ? 'active' : ''; ?>">
      <i class="bi bi-file-earmark-text"></i> Assignments
    </a>
    <a href="../dashboard/timetable.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'timetable.php') ? 'active' : ''; ?>">
      <i class="bi bi-calendar3"></i> Timetable
    </a>
    <a href="../dashboard/grades.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'grades.php') ? 'active' : ''; ?>">
      <i class="bi bi-mortarboard"></i> Grades
    </a>
    <a href="../dashboard/profile.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
      <i class="bi bi-person"></i> Profile
    </a>
  </nav>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var closeBtn = document.getElementById('sidebarToggleClose');
    if (closeBtn && sidebar) {
      closeBtn.addEventListener('click', function() {
        sidebar.classList.remove('active');
      });
    }
  });
</script>
