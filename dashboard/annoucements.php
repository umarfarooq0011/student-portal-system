<?php
require_once '../auth/authsession.php';
require_once '../models/Announcement.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
$announcementModel = new Announcement();
$announcements = $announcementModel->getAll();
?>
<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Announcements</h2>
        <div class="row g-4">
            <?php $hasImportant = false; ?>
            <?php foreach ($announcements as $announcement): ?>
                <?php if ($announcement['status'] !== 'Active') continue; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card mb-3<?php if ($announcement['status'] === 'Active' && strtolower($announcement['category']) === 'academic' && !$hasImportant) { echo ' border-warning'; $hasImportant = true; } ?>">
                        <div class="card-body">
                            <h5 class="card-title mb-2">
                                <?php echo htmlspecialchars($announcement['title']); ?>
                                <?php if ($announcement['status'] === 'Active' && strtolower($announcement['category']) === 'academic' && !$hasImportant): ?>
                                    <span class="badge bg-warning text-dark ms-2">Important</span>
                                <?php endif; ?>
                            </h5>
                            <div class="mb-2 text-muted small">
                                <?php echo htmlspecialchars($announcement['category']); ?> |
                                <?php echo date('M d, Y', strtotime($announcement['created_at'])); ?>
                            </div>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                            <div class="text-end">
                                <span class="text-muted small">By Admin</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($announcements)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No announcements found.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
