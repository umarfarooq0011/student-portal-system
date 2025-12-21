<?php
session_start();
require_once '../models/timetable.php';
$timetableModel = new Timetable();
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = '';
    $status = 'error';

    if (isset($_POST['add'])) {
        $data = [
            'day_of_week' => $_POST['day_of_week'],
            'subject' => $_POST['subject'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'room' => $_POST['room'],
            'teacher' => $_POST['teacher']
        ];
        $result = $timetableModel->create($data, $user_id);
        $action = 'added';
        $status = $result ? 'success' : 'error';
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $data = [
            'day_of_week' => $_POST['day_of_week'],
            'subject' => $_POST['subject'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'room' => $_POST['room'],
            'teacher' => $_POST['teacher']
        ];
        $result = $timetableModel->update($id, $data);
        $action = 'updated';
        $status = $result ? 'success' : 'error';
    } elseif (isset($_POST['delete_id'])) {
        $result = $timetableModel->delete($_POST['delete_id']);
        $action = 'deleted';
        $status = $result ? 'success' : 'error';
    }

    header("Location: ../admin/manage_timetable.php?action={$action}&status={$status}");
    exit;
}
