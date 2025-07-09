<?php
require_once '../models/Assignment.php';
$assignment = new Assignment();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DELETE
    if (isset($_POST['delete_assignment_id'])) {
        $id = intval($_POST['delete_assignment_id']);
        $result = $assignment->delete($id);
        header("Location: ../admin/manage_assignments.php?" . ($result ? "success=1" : "error=1"));
        exit;
    }

    // EDIT/UPDATE
    if (isset($_POST['edit_assignment_id'])) {
        $id = intval($_POST['edit_assignment_id']);
        $filename = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assignments/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $originalName = basename($_FILES['attachment']['name']);
            $filename = time() . '_' . $originalName;
            move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $filename);
        }
        $result = $assignment->update($id, $_POST, $filename);
        header("Location: ../admin/manage_assignments.php?" . ($result ? "success=1" : "error=1"));
        exit;
    }

    // CREATE
    $filename = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assignments/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $originalName = basename($_FILES['attachment']['name']);
        $filename = time() . '_' . $originalName;
        move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $filename);
    }
    $result = $assignment->create($_POST, $filename);
    header("Location: ../admin/manage_assignments.php?" . ($result ? "success=1" : "error=1"));
    exit;
}
