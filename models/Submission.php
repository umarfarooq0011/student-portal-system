<?php
require_once __DIR__ . '/../config/db.php';

class Submission
{
    public function create($data, $filename)
    {
        global $conn;
        $assignment_id = $data['assignment_id'];
        $student_id = $data['student_id'];
        $comment = $data['comment'];
        $stmt = $conn->prepare("INSERT INTO submissions (assignment_id, student_id, file, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $assignment_id, $student_id, $filename, $comment);
        return $stmt->execute();
    }

    public function getByAssignmentAndStudent($assignment_id, $student_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM submissions WHERE assignment_id=? AND student_id=?");
        $stmt->bind_param("ii", $assignment_id, $student_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getByAssignment($assignment_id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM submissions WHERE assignment_id=?");
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
