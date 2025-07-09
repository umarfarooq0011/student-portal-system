<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
?>
<div class="main-content">
    <div class="container-fluid p-4">
        <h2 class="mb-4">Profile</h2>
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h5 class="card-title mb-3">Personal Information</h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($_SESSION['role']); ?></p>
                <a href="#" class="btn btn-primary disabled">Edit Profile (Coming Soon)</a>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
