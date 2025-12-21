<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';

// Get statistics
require_once '../models/Assignment.php';
require_once '../models/Submission.php';
require_once '../models/Announcement.php';
$conn = $GLOBALS['conn'];

$lastWeek = date('Y-m-d', strtotime('-7 days'));

// Recent assignments count
$recentAssignmentsQuery = "SELECT COUNT(*) as count FROM assignments WHERE created_at >= '$lastWeek'";
$recentAssignments = mysqli_fetch_assoc(mysqli_query($conn, $recentAssignmentsQuery))['count'];

// Pending assignments
$pendingAssignmentsQuery = "SELECT a.id FROM assignments a LEFT JOIN submissions s ON a.id = s.assignment_id GROUP BY a.id HAVING SUM(CASE WHEN s.id IS NULL THEN 1 ELSE 0 END) > 0";
$pendingAssignmentsResult = mysqli_query($conn, $pendingAssignmentsQuery);
$pendingAssignments = mysqli_num_rows($pendingAssignmentsResult);

// Recent announcements
$recentAnnouncementsQuery = "SELECT COUNT(*) as count FROM announcements WHERE created_at >= '$lastWeek'";
$recentAnnouncements = mysqli_fetch_assoc(mysqli_query($conn, $recentAnnouncementsQuery))['count'];

// Total students count
$totalStudentsQuery = "SELECT COUNT(*) as count FROM users WHERE role = 'student'";
$totalStudents = mysqli_fetch_assoc(mysqli_query($conn, $totalStudentsQuery))['count'];

// Total assignments
$totalAssignmentsQuery = "SELECT COUNT(*) as count FROM assignments";
$totalAssignments = mysqli_fetch_assoc(mysqli_query($conn, $totalAssignmentsQuery))['count'];

// Total announcements
$totalAnnouncementsQuery = "SELECT COUNT(*) as count FROM announcements";
$totalAnnouncements = mysqli_fetch_assoc(mysqli_query($conn, $totalAnnouncementsQuery))['count'];

// Recent submissions
$recentSubmissionsQuery = "SELECT COUNT(*) as count FROM submissions WHERE submitted_at >= '$lastWeek'";
$recentSubmissions = mysqli_fetch_assoc(mysqli_query($conn, $recentSubmissionsQuery))['count'];
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-slate-500 text-sm mt-1">Welcome back, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>! Here's your overview.</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 border border-slate-200">
        <i class="bi bi-calendar-event text-slate-600"></i>
        <div class="text-sm">
            <p class="font-semibold text-slate-700"><?php echo date('l'); ?></p>
            <p class="text-slate-500 text-xs"><?php echo date('M d, Y'); ?></p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <!-- Total Students -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-sky-100 flex items-center justify-center">
                <i class="bi bi-people-fill text-lg text-sky-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $totalStudents; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Total Students</p>
    </div>

    <!-- Total Assignments -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-emerald-100 flex items-center justify-center">
                <i class="bi bi-file-earmark-text-fill text-lg text-emerald-600"></i>
            </div>
            <?php if ($recentAssignments > 0): ?>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
                +<?php echo $recentAssignments; ?> this week
            </span>
            <?php endif; ?>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $totalAssignments; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Total Assignments</p>
    </div>

    <!-- Pending Submissions -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-amber-100 flex items-center justify-center">
                <i class="bi bi-hourglass-split text-lg text-amber-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $pendingAssignments; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Pending Submissions</p>
    </div>

    <!-- Recent Submissions -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-purple-100 flex items-center justify-center">
                <i class="bi bi-check2-circle text-lg text-purple-600"></i>
            </div>
            <?php if ($recentSubmissions > 0): ?>
            <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-md">
                +<?php echo $recentSubmissions; ?> this week
            </span>
            <?php endif; ?>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $recentSubmissions; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Recent Submissions</p>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Quick Actions</h3>
                <p class="text-xs text-slate-500 mt-0.5">Common administrative tasks</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center">
                <i class="bi bi-lightning-charge-fill text-sky-600"></i>
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Announcements -->
            <a href="manage_announcements.php" class="group relative overflow-hidden rounded-lg border-2 border-amber-200 bg-amber-50 hover:bg-amber-100 transition-all p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="bi bi-megaphone-fill text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 mb-0.5 flex items-center gap-2">
                            Announcements
                            <i class="bi bi-arrow-right text-amber-600 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </h4>
                        <p class="text-xs text-slate-600">Post updates and notices</p>
                    </div>
                </div>
            </a>

            <!-- Assignments -->
            <a href="manage_assignments.php" class="group relative overflow-hidden rounded-lg border-2 border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition-all p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="bi bi-file-earmark-text-fill text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 mb-0.5 flex items-center gap-2">
                            Assignments
                            <i class="bi bi-arrow-right text-emerald-600 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </h4>
                        <p class="text-xs text-slate-600">Create and manage tasks</p>
                    </div>
                </div>
            </a>

            <!-- Timetable -->
            <a href="manage_timetable.php" class="group relative overflow-hidden rounded-lg border-2 border-purple-200 bg-purple-50 hover:bg-purple-100 transition-all p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-purple-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="bi bi-calendar3 text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 mb-0.5 flex items-center gap-2">
                            Timetable
                            <i class="bi bi-arrow-right text-purple-600 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </h4>
                        <p class="text-xs text-slate-600">Manage class schedules</p>
                    </div>
                </div>
            </a>

            <!-- Profile -->
            <a href="profile.php" class="group relative overflow-hidden rounded-lg border-2 border-sky-200 bg-sky-50 hover:bg-sky-100 transition-all p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-sky-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="bi bi-person-circle text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 mb-0.5 flex items-center gap-2">
                            Profile Settings
                            <i class="bi bi-arrow-right text-sky-600 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </h4>
                        <p class="text-xs text-slate-600">Update account details</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Weekly Summary -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Weekly Summary</h3>
                <p class="text-xs text-slate-500 mt-0.5">Activity from the last 7 days</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center">
                <i class="bi bi-bar-chart-line text-slate-600"></i>
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 rounded-lg bg-sky-50 border border-sky-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                        <i class="bi bi-file-earmark-plus text-sky-600 text-sm"></i>
                    </div>
                    <span class="text-2xl font-bold text-sky-700"><?php echo $recentAssignments; ?></span>
                </div>
                <p class="text-xs font-medium text-slate-700">New Assignments</p>
            </div>

            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <i class="bi bi-check2-circle text-emerald-600 text-sm"></i>
                    </div>
                    <span class="text-2xl font-bold text-emerald-700"><?php echo $recentSubmissions; ?></span>
                </div>
                <p class="text-xs font-medium text-slate-700">Submissions Received</p>
            </div>

            <div class="p-4 rounded-lg bg-purple-50 border border-purple-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="bi bi-megaphone text-purple-600 text-sm"></i>
                    </div>
                    <span class="text-2xl font-bold text-purple-700"><?php echo $recentAnnouncements; ?></span>
                </div>
                <p class="text-xs font-medium text-slate-700">New Announcements</p>
            </div>

            <div class="p-4 rounded-lg bg-amber-50 border border-amber-100">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                        <i class="bi bi-hourglass-split text-amber-600 text-sm"></i>
                    </div>
                    <span class="text-2xl font-bold text-amber-700"><?php echo $totalAnnouncements; ?></span>
                </div>
                <p class="text-xs font-medium text-slate-700">Total Announcements</p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../admin_includes/footer.php'; ?>
