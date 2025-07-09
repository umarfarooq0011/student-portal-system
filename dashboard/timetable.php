<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
require_once '../models/Timetable.php';

$timetableModel = new Timetable();
$timetables = $timetableModel->getAll();
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Timetable</h2>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Day</th>
                        <th>Subject</th>
                        <th>Time</th>
                        <th>Room</th>
                        <th>Teacher</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($timetables)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['day_of_week']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= date("g:i A", strtotime($row['start_time'])) . " - " . date("g:i A", strtotime($row['end_time'])) ?></td>
                        <td><?= htmlspecialchars($row['room']) ?></td>
                        <td><?= htmlspecialchars($row['teacher']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
