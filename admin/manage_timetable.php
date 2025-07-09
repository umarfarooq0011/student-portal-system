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
<?php
require_once '../models/Timetable.php';
$timetableModel = new Timetable();
$timetables = $timetableModel->getAll();
while ($row = mysqli_fetch_assoc($timetables)):
?>
<tr>
    <td><?= htmlspecialchars($row['day_of_week']) ?></td>
    <td><?= htmlspecialchars($row['subject']) ?></td>
    <td><?= date("g:i A", strtotime($row['start_time'])) . " - " . date("g:i A", strtotime($row['end_time'])) ?></td>
    <td><?= htmlspecialchars($row['room']) ?></td>
    <td><?= htmlspecialchars($row['teacher']) ?></td>
    <td>
        <form method="POST" action="../controllers/timetable_controller.php" style="display:inline-block;">
            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></button>
        </form>
        <!-- You can later add Edit modal trigger here -->
    </td>
</tr>
<?php endwhile; ?>
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
               <form method="POST" action="../controllers/timetable_controller.php">
    <input type="hidden" name="add" value="1">
    <div class="mb-3">
        <label class="form-label">Day</label>
        <select class="form-select" name="day_of_week" required>
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
        <input type="text" name="subject" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Start Time</label>
        <input type="time" name="start_time" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">End Time</label>
        <input type="time" name="end_time" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Room</label>
        <input type="text" name="room" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Teacher</label>
        <input type="text" name="teacher" class="form-control" required>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

            </div>
        </div>
    </div>
</div>
<?php require_once '../admin_includes/footer.php'; ?>
