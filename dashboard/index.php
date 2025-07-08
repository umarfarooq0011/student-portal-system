<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
?>

<div class="main-content">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <h1 class="mb-2">Hi, <?php echo $_SESSION['full_name']; ?></h1>
        <p class="mb-0">Welcome to your student portal</p>
    </div>

    <div class="row g-4">
        <!-- Upcoming Class -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-4">Upcoming Class</h5>
                <h6>Mathematics</h6>
                <p class="text-muted mb-0">
                    <?php 
                    $date = new DateTime();
                    $date->modify('+1 day');
                    echo $date->format('M d'); 
                    ?>
                    <br>9:00 AM
                </p>
            </div>
        </div>

        <!-- New Announcements -->
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-4">New Announcements</h5>
                <h3 class="mb-0">3 new</h3>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="quick-links mt-4">
        <h5 class="mb-4">Quick Links</h5>
        <a href="assignments.php" class="submit-btn text-decoration-none">Submit Assignment</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
