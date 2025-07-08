<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm d-flex align-items-center" style="margin-left:250px; min-height:64px;">
  <div class="container-fluid justify-content-between">
    <!-- Hamburger for mobile -->
    <button class="btn d-lg-none me-2" id="sidebarToggle" aria-label="Toggle sidebar" style="border-radius: 50%; background: #f3f3f3; box-shadow: 0 1px 4px rgba(0,0,0,0.07);">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="d-flex align-items-center ms-auto">
      <div class="dropdown">
        <button class="btn btn-outline-secondary rounded-pill px-3 dropdown-toggle fw-semibold" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle me-2"></i><?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User'; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li><a class="dropdown-item" href="../dashboard/profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var sidebar = document.getElementById('sidebar');
    var toggle = document.getElementById('sidebarToggle');
    var overlay = document.getElementById('sidebarOverlay');
    function openSidebar() {
      sidebar.classList.add('active');
      overlay.style.display = 'block';
      document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
      sidebar.classList.remove('active');
      overlay.style.display = 'none';
      document.body.style.overflow = '';
    }
    if (toggle && sidebar) {
      toggle.addEventListener('click', function() {
        if (!sidebar.classList.contains('active')) {
          openSidebar();
        } else {
          closeSidebar();
        }
      });
    }
    if (overlay) {
      overlay.addEventListener('click', function() {
        closeSidebar();
      });
    }
    // Close sidebar on resize if desktop
    window.addEventListener('resize', function() {
      if (window.innerWidth > 991) {
        closeSidebar();
      }
    });
  });
</script>
