<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../models/Assignment.php';

$assignmentModel = new Assignment();
$conn = $GLOBALS['conn'];

// Pagination setup
$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total count
$totalResult = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM assignments");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalAssignments = $totalRow['cnt'];
$totalPages = ceil($totalAssignments / $perPage);

// Get unique subjects
$subjectsResult = mysqli_query($conn, "SELECT DISTINCT subject FROM assignments WHERE subject IS NOT NULL AND subject != '' ORDER BY subject ASC");
$subjects = [];
while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
    $subjects[] = $subjectRow['subject'];
}

// Get paginated assignments
$subjectFilter = isset($_GET['subject']) && $_GET['subject'] !== 'all' ? $_GET['subject'] : null;
$query = "SELECT * FROM assignments";
if ($subjectFilter) {
    $subjectFilter = mysqli_real_escape_string($conn, $subjectFilter);
    $query .= " WHERE subject = '$subjectFilter'";
}
$query .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$assignments = mysqli_query($conn, $query);

// For each assignment, check if any submission exists
$submissions = [];
$subRes = mysqli_query($conn, "SELECT assignment_id, COUNT(*) as cnt FROM submissions GROUP BY assignment_id");
while ($row = mysqli_fetch_assoc($subRes)) {
    $submissions[$row['assignment_id']] = $row['cnt'];
}
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manage Assignments</h2>
        <p class="text-slate-500 text-sm mt-1">Create and manage student assignments</p>
    </div>
    <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 text-white font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition-all">
        <i class="bi bi-plus-lg"></i>
        <span>New Assignment</span>
    </button>
</div>

<!-- Assignments Card -->
<div class="premium-card bg-white rounded-2xl border border-slate-200/60 shadow-lg shadow-slate-200/50 overflow-hidden">
    <!-- Search and Filter -->
    <div class="p-6 border-b border-slate-100">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="searchInput" placeholder="Search assignments..."
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 transition-all">
                </div>
            </div>
            <div>
                <select id="subjectFilter" class="px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 transition-all">
                    <option value="all">All Subjects</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= htmlspecialchars($subject) ?>" <?= (isset($_GET['subject']) && $_GET['subject'] === $subject) ? 'selected' : '' ?>><?= htmlspecialchars($subject) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Assignment</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Attachment</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php while ($row = mysqli_fetch_assoc($assignments)): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-file-earmark-text-fill text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800"><?= htmlspecialchars($row['title']) ?></p>
                                <p class="text-xs text-slate-500 line-clamp-1"><?= htmlspecialchars(substr($row['instructions'], 0, 40)) ?>...</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                            <?= htmlspecialchars($row['subject']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $dueDate = strtotime($row['due_date']);
                        $today = strtotime('today');
                        $isOverdue = $dueDate < $today;
                        ?>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-calendar3 text-slate-400"></i>
                            <span class="text-sm <?= $isOverdue ? 'text-red-600 font-semibold' : 'text-slate-600' ?>">
                                <?= date('M d, Y', $dueDate) ?>
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $aid = $row['id'];
                        if (isset($submissions[$aid]) && $submissions[$aid] > 0):
                        ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <?= $submissions[$aid] ?> Submitted
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Pending
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if (!empty($row['attachment'])): ?>
                            <button onclick="openFilePreview('<?= htmlspecialchars($row['attachment']) ?>', '<?= htmlspecialchars(addslashes($row['title'])) ?>')"
                               class="inline-flex items-center gap-1.5 text-sm text-sky-600 hover:text-sky-700 font-medium">
                                <i class="bi bi-paperclip"></i>
                                View
                            </button>
                        <?php else: ?>
                            <span class="text-slate-400 text-sm">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['title'])) ?>', '<?= htmlspecialchars(addslashes($row['subject'])) ?>', '<?= $row['due_date'] ?>', '<?= htmlspecialchars(addslashes($row['instructions'])) ?>')"
                                class="p-2 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 transition-all" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="view_submissions.php?assignment_id=<?= $row['id'] ?>"
                               class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all" title="View Submissions">
                                <i class="bi bi-file-earmark-check"></i>
                            </a>
                            <form method="POST" action="../controllers/assignment_controller.php" class="inline" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                <input type="hidden" name="delete_assignment_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="p-6 border-t border-slate-100">
        <div class="flex items-center justify-center gap-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?><?= isset($_GET['subject']) ? '&subject=' . urlencode($_GET['subject']) : '' ?>"
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?= $i === $page ? 'bg-sky-600 text-white shadow-lg shadow-sky-500/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Add Assignment Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="relative bg-emerald-500 px-6 py-8">
                <div class="absolute top-4 right-4">
                    <button onclick="closeAddModal()" class="p-2 rounded-xl bg-white/20 hover:bg-white/30 text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                        <i class="bi bi-file-earmark-plus text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">New Assignment</h3>
                        <p class="text-emerald-100 text-sm">Create a new assignment for students</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="POST" enctype="multipart/form-data" action="../controllers/assignment_controller.php" class="overflow-y-auto max-h-[60vh]">
                <div class="p-6 space-y-5">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-type text-emerald-500"></i> Title
                        </label>
                        <input type="text" name="title" required placeholder="Enter assignment title"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <i class="bi bi-book text-indigo-500"></i> Subject
                            </label>
                            <input type="text" name="subject" required placeholder="e.g. Mathematics"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <i class="bi bi-calendar-event text-rose-500"></i> Due Date
                            </label>
                            <input type="date" name="due_date" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-text-paragraph text-sky-500"></i> Instructions
                        </label>
                        <textarea name="instructions" rows="4" required placeholder="Provide detailed instructions for this assignment..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all resize-none"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-paperclip text-amber-500"></i> Attachment
                        </label>
                        <input type="file" name="attachment"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-400 focus:bg-white transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-semibold hover:file:bg-emerald-100">
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                        <input type="checkbox" name="allow_late" id="addAllowLate" class="w-5 h-5 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="addAllowLate" class="text-sm font-medium text-emerald-800">
                            <span class="font-semibold">Allow Late Submissions</span>
                            <span class="block text-emerald-600 text-xs">Students can submit after the due date</span>
                        </label>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-500 text-white font-semibold shadow-lg shadow-emerald-500/25 hover:shadow-xl transition-all flex items-center gap-2">
                        <i class="bi bi-check-lg"></i> Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Assignment Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="relative bg-sky-600 px-6 py-8">
                <div class="absolute top-4 right-4">
                    <button onclick="closeEditModal()" class="p-2 rounded-xl bg-white/20 hover:bg-white/30 text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                        <i class="bi bi-pencil-square text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Edit Assignment</h3>
                        <p class="text-sky-100 text-sm">Update assignment details</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="POST" enctype="multipart/form-data" action="../controllers/assignment_controller.php" class="overflow-y-auto max-h-[60vh]">
                <input type="hidden" name="edit_assignment_id" id="editId">
                <div class="p-6 space-y-5">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-type text-sky-500"></i> Title
                        </label>
                        <input type="text" name="title" id="editTitle" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <i class="bi bi-book text-indigo-500"></i> Subject
                            </label>
                            <input type="text" name="subject" id="editSubject" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <i class="bi bi-calendar-event text-rose-500"></i> Due Date
                            </label>
                            <input type="date" name="due_date" id="editDueDate" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-text-paragraph text-emerald-500"></i> Instructions
                        </label>
                        <textarea name="instructions" id="editInstructions" rows="4" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all resize-none"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-paperclip text-amber-500"></i> Attachment
                        </label>
                        <input type="file" name="attachment"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-sky-50 file:text-sky-700 file:font-semibold hover:file:bg-sky-100">
                        <p class="text-xs text-slate-500 mt-1">Leave blank to keep current attachment</p>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-sky-50 border border-sky-100">
                        <input type="checkbox" name="allow_late" id="editAllowLate" class="w-5 h-5 rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                        <label for="editAllowLate" class="text-sm font-medium text-sky-800">
                            <span class="font-semibold">Allow Late Submissions</span>
                            <span class="block text-sky-600 text-xs">Students can submit after the due date</span>
                        </label>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 text-white font-semibold shadow-lg shadow-sky-500/25 hover:shadow-xl transition-all flex items-center gap-2">
                        <i class="bi bi-check-lg"></i> Update Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- File Preview Modal -->
<div id="filePreviewModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" onclick="closeFilePreview()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="relative bg-slate-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <i class="bi bi-file-earmark text-xl text-white" id="previewFileIcon"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white" id="previewTitle">File Preview</h3>
                            <p class="text-slate-300 text-sm" id="previewFileName"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="#" id="downloadBtn" download
                           class="no-loader p-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white transition-all" title="Download">
                            <i class="bi bi-download"></i>
                        </a>
                        <button onclick="closeFilePreview()" class="p-2.5 rounded-xl bg-white/20 hover:bg-white/30 text-white transition-all">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preview Content -->
            <div class="bg-slate-100 p-4 overflow-auto" style="max-height: calc(90vh - 80px);">
                <div id="previewContent" class="flex items-center justify-center min-h-[400px]">
                    <!-- Content will be injected here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// File Preview Functions
function openFilePreview(filename, title) {
    const modal = document.getElementById('filePreviewModal');
    const previewContent = document.getElementById('previewContent');
    const previewTitle = document.getElementById('previewTitle');
    const previewFileName = document.getElementById('previewFileName');
    const downloadBtn = document.getElementById('downloadBtn');
    const fileIcon = document.getElementById('previewFileIcon');

    const filePath = '../assignments/uploads/' + filename;
    const ext = filename.split('.').pop().toLowerCase();

    previewTitle.textContent = title;
    previewFileName.textContent = filename;
    downloadBtn.href = filePath;
    downloadBtn.download = filename;

    // Set appropriate icon
    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext)) {
        fileIcon.className = 'bi bi-image text-xl text-white';
    } else if (ext === 'pdf') {
        fileIcon.className = 'bi bi-file-earmark-pdf text-xl text-white';
    } else if (['doc', 'docx'].includes(ext)) {
        fileIcon.className = 'bi bi-file-earmark-word text-xl text-white';
    } else if (['xls', 'xlsx'].includes(ext)) {
        fileIcon.className = 'bi bi-file-earmark-excel text-xl text-white';
    } else if (['ppt', 'pptx'].includes(ext)) {
        fileIcon.className = 'bi bi-file-earmark-ppt text-xl text-white';
    } else {
        fileIcon.className = 'bi bi-file-earmark text-xl text-white';
    }

    // Generate preview based on file type
    let previewHTML = '';

    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext)) {
        // Image preview
        previewHTML = `
            <img src="${filePath}" alt="${title}"
                 class="max-w-full max-h-[70vh] rounded-xl shadow-lg object-contain"
                 onerror="this.parentElement.innerHTML='<div class=\\'text-center p-8\\'><i class=\\'bi bi-exclamation-triangle text-4xl text-amber-500 mb-3\\'></i><p class=\\'text-slate-600\\'>Unable to load image</p></div>'">
        `;
    } else if (ext === 'pdf') {
        // PDF preview
        previewHTML = `
            <iframe src="${filePath}"
                    class="w-full h-[70vh] rounded-xl border-0 bg-white shadow-lg"
                    title="${title}"></iframe>
        `;
    } else if (['docx'].includes(ext)) {
        // DOCX preview using Mammoth.js
        previewHTML = `
            <div class="w-full bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 bg-blue-50 border-b border-blue-100 flex items-center gap-3">
                    <i class="bi bi-file-earmark-word text-2xl text-blue-600"></i>
                    <span class="font-semibold text-blue-800">${filename}</span>
                </div>
                <div id="docxPreviewContent" class="p-6 max-h-[60vh] overflow-auto prose prose-sm max-w-none">
                    <div class="flex items-center justify-center py-8">
                        <div class="inline-spinner mr-3"></div>
                        <span class="text-slate-500">Loading document...</span>
                    </div>
                </div>
            </div>
        `;
        previewContent.innerHTML = previewHTML;

        // Fetch and render DOCX
        fetch(filePath)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => {
                mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
                    .then(result => {
                        document.getElementById('docxPreviewContent').innerHTML = result.value || '<p class="text-slate-500">No content found in document.</p>';
                    })
                    .catch(err => {
                        document.getElementById('docxPreviewContent').innerHTML = '<div class="text-center py-8"><i class="bi bi-exclamation-triangle text-4xl text-amber-500 mb-3"></i><p class="text-slate-600">Unable to preview this document.</p><a href="' + filePath + '" download class="no-loader mt-4 inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg">Download Instead</a></div>';
                    });
            })
            .catch(err => {
                document.getElementById('docxPreviewContent').innerHTML = '<div class="text-center py-8"><i class="bi bi-exclamation-triangle text-4xl text-red-500 mb-3"></i><p class="text-slate-600">Failed to load document.</p></div>';
            });

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        return;
    } else if (['xlsx', 'xls'].includes(ext)) {
        // Excel preview using SheetJS
        previewHTML = `
            <div class="w-full bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 bg-green-50 border-b border-green-100 flex items-center gap-3">
                    <i class="bi bi-file-earmark-excel text-2xl text-green-600"></i>
                    <span class="font-semibold text-green-800">${filename}</span>
                </div>
                <div id="excelPreviewContent" class="p-4 max-h-[60vh] overflow-auto">
                    <div class="flex items-center justify-center py-8">
                        <div class="inline-spinner mr-3"></div>
                        <span class="text-slate-500">Loading spreadsheet...</span>
                    </div>
                </div>
            </div>
        `;
        previewContent.innerHTML = previewHTML;

        // Fetch and render Excel
        fetch(filePath)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => {
                const workbook = XLSX.read(arrayBuffer, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const html = XLSX.utils.sheet_to_html(firstSheet, { editable: false });
                document.getElementById('excelPreviewContent').innerHTML = '<div class="overflow-x-auto"><style>.excel-table table { border-collapse: collapse; width: 100%; } .excel-table td, .excel-table th { border: 1px solid #e2e8f0; padding: 8px 12px; text-align: left; font-size: 14px; } .excel-table tr:nth-child(even) { background: #f8fafc; } .excel-table tr:first-child { background: #f1f5f9; font-weight: 600; }</style><div class="excel-table">' + html + '</div></div>';
            })
            .catch(err => {
                document.getElementById('excelPreviewContent').innerHTML = '<div class="text-center py-8"><i class="bi bi-exclamation-triangle text-4xl text-red-500 mb-3"></i><p class="text-slate-600">Failed to load spreadsheet.</p><a href="' + filePath + '" download class="no-loader mt-4 inline-flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg">Download Instead</a></div>';
            });

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        return;
    } else if (['doc', 'ppt', 'pptx'].includes(ext)) {
        // Old format or PowerPoint - show download option
        let iconClass = 'bi-file-earmark-word';
        let iconColor = 'text-blue-500';
        let appName = 'Word';

        if (['ppt', 'pptx'].includes(ext)) {
            iconClass = 'bi-file-earmark-ppt';
            iconColor = 'text-orange-500';
            appName = 'PowerPoint';
        }

        previewHTML = `
            <div class="text-center p-8 bg-white rounded-2xl shadow-lg max-w-lg mx-auto">
                <div class="w-24 h-24 mx-auto mb-6 rounded-2xl bg-slate-100 flex items-center justify-center">
                    <i class="bi ${iconClass} text-5xl ${iconColor}"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Microsoft ${appName} Document</h4>
                <p class="text-slate-500 mb-2">${filename}</p>
                <p class="text-sm text-slate-400 mb-6">This format cannot be previewed in browser. Please download to view.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="${filePath}" download
                       class="no-loader inline-flex items-center gap-2 px-6 py-3 bg-sky-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                        <i class="bi bi-download"></i>
                        Download File
                    </a>
                </div>
            </div>
        `;
    } else if (['txt', 'csv', 'json', 'xml', 'html', 'css', 'js', 'php', 'py', 'java', 'c', 'cpp'].includes(ext)) {
        // Text-based files
        previewHTML = `
            <div class="w-full">
                <iframe src="${filePath}"
                        class="w-full h-[70vh] rounded-xl border-0 bg-white shadow-lg font-mono"
                        title="${title}"></iframe>
            </div>
        `;
    } else {
        // Other files - show download option
        previewHTML = `
            <div class="text-center p-12 bg-white rounded-2xl shadow-lg">
                <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                    <i class="bi bi-file-earmark text-4xl text-slate-400"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-2">Preview not available</h4>
                <p class="text-slate-500 mb-6">This file type (.${ext}) cannot be previewed in the browser.</p>
                <a href="${filePath}" download
                   class="no-loader inline-flex items-center gap-2 px-6 py-3 bg-sky-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                    <i class="bi bi-download"></i>
                    Download File
                </a>
            </div>
        `;
    }

    previewContent.innerHTML = previewHTML;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeFilePreview() {
    const modal = document.getElementById('filePreviewModal');
    const previewContent = document.getElementById('previewContent');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    // Clear content to stop any playing media
    previewContent.innerHTML = '';
}

// Modal functions
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
    document.body.style.overflow = '';
}
function openEditModal(id, title, subject, due_date, instructions) {
    document.getElementById('editId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editSubject').value = subject;
    document.getElementById('editDueDate').value = due_date;
    document.getElementById('editInstructions').value = instructions;
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Subject filter
document.getElementById('subjectFilter').addEventListener('change', function() {
    const subject = this.value;
    const urlParams = new URLSearchParams(window.location.search);
    if (subject === 'all') {
        urlParams.delete('subject');
    } else {
        urlParams.set('subject', subject);
    }
    urlParams.delete('page');
    window.location.href = '?' + urlParams.toString();
});

// Search
document.getElementById('searchInput').addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('tbody tr').forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
});
</script>

<?php require_once '../admin_includes/footer.php'; ?>
