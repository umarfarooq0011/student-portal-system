<?php
// Handle feedback/grade POST before any output
require_once '../auth/authsession.php';
$conn = $GLOBALS['conn'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'])) {
    $assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
    $submission_id = intval($_POST['submission_id']);
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];
    $stmt2 = $conn->prepare("UPDATE submissions SET grade=?, feedback=? WHERE id=?");
    $stmt2->bind_param("ssi", $grade, $feedback, $submission_id);
    $stmt2->execute();
    header("Location: view_submissions.php?assignment_id=$assignment_id&updated=1");
    exit;
}

require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../models/Submission.php';
require_once '../models/Assignment.php';

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;
$assignment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM assignments WHERE id=$assignment_id"));

// Fetch submissions with student full name
$stmt = $conn->prepare("SELECT s.*, u.full_name as student_name FROM submissions s JOIN users u ON s.student_id = u.id WHERE s.assignment_id=?");
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$submissions = $stmt->get_result();
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <h2>Submissions for: <?= htmlspecialchars($assignment['title']) ?></h2>
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Feedback/Grade updated!</div>
        <?php endif; ?>
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>File</th>
                                <th>Comment</th>
                                <th>Submitted At</th>
                                <th>Grade</th>
                                <th>Feedback</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $submissions->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['student_name']) ?></td>
                                <td>
                                    <?php if ($row['file']): ?>
                                        <a href="../assignments/uploads/<?= htmlspecialchars($row['file']) ?>" target="_blank">Download</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['comment']) ?></td>
                                <td><?= htmlspecialchars($row['submitted_at']) ?></td>
                                <td><?= htmlspecialchars($row['grade']) ?></td>
                                <td><?= htmlspecialchars($row['feedback']) ?></td>
                                <td>
                                    <!-- Feedback/Grade Form -->
                                    <form method="POST" class="d-flex flex-column gap-1">
                                        <input type="hidden" name="submission_id" value="<?= $row['id'] ?>">
                                        <input type="text" name="grade" class="form-control form-control-sm" placeholder="Grade" value="<?= htmlspecialchars($row['grade']) ?>">
                                        <input type="text" name="feedback" class="form-control form-control-sm" placeholder="Feedback" value="<?= htmlspecialchars($row['feedback']) ?>">
                                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../admin_includes/footer.php'; ?>
