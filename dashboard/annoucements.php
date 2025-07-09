<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
?>
<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Announcements</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3 border-warning">
                    <div class="card-body">
                        <h5 class="card-title mb-2">
                            Exam Schedule Update
                            <span class="badge bg-warning text-dark ms-2">Important</span>
                        </h5>
                        <div class="mb-2 text-muted small">Academic | Jul 10, 2025</div>
                        <p class="card-text">The final exam schedule has been updated. Please check the timetable section for details.</p>
                        <div class="text-end">
                            <span class="text-muted small">By Admin</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-2">Sports Day Announced</h5>
                        <div class="mb-2 text-muted small">Events | Jul 8, 2025</div>
                        <p class="card-text">Annual Sports Day will be held on July 20th. All students are encouraged to participate.</p>
                        <div class="text-end">
                            <span class="text-muted small">By Admin</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-2">Library Timings Extended</h5>
                        <div class="mb-2 text-muted small">General | Jul 5, 2025</div>
                        <p class="card-text">Library will now remain open till 7 PM on weekdays for exam preparation.</p>
                        <div class="text-end">
                            <span class="text-muted small">By Admin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
