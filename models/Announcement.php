<?php
require_once __DIR__ . '/../config/db.php';

class Announcement
{
    public function getAll()
    {
        global $conn;
        $result = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC");
        $announcements = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $announcements[] = $row;
        }
        return $announcements;
    }

    public function create($data)
    {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO announcements (title, category, content, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $data['title'], $data['category'], $data['content'], $data['status']);
        return $stmt->execute();
    }

    public function delete($id)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        global $conn;
        $stmt = $conn->prepare("UPDATE announcements SET title = ?, category = ?, content = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $data['title'], $data['category'], $data['content'], $data['status'], $id);
        return $stmt->execute();
    }

    public function getById($id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
