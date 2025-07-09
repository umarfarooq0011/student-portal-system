<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Timetable</h2>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Day</th>
                        <th>Subject</th>
                        <th>Time</th>
                        <th>Room</th>
                        <th>Teacher</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Monday</td>
                        <td>Mathematics</td>
                        <td>9:00 AM - 10:00 AM</td>
                        <td>Room 101</td>
                        <td>Mr. Ali</td>
                    </tr>
                    <tr>
                        <td>Monday</td>
                        <td>Science</td>
                        <td>10:15 AM - 11:15 AM</td>
                        <td>Room 102</td>
                        <td>Ms. Fatima</td>
                    </tr>
                    <tr>
                        <td>Tuesday</td>
                        <td>English</td>
                        <td>9:00 AM - 10:00 AM</td>
                        <td>Room 103</td>
                        <td>Mr. Ahmed</td>
                    </tr>
                    <!-- More rows as needed -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
