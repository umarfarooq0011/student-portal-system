<!-- Premium Sidebar -->
<aside class="sidebar bg-white/80 backdrop-blur-xl border-r border-slate-200/60 shadow-xl" id="sidebarAdmin">
    <!-- Logo Section -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100">
        <a href="../admin/index.php" class="flex items-center gap-3 sidebar-logo-link">
            <div class="w-10 h-10 rounded-xl bg-sky-600 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-mortarboard-fill text-white text-lg"></i>
            </div>
            <div class="sidebar-text">
                <h1 class="text-lg font-bold text-slate-800">Admin</h1>
                <p class="text-xs text-slate-500 -mt-0.5">Student Portal</p>
            </div>
        </a>
        <!-- Close button for mobile -->
        <button class="lg:hidden p-2 rounded-xl hover:bg-slate-100 text-slate-500" id="sidebarClose">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-1 sidebar-nav">
        <p class="px-3 mb-3 text-xs font-semibold text-slate-400 uppercase tracking-wider sidebar-text">Main Menu</p>

        <a href="../admin/index.php" data-tooltip="Dashboard" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active bg-sky-50 text-sky-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-grid-1x2-fill text-sky-600"></i>
            </div>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="../admin/manage_announcements.php" data-tooltip="Announcements" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_announcements.php') ? 'active bg-sky-50 text-sky-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-megaphone-fill text-amber-600"></i>
            </div>
            <span class="sidebar-text">Announcements</span>
        </a>

        <a href="../admin/manage_assignments.php" data-tooltip="Assignments" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_assignments.php') ? 'active bg-sky-50 text-sky-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-file-earmark-text-fill text-emerald-600"></i>
            </div>
            <span class="sidebar-text">Assignments</span>
        </a>

        <a href="../admin/manage_timetable.php" data-tooltip="Timetables" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_timetable.php') ? 'active bg-sky-50 text-sky-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-calendar3 text-purple-600"></i>
            </div>
            <span class="sidebar-text">Timetables</span>
        </a>

        <p class="px-3 mt-6 mb-3 text-xs font-semibold text-slate-400 uppercase tracking-wider sidebar-text">Analytics</p>

        <a href="../admin/analytics.php" data-tooltip="Analytics" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'analytics.php') ? 'active bg-sky-50 text-sky-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-graph-up text-indigo-600"></i>
            </div>
            <span class="sidebar-text">Analytics</span>
        </a>

        <a href="../admin/export_reports.php" data-tooltip="Export Reports" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'export_reports.php') ? 'active bg-sky-50 text-sky-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-file-earmark-arrow-down text-teal-600"></i>
            </div>
            <span class="sidebar-text">Export Reports</span>
        </a>

        <a href="../admin/plagiarism.php" data-tooltip="Plagiarism Check" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'plagiarism.php') ? 'active bg-sky-50 text-sky-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-rose-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-shield-exclamation text-rose-600"></i>
            </div>
            <span class="sidebar-text">Plagiarism Check</span>
        </a>

        <p class="px-3 mt-6 mb-3 text-xs font-semibold text-slate-400 uppercase tracking-wider sidebar-text">Account</p>

        <a href="../admin/profile.php" data-tooltip="Profile" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active bg-sky-50 text-sky-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-person-circle text-slate-600"></i>
            </div>
            <span class="sidebar-text">Profile</span>
        </a>

        <a href="../auth/logout.php" data-tooltip="Logout" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-red-50 hover:text-red-600 mt-2">
            <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-box-arrow-right text-red-500"></i>
            </div>
            <span class="sidebar-text">Logout</span>
        </a>
    </nav>

</aside>

<!-- Sidebar Collapse Toggle Button (Outside sidebar for better positioning) -->
<button class="sidebar-toggle hidden lg:flex items-center justify-center w-8 h-8 rounded-full bg-white border border-slate-200 shadow-lg hover:shadow-xl hover:bg-slate-50 transition-all" id="sidebarCollapseBtn" title="Toggle Sidebar">
    <i class="bi bi-chevron-left text-slate-600 text-sm transition-transform duration-300" id="collapseIcon"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebarAdmin');
    const closeBtn = document.getElementById('sidebarClose');
    const overlay = document.getElementById('sidebarOverlay');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const collapseIcon = document.getElementById('collapseIcon');
    const mainWrapper = document.querySelector('.main-wrapper');

    // Mobile close button
    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', function() {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Desktop collapse toggle
    if (collapseBtn && sidebar) {
        // Check localStorage for saved state
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            if (mainWrapper) mainWrapper.classList.add('sidebar-collapsed');
            if (collapseIcon) collapseIcon.style.transform = 'rotate(180deg)';
            collapseBtn.classList.add('collapsed-pos');
        }
        // Remove init class after proper classes are applied
        document.documentElement.classList.remove('sidebar-collapsed-init');

        collapseBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            if (mainWrapper) mainWrapper.classList.toggle('sidebar-collapsed');
            collapseBtn.classList.toggle('collapsed-pos');

            // Rotate icon
            if (collapseIcon) {
                if (sidebar.classList.contains('collapsed')) {
                    collapseIcon.style.transform = 'rotate(180deg)';
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    collapseIcon.style.transform = 'rotate(0deg)';
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            }
        });
    }
});
</script>
