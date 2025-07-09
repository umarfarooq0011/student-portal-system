<?php
require_once '../models/Submission.php';
$submissionModel = new Submission();

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        'assignment_id' => $_POST['assignment_id'],
        'student_id' => $_SESSION['user_id'],
        'comment' => $_POST['comment'] ?? ''
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
