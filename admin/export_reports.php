<?php
require_once '../auth/authsession.php';
require_once '../models/Analytics.php';

$analytics = new Analytics();

// Handle export requests
if (isset($_GET['export'])) {
    $exportType = $_GET['export'];

    if ($exportType === 'csv_grades') {
        // Export all grades to CSV
        $data = $analytics->getAllSubmissionsForExport();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="grades_report_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        // Add BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header row
        fputcsv($output, ['Student Name', 'Email', 'Assignment', 'Subject', 'Submitted At', 'Grade', 'Feedback', 'Status']);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['student_name'],
                $row['email'],
                $row['assignment_title'],
                $row['subject'],
                $row['submitted_at'],
                $row['grade'] ?: 'Not Graded',
                $row['feedback'] ?: '',
                $row['status']
            ]);
        }

        fclose($output);
        exit;
    }

    if ($exportType === 'csv_students') {
        // Export student rankings to CSV
        $data = $analytics->getTopStudents(100);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="student_rankings_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['Rank', 'Student Name', 'Total Submissions', 'Graded Submissions', 'Average Grade (%)']);

        $rank = 1;
        foreach ($data as $row) {
            fputcsv($output, [
                $rank++,
                $row['full_name'],
                $row['total_submissions'],
                $row['graded_submissions'],
                round($row['avg_grade'] ?? 0, 1)
            ]);
        }

        fclose($output);
        exit;
    }

    if ($exportType === 'csv_subjects') {
        // Export subject stats to CSV
        $data = $analytics->getSubjectStats();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="subject_performance_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['Subject', 'Total Assignments', 'Total Submissions', 'Average Grade (%)']);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['subject'],
                $row['total_assignments'],
                $row['total_submissions'],
                round($row['avg_grade'] ?? 0, 1)
            ]);
        }

        fclose($output);
        exit;
    }
}

require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';

$stats = $analytics->getOverallStats();
$topStudents = $analytics->getTopStudents(10);
$subjectStats = $analytics->getSubjectStats();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Export Reports</h1>
        <p class="text-slate-500 text-sm mt-1">Download reports in CSV or print as PDF</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 border border-slate-200">
        <i class="bi bi-calendar-event text-slate-600"></i>
        <div class="text-sm">
            <p class="font-semibold text-slate-700"><?php echo date('l'); ?></p>
            <p class="text-slate-500 text-xs"><?php echo date('M d, Y'); ?></p>
        </div>
    </div>
</div>

<!-- Export Options Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Grades Report -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-6">
            <div class="w-14 h-14 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                <i class="bi bi-file-earmark-spreadsheet text-2xl text-emerald-600"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">All Grades Report</h3>
            <p class="text-sm text-slate-500 mb-4">Export all student submissions with grades, feedback, and submission status.</p>
            <div class="flex gap-2">
                <a href="?export=csv_grades" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition-colors text-sm font-medium">
                    <i class="bi bi-filetype-csv"></i> CSV
                </a>
                <button onclick="printReport('grades')" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 transition-colors text-sm font-medium">
                    <i class="bi bi-printer"></i> PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Student Rankings -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-6">
            <div class="w-14 h-14 rounded-xl bg-indigo-100 flex items-center justify-center mb-4">
                <i class="bi bi-trophy text-2xl text-indigo-600"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Student Rankings</h3>
            <p class="text-sm text-slate-500 mb-4">Export student performance rankings with average grades and submission counts.</p>
            <div class="flex gap-2">
                <a href="?export=csv_students" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors text-sm font-medium">
                    <i class="bi bi-filetype-csv"></i> CSV
                </a>
                <button onclick="printReport('students')" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 transition-colors text-sm font-medium">
                    <i class="bi bi-printer"></i> PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Subject Performance -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-6">
            <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center mb-4">
                <i class="bi bi-book text-2xl text-purple-600"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Subject Performance</h3>
            <p class="text-sm text-slate-500 mb-4">Export performance statistics grouped by subject with average grades.</p>
            <div class="flex gap-2">
                <a href="?export=csv_subjects" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 transition-colors text-sm font-medium">
                    <i class="bi bi-filetype-csv"></i> CSV
                </a>
                <button onclick="printReport('subjects')" class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 transition-colors text-sm font-medium">
                    <i class="bi bi-printer"></i> PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Overview -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Quick Statistics</h3>
                <p class="text-xs text-slate-500 mt-0.5">Current system overview</p>
            </div>
            <div class="w-9 h-9 rounded-lg bg-sky-100 flex items-center justify-center">
                <i class="bi bi-bar-chart-line text-sky-600"></i>
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-4 rounded-lg bg-sky-50 border border-sky-100 text-center">
                <p class="text-3xl font-bold text-sky-700"><?php echo $stats['total_students']; ?></p>
                <p class="text-sm text-slate-600 mt-1">Total Students</p>
            </div>
            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-100 text-center">
                <p class="text-3xl font-bold text-emerald-700"><?php echo $stats['total_submissions']; ?></p>
                <p class="text-sm text-slate-600 mt-1">Total Submissions</p>
            </div>
            <div class="p-4 rounded-lg bg-indigo-50 border border-indigo-100 text-center">
                <p class="text-3xl font-bold text-indigo-700"><?php echo $stats['graded_submissions']; ?></p>
                <p class="text-sm text-slate-600 mt-1">Graded</p>
            </div>
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-100 text-center">
                <p class="text-3xl font-bold text-amber-700"><?php echo $stats['avg_grade']; ?>%</p>
                <p class="text-sm text-slate-600 mt-1">Avg Grade</p>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Print Templates -->
<?php
$allGrades = $analytics->getAllSubmissionsForExport();
$allStudents = $analytics->getTopStudents(100);
$totalGraded = count(array_filter($allGrades, function($r) { return !empty($r['grade']); }));
$totalOnTime = count(array_filter($allGrades, function($r) { return $r['status'] === 'On Time'; }));
?>
<div id="printArea" class="hidden">
    <!-- Grades Report Template -->
    <div id="gradesReport">
        <div class="report-header">
            <div class="logo-section">
                <div class="logo-icon">SP</div>
                <div class="logo-text">
                    <h1>Student Portal</h1>
                    <p>Academic Management System</p>
                </div>
            </div>
            <div class="report-meta">
                <h2>Grades Report</h2>
                <p>Generated: <?php echo date('F d, Y - h:i A'); ?></p>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <span class="stat-number"><?php echo count($allGrades); ?></span>
                <span class="stat-label">Total Submissions</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo $totalGraded; ?></span>
                <span class="stat-label">Graded</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo $totalOnTime; ?></span>
                <span class="stat-label">On Time</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo $stats['avg_grade']; ?>%</span>
                <span class="stat-label">Avg Grade</span>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Assignment</th>
                    <th>Subject</th>
                    <th>Submitted</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($allGrades as $row): ?>
                <tr>
                    <td class="center"><?php echo $i++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['student_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['assignment_title']); ?></td>
                    <td><span class="badge"><?php echo htmlspecialchars($row['subject']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></td>
                    <td class="center"><span class="grade <?php echo empty($row['grade']) ? 'pending' : ''; ?>"><?php echo $row['grade'] ?: 'Pending'; ?></span></td>
                    <td class="center"><span class="status <?php echo $row['status'] === 'On Time' ? 'on-time' : 'late'; ?>"><?php echo $row['status']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="report-footer">
            <p>Student Portal System &copy; <?php echo date('Y'); ?> | This report contains <?php echo count($allGrades); ?> records</p>
        </div>
    </div>

    <!-- Students Report Template -->
    <div id="studentsReport">
        <div class="report-header">
            <div class="logo-section">
                <div class="logo-icon">SP</div>
                <div class="logo-text">
                    <h1>Student Portal</h1>
                    <p>Academic Management System</p>
                </div>
            </div>
            <div class="report-meta">
                <h2>Student Rankings</h2>
                <p>Generated: <?php echo date('F d, Y - h:i A'); ?></p>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <span class="stat-number"><?php echo $stats['total_students']; ?></span>
                <span class="stat-label">Total Students</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo count($allStudents); ?></span>
                <span class="stat-label">Active Students</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo $stats['total_submissions']; ?></span>
                <span class="stat-label">Submissions</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo $stats['avg_grade']; ?>%</span>
                <span class="stat-label">Avg Grade</span>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Student Name</th>
                    <th>Total Submissions</th>
                    <th>Graded</th>
                    <th>Average Grade</th>
                    <th>Performance</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($allStudents as $row):
                    $avg = round($row['avg_grade'] ?? 0, 1);
                    $perfClass = $avg >= 80 ? 'excellent' : ($avg >= 60 ? 'good' : 'needs-improvement');
                    $perfText = $avg >= 80 ? 'Excellent' : ($avg >= 60 ? 'Good' : 'Needs Improvement');
                ?>
                <tr>
                    <td class="center">
                        <?php if ($rank <= 3): ?>
                            <span class="rank-badge rank-<?php echo $rank; ?>"><?php echo $rank; ?></span>
                        <?php else: ?>
                            <?php echo $rank; ?>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                    <td class="center"><?php echo $row['total_submissions']; ?></td>
                    <td class="center"><?php echo $row['graded_submissions']; ?></td>
                    <td class="center"><span class="grade-percent"><?php echo $avg; ?>%</span></td>
                    <td class="center"><span class="performance <?php echo $perfClass; ?>"><?php echo $perfText; ?></span></td>
                </tr>
                <?php $rank++; endforeach; ?>
            </tbody>
        </table>

        <div class="report-footer">
            <p>Student Portal System &copy; <?php echo date('Y'); ?> | This report contains <?php echo count($allStudents); ?> students</p>
        </div>
    </div>

    <!-- Subjects Report Template -->
    <div id="subjectsReport">
        <div class="report-header">
            <div class="logo-section">
                <div class="logo-icon">SP</div>
                <div class="logo-text">
                    <h1>Student Portal</h1>
                    <p>Academic Management System</p>
                </div>
            </div>
            <div class="report-meta">
                <h2>Subject Performance</h2>
                <p>Generated: <?php echo date('F d, Y - h:i A'); ?></p>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <span class="stat-number"><?php echo count($subjectStats); ?></span>
                <span class="stat-label">Total Subjects</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo $stats['total_assignments']; ?></span>
                <span class="stat-label">Assignments</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo $stats['total_submissions']; ?></span>
                <span class="stat-label">Submissions</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo $stats['avg_grade']; ?>%</span>
                <span class="stat-label">Avg Grade</span>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Total Assignments</th>
                    <th>Total Submissions</th>
                    <th>Average Grade</th>
                    <th>Performance</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach ($subjectStats as $row):
                    $avg = round($row['avg_grade'] ?? 0, 1);
                    $perfClass = $avg >= 80 ? 'excellent' : ($avg >= 60 ? 'good' : ($avg > 0 ? 'needs-improvement' : ''));
                    $perfText = $avg >= 80 ? 'Excellent' : ($avg >= 60 ? 'Good' : ($avg > 0 ? 'Needs Improvement' : 'No Data'));
                ?>
                <tr>
                    <td class="center"><?php echo $i++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['subject']); ?></strong></td>
                    <td class="center"><?php echo $row['total_assignments']; ?></td>
                    <td class="center"><?php echo $row['total_submissions']; ?></td>
                    <td class="center"><span class="grade-percent"><?php echo $avg; ?>%</span></td>
                    <td class="center"><span class="performance <?php echo $perfClass; ?>"><?php echo $perfText; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="report-footer">
            <p>Student Portal System &copy; <?php echo date('Y'); ?> | This report contains <?php echo count($subjectStats); ?> subjects</p>
        </div>
    </div>
</div>

<script>
function printReport(type) {
    let content = '';
    if (type === 'grades') {
        content = document.getElementById('gradesReport').innerHTML;
    } else if (type === 'students') {
        content = document.getElementById('studentsReport').innerHTML;
    } else if (type === 'subjects') {
        content = document.getElementById('subjectsReport').innerHTML;
    }

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Report - Student Portal</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; padding: 30px; background: #fff; }

                .report-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 3px solid #0ea5e9; margin-bottom: 25px; }
                .logo-section { display: flex; align-items: center; gap: 12px; }
                .logo-icon { width: 50px; height: 50px; background: linear-gradient(135deg, #0ea5e9, #6366f1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px; }
                .logo-text h1 { font-size: 20px; color: #0f172a; margin-bottom: 2px; }
                .logo-text p { font-size: 11px; color: #64748b; }
                .report-meta { text-align: right; }
                .report-meta h2 { font-size: 22px; color: #0ea5e9; margin-bottom: 4px; }
                .report-meta p { font-size: 11px; color: #64748b; }

                .stats-row { display: flex; gap: 15px; margin-bottom: 25px; }
                .stat-box { flex: 1; background: linear-gradient(135deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; text-align: center; }
                .stat-number { display: block; font-size: 24px; font-weight: 700; color: #0ea5e9; }
                .stat-label { display: block; font-size: 11px; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }

                .data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 12px; }
                .data-table thead tr { background: linear-gradient(135deg, #0ea5e9, #6366f1); }
                .data-table th { padding: 12px 10px; text-align: left; color: white; font-weight: 600; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; }
                .data-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
                .data-table tbody tr:hover { background: #f8fafc; }
                .data-table tbody tr:nth-child(even) { background: #fafafa; }
                .center { text-align: center; }

                .badge { background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 500; }
                .grade { font-weight: 600; color: #059669; }
                .grade.pending { color: #d97706; font-style: italic; }
                .status { padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; }
                .status.on-time { background: #dcfce7; color: #166534; }
                .status.late { background: #fee2e2; color: #991b1b; }

                .rank-badge { display: inline-flex; width: 24px; height: 24px; border-radius: 50%; align-items: center; justify-content: center; font-weight: bold; font-size: 11px; }
                .rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; }
                .rank-2 { background: linear-gradient(135deg, #9ca3af, #6b7280); color: white; }
                .rank-3 { background: linear-gradient(135deg, #f97316, #ea580c); color: white; }

                .grade-percent { font-weight: 700; color: #6366f1; }
                .performance { padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; }
                .performance.excellent { background: #dcfce7; color: #166534; }
                .performance.good { background: #e0f2fe; color: #0369a1; }
                .performance.needs-improvement { background: #fef3c7; color: #92400e; }

                .report-footer { border-top: 2px solid #e2e8f0; padding-top: 15px; text-align: center; }
                .report-footer p { font-size: 11px; color: #94a3b8; }

                @media print {
                    body { padding: 15px; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                    .data-table { page-break-inside: auto; }
                    .data-table tr { page-break-inside: avoid; page-break-after: auto; }
                }
            </style>
        </head>
        <body>
            ${content}
            <script>
                window.onload = function() {
                    setTimeout(function() { window.print(); }, 300);
                    window.onafterprint = function() { window.close(); };
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>

<?php require_once '../admin_includes/footer.php'; ?>
