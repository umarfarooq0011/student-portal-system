<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Assignments</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3 border-primary">
                    <div class="card-body">
                        <h5 class="card-title">Math Homework 1</h5>
                        <div class="mb-2 text-muted small">Due: Jul 15, 2025</div>
                        <span class="badge bg-warning text-dark mb-2">Pending</span>
                        <p class="card-text">Solve all exercises from chapter 3 and upload your solutions.</p>
                        <a href="#" class="btn btn-primary btn-sm">Submit</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Science Project</h5>
                        <div class="mb-2 text-muted small">Due: Jul 18, 2025</div>
                        <span class="badge bg-success mb-2">Submitted</span>
                        <p class="card-text">Prepare a model of the solar system and submit a report.</p>
                        <a href="#" class="btn btn-outline-secondary btn-sm disabled">View Submission</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">English Essay</h5>
                        <div class="mb-2 text-muted small">Due: Jul 20, 2025</div>
                        <span class="badge bg-danger mb-2">Overdue</span>
                        <p class="card-text">Write an essay on the topic "My Favorite Book" (500 words).</p>
                        <a href="#" class="btn btn-danger btn-sm">Submit Late</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
