<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID and role from session
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $user_role = $_SESSION['role'];
    $redirect_path = ($user_role === 'admin') ? '../admin/profile.php' : '../dashboard/profile.php';
    
    if ($user_id <= 0) {
        header('Location: ../auth/login.php');
        exit;
    }

    // Handle photo upload
    if (isset($_FILES['profile_photo'])) {
        $file = $_FILES['profile_photo'];
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $max_size = 2 * 1024 * 1024; // 2MB

            // Validate file type
            if (!in_array($file['type'], $allowed_types)) {
                header("Location: $redirect_path?error=Invalid file type. Only JPG, JPEG, and PNG are allowed");
                exit;
            }

            // Validate file size
            if ($file['size'] > $max_size) {
                header("Location: $redirect_path?error=File is too large. Maximum size is 2MB");
                exit;
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
            $upload_path = __DIR__ . '/../assets/uploads/profile_photos/' . $filename;

            // Upload file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Delete old profile photo if exists
                $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                if ($user && !empty($user['profile_image'])) {
                    $old_file = __DIR__ . '/../assets/uploads/profile_photos/' . $user['profile_image'];
                    if (file_exists($old_file)) {
                        unlink($old_file);
                    }
                }

                // Update database
                $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->bind_param("si", $filename, $user_id);
                
                if ($stmt->execute()) {
                    header("Location: $redirect_path?success=Profile photo updated successfully");
                } else {
                    header("Location: $redirect_path?error=Failed to update profile photo");
                }
                exit;
            } else {
                header("Location: $redirect_path?error=Failed to upload file");
                exit;
            }
        } else {
            header("Location: $redirect_path?error=File upload error");
            exit;
        }
    }

    // Handle name update
    if (isset($_POST['full_name'])) {
        $new_name = trim($_POST['full_name']);
    
        // Determine redirect path based on user role
        $user_role = $_SESSION['role'];
        $redirect_path = ($user_role === 'admin') ? '../admin/profile.php' : '../dashboard/profile.php';

        if (empty($new_name)) {
            header("Location: $redirect_path?error=Name cannot be empty");
            exit;
        }

        // Update the name in database
        $stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        $stmt->bind_param("si", $new_name, $user_id);
        
        if ($stmt->execute()) {
            // Update session name
            $_SESSION['full_name'] = $new_name;
            header("Location: $redirect_path?success=Profile updated successfully");
        } else {
            header("Location: $redirect_path?error=Failed to update profile");
        }
        
        $stmt->close();
        $conn->close();
        exit;
    }
} else {
    $user_role = $_SESSION['role'];
    $redirect_path = ($user_role === 'admin') ? '../admin/profile.php' : '../dashboard/profile.php';
    header("Location: $redirect_path");
    exit;
}
?>
