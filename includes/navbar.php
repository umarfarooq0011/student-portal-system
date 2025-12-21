<?php
require_once __DIR__ . '/../config/db.php';
?>
<!-- Premium Top Navbar -->
<div class="main-wrapper min-h-screen flex flex-col">
    <nav class="sticky top-0 z-30 bg-white/70 backdrop-blur-xl border-b border-slate-200/60">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Left: Mobile menu button + Page title -->
                <div class="flex items-center gap-4">
                    <!-- Mobile menu toggle -->
                    <button class="lg:hidden p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all" id="sidebarToggle">
                        <i class="bi bi-list text-xl"></i>
                    </button>

                    <!-- Page Title -->
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">
                            <?php
                            $currentPage = basename($_SERVER['PHP_SELF']);
                            switch($currentPage) {
                                case 'index.php':
                                    echo 'Dashboard';
                                    break;
                                case 'annoucements.php':
                                case 'announcements.php':
                                    echo 'Announcements';
                                    break;
                                case 'assignments.php':
                                    echo 'Assignments';
                                    break;
                                case 'timetable.php':
                                    echo 'Timetable';
                                    break;
                                case 'profile.php':
                                    echo 'Profile';
                                    break;
                                default:
                                    echo 'Student Portal';
                            }
                            ?>
                        </h2>
                        <p class="text-sm text-slate-500"><?php echo date('l, F j, Y'); ?></p>
                    </div>
                </div>

                <!-- Right: User dropdown -->
                <div class="flex items-center gap-4">
                    <!-- User Dropdown -->
                    <div class="relative" id="userDropdownContainer">
                        <?php
                        // Get user's profile photo
                        $user_id = $_SESSION['user_id'];
                        $photo_query = "SELECT profile_image FROM users WHERE id = ?";
                        $stmt = $conn->prepare($photo_query);
                        $stmt->bind_param('i', $user_id);
                        $stmt->execute();
                        $photo_result = $stmt->get_result();
                        $user = $photo_result->fetch_assoc();
                        $profile_image = $user['profile_image'];
                        $profile_path = $profile_image ? "../assets/uploads/profile_photos/" . $profile_image : "";
                        ?>
                        <button class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 transition-all" id="userDropdownBtn">
                            <?php if ($profile_image && file_exists($profile_path)): ?>
                                <img src="<?php echo $profile_path; ?>" alt="Profile" class="w-8 h-8 rounded-lg object-cover border-2 border-white shadow-sm">
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-lg bg-violet-600 flex items-center justify-center text-white font-semibold text-sm">
                                    <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-semibold text-slate-700"><?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Student'; ?></p>
                                <p class="text-xs text-slate-500">Student</p>
                            </div>
                            <i class="bi bi-chevron-down text-slate-400 text-sm ml-1"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 hidden" id="userDropdownMenu">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-800"><?php echo $_SESSION['full_name']; ?></p>
                                <p class="text-xs text-slate-500"><?php echo $_SESSION['email'] ?? ''; ?></p>
                            </div>
                            <a href="../dashboard/profile.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 transition-all">
                                <i class="bi bi-person text-lg"></i>
                                <span>View Profile</span>
                            </a>
                            <a href="../dashboard/assignments.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 transition-all">
                                <i class="bi bi-file-earmark-text text-lg"></i>
                                <span>Assignments</span>
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <a href="../auth/logout.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-all">
                                <i class="bi bi-box-arrow-right text-lg"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-1 p-6">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle for mobile
    const sidebar = document.getElementById('sidebarStudent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('active');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            if (!sidebar.classList.contains('active')) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on window resize (desktop)
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            closeSidebar();
        }
    });

    // User dropdown toggle
    const dropdownBtn = document.getElementById('userDropdownBtn');
    const dropdownMenu = document.getElementById('userDropdownMenu');

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }

    // Initialize Notyf for alerts
    window.notyf = new Notyf({
        duration: 3500,
        position: { x: 'right', y: 'top' },
        dismissible: true,
        ripple: true
    });

    // Also provide StudentNotify wrapper for compatibility
    window.StudentNotify = {
        success: function(msg) { window.notyf.success(msg); },
        error: function(msg) { window.notyf.error(msg); },
        warning: function(msg) { window.notyf.open({ type: 'warning', message: msg }); },
        info: function(msg) { window.notyf.open({ type: 'info', message: msg }); }
    };

    // Check URL params for alerts
    const urlParams = new URLSearchParams(window.location.search);
    let alertShown = false;

    if (urlParams.has('error')) {
        window.notyf.error(urlParams.get('error'));
        alertShown = true;
    }
    if (urlParams.has('success')) {
        window.notyf.success(urlParams.get('success'));
        alertShown = true;
    }

    if (alertShown && window.history.replaceState) {
        const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }
});
</script>
