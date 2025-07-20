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
                        <div class="mb-3">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-calendar-event"></i> Due: <?= date('M d, Y', strtotime($row['due_date'])) ?>
                            </div>
                            <?php
                            $today = date('Y-m-d');
                            
                            // Calculate assignment status
                            if ($submission) {
                                $status_class = 'bg-success';
                                $status_icon = 'bi-check-circle';
                                $status_text = 'Submitted';
                                
                                if ($submission['grade']) {
                                    $status_text = 'Graded: ' . $submission['grade'];
                                }
                            } else {
                                if ($row['due_date'] < $today) {
                                    $status_class = 'bg-danger';
                                    $status_icon = 'bi-exclamation-circle';
                                    $status_text = 'Overdue';
                                } elseif ($row['due_date'] == $today) {
                                    $status_class = 'bg-warning text-dark';
                                    $status_icon = 'bi-exclamation-triangle';
                                    $status_text = 'Due Today';
                                } else {
                                    $status_class = 'bg-primary';
                                    $status_icon = 'bi-hourglass-split';
                                    $status_text = 'Pending';
                                }
                            }
                            ?>
                            <div class="d-inline-block">
                                <span class="badge <?= $status_class ?> p-2">
                                    <i class="bi <?= $status_icon ?>"></i> <?= $status_text ?>
                                </span>
                            </div>
                        </div>
                        <p class="card-text"><?= nl2br(htmlspecialchars($row['instructions'])) ?></p>
                        <?php if (!empty($row['attachment'])): ?>
                            <a href="../assignments/uploads/<?= htmlspecialchars($row['attachment']) ?>" 
                               class="btn btn-outline-info btn-sm mb-2">
                               <i class="bi bi-download"></i> Download Assignment
                            </a><br>
                        <?php endif; ?>
                        <?php if ($submission): ?>
                            <?php if ($submission['feedback']): ?>
                            <div class="alert alert-info p-2 mb-2">
                                <small>
                                    <i class="bi bi-chat-text"></i> 
                                    Feedback: <?= htmlspecialchars($submission['feedback']) ?>
                                </small>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($submission['file']): ?>
                            <div class="text-end mb-2">
                                <a href="../assignments/uploads/<?= htmlspecialchars($submission['file']) ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text"></i> View Submission
                                </a>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php
                            $is_overdue = strtotime($row['due_date']) < strtotime($today);
                            if ($is_overdue && !$row['allow_late']): ?>
                                <div class="alert alert-danger p-2 mb-2">
                                    <i class="bi bi-exclamation-triangle-fill"></i> 
                                    <strong>Submission Closed</strong><br>
                                    This assignment was due on <?= date('M d, Y', strtotime($row['due_date'])) ?> and late submissions are not allowed.
                                </div>
                            <?php else: ?>
                                <form method="POST" enctype="multipart/form-data" action="../controllers/submission_controller.php">
                                    <input type="hidden" name="assignment_id" value="<?= $row['id'] ?>">
                                    <?php if ($is_overdue): ?>
                                        <div class="alert alert-warning p-2 mb-2">
                                            <small>
                                                <i class="bi bi-exclamation-triangle"></i>
                                                <strong>Late Submission Allowed</strong><br>
                                                This assignment was due on <?= date('M d, Y', strtotime($row['due_date'])) ?>.
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    <div class="mb-2">
                                        <label class="form-label">Upload File</label>
                                        <input type="file" name="file" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Comment (optional)</label>
                                        <textarea name="comment" class="form-control form-control-sm"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <?= $is_overdue ? 'Submit Late' : 'Submit' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
