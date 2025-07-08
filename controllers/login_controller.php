<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Sanitize inputs
    $email = mysqli_real_escape_string($conn, $email);
    $role = mysqli_real_escape_string($conn, $role);

    // Prepare statement
    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Match password using password_verify for hashed passwords
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../dashboard/index.php");
            }
            exit;
        } else {
            header("Location: ../auth/login.php?error=Invalid+password.");
            exit;
        }
    } else {
        header("Location: ../auth/login.php?error=User+not+found.");
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../auth/login.php?error=Invalid+request.");
    exit;
}
