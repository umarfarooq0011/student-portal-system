<!-- Premium Sidebar -->
<aside class="sidebar bg-white/80 backdrop-blur-xl border-r border-slate-200/60 shadow-xl" id="sidebarStudent">
    <!-- Logo Section -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100">
        <a href="../dashboard/index.php" class="flex items-center gap-3 sidebar-logo-link">
            <div class="w-10 h-10 rounded-xl bg-violet-600 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-mortarboard-fill text-white text-lg"></i>
            </div>
            <div class="sidebar-text">
                <h1 class="text-lg font-bold text-slate-800">Student</h1>
                <p class="text-xs text-slate-500 -mt-0.5">Portal</p>
            </div>
        </a>
        <!-- Close button for mobile -->
        <button class="lg:hidden p-2 rounded-xl hover:bg-slate-100 text-slate-500" id="sidebarClose">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-1 sidebar-nav">
        <p class="px-3 mb-3 text-xs font-semibold text-slate-400 uppercase tracking-wider sidebar-text">Menu</p>

        <a href="../dashboard/index.php" data-tooltip="Dashboard" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active bg-violet-50 text-violet-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-house-door-fill text-violet-600"></i>
            </div>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="../dashboard/annoucements.php" data-tooltip="Announcements" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'annoucements.php' || basename($_SERVER['PHP_SELF']) == 'announcements.php') ? 'active bg-violet-50 text-violet-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-megaphone-fill text-amber-600"></i>
            </div>
            <span class="sidebar-text">Announcements</span>
        </a>

        <a href="../dashboard/assignments.php" data-tooltip="Assignments" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'assignments.php') ? 'active bg-violet-50 text-violet-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-file-earmark-text-fill text-emerald-600"></i>
            </div>
            <span class="sidebar-text">Assignments</span>
        </a>

        <a href="../dashboard/timetable.php" data-tooltip="Timetable" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'timetable.php') ? 'active bg-violet-50 text-violet-700 font-semibold' : ''; ?>">
            <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-calendar3 text-sky-600"></i>
            </div>
            <span class="sidebar-text">Timetable</span>
        </a>

        <p class="px-3 mt-6 mb-3 text-xs font-semibold text-slate-400 uppercase tracking-wider sidebar-text">Account</p>

        <a href="../dashboard/profile.php" data-tooltip="Profile" class="nav-link-item flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100/80 <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active bg-violet-50 text-violet-700 font-semibold' : ''; ?>">
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

<!-- Sidebar Collapse Toggle Button -->
<button class="sidebar-toggle hidden lg:flex items-center justify-center w-8 h-8 rounded-full bg-white border border-slate-200 shadow-lg hover:shadow-xl hover:bg-slate-50 transition-all" id="sidebarCollapseBtn" title="Toggle Sidebar">
    <i class="bi bi-chevron-left text-slate-600 text-sm transition-transform duration-300" id="collapseIcon"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebarStudent');
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
        const isCollapsed = localStorage.getItem('studentSidebarCollapsed') === 'true';
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
                    localStorage.setItem('studentSidebarCollapsed', 'true');
                } else {
                    collapseIcon.style.transform = 'rotate(0deg)';
                    localStorage.setItem('studentSidebarCollapsed', 'false');
                }
            }
        });
    }
});
</script>
