<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../models/Assignment.php';

$assignmentModel = new Assignment();

// Pagination setup
$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total count
$conn = $GLOBALS['conn'];
$totalResult = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM assignments");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalAssignments = $totalRow['cnt'];
$totalPages = ceil($totalAssignments / $perPage);

// Get paginated assignments
$assignments = mysqli_query($conn, "SELECT * FROM assignments ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");

// Get unique subjects for filter dropdown
$subjects = [];
$allAssignments = $assignmentModel->getAll();
while ($subjRow = mysqli_fetch_assoc($allAssignments)) {
    $subj = trim($subjRow['subject']);
    if ($subj && !in_array($subj, $subjects)) {
        $subjects[] = $subj;
    }
}
?>

<div class="main-content">
    <div class="container-fluid p-4">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Assignments</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                <i class="bi bi-plus-lg me-2"></i>New Assignment
            </button>
        </div>

        <!-- Assignment List -->
        <div class="card">
            <div class="card-body">

                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search assignments...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <select class="form-select w-auto d-inline-block" id="subjectFilter">
                            <option value="all">All Subjects</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= htmlspecialchars($subject) ?>"><?= htmlspecialchars($subject) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Assignments Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Subject</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Attachment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysqli_fetch_assoc($assignments)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td><?= htmlspecialchars($row['subject']) ?></td>
                                <td><?= $row['due_date'] ?></td>
                                <td>
                                    <span class="badge <?= $row['due_date'] >= date('Y-m-d') ? 'bg-warning' : 'bg-secondary' ?>">
                                        <?= $row['due_date'] >= date('Y-m-d') ? 'Pending' : 'Closed' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['attachment'])): ?>
                                        <a href="../assignments/uploads/<?= htmlspecialchars($row['attachment']) ?>" target="_blank">View</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" title="Edit" onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['title'])) ?>', '<?= htmlspecialchars(addslashes($row['subject'])) ?>', '<?= $row['due_date'] ?>', '<?= htmlspecialchars(addslashes($row['instructions'])) ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="view_submissions.php?assignment_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-success me-1" title="View Submissions">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </a>
                                    <form method="POST" action="../controllers/assignment_controller.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                        <input type="hidden" name="delete_assignment_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
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
                        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item<?= $i == $page ? ' active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Add Assignment Modal -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="../controllers/assignment_controller.php">
                <div class="modal-header">
                    <h5 class="modal-title">New Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required placeholder="Enter subject name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachment</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="allow_late" class="form-check-input" id="allowLate">
                        <label class="form-check-label" for="allowLate">Allow Late Submissions</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Assignment Modal -->
<div class="modal fade" id="editAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="../controllers/assignment_controller.php">
                <input type="hidden" name="edit_assignment_id" id="edit_assignment_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" id="edit_subject" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="edit_due_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea name="instructions" id="edit_instructions" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachment (leave blank to keep current)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="allow_late" class="form-check-input" id="edit_allowLate">
                        <label class="form-check-label" for="edit_allowLate">Allow Late Submissions</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function openEditModal(id, title, subject, due_date, instructions) {
    document.getElementById('edit_assignment_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_subject').value = subject;
    document.getElementById('edit_due_date').value = due_date;
    document.getElementById('edit_instructions').value = instructions;
    var modal = new bootstrap.Modal(document.getElementById('editAssignmentModal'));
    modal.show();
}

// Subject filter
const subjectFilter = document.getElementById('subjectFilter');
subjectFilter.addEventListener('change', function() {
    const value = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(function(row) {
        const subject = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        if (value === 'all' || subject === value) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Search bar
const searchInput = document.querySelector('.input-group input[type="text"]');
searchInput.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('table tbody tr').forEach(function(row) {
        const title = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
        const subject = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        if (title.includes(search) || subject.includes(search)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>

<?php
$uploadDir = __DIR__ . '/../assignments/uploads/';
$filename = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
    $originalName = basename($_FILES['attachment']['name']);
    $filename = time() . '_' . $originalName;
    move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $filename);
}

require_once '../admin_includes/footer.php';
