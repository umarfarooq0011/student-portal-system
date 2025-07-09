<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
?>
<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Grades</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Mathematics</h5>
                        <p class="mb-1">Assignment: <span class="fw-semibold">92%</span></p>
                        <p class="mb-1">Midterm: <span class="fw-semibold">88%</span></p>
                        <p class="mb-0">Final: <span class="fw-semibold">95%</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Science</h5>
                        <p class="mb-1">Assignment: <span class="fw-semibold">85%</span></p>
                        <p class="mb-1">Midterm: <span class="fw-semibold">80%</span></p>
                        <p class="mb-0">Final: <span class="fw-semibold">89%</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">English</h5>
                        <p class="mb-1">Assignment: <span class="fw-semibold">90%</span></p>
                        <p class="mb-1">Midterm: <span class="fw-semibold">87%</span></p>
                        <p class="mb-0">Final: <span class="fw-semibold">93%</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
