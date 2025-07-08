<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
destroySession();

// Redirect to login page with logout param
header("Location: ../auth/login.php?logout=1");
exit;

// Helper function to destroy session and cookie
function destroySession() {
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
}
?>
