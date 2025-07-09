<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
require_once '../models/Assignment.php';

$assignmentModel = new Assignment();
$assignments = $assignmentModel->getAll();
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Assignments</h2>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($assignments)): ?>
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
                        <a href="#" class="btn btn-primary btn-sm">Submit</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
