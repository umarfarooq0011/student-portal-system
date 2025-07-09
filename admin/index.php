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
        <!-- <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-4">Students Overview</h5>
                <h3 class="mb-2">150</h3>
                <p class="text-muted mb-0">Total Active Students</p>
            </div>
        </div> -->

        <!-- Recent Activities -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-4">Recent Activities</h5>
                <?php
                require_once '../models/Assignment.php';
                require_once '../models/Submission.php';
                require_once '../models/Announcement.php';
                $conn = $GLOBALS['conn'];
                $lastWeek = date('Y-m-d', strtotime('-7 days'));
                $recentAssignmentsQuery = "SELECT COUNT(*) as count FROM assignments WHERE created_at >= '$lastWeek'";
                $recentAssignments = mysqli_fetch_assoc(mysqli_query($conn, $recentAssignmentsQuery))['count'];
                // Pending assignments: assignments with at least one student who has not submitted
                $pendingAssignmentsQuery = "SELECT a.id FROM assignments a LEFT JOIN submissions s ON a.id = s.assignment_id GROUP BY a.id HAVING SUM(CASE WHEN s.id IS NULL THEN 1 ELSE 0 END) > 0";
                $pendingAssignmentsResult = mysqli_query($conn, $pendingAssignmentsQuery);
                $pendingAssignments = mysqli_num_rows($pendingAssignmentsResult);
                $recentAnnouncementsQuery = "SELECT COUNT(*) as count FROM announcements WHERE created_at >= '$lastWeek'";
                $recentAnnouncements = mysqli_fetch_assoc(mysqli_query($conn, $recentAnnouncementsQuery))['count'];
                ?>
                <div class="d-flex justify-content-between align-items-center">
                    <span>New Assignments (7 days)</span>
                    <span class="badge bg-primary"><?php echo $recentAssignments; ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span>Pending Assignments</span>
                    <span class="badge bg-warning"><?php echo $pendingAssignments; ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span>New Announcements (7 days)</span>
                    <span class="badge bg-info"><?php echo $recentAnnouncements; ?></span>
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
            <!-- <a href="students.php" class="submit-btn text-decoration-none">View Students</a> -->
        </div>
    </div>
</div>

<?php require_once '../admin_includes/footer.php'; ?>
