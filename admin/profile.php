<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../config/db.php';

// Get admin user id from session
$admin_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

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

<!-- Page Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800">My Profile</h2>
    <p class="text-slate-500 text-sm mt-1">Manage your account settings</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Card -->
    <div class="lg:col-span-1">
        <div class="premium-card bg-white rounded-2xl border border-slate-200/60 shadow-lg shadow-slate-200/50 overflow-hidden">
            <div class="relative h-32 bg-sky-600"></div>
            <div class="px-6 pb-6">
                <div class="relative -mt-16 mb-4">
                    <div class="relative inline-block">
                        <?php if (!empty($admin['profile_image'])): ?>
                            <img src="../assets/uploads/profile_photos/<?php echo htmlspecialchars($admin['profile_image']); ?>"
                                 class="w-28 h-28 rounded-2xl object-cover border-4 border-white shadow-xl"
                                 alt="Profile Photo">
                        <?php else: ?>
                            <div class="w-28 h-28 rounded-2xl bg-sky-600 border-4 border-white shadow-xl flex items-center justify-center">
                                <span class="text-4xl font-bold text-white"><?= strtoupper(substr($admin['full_name'], 0, 1)) ?></span>
                            </div>
                        <?php endif; ?>

                        <form action="../controllers/profile_controller.php" method="POST" enctype="multipart/form-data">
                            <input type="file" name="profile_photo" id="profile_photo" class="hidden"
                                   accept="image/jpeg,image/png,image/jpg" onchange="this.form.submit()">
                            <label for="profile_photo"
                                   class="absolute bottom-0 right-0 w-9 h-9 rounded-xl bg-white shadow-lg flex items-center justify-center cursor-pointer hover:bg-slate-50 transition-all border border-slate-200">
                                <i class="bi bi-camera-fill text-sky-600"></i>
                            </label>
                        </form>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($admin['full_name']); ?></h3>
                <p class="text-slate-500 text-sm mb-4"><?php echo htmlspecialchars($admin['email']); ?></p>

                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-sky-600 text-white">
                    <i class="bi bi-shield-check"></i>
                    <?php echo ucfirst(htmlspecialchars($admin['role'])); ?>
                </span>

                <?php if (empty($admin['profile_image'])): ?>
                <div class="mt-4 p-3 rounded-xl bg-sky-50 border border-sky-100">
                    <div class="flex items-center gap-2 text-sm text-sky-700">
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
        <div class="premium-card bg-white rounded-2xl border border-slate-200/60 shadow-lg shadow-slate-200/50 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                        <i class="bi bi-person-gear text-sky-600"></i>
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
                                   value="<?php echo htmlspecialchars($admin['full_name']); ?>"
                                   required
                                   placeholder="Enter your full name"
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                        <div class="relative">
                            <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="email"
                                   value="<?php echo htmlspecialchars($admin['email']); ?>"
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
                            <i class="bi bi-shield-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text"
                                   value="<?php echo ucfirst(htmlspecialchars($admin['role'])); ?>"
                                   disabled
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed">
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-sky-600 text-white font-semibold rounded-xl hover:bg-sky-700 transition-all">
                        <i class="bi bi-check-lg"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Danger Zone -->
<div class="mt-6">
    <div class="premium-card bg-white rounded-2xl border border-red-200 shadow-lg shadow-red-100/50 overflow-hidden">
        <div class="p-6 border-b border-red-100 bg-red-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800">Danger Zone</h3>
                    <p class="text-sm text-red-600">Irreversible and destructive actions</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h4 class="font-semibold text-slate-800">Delete Account</h4>
                    <p class="text-sm text-slate-500 mt-1">Permanently delete your admin account and all associated data. This action cannot be undone.</p>
                </div>
                <button onclick="openDeleteAccountModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/25 hover:bg-red-700 hover:shadow-xl transition-all whitespace-nowrap">
                    <i class="bi bi-trash3"></i>
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm" onclick="closeDeleteAccountModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="relative bg-red-600 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-red-500 flex items-center justify-center">
                        <i class="bi bi-exclamation-triangle-fill text-xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Delete Account</h3>
                        <p class="text-red-100 text-xs">This action is permanent and cannot be undone</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form action="../controllers/profile_controller.php" method="POST" id="deleteAccountForm">
                <input type="hidden" name="delete_account" value="1">
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-5">
                        <!-- Left Column: Warning -->
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100">
                            <div class="flex gap-2 mb-3">
                                <i class="bi bi-info-circle text-red-600 flex-shrink-0 mt-0.5"></i>
                                <p class="text-sm font-semibold text-red-700">This will permanently delete:</p>
                            </div>
                            <ul class="text-xs text-red-600 space-y-1.5 ml-5 list-disc">
                                <li>Your admin account</li>
                                <li>Your profile photo</li>
                                <li>All announcements you created</li>
                                <li>All assignments & attachments</li>
                                <li>All timetable entries</li>
                                <li>All your session data</li>
                            </ul>
                            <div class="mt-3 pt-3 border-t border-red-200">
                                <p class="text-xs text-red-500 flex items-start gap-1">
                                    <i class="bi bi-exclamation-triangle flex-shrink-0 mt-0.5"></i>
                                    <span>Complete data wipeout. Cannot be undone.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Right Column: Verification -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Enter your password
                                </label>
                                <div class="relative">
                                    <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                    <input type="password" name="confirm_password" id="confirmDeletePassword" required
                                        placeholder="Password"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Type <span class="text-red-600 font-mono">DELETE</span>
                                </label>
                                <input type="text" id="deleteConfirmText" required
                                    placeholder="Type DELETE in capitals"
                                    pattern="DELETE"
                                    class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 transition-all font-mono uppercase">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeDeleteAccountModal()"
                        class="px-5 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-600 font-semibold hover:bg-slate-50 transition-all text-sm">
                        Cancel
                    </button>
                    <button type="submit" id="confirmDeleteBtn" disabled
                        class="px-5 py-2.5 rounded-lg bg-red-600 text-white font-semibold shadow-lg shadow-red-500/20 hover:bg-red-700 transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        <i class="bi bi-trash3"></i>
                        Delete My Account
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
        AdminNotify.error(urlParams.get('error'));
        // Clean up URL
        if (window.history.replaceState) {
            const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }

    if (urlParams.has('success')) {
        AdminNotify.success(urlParams.get('success'));
        // Clean up URL
        if (window.history.replaceState) {
            const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }
});

// Delete Account Modal Functions
function openDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    // Reset form
    document.getElementById('deleteAccountForm').reset();
    document.getElementById('confirmDeleteBtn').disabled = true;
}

function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Enable delete button only when DELETE is typed correctly
document.getElementById('deleteConfirmText').addEventListener('input', function() {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const passwordField = document.getElementById('confirmDeletePassword');

    if (this.value.toUpperCase() === 'DELETE' && passwordField.value.length > 0) {
        deleteBtn.disabled = false;
    } else {
        deleteBtn.disabled = true;
    }
});

document.getElementById('confirmDeletePassword').addEventListener('input', function() {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const confirmText = document.getElementById('deleteConfirmText');

    if (confirmText.value.toUpperCase() === 'DELETE' && this.value.length > 0) {
        deleteBtn.disabled = false;
    } else {
        deleteBtn.disabled = true;
    }
});

// Confirm before final submission
document.getElementById('deleteAccountForm').addEventListener('submit', function(e) {
    const confirmText = document.getElementById('deleteConfirmText').value.toUpperCase();
    if (confirmText !== 'DELETE') {
        e.preventDefault();
        alert('Please type DELETE to confirm');
        return false;
    }

    if (!confirm('Are you absolutely sure? This action CANNOT be undone!')) {
        e.preventDefault();
        return false;
    }
});
</script>

<?php require_once '../admin_includes/footer.php'; ?>
