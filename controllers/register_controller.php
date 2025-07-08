<?php
require_once '../config/db.php'; // adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $password  = trim($_POST['password']);
    $role      = $_POST['role'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email already exists
        header("Location: ../auth/register.php?error=Email+already+registered");
        exit;
    }

    // Insert user into DB
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        header("Location: ../auth/login.php?success=Registration+successful!+You+can+now+login.");
        exit;
    } else {
        header("Location: ../auth/register.php?error=Error+occurred.+Please+try+again.");
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../auth/register.php");
    exit;
}
?>
