<?php
// Handle feedback/grade POST before any output
require_once '../auth/authsession.php';
$conn = $GLOBALS['conn'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'])) {
    $assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
    $submission_id = intval($_POST['submission_id']);
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];
    $stmt2 = $conn->prepare("UPDATE submissions SET grade=?, feedback=? WHERE id=?");
    $stmt2->bind_param("ssi", $grade, $feedback, $submission_id);
    $stmt2->execute();
    header("Location: view_submissions.php?assignment_id=$assignment_id&success=Feedback updated successfully");
    exit;
}

require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../models/Submission.php';
require_once '../models/Assignment.php';

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
$assignment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM assignments WHERE id=$assignment_id"));

// Fetch submissions with student full name
$stmt = $conn->prepare("SELECT s.*, u.full_name as student_name FROM submissions s JOIN users u ON s.student_id = u.id WHERE s.assignment_id=?");
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$submissions = $stmt->get_result();
$submissionData = [];
while ($row = $submissions->fetch_assoc()) {
    $submissionData[] = $row;
}
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="manage_assignments.php" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-all">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-slate-800">Submissions</h2>
        </div>
        <p class="text-slate-500 text-sm">For: <span class="font-semibold text-slate-700"><?= htmlspecialchars($assignment['title']) ?></span></p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 border border-emerald-100">
        <i class="bi bi-people-fill text-emerald-600"></i>
        <span class="text-sm font-semibold text-emerald-700"><?= count($submissionData) ?> Submission(s)</span>
    </div>
</div>

<!-- Submissions Card -->
<div class="premium-card bg-white rounded-2xl border border-slate-200/60 shadow-lg shadow-slate-200/50 overflow-hidden">
    <?php if (count($submissionData) > 0): ?>
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">File</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Comment</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Submitted</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Grade & Feedback</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach ($submissionData as $row): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-sky-600 flex items-center justify-center text-white font-bold">
                                <?= strtoupper(substr($row['student_name'], 0, 1)) ?>
                            </div>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['student_name']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($row['file']): ?>
                            <a href="../assignments/uploads/<?= htmlspecialchars($row['file']) ?>" target="_blank"
                               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 font-medium text-sm transition-all">
                                <i class="bi bi-download"></i>
                                Download
                            </a>
                        <?php else: ?>
                            <span class="text-slate-400 text-sm">No file</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-600 max-w-xs truncate"><?= htmlspecialchars($row['comment'] ?: '—') ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-clock text-slate-400"></i>
                            <span class="text-sm text-slate-600"><?= date('M d, Y h:i A', strtotime($row['submitted_at'])) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" class="flex flex-col gap-2">
                            <input type="hidden" name="submission_id" value="<?= $row['id'] ?>">
                            <div class="flex items-center gap-2">
                                <input type="text" name="grade" placeholder="Grade"
                                       value="<?= htmlspecialchars($row['grade']) ?>"
                                       class="w-20 px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400">
                                <input type="text" name="feedback" placeholder="Feedback"
                                       value="<?= htmlspecialchars($row['feedback']) ?>"
                                       class="flex-1 min-w-[120px] px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400">
                                <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition-all" title="Save">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <!-- Empty State -->
    <div class="p-12 text-center">
        <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
            <i class="bi bi-inbox text-4xl text-slate-400"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">No Submissions Yet</h3>
        <p class="text-slate-500 max-w-md mx-auto">No students have submitted their work for this assignment yet. Check back later.</p>
    </div>
    <?php endif; ?>
</div>

<?php require_once '../admin_includes/footer.php'; ?>
