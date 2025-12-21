<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
require_once '../models/Timetable.php';

$timetableModel = new Timetable();
$timetables = $timetableModel->getAll();
$today = date('l');

// Group timetable by days
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$schedule = [];
mysqli_data_seek($timetables, 0);
while ($row = mysqli_fetch_assoc($timetables)) {
    $schedule[$row['day_of_week']][] = $row;
}
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Timetable</h1>
        <p class="text-slate-500 text-sm mt-1">Your weekly class schedule</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-50 border border-sky-100">
        <i class="bi bi-calendar3 text-sky-600"></i>
        <span class="text-sm font-semibold text-sky-700">Today: <?= $today ?></span>
    </div>
</div>

<!-- Timetable Card -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">Day</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Room</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Teacher</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php
                mysqli_data_seek($timetables, 0);
                $hasData = false;
                while ($row = mysqli_fetch_assoc($timetables)):
                    $hasData = true;
                    $isToday = ($row['day_of_week'] === $today);
                ?>
                <tr class="<?= $isToday ? 'bg-violet-50' : 'hover:bg-slate-50' ?> transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <?php if ($isToday): ?>
                            <span class="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></span>
                            <?php endif; ?>
                            <span class="font-semibold <?= $isToday ? 'text-violet-700' : 'text-slate-700' ?>"><?= htmlspecialchars($row['day_of_week']) ?></span>
                            <?php if ($isToday): ?>
                            <span class="text-xs font-medium text-violet-600 bg-violet-100 px-2 py-0.5 rounded-full">Today</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-book text-violet-600"></i>
                            </div>
                            <span class="font-medium text-slate-800"><?= htmlspecialchars($row['subject']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-slate-600">
                            <i class="bi bi-clock text-slate-400"></i>
                            <span><?= date("g:i A", strtotime($row['start_time'])) ?> - <?= date("g:i A", strtotime($row['end_time'])) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium">
                            <i class="bi bi-geo-alt text-slate-500"></i>
                            <?= htmlspecialchars($row['room']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-sky-100 flex items-center justify-center">
                                <i class="bi bi-person text-sky-600 text-sm"></i>
                            </div>
                            <span class="text-slate-700"><?= htmlspecialchars($row['teacher']) ?></span>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>

                <?php if (!$hasData): ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <i class="bi bi-calendar-x text-2xl text-slate-400"></i>
                        </div>
                        <h3 class="font-semibold text-slate-800 mb-1">No Classes Scheduled</h3>
                        <p class="text-sm text-slate-500">The timetable is empty. Please check back later.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Day Cards (Mobile View Enhancement) -->
<div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:hidden">
    <?php foreach ($days as $day):
        if (!isset($schedule[$day]) || empty($schedule[$day])) continue;
        $isToday = ($day === $today);
    ?>
    <div class="bg-white rounded-xl border <?= $isToday ? 'border-violet-300 ring-2 ring-violet-100' : 'border-slate-200' ?> overflow-hidden">
        <div class="px-4 py-3 <?= $isToday ? 'bg-violet-600' : 'bg-slate-100' ?> border-b border-slate-100">
            <div class="flex items-center justify-between">
                <h4 class="font-bold <?= $isToday ? 'text-white' : 'text-slate-800' ?>"><?= $day ?></h4>
                <?php if ($isToday): ?>
                <span class="text-xs font-medium text-violet-200">Today</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="p-4 space-y-3">
            <?php foreach ($schedule[$day] as $class): ?>
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                <h5 class="font-semibold text-slate-800 mb-1"><?= htmlspecialchars($class['subject']) ?></h5>
                <div class="text-xs text-slate-500 space-y-1">
                    <div class="flex items-center gap-1">
                        <i class="bi bi-clock"></i>
                        <?= date("g:i A", strtotime($class['start_time'])) ?> - <?= date("g:i A", strtotime($class['end_time'])) ?>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="bi bi-geo-alt"></i>
                        Room <?= htmlspecialchars($class['room']) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
