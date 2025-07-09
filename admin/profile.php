<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../config/db.php';

// Get admin user id from session
$admin_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$success_message = $error_message = '';

// Fetch admin details
if ($admin_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
}

if (!$admin) {
    header('Location: ../auth/login.php');
    exit;
}
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-person-circle mb-3" style="font-size: 100px; color: #0d6efd;"></i>
                        <h4 class="mb-2"><?php echo htmlspecialchars($admin['full_name']); ?></h4>
                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
                        <span class="badge bg-primary text-capitalize"><?php echo htmlspecialchars($admin['role']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../admin_includes/footer.php'; ?>