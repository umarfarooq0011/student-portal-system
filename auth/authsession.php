<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Check if user exists in the database
require_once __DIR__ . '/../config/db.php';
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    // User does not exist, destroy session and redirect
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php');
    exit;
}
$stmt->close();

// Role-based route protection
$current_page = basename($_SERVER['PHP_SELF']);
$isAdminPage = strpos($_SERVER['REQUEST_URI'], 'admin') !== false;
$isDashboardPage = strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false;

// If not admin, block admin pages
if ($isAdminPage && $_SESSION['role'] !== 'admin') {
    header('Location: ../dashboard/index.php');
    exit;
}
// If admin, block dashboard pages
if ($isDashboardPage && $_SESSION['role'] === 'admin') {
    header('Location: ../admin/index.php');
    exit;
}
?>
