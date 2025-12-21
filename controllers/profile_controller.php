<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID and role from session
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    $redirect_path = ($user_role === 'admin') ? '../admin/profile.php' : '../dashboard/profile.php';

    if ($user_id <= 0) {
        header('Location: ../auth/login.php');
        exit;
    }

    // Handle account deletion
    if (isset($_POST['delete_account']) && $_POST['delete_account'] === '1') {
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        if (empty($confirm_password)) {
            header("Location: $redirect_path?error=Password is required to delete account");
            exit;
        }

        // Verify password
        $stmt = $conn->prepare("SELECT password, profile_image, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($confirm_password, $user['password'])) {
            header("Location: $redirect_path?error=Incorrect password. Account deletion cancelled.");
            exit;
        }

        // Start transaction for data integrity
        $conn->begin_transaction();

        try {
            // ============================================
            // DELETE ALL ASSOCIATED DATA BASED ON USER ROLE
            // ============================================

            if ($user['role'] === 'student') {
                // ---- STUDENT DATA DELETION ----

                // 1. Get all submission files to delete from filesystem
                $stmt = $conn->prepare("SELECT file FROM submissions WHERE student_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $submissions_result = $stmt->get_result();
                $submission_files = [];
                while ($row = $submissions_result->fetch_assoc()) {
                    if (!empty($row['file'])) {
                        $submission_files[] = $row['file'];
                    }
                }
                $stmt->close();

                // 2. Delete submissions from database
                $stmt = $conn->prepare("DELETE FROM submissions WHERE student_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->close();

                // 3. Delete submission files from filesystem (stored in assignments/uploads/)
                foreach ($submission_files as $file) {
                    $file_path = __DIR__ . '/../assignments/uploads/' . $file;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }

            } elseif ($user['role'] === 'admin') {
                // ---- ADMIN DATA DELETION ----
                // Delete all content created by this admin

                // 1. Delete announcements created by this admin
                $stmt = $conn->prepare("DELETE FROM announcements WHERE created_by = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->close();

                // 2. Get assignment attachment files to delete from filesystem
                $stmt = $conn->prepare("SELECT attachment FROM assignments WHERE created_by = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $assignments_result = $stmt->get_result();
                $assignment_files = [];
                while ($row = $assignments_result->fetch_assoc()) {
                    if (!empty($row['attachment'])) {
                        $assignment_files[] = $row['attachment'];
                    }
                }
                $stmt->close();

                // 3. Delete assignments from database
                $stmt = $conn->prepare("DELETE FROM assignments WHERE created_by = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->close();

                // 4. Delete assignment files from filesystem
                foreach ($assignment_files as $file) {
                    $file_path = __DIR__ . '/../assignments/uploads/' . $file;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }

                // 5. Delete timetables created by this admin
                $stmt = $conn->prepare("DELETE FROM timetables WHERE created_by = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->close();
            }

            // ============================================
            // DELETE USER'S PROFILE IMAGE
            // ============================================
            if (!empty($user['profile_image'])) {
                $profile_image_path = __DIR__ . '/../assets/uploads/profile_photos/' . $user['profile_image'];
                if (file_exists($profile_image_path)) {
                    unlink($profile_image_path);
                }
            }

            // ============================================
            // FINALLY DELETE THE USER ACCOUNT
            // ============================================
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);

            if (!$stmt->execute()) {
                throw new Exception("Failed to delete user account");
            }
            $stmt->close();

            // Commit the transaction
            $conn->commit();
            $conn->close();

            // Destroy session
            session_unset();
            session_destroy();

            // Redirect to login with success message
            header("Location: ../auth/login.php?account_deleted=1");
            exit;

        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            header("Location: $redirect_path?error=Failed to delete account. " . $e->getMessage());
            exit;
        }
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
