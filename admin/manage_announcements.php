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

<div class="main-content">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Announcements</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                <i class="bi bi-plus-lg me-2"></i>New Announcement
            </button>
        </div>

        <!-- Announcement List -->
        <div class="card">
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search announcements...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <select class="form-select w-auto d-inline-block" id="categoryFilter">
                            <option value="all" <?= !$categoryFilter ? 'selected' : '' ?>>All Categories</option>
                            <option value="Academic" <?= $categoryFilter === 'Academic' ? 'selected' : '' ?>>Academic</option>
                            <option value="Event" <?= $categoryFilter === 'Event' ? 'selected' : '' ?>>Event</option>
                            <option value="General" <?= $categoryFilter === 'General' ? 'selected' : '' ?>>General</option>
                        </select>
                    </div>
                </div>

                <!-- Announcements Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Date Posted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysqli_fetch_assoc($announcements)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td><span class="badge bg-<?= $row['status'] === 'Active' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal" data-id="<?= $row['id'] ?>" data-title="<?= htmlspecialchars($row['title']) ?>" data-category="<?= htmlspecialchars($row['category']) ?>" data-content="<?= htmlspecialchars($row['content']) ?>" data-status="<?= htmlspecialchars($row['status']) ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" action="../controllers/announcement_controller.php" style="display:inline-block;">
                                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $categoryFilter ? '&category=' . urlencode($categoryFilter) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Add Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="announcementForm" method="POST" action="../controllers/announcement_controller.php">
                    <input type="hidden" name="add" value="1">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="Academic">Academic</option>
                            <option value="Event">Event</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="important" name="status" value="Active">
                            <label class="form-check-label" for="important">Mark as Important</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Post Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Announcement Modal -->
<div class="modal fade" id="editAnnouncementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAnnouncementForm" method="POST" action="../controllers/announcement_controller.php">
                    <input type="hidden" name="update_id" id="editAnnouncementId">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="editAnnouncementTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" id="editAnnouncementCategory" required>
                            <option value="Academic">Academic</option>
                            <option value="Event">Event</option>
                            <option value="General">General</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="5" id="editAnnouncementContent" required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="editImportant" name="status" value="Active">
                            <label class="form-check-label" for="editImportant">Mark as Important</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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

const searchInput = document.querySelector('.input-group input[type="text"]');
searchInput.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
});

var editModal = document.getElementById('editAnnouncementModal');
editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('editAnnouncementId').value = button.getAttribute('data-id');
    document.getElementById('editAnnouncementTitle').value = button.getAttribute('data-title');
    document.getElementById('editAnnouncementCategory').value = button.getAttribute('data-category');
    document.getElementById('editAnnouncementContent').value = button.getAttribute('data-content');
    document.getElementById('editImportant').checked = button.getAttribute('data-status') === 'Active';
});
</script>

<?php require_once '../admin_includes/footer.php'; ?>