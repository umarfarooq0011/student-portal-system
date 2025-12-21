<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../models/Announcement.php';
$announcementModel = new Announcement();

$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

$categoryFilter = isset($_GET['category']) && $_GET['category'] !== 'all' ? $_GET['category'] : null;
$conn = $GLOBALS['conn'];
$countQuery = "SELECT COUNT(*) as cnt FROM announcements";
if ($categoryFilter) {
    $cat = mysqli_real_escape_string($conn, $categoryFilter);
    $countQuery .= " WHERE category = '$cat'";
}
$totalResult = mysqli_query($conn, $countQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalAnnouncements = $totalRow['cnt'];
$totalPages = ceil($totalAnnouncements / $perPage);

$query = "SELECT * FROM announcements";
if ($categoryFilter) {
    $cat = mysqli_real_escape_string($conn, $categoryFilter);
    $query .= " WHERE category = '$cat'";
}
$query .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$announcements = mysqli_query($conn, $query);
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manage Announcements</h2>
        <p class="text-slate-500 text-sm mt-1">Create and manage portal announcements</p>
    </div>
    <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 text-white font-semibold rounded-xl hover:bg-sky-700 transition-all">
        <i class="bi bi-plus-lg"></i>
        <span>New Announcement</span>
    </button>
</div>

<!-- Announcements Card -->
<div class="premium-card bg-white rounded-2xl border border-slate-200/60 shadow-lg shadow-slate-200/50 overflow-hidden">
    <!-- Search and Filter -->
    <div class="p-6 border-b border-slate-100">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="searchInput" placeholder="Search announcements..."
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 transition-all">
                </div>
            </div>
            <div>
                <select id="categoryFilter" class="px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 transition-all">
                    <option value="all" <?= !$categoryFilter ? 'selected' : '' ?>>All Categories</option>
                    <option value="Academic" <?= $categoryFilter === 'Academic' ? 'selected' : '' ?>>Academic</option>
                    <option value="Event" <?= $categoryFilter === 'Event' ? 'selected' : '' ?>>Event</option>
                    <option value="General" <?= $categoryFilter === 'General' ? 'selected' : '' ?>>General</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date Posted</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php while ($row = mysqli_fetch_assoc($announcements)): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-megaphone-fill text-amber-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800"><?= htmlspecialchars($row['title']) ?></p>
                                <p class="text-xs text-slate-500 line-clamp-1"><?= htmlspecialchars(substr($row['content'], 0, 50)) ?>...</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php
                        $catColors = [
                            'Academic' => 'bg-sky-100 text-sky-700',
                            'Event' => 'bg-purple-100 text-purple-700',
                            'General' => 'bg-slate-100 text-slate-700'
                        ];
                        $catColor = $catColors[$row['category']] ?? 'bg-slate-100 text-slate-700';
                        ?>
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold <?= $catColor ?>">
                            <?= htmlspecialchars($row['category']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        <?= date('M d, Y', strtotime($row['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($row['status'] === 'Active'): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Inactive
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['title'])) ?>', '<?= htmlspecialchars($row['category']) ?>', '<?= htmlspecialchars(addslashes($row['content'])) ?>', '<?= htmlspecialchars($row['status']) ?>')"
                                class="p-2 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 transition-all" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="../controllers/announcement_controller.php" class="inline">
                                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this announcement?')"
                                    class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all" title="Delete">
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
                <a href="?page=<?= $i ?><?= $categoryFilter ? '&category=' . urlencode($categoryFilter) : '' ?>"
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?= $i === $page ? 'bg-sky-600 text-white shadow-lg shadow-sky-500/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Add Announcement Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="relative bg-amber-500 px-6 py-8">
                <div class="absolute top-4 right-4">
                    <button onclick="closeAddModal()" class="p-2 rounded-xl bg-white/20 hover:bg-white/30 text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-400 flex items-center justify-center">
                        <i class="bi bi-megaphone text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">New Announcement</h3>
                        <p class="text-amber-100 text-sm">Create a new announcement for students</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="../controllers/announcement_controller.php" class="overflow-y-auto max-h-[60vh]">
                <input type="hidden" name="add" value="1">
                <div class="p-6 space-y-5">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-type text-amber-500"></i> Title
                        </label>
                        <input type="text" name="title" required placeholder="Enter announcement title"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-100 focus:border-amber-400 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-tag text-indigo-500"></i> Category
                        </label>
                        <select name="category" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-amber-100 focus:border-amber-400 focus:bg-white transition-all">
                            <option value="Academic">Academic</option>
                            <option value="Event">Event</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-text-paragraph text-emerald-500"></i> Content
                        </label>
                        <textarea name="content" rows="5" required placeholder="Write your announcement content here..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-amber-100 focus:border-amber-400 focus:bg-white transition-all resize-none"></textarea>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-100">
                        <input type="checkbox" name="status" value="Active" id="addImportant" class="w-5 h-5 rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                        <label for="addImportant" class="text-sm font-medium text-amber-800">
                            <span class="font-semibold">Mark as Important</span>
                            <span class="block text-amber-600 text-xs">This will highlight the announcement</span>
                        </label>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 transition-all flex items-center gap-2">
                        <i class="bi bi-send"></i> Post Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Announcement Modal -->
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
                    <div class="w-14 h-14 rounded-2xl bg-sky-500 flex items-center justify-center">
                        <i class="bi bi-pencil-square text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Edit Announcement</h3>
                        <p class="text-sky-100 text-sm">Update announcement details</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="../controllers/announcement_controller.php" class="overflow-y-auto max-h-[60vh]">
                <input type="hidden" name="update_id" id="editId">
                <div class="p-6 space-y-5">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-type text-sky-500"></i> Title
                        </label>
                        <input type="text" name="title" id="editTitle" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-tag text-indigo-500"></i> Category
                        </label>
                        <select name="category" id="editCategory" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                            <option value="Academic">Academic</option>
                            <option value="Event">Event</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-text-paragraph text-emerald-500"></i> Content
                        </label>
                        <textarea name="content" id="editContent" rows="5" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all resize-none"></textarea>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-sky-50 border border-sky-100">
                        <input type="checkbox" name="status" value="Active" id="editImportant" class="w-5 h-5 rounded border-sky-300 text-sky-600 focus:ring-sky-500">
                        <label for="editImportant" class="text-sm font-medium text-sky-800">
                            <span class="font-semibold">Mark as Important</span>
                            <span class="block text-sky-600 text-xs">This will highlight the announcement</span>
                        </label>
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700 transition-all flex items-center gap-2">
                        <i class="bi bi-check-lg"></i> Update Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal functions
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
    document.body.style.overflow = '';
}
function openEditModal(id, title, category, content, status) {
    document.getElementById('editId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editCategory').value = category;
    document.getElementById('editContent').value = content;
    document.getElementById('editImportant').checked = status === 'Active';
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Category filter
document.getElementById('categoryFilter').addEventListener('change', function() {
    const cat = this.value;
    const urlParams = new URLSearchParams(window.location.search);
    if (cat === 'all') {
        urlParams.delete('category');
    } else {
        urlParams.set('category', cat);
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
