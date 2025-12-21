<?php
require_once __DIR__ . '/../config/db.php';

class Timetable {
    public function getAll() {
        global $conn;
        $sql = "SELECT * FROM timetables ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday')";
        return mysqli_query($conn, $sql);
    }

    public function create($data, $created_by = null) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO timetables (day_of_week, subject, start_time, end_time, room, teacher, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $data['day_of_week'], $data['subject'], $data['start_time'], $data['end_time'], $data['room'], $data['teacher'], $created_by);
        return $stmt->execute();
    }

    public function delete($id) {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM timetables WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getById($id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM timetables WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $data) {
        global $conn;
        $stmt = $conn->prepare("UPDATE timetables SET day_of_week=?, subject=?, start_time=?, end_time=?, room=?, teacher=? WHERE id=?");
        $stmt->bind_param("ssssssi", $data['day_of_week'], $data['subject'], $data['start_time'], $data['end_time'], $data['room'], $data['teacher'], $id);
        return $stmt->execute();
    }
}
