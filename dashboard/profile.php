<?php
require_once '../auth/authsession.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';
require_once '../config/db.php';

// Get student user id from session
$student_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Fetch student details
if ($student_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

if (!$student) {
    header('Location: ../auth/login.php');
    exit;
}
?>

<div class="main-content">
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">My Profile</h2>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-4 position-relative">
                            <div class="profile-photo-container position-relative d-inline-block">
                                <?php if (!empty($student['profile_image'])): ?>
                                    <img src="../assets/uploads/profile_photos/<?php echo htmlspecialchars($student['profile_image']); ?>" 
                                         class="rounded-circle profile-image"
                                         style="width: 120px; height: 120px; object-fit: cover;" 
                                         alt="Profile Photo">
                                <?php else: ?>
                                    <div class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 120px; height: 120px; background-color: #f8f9fa; border: 2px dashed #0d6efd;">
                                        <i class="bi bi-person-circle" style="font-size: 60px; color: #0d6efd;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="../controllers/profile_controller.php" 
                                      method="POST" 
                                      enctype="multipart/form-data" 
                                      class="profile-upload-form">
                                    <input type="file" 
                                           name="profile_photo" 
                                           id="profile_photo" 
                                           class="d-none" 
                                           accept="image/jpeg,image/png,image/jpg"
                                           onchange="this.form.submit()">
                                    <label for="profile_photo" 
                                           class="btn btn-light btn-sm upload-btn position-absolute"
                                           style="bottom: 0; right: 0; border-radius: 50%; width: 32px; height: 32px; padding: 0; line-height: 32px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                </form>
                            </div>
                            <?php if (empty($student['profile_image'])): ?>
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-info-circle"></i> 
                                    Click the camera icon to upload your profile photo
                                </div>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h5>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($student['email']); ?></p>
                        <span class="badge bg-primary text-capitalize px-3 py-2">
                            <i class="bi bi-mortarboard me-1"></i>
                            <?php echo htmlspecialchars($student['role']); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Card -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="card-title mb-0">Edit Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="../controllers/profile_controller.php" method="POST">
                            <div class="mb-4">
                                <label for="full_name" class="form-label">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="full_name" 
                                           name="full_name" 
                                           value="<?php echo htmlspecialchars($student['full_name']); ?>" 
                                           required
                                           placeholder="Enter your full name">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted mb-2">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control bg-light" 
                                           value="<?php echo htmlspecialchars($student['email']); ?>" 
                                           readonly
                                           disabled>
                                </div>
                                <small class="text-muted">Email address cannot be changed</small>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
