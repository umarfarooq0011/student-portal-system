<?php
require_once '../auth/authsession.php';
require_once '../models/Announcement.php';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
require_once '../includes/navbar.php';

$announcementModel = new Announcement();
$announcements = $announcementModel->getAll();
$activeAnnouncements = array_filter($announcements, function($a) { return $a['status'] === 'Active'; });
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Announcements</h1>
        <p class="text-slate-500 text-sm mt-1">Stay updated with the latest news</p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 border border-amber-100">
        <i class="bi bi-megaphone-fill text-amber-600"></i>
        <span class="text-sm font-semibold text-amber-700"><?= count($activeAnnouncements) ?> Active</span>
    </div>
</div>

<!-- Announcements List -->
<?php if (!empty($activeAnnouncements)): ?>
<div class="space-y-4">
    <?php
    $isFirst = true;
    foreach ($activeAnnouncements as $announcement):
        $category = strtolower($announcement['category']);
        if ($category === 'academic') {
            $icon_bg = 'bg-violet-500';
            $badge_bg = 'bg-violet-100 text-violet-700';
        } elseif ($category === 'event') {
            $icon_bg = 'bg-amber-500';
            $badge_bg = 'bg-amber-100 text-amber-700';
        } elseif ($category === 'general') {
            $icon_bg = 'bg-sky-500';
            $badge_bg = 'bg-sky-100 text-sky-700';
        } else {
            $icon_bg = 'bg-slate-500';
            $badge_bg = 'bg-slate-100 text-slate-700';
        }
    ?>
    <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
        <div class="flex gap-4">
            <!-- Icon -->
            <div class="w-12 h-12 rounded-xl <?= $icon_bg ?> flex items-center justify-center flex-shrink-0">
                <i class="bi bi-megaphone-fill text-white text-lg"></i>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <!-- Header -->
                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($announcement['title']) ?></h3>
                        <?php if ($isFirst && $category === 'academic'): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                            <i class="bi bi-star-fill"></i> Important
                        </span>
                        <?php $isFirst = false; endif; ?>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="px-2.5 py-1 rounded-lg <?= $badge_bg ?> font-medium text-xs">
                            <?= htmlspecialchars(ucfirst($announcement['category'])) ?>
                        </span>
                    </div>
                </div>

                <!-- Body -->
                <p class="text-slate-600 text-sm leading-relaxed mb-3"><?= nl2br(htmlspecialchars($announcement['content'])) ?></p>

                <!-- Footer -->
                <div class="flex items-center gap-4 text-xs text-slate-400">
                    <span class="flex items-center gap-1">
                        <i class="bi bi-calendar3"></i>
                        <?= date('M d, Y', strtotime($announcement['created_at'])) ?>
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="bi bi-person"></i>
                        Posted by Admin
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<!-- Empty State -->
<div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
    <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
        <i class="bi bi-megaphone text-4xl text-slate-400"></i>
    </div>
    <h3 class="text-lg font-bold text-slate-800 mb-2">No Announcements</h3>
    <p class="text-slate-500 max-w-md mx-auto">There are no announcements at the moment. Check back later for updates.</p>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
