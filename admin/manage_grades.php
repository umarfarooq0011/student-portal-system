<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
?>
<div class="main-content">
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Grades</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGradeModal">
                <i class="bi bi-plus-lg me-2"></i>Add Grade
            </button>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Assignment</th>
                                <th>Midterm</th>
                                <th>Final</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John Doe</td>
                                <td>Mathematics</td>
                                <td>92%</td>
                                <td>88%</td>
                                <td>95%</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <!-- More rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Grade Modal -->
<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Grade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assignment</label>
                        <input type="number" class="form-control" placeholder="e.g. 92" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Midterm</label>
                        <input type="number" class="form-control" placeholder="e.g. 88" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Final</label>
                        <input type="number" class="form-control" placeholder="e.g. 95" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
<?php require_once '../admin_includes/footer.php'; ?>
