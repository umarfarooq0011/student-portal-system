<?php
session_start();
require_once '../models/Announcement.php';

$announcementModel = new Announcement();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = '';
    $status = 'error';

    if (isset($_POST['add'])) {
        $data = [
            'title'    => $_POST['title'],
            'category' => $_POST['category'],
            'content'  => $_POST['content'],
            'status'   => isset($_POST['status']) ? $_POST['status'] : 'Inactive'
        ];
        $result = $announcementModel->create($data, $user_id);
        $action = 'added';
        $status = $result ? 'success' : 'error';
    } elseif (isset($_POST['update_id'])) {
        $data = [
            'title'    => $_POST['title'],
            'category' => $_POST['category'],
            'content'  => $_POST['content'],
            'status'   => isset($_POST['status']) ? $_POST['status'] : 'Inactive'
        ];
        $result = $announcementModel->update($_POST['update_id'], $data);
        $action = 'updated';
        $status = $result ? 'success' : 'error';
    } elseif (isset($_POST['delete_id'])) {
        $result = $announcementModel->delete($_POST['delete_id']);
        $action = 'deleted';
        $status = $result ? 'success' : 'error';
    }

    header("Location: ../admin/manage_announcements.php?action={$action}&status={$status}");
    exit;
}
