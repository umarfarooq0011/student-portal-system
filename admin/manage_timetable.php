<?php
require_once '../auth/authsession.php';
require_once '../admin_includes/header.php';
require_once '../admin_includes/sidebar.php';
require_once '../admin_includes/navbar.php';
require_once '../models/Timetable.php';

$timetableModel = new Timetable();
$timetables = $timetableModel->getAll();
$timetableData = [];
while ($row = mysqli_fetch_assoc($timetables)) {
    $timetableData[] = $row;
}
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Manage Timetables</h2>
        <p class="text-slate-500 text-sm mt-1">Create and manage class schedules</p>
    </div>
    <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-600 text-white font-semibold rounded-xl shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 transition-all">
        <i class="bi bi-plus-lg"></i>
        <span>Add Timetable Entry</span>
    </button>
</div>

<!-- Timetable Card -->
<div class="premium-card bg-white rounded-2xl border border-slate-200/60 shadow-lg shadow-slate-200/50 overflow-hidden">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Day</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Room</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Teacher</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach ($timetableData as $row): ?>
                <?php
                $dayColors = [
                    'Monday' => 'bg-sky-100 text-sky-600',
                    'Tuesday' => 'bg-emerald-100 text-emerald-600',
                    'Wednesday' => 'bg-amber-100 text-amber-600',
                    'Thursday' => 'bg-purple-100 text-purple-600',
                    'Friday' => 'bg-rose-100 text-rose-600'
                ];
                $dayColor = $dayColors[$row['day_of_week']] ?? 'bg-slate-100 text-slate-600';
                ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg <?= $dayColor ?> flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-calendar-day"></i>
                            </div>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['day_of_week']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                            <?= htmlspecialchars($row['subject']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-clock text-slate-400"></i>
                            <span class="text-sm text-slate-600">
                                <?= date("g:i A", strtotime($row['start_time'])) ?> - <?= date("g:i A", strtotime($row['end_time'])) ?>
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-geo-alt text-slate-400"></i>
                            <span class="text-sm text-slate-600"><?= htmlspecialchars($row['room']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-sky-600 flex items-center justify-center text-white text-xs font-bold">
                                <?= strtoupper(substr($row['teacher'], 0, 1)) ?>
                            </div>
                            <span class="text-sm text-slate-700"><?= htmlspecialchars($row['teacher']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['day_of_week']) ?>', '<?= htmlspecialchars(addslashes($row['subject'])) ?>', '<?= $row['start_time'] ?>', '<?= $row['end_time'] ?>', '<?= htmlspecialchars(addslashes($row['room'])) ?>', '<?= htmlspecialchars(addslashes($row['teacher'])) ?>')"
                                class="p-2 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 transition-all" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="../controllers/timetable_controller.php" class="inline">
                                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this entry?')"
                                    class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Timetable Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAddModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="relative bg-sky-600 px-6 py-8">
                <div class="absolute top-4 right-4">
                    <button onclick="closeAddModal()" class="p-2 rounded-xl bg-white/20 hover:bg-white/30 text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                        <i class="bi bi-calendar-plus text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Add Timetable Entry</h3>
                        <p class="text-sky-100 text-sm">Create a new class schedule</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="../controllers/timetable_controller.php" class="overflow-y-auto max-h-[60vh]">
                <input type="hidden" name="add" value="1">
                <div class="p-6 space-y-5">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-calendar-week text-sky-500"></i> Day
                        </label>
                        <select name="day_of_week" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                            <option value="">Select Day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-book text-indigo-500"></i> Subject
                        </label>
                        <input type="text" name="subject" required placeholder="e.g. Mathematics"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <i class="bi bi-clock text-emerald-500"></i> Start Time
                            </label>
                            <input type="time" name="start_time" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <i class="bi bi-clock-history text-amber-500"></i> End Time
                            </label>
                            <input type="time" name="end_time" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-geo-alt text-rose-500"></i> Room
                        </label>
                        <input type="text" name="room" required placeholder="e.g. Room 101"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-person text-purple-500"></i> Teacher
                        </label>
                        <input type="text" name="teacher" required placeholder="e.g. Dr. John Smith"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 text-white font-semibold shadow-lg shadow-sky-500/25 hover:shadow-xl transition-all flex items-center gap-2">
                        <i class="bi bi-check-lg"></i> Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Timetable Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="relative bg-amber-500 px-6 py-8">
                <div class="absolute top-4 right-4">
                    <button onclick="closeEditModal()" class="p-2 rounded-xl bg-white/20 hover:bg-white/30 text-white transition-all">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center">
                        <i class="bi bi-pencil-square text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Edit Timetable Entry</h3>
                        <p class="text-amber-100 text-sm">Update class schedule details</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="../controllers/timetable_controller.php" class="overflow-y-auto max-h-[60vh]">
                <input type="hidden" name="edit" value="1">
                <input type="hidden" name="id" id="editId">
                <div class="p-6 space-y-5">
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-calendar-week text-sky-500"></i> Day
                        </label>
                        <select name="day_of_week" id="editDay" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-book text-indigo-500"></i> Subject
                        </label>
                        <input type="text" name="subject" id="editSubject" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <i class="bi bi-clock text-emerald-500"></i> Start Time
                            </label>
                            <input type="time" name="start_time" id="editStartTime" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                                <i class="bi bi-clock-history text-amber-500"></i> End Time
                            </label>
                            <input type="time" name="end_time" id="editEndTime" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-geo-alt text-rose-500"></i> Room
                        </label>
                        <input type="text" name="room" id="editRoom" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 mb-2">
                            <i class="bi bi-person text-purple-500"></i> Teacher
                        </label>
                        <input type="text" name="teacher" id="editTeacher" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 focus:outline-none focus:ring-4 focus:ring-sky-100 focus:border-sky-400 focus:bg-white transition-all">
                    </div>
                </div>
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 font-semibold hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 text-white font-semibold shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all flex items-center gap-2">
                        <i class="bi bi-check-lg"></i> Update Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
    document.body.style.overflow = '';
}
function openEditModal(id, day, subject, startTime, endTime, room, teacher) {
    document.getElementById('editId').value = id;
    document.getElementById('editDay').value = day;
    document.getElementById('editSubject').value = subject;
    document.getElementById('editStartTime').value = startTime;
    document.getElementById('editEndTime').value = endTime;
    document.getElementById('editRoom').value = room;
    document.getElementById('editTeacher').value = teacher;
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.style.overflow = '';
}
</script>

<?php require_once '../admin_includes/footer.php'; ?>
