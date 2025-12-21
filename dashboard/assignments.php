<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
require_once '../models/Assignment.php';
require_once '../models/Submission.php';

$assignmentModel = new Assignment();
$submissionModel = new Submission();
$assignments = $assignmentModel->getAll();
$student_id = $_SESSION['user_id'];
$today = date('Y-m-d');
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Assignments</h1>
        <p class="text-slate-500 text-sm mt-1">View and submit your assignments</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 border border-emerald-100">
        <i class="bi bi-file-earmark-text-fill text-emerald-600"></i>
        <span class="text-sm font-semibold text-emerald-700">Active Assignments</span>
    </div>
</div>

<!-- Assignments List -->
<div class="space-y-4">
    <?php
    $hasAssignments = false;
    while ($row = mysqli_fetch_assoc($assignments)):
        $hasAssignments = true;
        $submission = $submissionModel->getByAssignmentAndStudent($row['id'], $student_id);
        $is_overdue = strtotime($row['due_date']) < strtotime($today);

        // Determine status styling
        if ($submission) {
            if ($submission['grade']) {
                $status_text = 'Graded: ' . $submission['grade'];
                $status_bg = 'bg-violet-100 text-violet-700';
                $status_icon = 'bi-award';
                $icon_bg = 'bg-violet-500';
            } else {
                $status_text = 'Submitted';
                $status_bg = 'bg-emerald-100 text-emerald-700';
                $status_icon = 'bi-check-circle';
                $icon_bg = 'bg-emerald-500';
            }
        } else {
            if ($is_overdue) {
                $status_text = 'Overdue';
                $status_bg = 'bg-red-100 text-red-700';
                $status_icon = 'bi-exclamation-circle';
                $icon_bg = 'bg-red-500';
            } elseif ($row['due_date'] == $today) {
                $status_text = 'Due Today';
                $status_bg = 'bg-amber-100 text-amber-700';
                $status_icon = 'bi-exclamation-triangle';
                $icon_bg = 'bg-amber-500';
            } else {
                $status_text = 'Pending';
                $status_bg = 'bg-sky-100 text-sky-700';
                $status_icon = 'bi-hourglass-split';
                $icon_bg = 'bg-sky-500';
            }
        }
    ?>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md transition-shadow">
        <!-- Card Header -->
        <div class="p-5 flex gap-4">
            <!-- Icon -->
            <div class="w-12 h-12 rounded-xl <?= $icon_bg ?> flex items-center justify-center flex-shrink-0">
                <i class="bi bi-file-earmark-text-fill text-white text-lg"></i>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                    <h3 class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($row['title']) ?></h3>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?= $status_bg ?>">
                        <i class="bi <?= $status_icon ?>"></i>
                        <?= $status_text ?>
                    </span>
                </div>

                <div class="flex items-center gap-4 text-sm text-slate-500 mb-3">
                    <span class="flex items-center gap-1">
                        <i class="bi bi-calendar-event"></i>
                        Due: <?= date('M d, Y', strtotime($row['due_date'])) ?>
                    </span>
                </div>

                <p class="text-sm text-slate-600 line-clamp-2"><?= htmlspecialchars($row['instructions']) ?></p>
            </div>
        </div>

        <!-- Card Actions -->
        <div class="px-5 pb-5 pt-0">
            <div class="flex flex-wrap items-center gap-3">
                <?php if (!empty($row['attachment'])): ?>
                <a href="../assignments/uploads/<?= htmlspecialchars($row['attachment']) ?>"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-sm font-medium transition-all">
                    <i class="bi bi-download"></i>
                    Download
                </a>
                <?php endif; ?>

                <?php if ($submission): ?>
                    <?php if ($submission['file']): ?>
                    <a href="../assignments/uploads/<?= htmlspecialchars($submission['file']) ?>"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 text-sm font-medium transition-all">
                        <i class="bi bi-file-earmark-check"></i>
                        View Submission
                    </a>
                    <?php endif; ?>

                    <?php if ($submission['feedback']): ?>
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-violet-100 text-violet-700 text-sm">
                        <i class="bi bi-chat-text"></i>
                        <?= htmlspecialchars($submission['feedback']) ?>
                    </span>
                    <?php endif; ?>

                <?php elseif ($is_overdue && !$row['allow_late']): ?>
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-red-100 text-red-700 text-sm">
                        <i class="bi bi-x-circle"></i>
                        Submission Closed
                    </span>

                <?php else: ?>
                    <button onclick="toggleSubmitForm(<?= $row['id'] ?>)"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg <?= $is_overdue ? 'bg-amber-500 hover:bg-amber-600' : 'bg-violet-600 hover:bg-violet-700' ?> text-white text-sm font-semibold transition-all">
                        <i class="bi bi-upload"></i>
                        <?= $is_overdue ? 'Submit Late' : 'Submit' ?>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Submission Form (Hidden by default) -->
            <?php if (!$submission && !($is_overdue && !$row['allow_late'])): ?>
            <div id="submitForm<?= $row['id'] ?>" class="hidden mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                <?php if ($is_overdue): ?>
                <div class="flex items-center gap-2 p-3 mb-4 rounded-lg bg-amber-100 text-amber-700 text-sm">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Late submission - Due was <?= date('M d, Y', strtotime($row['due_date'])) ?></span>
                </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" action="../controllers/submission_controller.php" class="space-y-4">
                    <input type="hidden" name="assignment_id" value="<?= $row['id'] ?>">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Upload File</label>
                        <input type="file" name="file" required
                            class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Comment (optional)</label>
                        <textarea name="comment" rows="2" placeholder="Add a comment..."
                            class="w-full px-3 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400 resize-none"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg <?= $is_overdue ? 'bg-amber-500 hover:bg-amber-600' : 'bg-violet-600 hover:bg-violet-700' ?> text-white text-sm font-semibold transition-all">
                            <i class="bi bi-upload"></i>
                            Submit
                        </button>
                        <button type="button" onclick="toggleSubmitForm(<?= $row['id'] ?>)"
                            class="px-4 py-2.5 rounded-lg bg-slate-200 text-slate-700 hover:bg-slate-300 text-sm font-medium transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php if (!$hasAssignments): ?>
<!-- Empty State -->
<div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
    <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
        <i class="bi bi-file-earmark-text text-4xl text-slate-400"></i>
    </div>
    <h3 class="text-lg font-bold text-slate-800 mb-2">No Assignments Yet</h3>
    <p class="text-slate-500 max-w-md mx-auto">There are no assignments available at the moment. Check back later for new assignments.</p>
</div>
<?php endif; ?>

<script>
function toggleSubmitForm(id) {
    const form = document.getElementById('submitForm' + id);
    if (form) {
        form.classList.toggle('hidden');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
