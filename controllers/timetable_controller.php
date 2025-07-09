<?php
require_once '../models/timetable.php';
$timetableModel = new Timetable();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $data = [
            'day_of_week' => $_POST['day_of_week'],
            'subject' => $_POST['subject'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'room' => $_POST['room'],
            'teacher' => $_POST['teacher']
        ];
        $timetableModel->create($data);
    } elseif (isset($_POST['delete_id'])) {
        $timetableModel->delete($_POST['delete_id']);
    }
    header('Location: ../admin/manage_timetable.php');
    exit;
}
