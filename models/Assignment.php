<?php
require_once __DIR__ . '/../config/db.php';

class Assignment
{
    public function getAll()
    {
        global $conn;
        $query = "SELECT * FROM assignments ORDER BY created_at DESC";
        return mysqli_query($conn, $query);
    }

    public function getById($id)
    {
        global $conn;
        $id = intval($id);
        $query = "SELECT * FROM assignments WHERE id = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data, $filename, $created_by = null)
    {
        global $conn;
        $title = $data['title'];
        $subject = $data['subject'];
        $due_date = $data['due_date'];
        $instructions = $data['instructions'];
        $allow_late = isset($data['allow_late']) ? 1 : 0;

        $stmt = $conn->prepare("INSERT INTO assignments (title, subject, due_date, instructions, attachment, allow_late, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssii", $title, $subject, $due_date, $instructions, $filename, $allow_late, $created_by);
        return $stmt->execute();
    }

    public function update($id, $data, $filename = null)
    {
        global $conn;
        $title = $data['title'];
        $subject = $data['subject'];
        $due_date = $data['due_date'];
        $instructions = $data['instructions'];
        $allow_late = isset($data['allow_late']) ? 1 : 0;
        if ($filename) {
            $stmt = $conn->prepare("UPDATE assignments SET title=?, subject=?, due_date=?, instructions=?, attachment=?, allow_late=? WHERE id=?");
            $stmt->bind_param("ssssssi", $title, $subject, $due_date, $instructions, $filename, $allow_late, $id);
        } else {
            $stmt = $conn->prepare("UPDATE assignments SET title=?, subject=?, due_date=?, instructions=?, allow_late=? WHERE id=?");
            $stmt->bind_param("ssssii", $title, $subject, $due_date, $instructions, $allow_late, $id);
        }
        return $stmt->execute();
    }

    public function delete($id)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM assignments WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
