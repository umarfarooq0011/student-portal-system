<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
?>

<div class="main-content">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <h1 class="mb-2">Welcome, <?php echo $_SESSION['full_name']; ?></h1>
        <p class="mb-0">Manage your student portal effectively</p>
    </div>

    <div class="row g-4">
        <!-- Student Stats -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-4">Students Overview</h5>
                <h3 class="mb-2">150</h3>
                <p class="text-muted mb-0">Total Active Students</p>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-4">Recent Activities</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <span>New Assignments</span>
                    <span class="badge bg-primary">5</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span>Pending Submissions</span>
                    <span class="badge bg-warning">12</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-links mt-4">
        <h5 class="mb-4">Quick Actions</h5>
        <div class="d-flex gap-3 flex-wrap">
            <a href="manage_announcements.php" class="submit-btn text-decoration-none">Post Announcement</a>
            <a href="manage_assignments.php" class="submit-btn text-decoration-none">Create Assignment</a>
            <a href="students.php" class="submit-btn text-decoration-none">View Students</a>
        </div>
    </div>
</div>

<?php require_once '../admin_includes/footer.php'; ?>
