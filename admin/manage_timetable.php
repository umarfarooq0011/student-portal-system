<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
?>
<div class="main-content">
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Timetables</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimetableModal">
                <i class="bi bi-plus-lg me-2"></i>Add Timetable Entry
            </button>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Day</th>
                                <th>Subject</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th>Teacher</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Monday</td>
                                <td>Mathematics</td>
                                <td>9:00 AM - 10:00 AM</td>
                                <td>Room 101</td>
                                <td>Mr. Ali</td>
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
<!-- Add Timetable Modal -->
<div class="modal fade" id="addTimetableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Timetable Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Day</label>
                        <select class="form-select" required>
                            <option value="">Select Day</option>
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time</label>
                        <input type="text" class="form-control" placeholder="e.g. 9:00 AM - 10:00 AM" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room</label>
                        <input type="text" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teacher</label>
                        <input type="text" class="form-control" required>
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
