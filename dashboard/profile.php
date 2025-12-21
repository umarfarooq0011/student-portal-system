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

<!-- Page Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">My Profile</h2>
    <p class="text-slate-500 text-sm mt-1">Manage your account settings</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden">
            <div class="relative h-32 bg-violet-600"></div>
            <div class="px-6 pb-6">
                <div class="relative -mt-16 mb-4">
                    <div class="relative inline-block">
                        <?php if (!empty($student['profile_image'])): ?>
                            <img src="../assets/uploads/profile_photos/<?php echo htmlspecialchars($student['profile_image']); ?>"
                                 class="w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-xl"
                                 alt="Profile Photo">
                        <?php else: ?>
                            <div class="w-28 h-28 rounded-2xl bg-violet-600 border-4 border-white shadow-xl flex items-center justify-center">
                                <span class="text-4xl font-bold text-white"><?= strtoupper(substr($student['full_name'], 0, 1)) ?></span>
                            </div>
                        <?php endif; ?>

                        <form action="../controllers/profile_controller.php" method="POST" enctype="multipart/form-data">
                            <input type="file" name="profile_photo" id="profile_photo" class="hidden"
                                   accept="image/jpeg,image/png,image/jpg" onchange="this.form.submit()">
                            <label for="profile_photo"
                                   class="absolute bottom-0 right-0 w-9 h-9 rounded-xl bg-white shadow-lg flex items-center justify-center cursor-pointer hover:bg-slate-50 transition-all border border-slate-200">
                                <i class="bi bi-camera-fill text-violet-600"></i>
                            </label>
                        </form>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($student['full_name']); ?></h3>
                <p class="text-slate-500 text-sm mb-4"><?php echo htmlspecialchars($student['email']); ?></p>

                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-violet-600 text-white">
                    <i class="bi bi-mortarboard-fill"></i>
                    <?php echo ucfirst(htmlspecialchars($student['role'])); ?>
                </span>

                <?php if (empty($student['profile_image'])): ?>
                <div class="mt-4 p-3 rounded-xl bg-violet-50 border border-violet-100">
                    <div class="flex items-center gap-2 text-sm text-violet-700">
                        <i class="bi bi-info-circle"></i>
                        <span>Click the camera icon to upload your photo</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Profile Card -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                        <i class="bi bi-person-gear text-violet-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Edit Profile Information</h3>
                        <p class="text-sm text-slate-500">Update your personal details</p>
                    </div>
                </div>
            </div>
            <form action="../controllers/profile_controller.php" method="POST">
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                        <div class="relative">
                            <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text"
                                   name="full_name"
                                   value="<?php echo htmlspecialchars($student['full_name']); ?>"
                                   required
                                   placeholder="Enter your full name"
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-violet-100 focus:border-violet-400 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                        <div class="relative">
                            <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="email"
                                   value="<?php echo htmlspecialchars($student['email']); ?>"
                                   disabled
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                        </div>
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                            <i class="bi bi-lock"></i>
                            Email address cannot be changed
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                        <div class="relative">
                            <i class="bi bi-mortarboard absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text"
                                   value="<?php echo ucfirst(htmlspecialchars($student['role'])); ?>"
                                   disabled
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-violet-600 text-white font-semibold rounded-xl hover:bg-violet-700 transition-all shadow-lg shadow-violet-500/25">
                        <i class="bi bi-check-lg"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle URL parameters for notifications
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('error')) {
        StudentNotify.error(urlParams.get('error'));
        if (window.history.replaceState) {
            const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }

    if (urlParams.has('success')) {
        StudentNotify.success(urlParams.get('success'));
        if (window.history.replaceState) {
            const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
