<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../models/Analytics.php';

$analytics = new Analytics();
$stats = $analytics->getOverallStats();
$gradeDistribution = $analytics->getGradeDistribution();
$submissionTrends = $analytics->getSubmissionTrends(30);
$topStudents = $analytics->getTopStudents(10);
$subjectStats = $analytics->getSubjectStats();
$weeklyPattern = $analytics->getWeeklyPattern();

// Prepare data for charts
$gradeLabels = [];
$gradeCounts = [];
$gradeColors = [
    'A' => '#10b981',
    'B' => '#0ea5e9',
    'C' => '#f59e0b',
    'D' => '#f97316',
    'F' => '#ef4444',
    'Ungraded' => '#94a3b8'
];

foreach ($gradeDistribution as $grade) {
    $gradeLabels[] = $grade['letter_grade'];
    $gradeCounts[] = $grade['count'];
}

// Submission trends data
$trendDates = [];
$trendCounts = [];
foreach ($submissionTrends as $trend) {
    $trendDates[] = date('M d', strtotime($trend['date']));
    $trendCounts[] = $trend['count'];
}

// Subject stats data
$subjectLabels = [];
$subjectSubmissions = [];
$subjectGrades = [];
foreach ($subjectStats as $subject) {
    $subjectLabels[] = $subject['subject'];
    $subjectSubmissions[] = $subject['total_submissions'];
    $subjectGrades[] = round($subject['avg_grade'] ?? 0, 1);
}

// Weekly pattern data
$weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$weekCounts = array_fill(0, 7, 0);
foreach ($weeklyPattern as $day) {
    $weekCounts[$day['day_num'] - 1] = $day['count'];
}
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Analytics Dashboard</h1>
        <p class="text-slate-500 text-sm mt-1">Comprehensive insights into student performance and submissions</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="export_reports.php" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 text-white hover:bg-teal-700 transition-colors text-sm font-medium">
            <i class="bi bi-download"></i>
            Export Reports
        </a>
    </div>
</div>

<!-- Overview Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <!-- Total Students -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-sky-100 flex items-center justify-center">
                <i class="bi bi-people-fill text-lg text-sky-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $stats['total_students']; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Total Students</p>
    </div>

    <!-- Total Submissions -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-emerald-100 flex items-center justify-center">
                <i class="bi bi-file-earmark-check text-lg text-emerald-600"></i>
            </div>
            <?php if ($stats['submissions_this_week'] > 0): ?>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
                +<?php echo $stats['submissions_this_week']; ?> this week
            </span>
            <?php endif; ?>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $stats['total_submissions']; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Total Submissions</p>
    </div>

    <!-- Average Grade -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="bi bi-graph-up-arrow text-lg text-indigo-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $stats['avg_grade']; ?>%</h3>
        <p class="text-slate-600 text-sm font-medium">Average Grade</p>
    </div>

    <!-- Late Submissions -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-11 h-11 rounded-lg bg-rose-100 flex items-center justify-center">
                <i class="bi bi-clock-history text-lg text-rose-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1"><?php echo $stats['late_submissions']; ?></h3>
        <p class="text-slate-600 text-sm font-medium">Late Submissions</p>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Submission Trends Chart -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Submission Trends</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Last 30 days</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center">
                    <i class="bi bi-graph-up text-sky-600"></i>
                </div>
            </div>
        </div>
        <div class="p-6">
            <canvas id="submissionTrendsChart" height="250"></canvas>
        </div>
    </div>

    <!-- Grade Distribution Chart -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Grade Distribution</h3>
                    <p class="text-xs text-slate-500 mt-0.5">All graded submissions</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="bi bi-pie-chart-fill text-emerald-600"></i>
                </div>
            </div>
        </div>
        <div class="p-6 flex justify-center">
            <div style="max-width: 300px; width: 100%;">
                <canvas id="gradeDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Subject Performance Chart -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Subject Performance</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Average grades by subject</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="bi bi-bar-chart-fill text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="p-6">
            <canvas id="subjectPerformanceChart" height="250"></canvas>
        </div>
    </div>

    <!-- Weekly Pattern Chart -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Weekly Submission Pattern</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Submissions by day of week</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="bi bi-calendar-week text-amber-600"></i>
                </div>
            </div>
        </div>
        <div class="p-6">
            <canvas id="weeklyPatternChart" height="250"></canvas>
        </div>
    </div>
</div>

<!-- Top Students Table -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Top Performing Students</h3>
                <p class="text-xs text-slate-500 mt-0.5">Based on average grade and submission count</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                <i class="bi bi-trophy-fill text-indigo-600"></i>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Submissions</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Graded</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Avg Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($topStudents)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        <i class="bi bi-inbox text-3xl mb-2 block"></i>
                        No graded submissions yet
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($topStudents as $index => $student): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <?php if ($index < 3): ?>
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                            <?php echo $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-slate-200 text-slate-700' : 'bg-orange-100 text-orange-700'); ?>">
                            <?php echo $index + 1; ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-600 font-medium"><?php echo $index + 1; ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <?php if (!empty($student['profile_image'])): ?>
                            <img src="../assets/uploads/profile_photos/<?php echo htmlspecialchars($student['profile_image']); ?>"
                                class="w-10 h-10 rounded-full object-cover" alt="">
                            <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold"><?php echo strtoupper(substr($student['full_name'], 0, 1)); ?></span>
                            </div>
                            <?php endif; ?>
                            <span class="font-medium text-slate-800"><?php echo htmlspecialchars($student['full_name']); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-slate-600"><?php echo $student['total_submissions']; ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-slate-600"><?php echo $student['graded_submissions']; ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $avg = round($student['avg_grade'] ?? 0, 1);
                        $colorClass = $avg >= 90 ? 'text-emerald-600 bg-emerald-50' :
                                     ($avg >= 80 ? 'text-sky-600 bg-sky-50' :
                                     ($avg >= 70 ? 'text-amber-600 bg-amber-50' :
                                     ($avg >= 60 ? 'text-orange-600 bg-orange-50' : 'text-rose-600 bg-rose-50')));
                        ?>
                        <span class="px-2 py-1 rounded-md text-sm font-semibold <?php echo $colorClass; ?>">
                            <?php echo $avg; ?>%
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Chart.js default settings
Chart.defaults.font.family = 'Inter, sans-serif';

// Submission Trends Line Chart
const submissionTrendsCtx = document.getElementById('submissionTrendsChart').getContext('2d');
new Chart(submissionTrendsCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($trendDates); ?>,
        datasets: [{
            label: 'Submissions',
            data: <?php echo json_encode($trendCounts); ?>,
            borderColor: '#0ea5e9',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#0ea5e9',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
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
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// Grade Distribution Pie Chart
const gradeDistCtx = document.getElementById('gradeDistributionChart').getContext('2d');
new Chart(gradeDistCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($gradeLabels); ?>,
        datasets: [{
            data: <?php echo json_encode($gradeCounts); ?>,
            backgroundColor: [
                <?php
                $colors = [];
                foreach ($gradeLabels as $label) {
                    $colors[] = "'" . ($gradeColors[$label] ?? '#94a3b8') . "'";
                }
                echo implode(',', $colors);
                ?>
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 20 }
            }
        },
        cutout: '60%'
    }
});

// Subject Performance Bar Chart
const subjectCtx = document.getElementById('subjectPerformanceChart').getContext('2d');
new Chart(subjectCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($subjectLabels); ?>,
        datasets: [{
            label: 'Average Grade %',
            data: <?php echo json_encode($subjectGrades); ?>,
            backgroundColor: '#8b5cf6',
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

// Weekly Pattern Bar Chart
const weeklyCtx = document.getElementById('weeklyPatternChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($weekDays); ?>,
        datasets: [{
            label: 'Submissions',
            data: <?php echo json_encode($weekCounts); ?>,
            backgroundColor: [
                '#f43f5e', '#f59e0b', '#10b981', '#0ea5e9', '#8b5cf6', '#ec4899', '#6366f1'
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
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>

<?php require_once '../admin_includes/footer.php'; ?>
