<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
?>

<div class="main-content">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <h1 class="mb-2">Hi, <?php echo $_SESSION['full_name']; ?></h1>
        <p class="mb-0">Welcome to your student portal</p>
    </div>

    <div class="row g-4">
        <!-- Upcoming Class (Dynamic) -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-4">Upcoming Class</h5>
                <?php
                require_once '../models/Timetable.php';
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
                    // If today and class time has passed, show it as the most recent class
                    $isToday = ($daysUntil === 0);
                    $isPast = ($isToday && $classTime <= $now);
                    $allClasses[] = [
                        'datetime' => $classDate . ' ' . $classTime,
                        'row' => $row,
                        'isPast' => $isPast,
                        'isToday' => $isToday
                    ];
                }
                // Sort all classes by datetime ascending
                usort($allClasses, function($a, $b) {
                    return strtotime($a['datetime']) - strtotime($b['datetime']);
                });
                // Find the next class (if any upcoming today, else the most recent past class today, else next in week)
                $nextClass = null;
                foreach ($allClasses as $class) {
                    if (!$class['isPast']) {
                        $nextClass = $class['row'];
                        break;
                    }
                }
                if (!$nextClass && !empty($allClasses)) {
                    // If all classes today are past, show the latest past class today
                    $todayPast = array_filter($allClasses, function($c) { return $c['isPast'] && $c['isToday']; });
                    if (!empty($todayPast)) {
                        $lastPast = end($todayPast);
                        $nextClass = $lastPast['row'];
                    } else {
                        // Otherwise, show the earliest in the week
                        $nextClass = $allClasses[0]['row'];
                    }
                }
                if ($nextClass):
                ?>
                    <h6><?= htmlspecialchars($nextClass['subject']) ?></h6>
                    <p class="text-muted mb-0">
                        <?= htmlspecialchars($nextClass['day_of_week']) ?>
                        <br><?= date('g:i A', strtotime($nextClass['start_time'])) ?> - <?= date('g:i A', strtotime($nextClass['end_time'])) ?>
                        <br>Room: <?= htmlspecialchars($nextClass['room']) ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted mb-0">No upcoming class found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- New Announcements (Dynamic) -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-4">New Announcements</h5>
                <?php
                require_once '../models/Announcement.php';
                $announcementModel = new Announcement();
                $announcements = $announcementModel->getAll();
                $activeAnnouncements = array_filter($announcements, function($a) { return $a['status'] === 'Active'; });
                $latest = array_slice($activeAnnouncements, 0, 2);
                $count = count($latest);
                ?>
                <h3 class="mb-0"><?php echo $count; ?> new</h3>
                <a href="annoucements.php" class="btn btn-link p-0 mt-2">View Announcements</a>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="quick-links mt-4">
        <h5 class="mb-4">Quick Links</h5>
        <a href="assignments.php" class="submit-btn text-decoration-none">Submit Assignment</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
