<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
require_once '../models/Analytics.php';

$analytics = new Analytics();
$student_id = $_SESSION['user_id'];

// Get student performance data
$performance = $analytics->getStudentPerformance($student_id);
$gradeTrend = $analytics->getStudentGradeTrend($student_id);

// Calculate completion rate
$completionRate = $performance['total_assignments'] > 0
    ? round(($performance['total_submissions'] / $performance['total_assignments']) * 100, 1)
    : 0;

// Prepare grade trend data for chart
$trendDates = [];
$trendGrades = [];
$trendTitles = [];
foreach ($gradeTrend as $item) {
    $trendDates[] = date('M d', strtotime($item['date']));
    $trendGrades[] = $item['grade'];
    $trendTitles[] = $item['title'];
}

// Prepare subject data for chart
$subjectLabels = array_keys($performance['subject_grades']);
$subjectGrades = array_values($performance['subject_grades']);

// Calculate grade letter
function getGradeLetter($percent) {
    if ($percent >= 90) return ['A', 'text-emerald-600', 'bg-emerald-100'];
    if ($percent >= 80) return ['B', 'text-sky-600', 'bg-sky-100'];
    if ($percent >= 70) return ['C', 'text-amber-600', 'bg-amber-100'];
    if ($percent >= 60) return ['D', 'text-orange-600', 'bg-orange-100'];
    return ['F', 'text-rose-600', 'bg-rose-100'];
}

$gradeInfo = getGradeLetter($performance['avg_grade']);
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">My Performance</h1>
        <p class="text-slate-500 text-sm mt-1">Track your academic progress and grade trends</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 border border-slate-200">
        <i class="bi bi-calendar-event text-slate-600"></i>
        <div class="text-sm">
            <p class="font-semibold text-slate-700"><?php echo date('l'); ?></p>
            <p class="text-slate-500 text-xs"><?php echo date('M d, Y'); ?></p>
        </div>
    </div>
</div>

<!-- Overview Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <!-- Average Grade -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg <?php echo $gradeInfo[2]; ?> flex items-center justify-center">
                <span class="text-lg font-bold <?php echo $gradeInfo[1]; ?>"><?php echo $gradeInfo[0]; ?></span>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $performance['avg_grade']; ?>%</h3>
        <p class="text-slate-600 text-sm font-medium">Average Grade</p>
    </div>

    <!-- Total Submissions -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-emerald-100 flex items-center justify-center">
                <i class="bi bi-check2-circle text-lg text-emerald-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $performance['total_submissions']; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Submissions</p>
    </div>

    <!-- Graded Submissions -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="bi bi-award text-lg text-indigo-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $performance['graded_submissions']; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Graded</p>
    </div>

    <!-- Pending Assignments -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-amber-100 flex items-center justify-center">
                <i class="bi bi-hourglass-split text-lg text-amber-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $performance['pending_assignments']; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Pending</p>
    </div>
</div>

<!-- Progress Section -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Completion Progress</h3>
                <p class="text-xs text-slate-500 mt-0.5">Your assignment completion rate</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center">
                <i class="bi bi-percent text-violet-600"></i>
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-slate-700">Overall Completion</span>
                <span class="text-sm font-bold text-slate-800"><?php echo $completionRate; ?>%</span>
            </div>
            <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full transition-all duration-500"
                    style="width: <?php echo $completionRate; ?>%"></div>
            </div>
            <p class="text-xs text-slate-500 mt-2">
                <?php echo $performance['total_submissions']; ?> of <?php echo $performance['total_assignments']; ?> assignments completed
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-100">
                <div class="flex items-center gap-2 mb-1">
                    <i class="bi bi-check-circle text-emerald-600"></i>
                    <span class="text-sm font-medium text-slate-700">Submitted</span>
                </div>
                <p class="text-2xl font-bold text-emerald-700"><?php echo $performance['total_submissions']; ?></p>
            </div>
            <div class="p-4 rounded-lg bg-indigo-50 border border-indigo-100">
                <div class="flex items-center gap-2 mb-1">
                    <i class="bi bi-star text-indigo-600"></i>
                    <span class="text-sm font-medium text-slate-700">Graded</span>
                </div>
                <p class="text-2xl font-bold text-indigo-700"><?php echo $performance['graded_submissions']; ?></p>
            </div>
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-100">
                <div class="flex items-center gap-2 mb-1">
                    <i class="bi bi-clock text-amber-600"></i>
                    <span class="text-sm font-medium text-slate-700">Pending</span>
                </div>
                <p class="text-2xl font-bold text-amber-700"><?php echo $performance['pending_assignments']; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Grade Trend Chart -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Grade Trend</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Your grades over time</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center">
                    <i class="bi bi-graph-up text-sky-600"></i>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if (empty($gradeTrend)): ?>
            <div class="text-center py-8 text-slate-500">
                <i class="bi bi-graph-up text-4xl mb-2 block opacity-50"></i>
                <p>No graded submissions yet</p>
            </div>
            <?php else: ?>
            <canvas id="gradeTrendChart" height="250"></canvas>
            <?php endif; ?>
        </div>
    </div>

    <!-- Subject Performance Chart -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Performance by Subject</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Average grade per subject</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="bi bi-bar-chart-fill text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if (empty($subjectLabels)): ?>
            <div class="text-center py-8 text-slate-500">
                <i class="bi bi-bar-chart text-4xl mb-2 block opacity-50"></i>
                <p>No graded submissions yet</p>
            </div>
            <?php else: ?>
            <canvas id="subjectChart" height="250"></canvas>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Submissions Table -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Recent Submissions</h3>
                <p class="text-xs text-slate-500 mt-0.5">Your latest assignment submissions</p>
            </div>
            <a href="assignments.php" class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Assignment</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Grade</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($performance['submissions'])): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        <i class="bi bi-inbox text-3xl mb-2 block"></i>
                        No submissions yet. Start by submitting your first assignment!
                    </td>
                </tr>
                <?php else: ?>
                <?php
                $recentSubs = array_slice(array_reverse($performance['submissions']), 0, 5);
                foreach ($recentSubs as $sub):
                    $isLate = strtotime($sub['submitted_at']) > strtotime($sub['due_date'] . ' 23:59:59');
                    $hasGrade = !empty($sub['grade']);
                    if ($hasGrade) {
                        $parts = explode('/', $sub['grade']);
                        $gradePercent = (count($parts) == 2 && $parts[1] > 0) ? round(($parts[0] / $parts[1]) * 100, 1) : 0;
                        $gradeClass = $gradePercent >= 70 ? 'text-emerald-600 bg-emerald-50' : ($gradePercent >= 50 ? 'text-amber-600 bg-amber-50' : 'text-rose-600 bg-rose-50');
                    }
                ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-medium text-slate-800"><?php echo htmlspecialchars($sub['title']); ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                            <?php echo htmlspecialchars($sub['subject']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-600 text-sm">
                        <?php echo date('M d, Y', strtotime($sub['submitted_at'])); ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($hasGrade): ?>
                        <span class="px-2 py-1 rounded-md text-sm font-semibold <?php echo $gradeClass; ?>">
                            <?php echo htmlspecialchars($sub['grade']); ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-400 text-sm">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($isLate): ?>
                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-rose-50 text-rose-600">Late</span>
                        <?php else: ?>
                        <span class="px-2 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-600">On Time</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($gradeTrend) || !empty($subjectLabels)): ?>
<script>
Chart.defaults.font.family = 'Inter, sans-serif';

<?php if (!empty($gradeTrend)): ?>
// Grade Trend Line Chart
const gradeTrendCtx = document.getElementById('gradeTrendChart').getContext('2d');
new Chart(gradeTrendCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($trendDates); ?>,
        datasets: [{
            label: 'Grade %',
            data: <?php echo json_encode($trendGrades); ?>,
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(139, 92, 246, 0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#8b5cf6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    title: function(context) {
                        const titles = <?php echo json_encode($trendTitles); ?>;
                        return titles[context[0].dataIndex];
                    },
                    label: function(context) {
                        return 'Grade: ' + context.raw + '%';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
<?php endif; ?>

<?php if (!empty($subjectLabels)): ?>
// Subject Performance Bar Chart
const subjectCtx = document.getElementById('subjectChart').getContext('2d');
new Chart(subjectCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($subjectLabels); ?>,
        datasets: [{
            label: 'Average Grade %',
            data: <?php echo json_encode($subjectGrades); ?>,
            backgroundColor: [
                '#8b5cf6', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#ec4899'
            ],
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
<?php endif; ?>
</script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
