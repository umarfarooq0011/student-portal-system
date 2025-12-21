<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';

// Get data for dashboard
require_once '../models/Timetable.php';
require_once '../models/Announcement.php';
require_once '../models/Assignment.php';
$conn = $GLOBALS['conn'];

// Get upcoming class
$timetableModel = new Timetable();
$today = date('l');
$now = date('H:i:s');
$timetables = $timetableModel->getAll();
$allClasses = [];
while ($row = mysqli_fetch_assoc($timetables)) {
    $classDay = $row['day_of_week'];
    $classTime = $row['start_time'];
    $daysOfWeek = ['Monday'=>1,'Tuesday'=>2,'Wednesday'=>3,'Thursday'=>4,'Friday'=>5,'Saturday'=>6,'Sunday'=>7];
    $todayNum = $daysOfWeek[$today];
    $classNum = $daysOfWeek[$classDay];
    $daysUntil = ($classNum - $todayNum + 7) % 7;
    $classDate = date('Y-m-d', strtotime("+{$daysUntil} days"));
    $isToday = ($daysUntil === 0);
    $isPast = ($isToday && $classTime <= $now);
    $allClasses[] = [
        'datetime' => $classDate . ' ' . $classTime,
        'row' => $row,
        'isPast' => $isPast,
        'isToday' => $isToday
    ];
}
usort($allClasses, function($a, $b) {
    return strtotime($a['datetime']) - strtotime($b['datetime']);
});
$nextClass = null;
foreach ($allClasses as $class) {
    if (!$class['isPast']) {
        $nextClass = $class['row'];
        break;
    }
}
if (!$nextClass && !empty($allClasses)) {
    $todayPast = array_filter($allClasses, function($c) { return $c['isPast'] && $c['isToday']; });
    if (!empty($todayPast)) {
        $lastPast = end($todayPast);
        $nextClass = $lastPast['row'];
    } else {
        $nextClass = $allClasses[0]['row'];
    }
}

// Get announcements count
$announcementModel = new Announcement();
$announcements = $announcementModel->getAll();
$activeAnnouncements = array_filter($announcements, function($a) { return $a['status'] === 'Active'; });
$announcementCount = count($activeAnnouncements);

// Get pending assignments count
$student_id = $_SESSION['user_id'];
$pendingQuery = "SELECT COUNT(*) as count FROM assignments a
                 WHERE a.due_date >= CURDATE()
                 AND a.id NOT IN (SELECT assignment_id FROM submissions WHERE student_id = ?)";
$stmt = $conn->prepare($pendingQuery);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$pendingAssignments = $stmt->get_result()->fetch_assoc()['count'];

// Get submitted assignments count
$submittedQuery = "SELECT COUNT(*) as count FROM submissions WHERE student_id = ?";
$stmt = $conn->prepare($submittedQuery);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$submittedAssignments = $stmt->get_result()->fetch_assoc()['count'];

// Get total classes this week
$totalClasses = count($allClasses);
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
    <!-- Pending Assignments -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-amber-100 flex items-center justify-center">
                <i class="bi bi-hourglass-split text-lg text-amber-600"></i>
            </div>
            <?php if ($pendingAssignments > 0): ?>
            <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-md">
                Pending
            </span>
            <?php endif; ?>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $pendingAssignments; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Pending Assignments</p>
    </div>

    <!-- Submitted Assignments -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-emerald-100 flex items-center justify-center">
                <i class="bi bi-check2-circle text-lg text-emerald-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $submittedAssignments; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Submitted</p>
    </div>

    <!-- Announcements -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-violet-100 flex items-center justify-center">
                <i class="bi bi-megaphone-fill text-lg text-violet-600"></i>
            </div>
            <?php if ($announcementCount > 0): ?>
            <span class="text-xs font-semibold text-violet-600 bg-violet-50 px-2 py-1 rounded-md">
                New
            </span>
            <?php endif; ?>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $announcementCount; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Announcements</p>
    </div>

    <!-- Classes This Week -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-sky-100 flex items-center justify-center">
                <i class="bi bi-calendar3 text-lg text-sky-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $totalClasses; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Classes This Week</p>
    </div>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Upcoming Class Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Upcoming Class</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Your next scheduled class</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center">
                    <i class="bi bi-clock text-sky-600"></i>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if ($nextClass): ?>
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl bg-violet-600 flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-book-fill text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-slate-800 text-lg mb-1"><?= htmlspecialchars($nextClass['subject']) ?></h4>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class="bi bi-calendar-day text-slate-400"></i>
                            <span><?= htmlspecialchars($nextClass['day_of_week']) ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class="bi bi-clock text-slate-400"></i>
                            <span><?= date('g:i A', strtotime($nextClass['start_time'])) ?> - <?= date('g:i A', strtotime($nextClass['end_time'])) ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class="bi bi-geo-alt text-slate-400"></i>
                            <span>Room <?= htmlspecialchars($nextClass['room']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-6">
                <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-calendar-x text-2xl text-slate-400"></i>
                </div>
                <p class="text-slate-500">No upcoming class found</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Announcements Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Recent Announcements</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Latest updates from admin</p>
                </div>
                <a href="annoucements.php" class="text-xs font-semibold text-violet-600 hover:text-violet-700 flex items-center gap-1">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            <?php
            $latestAnnouncements = array_slice($activeAnnouncements, 0, 2);
            if (!empty($latestAnnouncements)):
            ?>
            <div class="space-y-4">
                <?php foreach ($latestAnnouncements as $announcement): ?>
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-100">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-megaphone text-amber-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-slate-800 truncate"><?= htmlspecialchars($announcement['title']) ?></h4>
                            <p class="text-sm text-slate-500 line-clamp-2 mt-1"><?= htmlspecialchars(substr($announcement['content'], 0, 80)) ?>...</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-6">
                <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-megaphone text-2xl text-slate-400"></i>
                </div>
                <p class="text-slate-500">No announcements yet</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Quick Actions</h3>
                <p class="text-xs text-slate-500 mt-0.5">Navigate to common tasks</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center">
                <i class="bi bi-lightning-charge-fill text-violet-600"></i>
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Submit Assignment -->
            <a href="assignments.php" class="group relative overflow-hidden rounded-lg border-2 border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition-all p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="bi bi-file-earmark-arrow-up-fill text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 mb-0.5 flex items-center gap-2">
                            Submit Assignment
                            <i class="bi bi-arrow-right text-emerald-600 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </h4>
                        <p class="text-xs text-slate-600">Upload your work</p>
                    </div>
                </div>
            </a>

            <!-- View Announcements -->
            <a href="annoucements.php" class="group relative overflow-hidden rounded-lg border-2 border-amber-200 bg-amber-50 hover:bg-amber-100 transition-all p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="bi bi-megaphone-fill text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 mb-0.5 flex items-center gap-2">
                            Announcements
                            <i class="bi bi-arrow-right text-amber-600 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </h4>
                        <p class="text-xs text-slate-600">Read latest updates</p>
                    </div>
                </div>
            </a>

            <!-- View Timetable -->
            <a href="timetable.php" class="group relative overflow-hidden rounded-lg border-2 border-sky-200 bg-sky-50 hover:bg-sky-100 transition-all p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-sky-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="bi bi-calendar3 text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 mb-0.5 flex items-center gap-2">
                            Timetable
                            <i class="bi bi-arrow-right text-sky-600 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </h4>
                        <p class="text-xs text-slate-600">View class schedule</p>
                    </div>
                </div>
            </a>

            <!-- My Profile -->
            <a href="profile.php" class="group relative overflow-hidden rounded-lg border-2 border-violet-200 bg-violet-50 hover:bg-violet-100 transition-all p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-violet-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="bi bi-person-circle text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 mb-0.5 flex items-center gap-2">
                            My Profile
                            <i class="bi bi-arrow-right text-violet-600 text-sm group-hover:translate-x-1 transition-transform"></i>
                        </h4>
                        <p class="text-xs text-slate-600">Update your details</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
