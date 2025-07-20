<?php
require_once '../models/Submission.php';
require_once '../models/Assignment.php';

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignmentModel = new Assignment();
    $submissionModel = new Submission();

    // Get assignment details to check due date
    $assignment_id = $_POST['assignment_id'];
    $assignment = $assignmentModel->getById($assignment_id);
    
    if (!$assignment) {
        header('Location: ../dashboard/assignments.php?error=Assignment not found');
        exit;
    }

    // Check if submission is allowed
    $today = date('Y-m-d');
    $is_overdue = strtotime($assignment['due_date']) < strtotime($today);
    
    if ($is_overdue && !$assignment['allow_late']) {
        header('Location: ../dashboard/assignments.php?error=This assignment does not accept late submissions');
        exit;
    }

    $filename = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assignments/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $originalName = basename($_FILES['file']['name']);
        $filename = time() . '_' . $originalName;
        move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $filename);
    }

    $data = [
        'assignment_id' => $assignment_id,
        'student_id' => $_SESSION['user_id'],
        'comment' => $_POST['comment'] ?? '',
        'is_late' => $is_overdue ? 1 : 0 // Mark if submission is late
    ];
    $result = $submissionModel->create($data, $filename);
    if ($result) {
        header('Location: ../dashboard/assignments.php?submitted=1');
        exit;
    } else {
        header('Location: ../dashboard/assignments.php?error=1');
        exit;
    }
}
