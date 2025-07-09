<?php
require_once '../models/Announcement.php';

$announcementModel = new Announcement();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $data = [
            'title'    => $_POST['title'],
            'category' => $_POST['category'],
            'content'  => $_POST['content'],
            'status'   => isset($_POST['status']) ? $_POST['status'] : 'Inactive'
        ];
        $announcementModel->create($data);
    } elseif (isset($_POST['update_id'])) {
        $data = [
            'title'    => $_POST['title'],
            'category' => $_POST['category'],
            'content'  => $_POST['content'],
            'status'   => $_POST['status']
        ];
        $announcementModel->update($_POST['update_id'], $data);
    } elseif (isset($_POST['delete_id'])) {
        $announcementModel->delete($_POST['delete_id']);
    }

    header('Location: ../admin/manage_announcements.php');
    exit;
}
