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
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Assignments</h2>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($assignments)): ?>
            <?php $submission = $submissionModel->getByAssignmentAndStudent($row['id'], $student_id); ?>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3 border-primary">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <div class="mb-2 text-muted small">Due: <?= htmlspecialchars($row['due_date']) ?></div>
                        <?php
                        $today = date('Y-m-d');
                        if ($row['due_date'] < $today) {
                            echo '<span class="badge bg-danger mb-2">Overdue</span>';
                        } elseif ($row['due_date'] == $today) {
                            echo '<span class="badge bg-warning text-dark mb-2">Due Today</span>';
                        } else {
                            echo '<span class="badge bg-warning text-dark mb-2">Pending</span>';
                        }
                        ?>
                        <p class="card-text"><?= nl2br(htmlspecialchars($row['instructions'])) ?></p>
                        <?php if (!empty($row['attachment'])): ?>
                            <a href="../assignments/uploads/<?= htmlspecialchars($row['attachment']) ?>" target="_blank" class="btn btn-outline-info btn-sm mb-2">Download Attachment</a><br>
                        <?php endif; ?>
                        <?php if ($submission): ?>
                            <div class="alert alert-success p-2 mb-2">Submitted
                                <?php if ($submission['file']): ?>
                                    <a href="../assignments/uploads/<?= htmlspecialchars($submission['file']) ?>" target="_blank">(View File)</a>
                                <?php endif; ?>
                                <?php if ($submission['grade']): ?>
                                    <br><span class="badge bg-info">Grade: <?= htmlspecialchars($submission['grade']) ?></span>
                                <?php endif; ?>
                                <?php if ($submission['feedback']): ?>
                                    <br><small>Feedback: <?= htmlspecialchars($submission['feedback']) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                        <form method="POST" enctype="multipart/form-data" action="../controllers/submission_controller.php">
                            <input type="hidden" name="assignment_id" value="<?= $row['id'] ?>">
                            <div class="mb-2">
                                <label class="form-label">Upload File</label>
                                <input type="file" name="file" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Comment (optional)</label>
                                <textarea name="comment" class="form-control form-control-sm"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
